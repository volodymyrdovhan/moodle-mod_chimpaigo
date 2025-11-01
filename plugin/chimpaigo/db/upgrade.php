<?php
// This file keeps track of upgrades to the mod_chimpaigo plugin.
defined('MOODLE_INTERNAL') || die();

/**
 * Execute mod_chimpaigo upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_chimpaigo_upgrade($oldversion) {
    global $DB;

    // Template for future upgrades:
    // if ($oldversion < 2025102801) {
    //     // DB/structure changes here.
    //     upgrade_mod_savepoint(true, 2025102801, 'chimpaigo');
    // }

    return true;
}
