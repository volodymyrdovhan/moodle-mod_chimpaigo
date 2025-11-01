<?php
require('../../config.php');

$id = required_param('id', PARAM_INT); // Course id.
$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_login($course);
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
$table->head = [get_string('name'), get_string('intro')];
foreach ($chimps as $c) {
    $link = html_writer::link(new moodle_url('/mod/chimpaigo/view.php', ['id' => $c->coursemodule]), format_string($c->name));
    $table->data[] = [$link, format_text($c->intro, $c->introformat)];
}
echo html_writer::table($table);

echo $OUTPUT->footer();
