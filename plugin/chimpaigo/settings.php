<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('modsettingchimpaigo', get_string('pluginname', 'mod_chimpaigo'));
    $ADMIN->add('modsettings', $settings);

    $settings->add(new admin_setting_configtext(
        'mod_chimpaigo/baseurl',
        'Base URL de la herramienta',
        'URL de lanzamiento LTI 1.3 de chimpAIgo!',
        'https://www.chimpaigo.com/edu/moodle/lti/launch.aspx',
        PARAM_URL
    ));
}
