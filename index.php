<?php
// This file is part of Moodle - http://moodle.org/

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
 * Notice management listing.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_smartnoticespro\local\manager;

$courseid = optional_param('courseid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$sort = optional_param('sort', 'timemodified', PARAM_ALPHANUMEXT);
$dir = strtoupper(optional_param('dir', 'DESC', PARAM_ALPHA));
$perpage = 20;
$limitfrom = $page * $perpage;
$allowedsort = ['id', 'title', 'active', 'timestart', 'timeend', 'timemodified'];
if (!in_array($sort, $allowedsort, true)) {
    $sort = 'timemodified';
}
if (!in_array($dir, ['ASC', 'DESC'], true)) {
    $dir = 'DESC';
}

if ($courseid > 0) {
    $course = get_course($courseid);
    require_login($course);
    $accesscontext = context_course::instance($courseid);
    require_capability('local/smartnoticespro:managecoursenotices', $accesscontext);
    $pagecontext = context_system::instance();

    $url = new moodle_url('/local/smartnoticespro/index.php', ['courseid' => $courseid, 'sort' => $sort, 'dir' => $dir]);
    $notices = manager::get_course_notices_paginated($courseid, $limitfrom, $perpage, $sort, $dir);
    $total = manager::count_course_notices($courseid);
    $heading = get_string('coursenotices', 'local_smartnoticespro') . ': ' . format_string($course->fullname);
    $returnparams = ['courseid' => $courseid, 'sort' => $sort, 'dir' => $dir];
} else {
    require_login();
    $accesscontext = context_system::instance();
    require_capability('local/smartnoticespro:manageglobalnotices', $accesscontext);
    $pagecontext = $accesscontext;

    $url = new moodle_url('/local/smartnoticespro/index.php', ['sort' => $sort, 'dir' => $dir]);
    $notices = manager::get_all_notices_paginated($limitfrom, $perpage, $sort, $dir);
    $total = manager::count_all_notices();
    $heading = get_string('managenotices', 'local_smartnoticespro');
    $returnparams = ['sort' => $sort, 'dir' => $dir];
}

$PAGE->set_context($pagecontext);
$PAGE->set_url($url);
$PAGE->set_pagelayout('admin');
$PAGE->set_title($heading);
$PAGE->set_heading($heading);

$editpage = $courseid > 0 ? '/local/smartnoticespro/course_edit.php' : '/local/smartnoticespro/edit.php';
$addurl = new moodle_url($editpage, $returnparams);
$addicon = $OUTPUT->pix_icon('t/add', '', 'core');
$addbutton = html_writer::link(
    $addurl,
    $addicon . html_writer::span(get_string('addnotice', 'local_smartnoticespro'), 'smartnotices-addlabel'),
    ['class' => 'btn btn-primary']
);
$toolbaritems = [$addbutton];

if ($courseid > 0) {
    $courseurl = new moodle_url('/course/view.php', ['id' => $courseid]);
    $courseicon = $OUTPUT->pix_icon('i/course', '', 'core');
    $toolbaritems[] = html_writer::link(
        $courseurl,
        $courseicon . html_writer::span(get_string('gotocourse', 'local_smartnoticespro'), 'smartnotices-addlabel'),
        ['class' => 'btn btn-secondary']
    );
}

echo $OUTPUT->header();

echo $OUTPUT->heading($heading);
echo html_writer::div(implode('', $toolbaritems), 'mb-3 smartnotices-toolbar');

if (empty($notices)) {
    echo $OUTPUT->notification(get_string('nonotices', 'local_smartnoticespro'), 'info');
    echo $OUTPUT->footer();
    exit;
}

$pagingbar = new paging_bar($total, $page, $perpage, $url);
echo $OUTPUT->render($pagingbar);

$table = new html_table();
$table->attributes['class'] = 'generaltable table table-striped table-hover smartnotices-table';
$sorticon = function(string $field) use ($sort, $dir): string {
    if ($sort !== $field) {
        return '';
    }
    return $dir === 'ASC' ? ' ▲' : ' ▼';
};
$sortlink = function(string $field, string $label) use ($url, $sort, $dir, $sorticon): string {
    $newdir = ($sort === $field && $dir === 'ASC') ? 'DESC' : 'ASC';
    $params = $url->params();
    $params['sort'] = $field;
    $params['dir'] = $newdir;
    $sorturl = new moodle_url('/local/smartnoticespro/index.php', $params);
    return html_writer::link($sorturl, s($label . $sorticon($field)));
};
$table->head = [
    $sortlink('id', get_string('table:id', 'local_smartnoticespro')),
    $sortlink('title', get_string('table:title', 'local_smartnoticespro')),
    get_string('table:scope', 'local_smartnoticespro'),
    get_string('table:course', 'local_smartnoticespro'),
    get_string('table:group', 'local_smartnoticespro'),
    get_string('table:locations', 'local_smartnoticespro'),
    get_string('table:targetrole', 'local_smartnoticespro'),
    $sortlink('active', get_string('table:status', 'local_smartnoticespro')),
    $sortlink('timestart', get_string('table:dates', 'local_smartnoticespro')),
    get_string('table:impressions', 'local_smartnoticespro'),
    get_string('table:closes', 'local_smartnoticespro'),
    get_string('table:confirmations', 'local_smartnoticespro'),
    get_string('table:ctr', 'local_smartnoticespro'),
    get_string('table:actions', 'local_smartnoticespro'),
];

foreach ($notices as $notice) {
    $course = '-';
    if (!empty($notice->courseid)) {
        $course = format_string($DB->get_field('course', 'fullname', ['id' => $notice->courseid], IGNORE_MISSING) ?: '-');
    }

    $dates = '-';
    if (!empty($notice->timestart) || !empty($notice->timeend)) {
        $start = !empty($notice->timestart) ? userdate($notice->timestart) : '-';
        $end = !empty($notice->timeend) ? userdate($notice->timeend) : '-';
        $dates = $start . ' → ' . $end;
    }
    $group = ($notice->scope === manager::SCOPE_COURSE) ? get_string('allgroups', 'local_smartnoticespro') : '-';
    if (!empty($notice->groupid)) {
        $group = format_string($DB->get_field('groups', 'name', ['id' => $notice->groupid], IGNORE_MISSING) ?: '-');
    }

    $editurl = new moodle_url($editpage, ['id' => $notice->id] + $returnparams);
    $deleteurl = new moodle_url('/local/smartnoticespro/delete.php', ['id' => $notice->id] + $returnparams);
    $reporturl = new moodle_url('/local/smartnoticespro/report.php', ['noticeid' => $notice->id] + $returnparams);
    $exporturl = new moodle_url('/local/smartnoticespro/report.php', [
        'noticeid' => $notice->id,
        'export' => 'csv',
        'sesskey' => sesskey(),
    ] + $returnparams);

    $editicon = $OUTPUT->pix_icon('t/edit', get_string('edit'));
    $deleteicon = $OUTPUT->pix_icon('t/delete', get_string('delete'));
    $reporticon = $OUTPUT->pix_icon('t/preview', get_string('table:reports', 'local_smartnoticespro'));
    $exporticon = $OUTPUT->pix_icon('t/download', get_string('report:exportcsv', 'local_smartnoticespro'));
    $actions = html_writer::link($editurl, $editicon) . ' ' .
        html_writer::link($deleteurl, $deleteicon) . ' ' .
        html_writer::link($reporturl, $reporticon) . ' ' .
        html_writer::link($exporturl, $exporticon);

    $noticeid = 'SN-' . str_pad((string)$notice->id, 6, '0', STR_PAD_LEFT);
    $scopebadge = html_writer::span(
        manager::format_scope($notice->scope),
        'badge rounded-pill smartnotices-badge smartnotices-badge--scope'
    );
    $rolebadge = html_writer::span(
        manager::format_target_role($notice->targetroles),
        'badge rounded-pill smartnotices-badge smartnotices-badge--role'
    );
    $statusbadge = html_writer::span(
        $notice->active ? get_string('active', 'local_smartnoticespro') : get_string('inactive', 'local_smartnoticespro'),
        'badge rounded-pill smartnotices-badge ' .
            ($notice->active ? 'smartnotices-badge--active' : 'smartnotices-badge--inactive')
    );
    $impressions = (int)($notice->impressions ?? 0);
    $closes = (int)($notice->closes ?? 0);
    $confirmations = (int)($notice->confirmations ?? 0);
    $ctr = $impressions > 0 ? round(($confirmations / $impressions) * 100, 2) . '%' : '0%';

    $table->data[] = [
        html_writer::tag('code', s($noticeid), ['class' => 'smartnotices-codeid']),
        format_string($notice->title),
        $scopebadge,
        $course,
        $group,
        manager::format_locations($notice->locations),
        $rolebadge,
        $statusbadge,
        $dates,
        $impressions,
        $closes,
        $confirmations,
        $ctr,
        $actions,
    ];
}

echo html_writer::table($table);
echo $OUTPUT->render($pagingbar);

echo $OUTPUT->footer();
