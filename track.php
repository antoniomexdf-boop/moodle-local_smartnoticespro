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

/**
 * Tracking endpoint for modal interactions.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This endpoint must be callable from public pages such as the login notice.
// phpcs:disable moodle.Files.RequireLogin.Missing
require_once(__DIR__ . '/../../config.php');
// phpcs:enable moodle.Files.RequireLogin.Missing

use local_smartnoticespro\local\manager;

global $USER;

$noticeid = required_param('noticeid', PARAM_INT);
$event = required_param('event', PARAM_ALPHA);

$allowed = ['close', 'confirm'];
if (!in_array($event, $allowed, true)) {
    throw new moodle_exception('invalidparameter');
}

require_sesskey();

$notice = manager::get_notice($noticeid);
if (!$notice) {
    throw new moodle_exception('error:noticemissing', 'local_smartnoticespro');
}

if (isloggedin() && !isguestuser()) {
    require_capability('local/smartnoticespro:viewnotices', context_system::instance());
}

if ($event === 'confirm' && isset($notice->confirmenabled) && empty($notice->confirmenabled)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ignored']);
    exit;
}

$userid = (isloggedin() && !isguestuser()) ? (int)$USER->id : null;
$courseid = optional_param('courseid', 0, PARAM_INT);
$pageurl = optional_param('pageurl', '', PARAM_LOCALURL);

if ($event === 'close') {
    manager::increment_metric($noticeid, 'closes');
    manager::log_notice_event($noticeid, 'close', $userid, $courseid ?: null, $pageurl ?: null);
}

if ($event === 'confirm') {
    manager::increment_metric($noticeid, 'confirmations');
    manager::log_notice_event($noticeid, 'confirm', $userid, $courseid ?: null, $pageurl ?: null);
}

header('Content-Type: application/json');
echo json_encode(['status' => 'ok']);
