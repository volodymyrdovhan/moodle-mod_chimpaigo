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
 * Tests for mod_chimpaigo lti_type_repository.
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
 * Unit tests for lti_type_repository.
 */
class mod_chimpaigo_lti_type_repository_test extends advanced_testcase {
    /**
     * Test that the repository reads and restores missing config.
     *
     * @covers mod_chimpaigo\local\lti_type_repository::get_config_map
     * @covers mod_chimpaigo\local\lti_type_repository::insert_config_if_missing
     */
    public function test_repository_reads_and_restores_missing_config(): void {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $baseurl = 'https://example.com/lti/repo-' . random_int(1, PHP_INT_MAX);
        set_config('baseurl', $baseurl, 'mod_chimpaigo');

        $service = new lti_setup_service();
        $type = $service->ensure_lti_type();

        $repo = new lti_type_repository();
        $config = $repo->get_config_map($type->id);

        $this->assertSame($baseurl, $config['toolurl']);
        $this->assertArrayHasKey('organizationid', $config);

        // Remove a config entry and verify the repository can re-add it.
        $DB->delete_records('lti_types_config', ['typeid' => $type->id, 'name' => 'organizationid']);
        $repo->insert_config_if_missing($type->id, 'organizationid', '');
        $refreshed = $repo->get_config_map($type->id);

        $this->assertArrayHasKey('organizationid', $refreshed);
        $this->assertSame('', $refreshed['organizationid']);
    }

    /**
     * Test that the repository can find and get a type by id.
     *
     * @covers mod_chimpaigo\local\lti_type_repository::find_by_baseurl_and_version
     * @covers mod_chimpaigo\local\lti_type_repository::get_type_by_id
     */
    public function test_find_and_get_by_id(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $baseurl = 'https://example.com/lti/repo-find-' . random_int(1, PHP_INT_MAX);
        set_config('baseurl', $baseurl, 'mod_chimpaigo');

        $service = new lti_setup_service();
        $type = $service->ensure_lti_type();

        $repo = new lti_type_repository();
        $found = $repo->find_by_baseurl_and_version($baseurl, LTI_VERSION_1P3);
        $byid = $repo->get_type_by_id($type->id);

        $this->assertNotNull($found);
        $this->assertSame($type->id, $found->id);
        $this->assertNotNull($byid);
        $this->assertSame($type->id, $byid->id);
    }
}
