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

use mod_chimpaigo\local\lti_setup_service;

/**
 * Upgrade procedure for the Chimpaigo module.
 *
 * @package    mod_chimpaigo
 * @copyright  2025 Unbit Software S.L.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @param int $oldversion the version we are upgrading from
 * @return bool
 */
function xmldb_chimpaigo_upgrade($oldversion) {

    if ($oldversion < 2025112200) {
        $service = new lti_setup_service();
        $service->ensure_lti_type();

        upgrade_mod_savepoint(true, 2025112200, 'chimpaigo');
    }

    return true;
}
