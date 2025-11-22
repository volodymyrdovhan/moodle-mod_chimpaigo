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
 * @package   mod_chimpaigo
 * @category  backup
 * @copyright 2025 Unbit Software S.L.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete chimpaigo structure for backup, with file and id annotations
 */
class backup_chimpaigo_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure of the chimpaigo activity
     *
     * @return array
     */
    protected function define_structure() {

        // Define each element separated.
        $chimpaigo = new backup_nested_element('chimpaigo', ['id'], [
            'name',
            'intro',
            'introformat',
            'typeid',
            'timecreated',
            'timemodified',
        ]);

        // Define sources.
        $chimpaigo->set_source_table('chimpaigo', ['id' => backup::VAR_ACTIVITYID]);

        // Define file annotations.
        $chimpaigo->annotate_files('mod_chimpaigo', 'intro', null);

        // Return the root element (chimpaigo), wrapped into standard activity structure.
        return $this->prepare_activity_structure($chimpaigo);
    }
}
