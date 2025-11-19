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
 * Chimpaigo module view page.
 *
 * @package    mod_chimpaigo
 * @copyright  2025 Unbit Software S.L.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_chimpaigo\local\config_service;

require('../../config.php');

$id = required_param('id', PARAM_INT); // Course Module ID.

$cm = get_coursemodule_from_id('chimpaigo', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$chim = $DB->get_record('chimpaigo', ['id' => $cm->instance], '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/chimpaigo:view', $context);

// Completion and trigger events.
chimpaigo_view($chim, $course, $cm, $context);

$PAGE->set_url('/mod/chimpaigo/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($chim->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

if (!empty($chim->intro)) {
    echo $OUTPUT->box(format_module_intro('chimpaigo', $chim, $cm->id), 'generalbox mod_introbox');
}

$baseurl = (new config_service())->get_base_url();

echo html_writer::tag('p', get_string('view_launch', 'mod_chimpaigo'));
echo $OUTPUT->single_button(new moodle_url($baseurl), get_string('view_launch', 'mod_chimpaigo'), 'get');

echo $OUTPUT->footer();
