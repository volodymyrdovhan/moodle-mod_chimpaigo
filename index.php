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
 * List of all chimpaigos in course.
 *
 * @package    mod_chimpaigo
 * @copyright  2025 Unbit Software S.L.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('id', PARAM_INT); // Course id.

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

// Trigger instances list viewed event.
$event = \mod_chimpaigo\event\course_module_instance_list_viewed::create(
    ['context' => context_course::instance($course->id)
]);
$event->add_record_snapshot('course', $course);
$event->trigger();

$PAGE->set_url('/mod/chimpaigo/index.php', ['id' => $id]);
$PAGE->set_title(get_string('modulenameplural', 'mod_chimpaigo'));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

if (!$chimps = get_all_instances_in_course('chimpaigo', $course)) {
    echo $OUTPUT->notification(get_string('none'), 'info');
    echo $OUTPUT->footer();
    exit;
}

$table = new html_table();
$table->head = [get_string('name'), get_string('moduleintro')];
foreach ($chimps as $c) {
    $link = html_writer::link(new moodle_url('/mod/chimpaigo/view.php', ['id' => $c->coursemodule]), format_string($c->name));
    $table->data[] = [$link, format_text($c->intro, $c->introformat)];
}
echo html_writer::table($table);

echo $OUTPUT->footer();
