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
 * Structure step to restore one chimpaigo activity
 *
 * @package   mod_chimpaigo
 * @category  backup
 * @copyright 2025 Unbit Software S.L.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_chimpaigo_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure of the chimpaigo activity
     *
     * @return array
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('chimpaigo', '/activity/chimpaigo');
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the chimpaigo record
     *
     * @param array $data The data from the XML file
     * @return void
     */
    protected function process_chimpaigo($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Insert the chimpaigo record.
        $newitemid = $DB->insert_record('chimpaigo', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Actions to be executed after the restore is completed
     *
     * @return void
     */
    protected function after_execute() {
        // Add chimpaigo related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_chimpaigo', 'intro', null);
    }
}
