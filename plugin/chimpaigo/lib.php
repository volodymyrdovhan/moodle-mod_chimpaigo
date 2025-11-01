<?php
defined('MOODLE_INTERNAL') || die();

function chimpaigo_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_SHOW_DESCRIPTION: return true;
        case FEATURE_GRADE_HAS_GRADE: return false;
        case FEATURE_BACKUP_MOODLE2: return true;
        default: return null;
    }
}

function chimpaigo_add_instance($data, $mform) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/lti/lib.php');

    $data->timecreated = time();
    $data->timemodified = time();

    // Garantiza tipo LTI 1.3 por baseurl+ltiversion.
    $baseurl = get_config('mod_chimpaigo', 'baseurl') ?: 'https://www.chimpaigo.com/edu/moodle/lti/launch.aspx';
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

function chimpaigo_update_instance($data, $mform) {
    global $DB;
    $data->id = $data->instance;
    $data->timemodified = time();
    return $DB->update_record('chimpaigo', $data);
}

function chimpaigo_delete_instance($id) {
    global $DB;
    if (!$record = $DB->get_record('chimpaigo', ['id' => $id])) {
        return false;
    }
    $DB->delete_records('chimpaigo', ['id' => $id]);
    return true;
}
