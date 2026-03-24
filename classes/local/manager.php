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

namespace local_smartnoticespro\local;

use context_course;
use context_system;
use moodle_page;
use stdClass;

/**
 * Notice manager.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** @var string */
    public const TABLE = 'local_smartnoticespro';
    /** @var string */
    public const SCOPE_GLOBAL = 'global';
    /** @var string */
    public const SCOPE_COURSE = 'course';

    /** @var string */
    public const ROLE_ALL = 'all';
    /** @var string */
    public const ROLE_STUDENT = 'student';
    /** @var string */
    public const ROLE_TEACHER = 'teacher';
    /** @var string */
    public const ROLE_MANAGER = 'manager';

    /** @var string */
    public const LOCATION_LOGIN = 'login';
    /** @var string */
    public const LOCATION_FRONTPAGE = 'frontpage';
    /** @var string */
    public const LOCATION_DASHBOARD = 'dashboard';
    /** @var string */
    public const LOCATION_MYCOURSES = 'mycourses';
    /** @var string */
    public const LOCATION_COURSE = 'course';

    /**
     * Get available scopes.
     *
     * @return array
     */
    public static function get_scope_options(): array {
        return [
            self::SCOPE_GLOBAL => get_string('scopeglobal', 'local_smartnoticespro'),
            self::SCOPE_COURSE => get_string('scopecourse', 'local_smartnoticespro'),
        ];
    }

    /**
     * Get available target roles.
     *
     * @return array
     */
    public static function get_target_role_options(): array {
        return [
            self::ROLE_ALL => get_string('targetrole_all', 'local_smartnoticespro'),
            self::ROLE_STUDENT => self::get_role_label_from_archetype('student', get_string('archetypestudent')),
            self::ROLE_TEACHER => self::get_teacher_role_label(),
            self::ROLE_MANAGER => self::get_role_label_from_archetype('manager', get_string('archetypemanager')),
        ];
    }

    /**
     * Get role label from Moodle role table by archetype.
     *
     * @param string $archetype
     * @param string $fallback
     * @return string
     */
    protected static function get_role_label_from_archetype(string $archetype, string $fallback): string {
        global $DB;

        $role = $DB->get_record('role', ['archetype' => $archetype], '*', IGNORE_MULTIPLE);
        if (!$role) {
            return $fallback;
        }

        return role_get_name($role, context_system::instance(), ROLENAME_ALIAS, true);
    }

    /**
     * Get teacher-like role label, preferring editingteacher.
     *
     * @return string
     */
    protected static function get_teacher_role_label(): string {
        global $DB;

        $roles = $DB->get_records_list('role', 'archetype', ['editingteacher', 'teacher'], 'sortorder ASC, id ASC');
        foreach ($roles as $role) {
            return role_get_name($role, context_system::instance(), ROLENAME_ALIAS, true);
        }

        return get_string('archetypeteacher');
    }

    /**
     * Get available locations.
     *
     * @return array
     */
    public static function get_location_options(): array {
        return [
            self::LOCATION_LOGIN => get_string('location_login', 'local_smartnoticespro'),
            self::LOCATION_FRONTPAGE => get_string('location_frontpage', 'local_smartnoticespro'),
            self::LOCATION_DASHBOARD => get_string('location_dashboard', 'local_smartnoticespro'),
            self::LOCATION_MYCOURSES => get_string('location_mycourses', 'local_smartnoticespro'),
            self::LOCATION_COURSE => get_string('location_course', 'local_smartnoticespro'),
        ];
    }

    /**
     * Detect current location.
     *
     * @param moodle_page $page
     * @return string|null
     */
    public static function detect_current_location(moodle_page $page): ?string {
        $pagetype = (string)$page->pagetype;
        $path = '';
        if (!empty($page->url)) {
            $path = (string)$page->url->get_path();
        }

        if (strpos($pagetype, 'login-') === 0 || $path === '/login/index.php') {
            return self::LOCATION_LOGIN;
        }

        if ($pagetype === 'site-index' || $path === '/index.php') {
            return self::LOCATION_FRONTPAGE;
        }

        if ($pagetype === 'my-index' || $path === '/my/' || $path === '/my/index.php') {
            return self::LOCATION_DASHBOARD;
        }

        if ($pagetype === 'my-courses' || $path === '/my/courses.php') {
            return self::LOCATION_MYCOURSES;
        }

        if (strpos($pagetype, 'course-view') === 0 || $path === '/course/view.php') {
            return self::LOCATION_COURSE;
        }

        return null;
    }

    /**
     * Build comma-separated locations string.
     *
     * @param array $rawlocations
     * @return string
     */
    public static function normalize_locations(array $rawlocations): string {
        $valid = array_keys(self::get_location_options());
        $locations = [];

        foreach ($rawlocations as $location => $value) {
            if (!empty($value) && in_array($location, $valid, true)) {
                $locations[] = $location;
            }
        }

        return implode(',', $locations);
    }

    /**
     * Convert locations csv to an array.
     *
     * @param string $locations
     * @return array
     */
    public static function locations_to_array(string $locations): array {
        if ($locations === '') {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $locations))));
    }

    /**
     * Build defaults for location checkboxes.
     *
     * @param string $locationscsv
     * @return array
     */
    public static function locations_to_form_data(string $locationscsv): array {
        $selected = array_flip(self::locations_to_array($locationscsv));
        $defaults = [];

        foreach (self::get_location_options() as $key => $label) {
            $defaults['location_' . $key] = isset($selected[$key]) ? 1 : 0;
        }

        return $defaults;
    }

    /**
     * Validate input payload.
     *
     * @param stdClass $data
     * @return void
     */
    public static function validate_notice_data(stdClass $data): void {
        $scopes = array_keys(self::get_scope_options());
        $roles = array_keys(self::get_target_role_options());
        $locations = self::locations_to_array($data->locations ?? '');
        $validlocations = array_keys(self::get_location_options());

        if (!in_array($data->scope, $scopes, true)) {
            throw new \moodle_exception('error:invalidscope', 'local_smartnoticespro');
        }

        if (!in_array($data->targetroles, $roles, true)) {
            throw new \moodle_exception('error:invalidtargetrole', 'local_smartnoticespro');
        }

        if (empty($locations)) {
            throw new \moodle_exception('error:nolocations', 'local_smartnoticespro');
        }

        foreach ($locations as $location) {
            if (!in_array($location, $validlocations, true)) {
                throw new \moodle_exception('error:invalidlocation', 'local_smartnoticespro');
            }
        }

        if (!empty($data->timestart) && !empty($data->timeend) && $data->timeend < $data->timestart) {
            throw new \moodle_exception('error:dateinvalid', 'local_smartnoticespro');
        }

        if ($data->scope === self::SCOPE_COURSE && empty($data->courseid)) {
            throw new \moodle_exception('error:courseidrequired', 'local_smartnoticespro');
        }
    }

    /**
     * Create notice.
     *
     * @param stdClass $data
     * @return int
     */
    public static function create_notice(stdClass $data): int {
        global $DB, $USER;

        self::validate_notice_data($data);

        $record = new stdClass();
        $record->title = trim($data->title);
        $record->message = $data->message;
        $record->hidetitle = !empty($data->hidetitle) ? 1 : 0;
        $record->confirmenabled = !empty($data->confirmenabled) ? 1 : 0;
        $record->active = !empty($data->active) ? 1 : 0;
        $record->scope = $data->scope;
        $record->targetroles = $data->targetroles;
        $record->locations = $data->locations;
        $record->courseid = ($data->scope === self::SCOPE_COURSE) ? (int)$data->courseid : null;
        $record->groupid = ($data->scope === self::SCOPE_COURSE && !empty($data->groupid)) ? (int)$data->groupid : null;
        $record->timestart = (int)($data->timestart ?? 0);
        $record->timeend = (int)($data->timeend ?? 0);
        $record->userid = (int)$USER->id;
        $record->timecreated = time();
        $record->timemodified = $record->timecreated;
        $record->impressions = 0;
        $record->closes = 0;
        $record->confirmations = 0;

        return (int)$DB->insert_record(self::TABLE, $record);
    }

    /**
     * Update notice.
     *
     * @param stdClass $data
     * @return void
     */
    public static function update_notice(stdClass $data): void {
        global $DB;

        self::validate_notice_data($data);

        $record = $DB->get_record(self::TABLE, ['id' => $data->id], '*', MUST_EXIST);
        $record->title = trim($data->title);
        $record->message = $data->message;
        $record->hidetitle = !empty($data->hidetitle) ? 1 : 0;
        $record->confirmenabled = !empty($data->confirmenabled) ? 1 : 0;
        $record->active = !empty($data->active) ? 1 : 0;
        $record->scope = $data->scope;
        $record->targetroles = $data->targetroles;
        $record->locations = $data->locations;
        $record->courseid = ($data->scope === self::SCOPE_COURSE) ? (int)$data->courseid : null;
        $record->groupid = ($data->scope === self::SCOPE_COURSE && !empty($data->groupid)) ? (int)$data->groupid : null;
        $record->timestart = (int)($data->timestart ?? 0);
        $record->timeend = (int)($data->timeend ?? 0);
        $record->timemodified = time();

        $DB->update_record(self::TABLE, $record);
    }

    /**
     * Delete notice.
     *
     * @param int $id
     * @return void
     */
    public static function delete_notice(int $id): void {
        global $DB;
        $DB->delete_records(self::TABLE, ['id' => $id]);
    }

    /**
     * Get one notice by id.
     *
     * @param int $id
     * @return stdClass|null
     */
    public static function get_notice(int $id): ?stdClass {
        global $DB;
        return $DB->get_record(self::TABLE, ['id' => $id]) ?: null;
    }

    /**
     * Get all notices.
     *
     * @return stdClass[]
     */
    public static function get_all_notices(): array {
        global $DB;
        return $DB->get_records(self::TABLE, null, 'timemodified DESC');
    }

    /**
     * Get all notices paginated.
     *
     * @param int $limitfrom
     * @param int $limitnum
     * @param string $sort
     * @param string $dir
     * @return stdClass[]
     */
    public static function get_all_notices_paginated(
        int $limitfrom,
        int $limitnum,
        string $sort = 'timemodified',
        string $dir = 'DESC'
    ): array {
        global $DB;
        return $DB->get_records(self::TABLE, null, self::get_sort_sql($sort, $dir), '*', $limitfrom, $limitnum);
    }

    /**
     * Count all notices.
     *
     * @return int
     */
    public static function count_all_notices(): int {
        global $DB;
        return (int)$DB->count_records(self::TABLE);
    }

    /**
     * Get notices for a given course.
     *
     * @param int $courseid
     * @return stdClass[]
     */
    public static function get_course_notices(int $courseid): array {
        global $DB;
        return $DB->get_records(self::TABLE, ['scope' => self::SCOPE_COURSE, 'courseid' => $courseid], 'timemodified DESC');
    }

    /**
     * Get course notices paginated.
     *
     * @param int $courseid
     * @param int $limitfrom
     * @param int $limitnum
     * @param string $sort
     * @param string $dir
     * @return stdClass[]
     */
    public static function get_course_notices_paginated(
        int $courseid,
        int $limitfrom,
        int $limitnum,
        string $sort = 'timemodified',
        string $dir = 'DESC'
    ): array {
        global $DB;
        return $DB->get_records(
            self::TABLE,
            ['scope' => self::SCOPE_COURSE, 'courseid' => $courseid],
            self::get_sort_sql($sort, $dir),
            '*',
            $limitfrom,
            $limitnum
        );
    }

    /**
     * Build safe ORDER BY clause for listings.
     *
     * @param string $sort
     * @param string $dir
     * @return string
     */
    public static function get_sort_sql(string $sort, string $dir): string {
        $allowed = [
            'id' => 'id',
            'title' => 'title',
            'active' => 'active',
            'timestart' => 'timestart',
            'timeend' => 'timeend',
            'timemodified' => 'timemodified',
        ];

        $field = $allowed[$sort] ?? 'timemodified';
        $direction = strtoupper($dir) === 'ASC' ? 'ASC' : 'DESC';

        return $field . ' ' . $direction . ', id DESC';
    }

    /**
     * Count course notices.
     *
     * @param int $courseid
     * @return int
     */
    public static function count_course_notices(int $courseid): int {
        global $DB;
        return (int)$DB->count_records(self::TABLE, ['scope' => self::SCOPE_COURSE, 'courseid' => $courseid]);
    }

    /**
     * Get active notices for current request.
     *
     * @param string $location
     * @param moodle_page $page
     * @return array
     */
    public static function get_active_notices_for_location(string $location, moodle_page $page): array {
        global $DB, $USER;

        $now = time();
        $params = [
            'active' => 1,
            'global' => self::SCOPE_GLOBAL,
            'location' => '%' . $location . '%',
            'now1' => $now,
            'now2' => $now,
        ];

        $courseid = 0;
        if ($location === self::LOCATION_COURSE && !empty($page->course->id)) {
            $courseid = (int)$page->course->id;
        }

        $sql = "SELECT *
                  FROM {" . self::TABLE . "}
                 WHERE active = :active
                   AND locations LIKE :location
                   AND (timestart = 0 OR timestart <= :now1)
                   AND (timeend = 0 OR timeend >= :now2)";

        if ($courseid > 0) {
            $sql .= " AND ((scope = :global) OR (scope = :course AND courseid = :courseid))";
            $params['course'] = self::SCOPE_COURSE;
            $params['courseid'] = $courseid;
        } else {
            $sql .= " AND scope = :global";
        }

        $sql .= " ORDER BY timemodified DESC";

        $records = $DB->get_records_sql($sql, $params);
        $visible = [];
        $confirmednoticeids = [];

        if (isloggedin() && !isguestuser() && !empty($records)) {
            $confirmable = [];
            foreach ($records as $record) {
                if (!empty($record->confirmenabled)) {
                    $confirmable[] = (int)$record->id;
                }
            }

            if (!empty($confirmable)) {
                [$insql, $inparams] = $DB->get_in_or_equal($confirmable, SQL_PARAMS_NAMED);
                $confirmsql = "SELECT DISTINCT noticeid
                                 FROM {local_smartnoticespro_log}
                                WHERE userid = :userid
                                  AND action = :action
                                  AND noticeid {$insql}";
                $confirmparams = ['userid' => (int)$USER->id, 'action' => 'confirm'] + $inparams;
                $confirmednoticeids = $DB->get_fieldset_sql($confirmsql, $confirmparams);
                $confirmednoticeids = array_map('intval', $confirmednoticeids);
                $confirmednoticeids = array_flip($confirmednoticeids);
            }
        }

        foreach ($records as $record) {
            if (!empty($record->confirmenabled) && isset($confirmednoticeids[(int)$record->id])) {
                continue;
            }

            if (!self::match_target_role($record, $courseid)) {
                continue;
            }

            if ($record->scope === self::SCOPE_COURSE && $courseid > 0) {
                if ($record->courseid != $courseid) {
                    continue;
                }
                if (!empty($record->groupid)) {
                    if (!isloggedin() || isguestuser() || !groups_is_member((int)$record->groupid)) {
                        continue;
                    }
                }
            }

            $visible[] = $record;
        }

        return $visible;
    }

    /**
     * Match role targeting.
     *
     * @param stdClass $notice
     * @param int $courseid
     * @return bool
     */
    public static function match_target_role(stdClass $notice, int $courseid = 0): bool {
        global $USER;

        if ($notice->targetroles === self::ROLE_ALL) {
            return true;
        }

        if (!isloggedin() || isguestuser()) {
            return false;
        }

        $ismanager = self::is_manager_user();
        $isteacher = self::is_teacher_user($courseid);

        if ($notice->targetroles === self::ROLE_MANAGER) {
            return $ismanager;
        }

        if ($notice->targetroles === self::ROLE_TEACHER) {
            return $isteacher;
        }

        if ($notice->targetroles === self::ROLE_STUDENT) {
            return self::is_student_user($courseid) && !$ismanager && !$isteacher && !is_siteadmin($USER);
        }

        return false;
    }

    /**
     * Check if current user is manager/admin.
     *
     * @return bool
     */
    public static function is_manager_user(): bool {
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        if (is_siteadmin()) {
            return true;
        }

        return has_capability('moodle/site:config', context_system::instance());
    }

    /**
     * Check if current user is teacher.
     *
     * @param int $courseid
     * @return bool
     */
    public static function is_teacher_user(int $courseid = 0): bool {
        global $USER;

        if (!isloggedin() || isguestuser()) {
            return false;
        }

        if ($courseid > 0) {
            $coursecontext = context_course::instance($courseid);
            return has_capability('local/smartnoticespro:managecoursenotices', $coursecontext) ||
                has_capability('moodle/course:update', $coursecontext) ||
                has_capability('moodle/course:manageactivities', $coursecontext);
        }

        if (self::has_any_role_shortname(['editingteacher', 'teacher'])) {
            return true;
        }

        $courses = get_user_capability_course('moodle/course:manageactivities', $USER->id, true, '', '', 1);
        if (!empty($courses)) {
            return true;
        }

        $courses = get_user_capability_course('moodle/course:update', $USER->id, true, '', '', 1);
        return !empty($courses);
    }

    /**
     * Check if current user is student.
     *
     * @param int $courseid
     * @return bool
     */
    public static function is_student_user(int $courseid = 0): bool {
        global $USER;

        if (!isloggedin() || isguestuser()) {
            return false;
        }

        if ($courseid > 0) {
            $coursecontext = context_course::instance($courseid);
            return is_enrolled($coursecontext, $USER, '', true) && !self::is_teacher_user($courseid);
        }

        return self::has_any_role_shortname(['student']);
    }

    /**
     * Check whether user has any role by shortname.
     *
     * @param string[] $shortnames
     * @return bool
     */
    protected static function has_any_role_shortname(array $shortnames): bool {
        global $DB, $USER;

        static $cache = [];

        sort($shortnames);
        $cachekey = implode(',', $shortnames);
        if (array_key_exists($cachekey, $cache)) {
            return $cache[$cachekey];
        }

        if (empty($shortnames)) {
            $cache[$cachekey] = false;
            return false;
        }

        $roles = $DB->get_records_list('role', 'shortname', $shortnames, '', 'id,shortname');
        if (empty($roles)) {
            $cache[$cachekey] = false;
            return false;
        }

        foreach ($roles as $role) {
            if (user_has_role_assignment($USER->id, $role->id)) {
                $cache[$cachekey] = true;
                return true;
            }
        }

        $cache[$cachekey] = false;
        return false;
    }

    /**
     * Human-readable scope.
     *
     * @param string $scope
     * @return string
     */
    public static function format_scope(string $scope): string {
        $options = self::get_scope_options();
        return $options[$scope] ?? s($scope);
    }

    /**
     * Human-readable role.
     *
     * @param string $role
     * @return string
     */
    public static function format_target_role(string $role): string {
        $options = self::get_target_role_options();
        return $options[$role] ?? s($role);
    }

    /**
     * Human-readable locations.
     *
     * @param string $locations
     * @return string
     */
    public static function format_locations(string $locations): string {
        $labels = self::get_location_options();
        $formatted = [];

        foreach (self::locations_to_array($locations) as $location) {
            $formatted[] = $labels[$location] ?? s($location);
        }

        return implode(', ', $formatted);
    }

    /**
     * Human-readable page label for report rows.
     *
     * @param string|null $pageurl
     * @param int|null $courseid
     * @return string
     */
    public static function format_report_page_label(?string $pageurl, ?int $courseid = null): string {
        global $DB;

        if (empty($pageurl)) {
            return '-';
        }

        $path = parse_url($pageurl, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = $pageurl;
        }

        switch ($path) {
            case '/login/index.php':
                return get_string('location_login', 'local_smartnoticespro');
            case '/index.php':
                return get_string('location_frontpage', 'local_smartnoticespro');
            case '/my/':
            case '/my/index.php':
                return get_string('location_dashboard', 'local_smartnoticespro');
            case '/my/courses.php':
                return get_string('location_mycourses', 'local_smartnoticespro');
            case '/course/view.php':
                if (!empty($courseid)) {
                    $coursename = $DB->get_field('course', 'fullname', ['id' => (int)$courseid], IGNORE_MISSING);
                    if (!empty($coursename)) {
                        return get_string('location_course', 'local_smartnoticespro') . ' - ' . format_string($coursename);
                    }
                }
                return get_string('location_course', 'local_smartnoticespro');
            default:
                return ltrim($path, '/');
        }
    }

    /**
     * Increment one metric counter.
     *
     * @param int $noticeid
     * @param string $metric
     * @return void
     */
    public static function increment_metric(int $noticeid, string $metric): void {
        global $DB;

        $allowed = ['impressions', 'closes', 'confirmations'];
        if (!in_array($metric, $allowed, true)) {
            return;
        }

        $sql = "UPDATE {" . self::TABLE . "}
                   SET {$metric} = COALESCE({$metric}, 0) + 1
                 WHERE id = :id";
        $DB->execute($sql, ['id' => $noticeid]);
    }

    /**
     * Log a notice event.
     *
     * @param int $noticeid
     * @param string $action
     * @param int|null $userid
     * @param int|null $courseid
     * @param string|null $pageurl
     * @return void
     */
    public static function log_notice_event(
        int $noticeid,
        string $action,
        ?int $userid = null,
        ?int $courseid = null,
        ?string $pageurl = null
    ): void {
        global $DB;

        $allowed = ['impression', 'close', 'confirm'];
        if (!in_array($action, $allowed, true)) {
            return;
        }

        $record = (object)[
            'noticeid' => $noticeid,
            'userid' => $userid,
            'action' => $action,
            'courseid' => $courseid,
            'pageurl' => $pageurl,
            'timecreated' => time(),
        ];
        $DB->insert_record('local_smartnoticespro_log', $record);
    }

    /**
     * Process one tracked notice interaction.
     *
     * @param int $noticeid
     * @param string $eventname
     * @param int|null $userid
     * @param int|null $courseid
     * @param string|null $pageurl
     * @return string
     */
    public static function process_notice_event(
        int $noticeid,
        string $eventname,
        ?int $userid = null,
        ?int $courseid = null,
        ?string $pageurl = null
    ): string {
        $allowed = ['close', 'confirm'];
        if (!in_array($eventname, $allowed, true)) {
            throw new \moodle_exception('invalidparameter');
        }

        $notice = self::get_notice($noticeid);
        if (!$notice) {
            throw new \moodle_exception('error:noticemissing', 'local_smartnoticespro');
        }

        if (isloggedin() && !isguestuser()) {
            require_capability('local/smartnoticespro:viewnotices', context_system::instance());
        }

        if ($eventname === 'confirm' && isset($notice->confirmenabled) && empty($notice->confirmenabled)) {
            return 'ignored';
        }

        if ($eventname === 'close') {
            self::increment_metric($noticeid, 'closes');
            self::log_notice_event($noticeid, 'close', $userid, $courseid, $pageurl);
        }

        if ($eventname === 'confirm') {
            self::increment_metric($noticeid, 'confirmations');
            self::log_notice_event($noticeid, 'confirm', $userid, $courseid, $pageurl);
        }

        return 'ok';
    }

    /**
     * Count logs by notice.
     *
     * @param int $noticeid
     * @return int
     */
    public static function count_notice_logs(int $noticeid): int {
        global $DB;
        return (int)$DB->count_records('local_smartnoticespro_log', ['noticeid' => $noticeid]);
    }

    /**
     * Get logs by notice.
     *
     * @param int $noticeid
     * @param int $limitfrom
     * @param int $limitnum
     * @return stdClass[]
     */
    public static function get_notice_logs(int $noticeid, int $limitfrom = 0, int $limitnum = 0): array {
        global $DB;
        $sql = "SELECT l.*, u.firstname, u.lastname, u.email
                  FROM {local_smartnoticespro_log} l
             LEFT JOIN {user} u ON u.id = l.userid
                 WHERE l.noticeid = :noticeid
              ORDER BY l.timecreated DESC";
        return $DB->get_records_sql($sql, ['noticeid' => $noticeid], $limitfrom, $limitnum);
    }
}
