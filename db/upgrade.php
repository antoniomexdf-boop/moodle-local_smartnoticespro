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

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade steps for local_smartnoticespro.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_smartnoticespro_upgrade(int $oldversion): bool {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2026030603) {
        $table = new xmldb_table('local_smartnoticespro');
        $field = new xmldb_field('hidetitle', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'message');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026030603, 'local', 'smartnotices');
    }

    if ($oldversion < 2026030703) {
        $table = new xmldb_table('local_smartnoticespro');

        $impressions = new xmldb_field('impressions', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'timemodified');
        if (!$dbman->field_exists($table, $impressions)) {
            $dbman->add_field($table, $impressions);
        }

        $closes = new xmldb_field('closes', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'impressions');
        if (!$dbman->field_exists($table, $closes)) {
            $dbman->add_field($table, $closes);
        }

        $confirmations = new xmldb_field('confirmations', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'closes');
        if (!$dbman->field_exists($table, $confirmations)) {
            $dbman->add_field($table, $confirmations);
        }

        upgrade_plugin_savepoint(true, 2026030703, 'local', 'smartnotices');
    }

    if ($oldversion < 2026030704) {
        $table = new xmldb_table('local_smartnoticespro');
        $confirmenabled = new xmldb_field(
            'confirmenabled',
            XMLDB_TYPE_INTEGER,
            '1',
            null,
            XMLDB_NOTNULL,
            null,
            '1',
            'hidetitle'
        );
        if (!$dbman->field_exists($table, $confirmenabled)) {
            $dbman->add_field($table, $confirmenabled);
        }

        $logtable = new xmldb_table('local_smartnoticespro_log');
        if (!$dbman->table_exists($logtable)) {
            $logtable->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $logtable->add_field('noticeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
            $logtable->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $logtable->add_field('action', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, '');
            $logtable->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $logtable->add_field('pageurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
            $logtable->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

            $logtable->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $logtable->add_key('noticeid_fk', XMLDB_KEY_FOREIGN, ['noticeid'], 'local_smartnoticespro', ['id']);
            $logtable->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
            $logtable->add_key('courseid_fk', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);

            $logtable->add_index('action_idx', XMLDB_INDEX_NOTUNIQUE, ['action']);
            $logtable->add_index('timecreated_idx', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);

            $dbman->create_table($logtable);
        }

        upgrade_plugin_savepoint(true, 2026030704, 'local', 'smartnotices');
    }

    if ($oldversion < 2026030707) {
        $table = new xmldb_table('local_smartnoticespro');
        $autonextactivity = new xmldb_field(
            'autonextactivity',
            XMLDB_TYPE_INTEGER,
            '1',
            null,
            XMLDB_NOTNULL,
            null,
            '0',
            'confirmenabled'
        );
        if (!$dbman->field_exists($table, $autonextactivity)) {
            $dbman->add_field($table, $autonextactivity);
        }

        upgrade_plugin_savepoint(true, 2026030707, 'local', 'smartnotices');
    }

    if ($oldversion < 2026030710) {
        $table = new xmldb_table('local_smartnoticespro');
        $autonextactivity = new xmldb_field('autonextactivity');
        if ($dbman->field_exists($table, $autonextactivity)) {
            $dbman->drop_field($table, $autonextactivity);
        }

        upgrade_plugin_savepoint(true, 2026030710, 'local', 'smartnotices');
    }

    if ($oldversion < 2026030711) {
        $table = new xmldb_table('local_smartnoticespro');
        $groupid = new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'courseid');
        if (!$dbman->field_exists($table, $groupid)) {
            $dbman->add_field($table, $groupid);
        }

        $groupkey = new xmldb_key('groupid_fk', XMLDB_KEY_FOREIGN, ['groupid'], 'groups', ['id']);
        if (method_exists($dbman, 'find_key_name')) {
            if (!$dbman->find_key_name($table, $groupkey)) {
                $dbman->add_key($table, $groupkey);
            }
        } else {
            $dbman->add_key($table, $groupkey);
        }

        $groupindex = new xmldb_index('groupid_idx', XMLDB_INDEX_NOTUNIQUE, ['groupid']);
        if (!$dbman->index_exists($table, $groupindex)) {
            $dbman->add_index($table, $groupindex);
        }

        upgrade_plugin_savepoint(true, 2026030711, 'local', 'smartnotices');
    }

    return true;
}
