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
 * Chimpaigo module library.
 *
 * @package    mod_chimpaigo
 * @copyright  2025 Unbit Software S.L.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_chimpaigo\local\config_service;

defined('MOODLE_INTERNAL') || die();

/**
 * List of features supported in Chimpaigo module.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function chimpaigo_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_SHOW_DESCRIPTION: return true;
        case FEATURE_GRADE_HAS_GRADE: return false;
        case FEATURE_BACKUP_MOODLE2: return true;
        default: return null;
    }
}

/**
 * Add a new instance of the Chimpaigo module.
 *
 * @param stdClass $data An object from the form in mod_form.php
 * @param mod_form $mform The form instance
 * @return int The id of the newly inserted Chimpaigo record
 */
function chimpaigo_add_instance($data, $mform) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/lti/lib.php');

    $data->timecreated = time();
    $data->timemodified = time();

    // Ensure LTI 1.3 type by baseurl+ltiversion.
    $baseurl = (new config_service())->get_base_url();
    $type = $DB->get_record('lti_types', ['baseurl' => $baseurl, 'ltiversion' => '1.3.0']);

    if ($type) {
        $data->typeid = $type->id;
    } else {
        $type = (object)[
            'name'          => 'chimpAIgo!',
            'baseurl'       => $baseurl,
            'description'   => 'Generador de quizzes por IA',
            'ltiversion'    => '1.3.0',
            'course'        => 1,
            'state'         => 1,
            'coursevisible' => 2,
            'visible'       => 1,
            'timecreated'   => time(),
            'timemodified'  => time()
        ];
        $data->typeid = $DB->insert_record('lti_types', $type);
    }

    return $DB->insert_record('chimpaigo', $data);
}

/**
 * Update an instance of the Chimpaigo module.
 *
 * @param stdClass $data An object from the form in mod_form.php
 * @param mod_form $mform The form instance
 * @return bool True if the instance was updated, false otherwise
 */
function chimpaigo_update_instance($data, $mform) {
    global $DB;
    $data->id = $data->instance;
    $data->timemodified = time();
    return $DB->update_record('chimpaigo', $data);
}

/**
 * Delete an instance of the Chimpaigo module.
 *
 * @param int $id The id of the instance to delete
 * @return bool True if the instance was deleted, false otherwise
 */
function chimpaigo_delete_instance($id) {
    global $DB;
    if (!$record = $DB->get_record('chimpaigo', ['id' => $id])) {
        return false;
    }
    $DB->delete_records('chimpaigo', ['id' => $id]);
    return true;
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $chimpaigo  chimpaigo object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 */
function chimpaigo_view($chimpaigo, $course, $cm, $context): void {

    // Trigger course_module_viewed event.
    $params = [
        'context' => $context,
        'objectid' => $chimpaigo->id
    ];

    $event = \mod_chimpaigo\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('chimpaigo', $chimpaigo);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}
