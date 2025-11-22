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

namespace mod_chimpaigo\local;

use stdClass;

/**
 * LTI setup service for Chimpaigo.
 *
 * Centralizes defaults and ensures the LTI type is present and configured.
 *
 * @package    mod_chimpaigo
 * @copyright  2025 Unbit Software S.L.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lti_setup_service {
    /** @var string */
    private const DEFAULT_LOGIN_URL = 'https://www.chimpaigo.com/edu/moodle/lti/login.aspx';

    /** @var string */
    private const DEFAULT_ICON_URL = 'https://www.chimpaigo.com/edu/moodle/chimpaigo_ico.png';

    /** @var string */
    private const DEFAULT_PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----\n"
        . "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvmwS+7Dz7C8U/ohbmRu9d33DJYZe7fFH\n"
        . "cL2mBRxn1wXsqA6cyMsmFDEm3cpTPV5b7V7SKkD9PKmnsP0aqexuiybNg5dPueGx3otnRAzEQRj4\n"
        . "nBukZfItVI74lP9JlPupmuoVDm6znWb9HfbHMOodKizNDmUyDGvWBcaM48oYNskwPGUaCXqIpZ8G\n"
        . "7F3xz9MVoYog8LIWupRosU0ma2LyP984kzCCNMhoVzWWS1yEyu/cL2r2hTwsh8j3GTYObOVTkK1G\n"
        . "U9Ieq9sYRzuvBPrQnzW92LXDmKnX9EjI/Y/nfIbbFpgeBKxNiU7vg7v2I/+LsuoZE/FwUQ4EGgU9\n"
        . "xarB4QIDAQAB\n"
        . "-----END PUBLIC KEY-----";

    /**
     * Ensure the LTI type exists and has all expected config.
     *
     * @return stdClass|null The lti_types record.
     * @throws \dml_exception
     */
    public function ensure_lti_type(): ?stdClass {
        global $CFG, $DB, $SITE;
        require_once($CFG->dirroot . '/lib/moodlelib.php');
        require_once($CFG->dirroot . '/mod/lti/lib.php');
        require_once($CFG->dirroot . '/mod/lti/locallib.php');

        $repo = new lti_type_repository();
        $baseurl = (new config_service())->get_base_url();
        $loginurl = self::DEFAULT_LOGIN_URL;
        $iconurl = self::DEFAULT_ICON_URL;
        $siteid = isset($SITE->id) ? (int) $SITE->id : 1;

        $existing = $repo->find_by_baseurl_and_version($baseurl, LTI_VERSION_1P3);
        if ($existing) {
            $this->ensure_config($repo, $existing->id, $baseurl, $loginurl, $iconurl, $siteid);
            return $existing;
        }

        $now = time();
        $adminid = get_admin()->id;
        $name = get_string('pluginname', 'mod_chimpaigo');
        $description = get_string('modulename_help', 'mod_chimpaigo');

        $record = (object) [
            'name'          => $name,
            'baseurl'       => $baseurl,
            'icon'          => $iconurl,
            'secureicon'    => $iconurl,
            'description'   => $description,
            'state'         => LTI_TOOL_STATE_CONFIGURED,
            'coursevisible' => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'ltiversion'    => LTI_VERSION_1P3,
            'timecreated'   => $now,
            'timemodified'  => $now,
            'createdby'     => $adminid,
        ];

        $columns = $DB->get_columns('lti_types');
        if (array_key_exists('clientid', $columns)) {
            if (class_exists('core\uuid')) {
                $record->clientid = \core\uuid::generate();
            } else {
                $record->clientid = bin2hex(random_bytes(16));
            }
        }

        $config = $this->build_config_object($baseurl, $loginurl, $iconurl, $siteid);

        $typeid = $repo->insert_with_config($record, $config);
        $this->ensure_config($repo, $typeid, $baseurl, $loginurl, $iconurl, $siteid);

        return $repo->get_type_by_id($typeid);
    }

    /**
     * Build config object using LTI expected keys (with lti_ prefix).
     *
     * @param string $baseurl
     * @param string $loginurl
     * @param string $iconurl
     * @param int $siteid
     * @return stdClass
     */
    private function build_config_object(string $baseurl, string $loginurl, string $iconurl, int $siteid): stdClass {
        $defaults = $this->get_config_defaults($baseurl, $loginurl, $iconurl, $siteid);

        $config = [];
        foreach ($defaults as $name => $value) {
            $key = str_starts_with($name, 'ltiservice_') ? $name : 'lti_' . $name;
            $config[$key] = $value;
        }

        return (object) $config;
    }

    /**
     * Ensure configuration entries exist for the LTI type.
     *
     * @param lti_type_repository $repo
     * @param int $typeid
     * @param string $baseurl
     * @param string $loginurl
     * @param string $iconurl
     * @param int $siteid
     * @return void
     * @throws \dml_exception
     */
    private function ensure_config(
        lti_type_repository $repo,
        int $typeid,
        string $baseurl,
        string $loginurl,
        string $iconurl,
        int $siteid
    ): void {
        $defaults = $this->get_config_defaults($baseurl, $loginurl, $iconurl, $siteid);

        $existing = $repo->get_config_map($typeid);
        foreach ($defaults as $name => $value) {
            if (!array_key_exists($name, $existing)) {
                $repo->insert_config_if_missing($typeid, $name, $value);
            }
        }
    }

    /**
     * Shared defaults for LTI config (unprefixed names as stored in lti_types_config).
     *
     * @param string $baseurl
     * @param string $loginurl
     * @param string $iconurl
     * @param int $siteid
     * @return array
     */
    private function get_config_defaults(string $baseurl, string $loginurl, string $iconurl, int $siteid): array {
        return [
            'acceptgrades'                    => '1',
            'clientid_disabled'               => '',
            'contentitem'                     => '0',
            'coursecategories'                => '',
            'coursevisible'                   => LTI_COURSEVISIBLE_ACTIVITYCHOOSER,
            'course'                          => $siteid,
            'customparameters'                => '',
            'forcessl'                        => '0',
            'initiatelogin'                   => $loginurl,
            'keytype'                         => 'RSA_KEY',
            'launchcontainer'                 => 'embed',
            'ltiservice_gradesynchronization' => '1',
            'ltiservice_memberships'          => '1',
            'ltiservice_toolsettings'         => '1',
            'organizationid'                  => '',
            'organizationid_default'          => 'SITEHOST',
            'organizationurl'                 => '',
            'publickey'                       => self::DEFAULT_PUBLIC_KEY,
            'toolurl'                         => $baseurl,
            'securetoolurl'                   => $baseurl,
            'redirectionuris'                 => $baseurl,
            'sendemailaddr'                   => '1',
            'sendname'                        => '1',
            'icon'                            => $iconurl,
        ];
    }
}
