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
 * Repository wrapper around lti_types and lti_types_config.
 *
 * @package    mod_chimpaigo
 * @copyright  2025 Unbit Software S.L.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lti_type_repository {
    /**
     * Find an LTI type by base URL and version using text-safe comparison.
     *
     * @param string $baseurl
     * @param string $ltiversion
     * @return stdClass|null
     */
    public function find_by_baseurl_and_version(string $baseurl, string $ltiversion): ?stdClass {
        global $DB;
        $sql = 'SELECT *
                  FROM {lti_types}
                 WHERE ' . $DB->sql_compare_text('baseurl') . ' = ' . $DB->sql_compare_text(':baseurl') . '
                   AND ltiversion = :ltiversion';

        return $DB->get_record_sql($sql, ['baseurl' => $baseurl, 'ltiversion' => $ltiversion]) ?: null;
    }

    /**
     * Insert a new LTI type with configuration via core helper.
     *
     * @param stdClass $type
     * @param stdClass $config
     * @return int|false New type id or false on failure.
     */
    public function insert_with_config(stdClass $type, stdClass $config): int {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        return (int) lti_add_type($type, $config);
    }

    /**
     * Get existing config values as name => value.
     *
     * @param int $typeid
     * @return array
     */
    public function get_config_map(int $typeid): array {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        return lti_get_type_config($typeid);
    }

    /**
     * Insert a single config entry if missing.
     *
     * @param int $typeid
     * @param string $name
     * @param string $value
     * @return void
     */
    public function insert_config_if_missing(int $typeid, string $name, string $value): void {
        global $CFG, $DB;

        if ($DB->record_exists('lti_types_config', ['typeid' => $typeid, 'name' => $name])) {
            return;
        }

        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        $record = (object) [
            'typeid' => $typeid,
            'name'   => $name,
            'value'  => $value,
        ];
        lti_add_config($record);
    }

    /**
     * Get a type by id.
     *
     * @param int $typeid
     * @return stdClass|null
     */
    public function get_type_by_id(int $typeid): ?stdClass {
        global $CFG;
        require_once($CFG->dirroot . '/mod/lti/locallib.php');
        return lti_get_type($typeid) ?: null;
    }
}
