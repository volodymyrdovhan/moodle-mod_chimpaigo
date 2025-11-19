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

namespace mod_chimpaigo\local;

/**
 * Configuration service for the Chimpaigo module.
 *
 * @package    mod_chimpaigo
 * @copyright  2025 Unbit Software S.L.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_service {

    /** @var string Default base URL for the Chimpaigo module. */
    public const DEFAULT_BASE_URL = 'https://www.chimpaigo.com/edu/moodle/lti/launch.aspx';

    /**
     * Get the base URL for the Chimpaigo module.
     *
     * @return string
     */
    public function get_base_url(): string {
        $config_url = (string) get_config('mod_chimpaigo', 'baseurl');
        return !empty($config_url) ? $config_url : self::DEFAULT_BASE_URL;
    }
}
