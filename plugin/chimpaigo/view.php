<?php
require('../../config.php');

$id = required_param('id', PARAM_INT); // Course Module ID.

$cm = get_coursemodule_from_id('chimpaigo', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$chim = $DB->get_record('chimpaigo', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/chimpaigo/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($chim->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();

if (!empty($chim->intro)) {
    echo $OUTPUT->box(format_module_intro('chimpaigo', $chim, $cm->id), 'generalbox mod_introbox');
}

$baseurl = get_config('mod_chimpaigo', 'baseurl') ?: 'https://www.chimpaigo.com/edu/moodle/lti/launch.aspx';

// Botón simple. En el siguiente paso podemos delegar en mod_lti para OIDC automático.
echo html_writer::tag('p', get_string('view_launch', 'mod_chimpaigo'));
echo $OUTPUT->single_button(new moodle_url($baseurl), get_string('view_launch', 'mod_chimpaigo'), 'get');

echo $OUTPUT->footer();
