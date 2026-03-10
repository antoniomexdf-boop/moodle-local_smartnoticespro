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

namespace local_smartnoticespro\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy provider for local_smartnoticespro.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns metadata about this plugin's data.
     *
     * @param \core_privacy\local\metadata\collection $items
     * @return \core_privacy\local\metadata\collection
     */
    public static function get_metadata(\core_privacy\local\metadata\collection $items): \core_privacy\local\metadata\collection {
        $items->add_database_table('local_smartnoticespro', [
            'title' => 'privacy:metadata:local_smartnoticespro:title',
            'message' => 'privacy:metadata:local_smartnoticespro:message',
            'userid' => 'privacy:metadata:local_smartnoticespro:userid',
            'timecreated' => 'privacy:metadata:local_smartnoticespro:timecreated',
            'timemodified' => 'privacy:metadata:local_smartnoticespro:timemodified',
        ], 'privacy:metadata:local_smartnoticespro');

        return $items;
    }

    /**
     * Export user data.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist
     * @return void
     */
    public static function export_user_data(\core_privacy\local\request\approved_contextlist $contextlist): void {
        global $DB;

        if (empty($contextlist->get_contextids())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        $records = $DB->get_records('local_smartnoticespro', ['userid' => $userid], 'timecreated DESC');
        if (empty($records)) {
            return;
        }

        $context = \context_system::instance();
        $export = [];
        foreach ($records as $record) {
            $export[] = (object)[
                'title' => $record->title,
                'message' => $record->message,
                'timecreated' => \core_privacy\local\request\transform::datetime($record->timecreated),
                'timemodified' => \core_privacy\local\request\transform::datetime($record->timemodified),
            ];
        }

        \core_privacy\local\request\writer::with_context($context)->export_data(
            [get_string('notices', 'local_smartnoticespro')],
            (object)['items' => $export]
        );
    }

    /**
     * Delete user data for all contexts.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;
        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }
        $DB->delete_records('local_smartnoticespro');
    }

    /**
     * Delete user data for selected users.
     *
     * @param \core_privacy\local\request\approved_userlist $userlist
     * @return void
     */
    public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist): void {
        global $DB;

        if ($userlist->get_context()->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        list($insql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $DB->delete_records_select('local_smartnoticespro', "userid {$insql}", $params);
    }

    /**
     * Delete user data.
     *
     * @param \core_privacy\local\request\approved_contextlist $contextlist
     * @return void
     */
    public static function delete_data_for_user(\core_privacy\local\request\approved_contextlist $contextlist): void {
        global $DB;
        $userid = $contextlist->get_user()->id;
        $DB->delete_records('local_smartnoticespro', ['userid' => $userid]);
    }

    /**
     * Get contexts containing user data.
     *
     * @param int $userid
     * @return \core_privacy\local\request\contextlist
     */
    public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
        global $DB;

        $contextlist = new \core_privacy\local\request\contextlist();
        if ($DB->record_exists('local_smartnoticespro', ['userid' => $userid])) {
            $contextlist->add_system_context();
        }
        return $contextlist;
    }

    /**
     * Get users in a context.
     *
     * @param \core_privacy\local\request\userlist $userlist
     * @return void
     */
    public static function get_users_in_context(\core_privacy\local\request\userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();
        if ($context->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $sql = "SELECT DISTINCT userid
                  FROM {local_smartnoticespro}
                 WHERE userid > 0";
        $userlist->add_from_sql('userid', $sql, []);
    }
}
