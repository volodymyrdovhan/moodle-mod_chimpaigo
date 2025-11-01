<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_chimpaigo_install() {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/lib/moodlelib.php'); // random_string, get_admin

    // Endpoints y datos de la herramienta.
    $loginurl   = 'https://www.chimpaigo.com/edu/moodle/lti/login.aspx';
    $launchurl  = 'https://www.chimpaigo.com/edu/moodle/lti/launch.aspx';
    $iconurl    = 'https://www.chimpaigo.com/edu/moodle/chimpaigo_ico.png';
    $ltiversion = '1.3.0';
    $name       = 'chimpAIgo!';
    $description= 'Generador de quizzes por IA';
    $pem = "-----BEGIN PUBLIC KEY-----\n"
         . "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvmwS+7Dz7C8U/ohbmRu9d33DJYZe7fFH\n"
         . "cL2mBRxn1wXsqA6cyMsmFDEm3cpTPV5b7V7SKkD9PKmnsP0aqexuiybNg5dPueGx3otnRAzEQRj4\n"
         . "nBukZfItVI74lP9JlPupmuoVDm6znWb9HfbHMOodKizNDmUyDGvWBcaM48oYNskwPGUaCXqIpZ8G\n"
         . "7F3xz9MVoYog8LIWupRosU0ma2LyP984kzCCNMhoVzWWS1yEyu/cL2r2hTwsh8j3GTYObOVTkK1G\n"
         . "U9Ieq9sYRzuvBPrQnzW92LXDmKnX9EjI/Y/nfIbbFpgeBKxNiU7vg7v2I/+LsuoZE/FwUQ4EGgU9\n"
         . "xarB4QIDAQAB\n"
         . "-----END PUBLIC KEY-----";

    $now     = time();
    $adminid = get_admin()->id;

    // Evitar duplicados por baseurl+ltiversion.
    $exists = $DB->get_record('lti_types', ['baseurl' => $launchurl, 'ltiversion' => $ltiversion]);
    if ($exists) {
        return true;
    }

    // Insert en lti_types.
    $rec = (object)[
        'name'          => $name,
        'baseurl'       => $launchurl,
        'icon'          => $iconurl,
        'secureicon'    => $iconurl,
        'description'   => $description,
        'state'         => 1,   // activo
        'course'        => 1,   // site-wide
        'coursevisible' => 2,   // visible en chooser
        'ltiversion'    => $ltiversion,
        'timecreated'   => $now,
        'timemodified'  => $now,
        'createdby'     => $adminid
    ];

    // clientid si la columna existe.
    $cols = $DB->get_columns('lti_types');
    if (array_key_exists('clientid', $cols)) {
        if (class_exists('core\uuid')) {
            $rec->clientid = \core\uuid::generate();
        } else {
            $rec->clientid = bin2hex(random_bytes(16));
        }
    }

    $typeid = $DB->insert_record('lti_types', $rec);

    // Config asociada en lti_types_config.
    $rows = [
        ['acceptgrades',                    '1'],
        ['clientid_disabled',               ''],
        ['contentitem',                     '0'],
        ['coursecategories',                ''],
        ['coursevisible',                   '2'],
        ['course',                          '1'],
        ['customparameters',                ''],
        ['forcessl',                        '0'],
        ['initiatelogin',                   $loginurl],
        ['keytype',                         'RSA_KEY'],
        ['launchcontainer',                 'embed'],
        ['ltiservice_gradesynchronization', '1'],
        ['ltiservice_memberships',          '1'],
        ['ltiservice_toolsettings',         '1'],
        ['organizationid',                  ''],
        ['organizationid_default',          'SITEHOST'],
        ['organizationurl',                 ''],
        ['publickey',                       $pem],
        ['redirectionuris',                 $launchurl],
        ['sendemailaddr',                   '1'],
        ['sendname',                        '1'],
        ['icon',                            $iconurl]
    ];
    foreach ($rows as $r) {
        $DB->insert_record('lti_types_config', (object)[
            'typeid' => $typeid,
            'name'   => $r[0],
            'value'  => $r[1]
        ]);
    }

    return true;
}
