<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Tests for mod_chimpaigo lti_setup_service.
 *
 * @package    mod_chimpaigo
 * @category   test
 * @copyright  2025 Unbit Software S.L.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_chimpaigo\local\lti_setup_service;
use mod_chimpaigo\local\lti_type_repository;

/**
 * Unit tests for lti_setup_service.
 */
class mod_chimpaigo_lti_setup_service_test extends advanced_testcase {
    /**
     * Test that the LTI type is created and is idempotent.
     *
     * @covers \mod_chimpaigo\local\lti_setup_service::ensure_lti_type
     */
    public function test_creates_lti_type_and_is_idempotent(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $baseurl = 'https://example.com/lti/setup-' . random_int(1, PHP_INT_MAX);
        set_config('baseurl', $baseurl, 'mod_chimpaigo');

        $service = new lti_setup_service();
        $first = $service->ensure_lti_type();
        $second = $service->ensure_lti_type();

        $this->assertSame($first->id, $second->id);
        $this->assertSame($baseurl, $first->baseurl);
        $this->assertSame(LTI_VERSION_1P3, $first->ltiversion);
    }

    /**
     * Test that missing config entries are readded.
     *
     * @covers \mod_chimpaigo\local\lti_setup_service::ensure_lti_type
     * @covers \mod_chimpaigo\local\lti_type_repository::get_config_map
     * @covers \mod_chimpaigo\local\lti_type_repository::insert_config_if_missing
     * @covers \mod_chimpaigo\local\lti_setup_service::ensure_config
     */
    public function test_missing_config_entries_are_readded(): void {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $baseurl = 'https://example.com/lti/setup-repair-' . random_int(1, PHP_INT_MAX);
        set_config('baseurl', $baseurl, 'mod_chimpaigo');

        $service = new lti_setup_service();
        $type = $service->ensure_lti_type();

        // Remove a config entry to simulate drift.
        $DB->delete_records('lti_types_config', ['typeid' => $type->id, 'name' => 'launchcontainer']);

        $service->ensure_lti_type();

        $config = (new lti_type_repository())->get_config_map($type->id);
        $this->assertArrayHasKey('launchcontainer', $config);
        $this->assertSame('embed', $config['launchcontainer']);
    }
}
