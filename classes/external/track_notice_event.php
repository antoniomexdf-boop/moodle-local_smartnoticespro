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

namespace local_smartnoticespro\external;

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_smartnoticespro\local\manager;

/**
 * External AJAX handler for notice tracking.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class track_notice_event extends external_api {
    /**
     * Returns execute parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'noticeid' => new external_value(PARAM_INT, 'Notice ID'),
            'eventname' => new external_value(PARAM_ALPHA, 'Notice event name'),
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_DEFAULT, 0),
            'pageurl' => new external_value(PARAM_LOCALURL, 'Page URL path', VALUE_DEFAULT, ''),
        ]);
    }

    /**
     * Executes tracking for a notice interaction.
     *
     * @param int $noticeid
     * @param string $eventname
     * @param int $courseid
     * @param string $pageurl
     * @return array
     */
    public static function execute(int $noticeid, string $eventname, int $courseid = 0, string $pageurl = ''): array {
        global $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'noticeid' => $noticeid,
            'eventname' => $eventname,
            'courseid' => $courseid,
            'pageurl' => $pageurl,
        ]);

        self::validate_context(context_system::instance());

        $userid = (isloggedin() && !isguestuser()) ? (int)$USER->id : null;
        $status = manager::process_notice_event(
            (int)$params['noticeid'],
            (string)$params['eventname'],
            $userid,
            !empty($params['courseid']) ? (int)$params['courseid'] : null,
            $params['pageurl'] !== '' ? (string)$params['pageurl'] : null
        );

        return ['status' => $status];
    }

    /**
     * Returns execute result structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'status' => new external_value(PARAM_ALPHA, 'Tracking status'),
        ]);
    }
}
