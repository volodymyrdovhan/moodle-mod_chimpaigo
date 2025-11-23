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
 * Tests for mod_chimpaigo config_service.
 *
 * @package    mod_chimpaigo
 * @category   test
 * @copyright  2025 Unbit Software S.L.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use mod_chimpaigo\local\config_service;

/**
 * Unit tests for config_service.
 */
class mod_chimpaigo_config_service_test extends advanced_testcase {
    /**
     * Test that the default base URL is used when no configuration is set.
     *
     * @covers \mod_chimpaigo\local\config_service::get_base_url
     * @covers \mod_chimpaigo\local\config_service::DEFAULT_BASE_URL
     */
    public function test_default_base_url_used_when_not_configured(): void {
        $this->resetAfterTest(true);
        unset_config('baseurl', 'mod_chimpaigo');

        $service = new config_service();
        $this->assertSame(config_service::DEFAULT_BASE_URL, $service->get_base_url());
    }

    /**
     * Test that the configured base URL is returned when it is set.
     *
     * @covers \mod_chimpaigo\local\config_service::get_base_url
     * @covers \mod_chimpaigo\local\config_service::DEFAULT_BASE_URL
     */
    public function test_configured_base_url_is_returned(): void {
        $this->resetAfterTest(true);
        $custom = 'https://example.com/custom/lti';
        set_config('baseurl', $custom, 'mod_chimpaigo');

        $service = new config_service();
        $this->assertSame($custom, $service->get_base_url());
    }
}
