<?php
// This file is part of Moodle - http://moodle.org/

/**
 * English language strings for local_smartnoticespro.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Smart Notices Pro';
$string['privacy:metadata'] = 'The Smart Notices Pro plugin stores notices created by users.';
$string['privacy:metadata:local_smartnoticespro'] = 'Stores notices and their metadata.';
$string['privacy:metadata:local_smartnoticespro:title'] = 'Notice title.';
$string['privacy:metadata:local_smartnoticespro:message'] = 'Notice content.';
$string['privacy:metadata:local_smartnoticespro:userid'] = 'User ID of the creator.';
$string['privacy:metadata:local_smartnoticespro:timecreated'] = 'Creation timestamp.';
$string['privacy:metadata:local_smartnoticespro:timemodified'] = 'Last update timestamp.';

$string['managenotices'] = 'Manage Smart Notices Pro';
$string['notices'] = 'Notices';
$string['addnotice'] = 'Add notice';
$string['editnotice'] = 'Edit notice';
$string['deletenotice'] = 'Delete notice';
$string['confirmdelete'] = 'Are you sure you want to delete this notice?';
$string['nonotices'] = 'No notices found.';

$string['title'] = 'Title';
$string['hidetitle'] = 'Hide title';
$string['confirmenabled'] = 'Enable confirmation button';
$string['message'] = 'Message';
$string['active'] = 'Active';
$string['inactive'] = 'Inactive';
$string['status'] = 'Status';
$string['scope'] = 'Scope';
$string['scopeglobal'] = 'Global';
$string['scopecourse'] = 'Course-specific';
$string['course'] = 'Course';
$string['targetroles'] = 'Target role';
$string['targetgroup'] = 'Target group';
$string['allgroups'] = 'All groups';
$string['targetrole_select'] = 'Select a target role';
$string['targetrole_all'] = 'All users';
$string['targetrole_student'] = 'Students';
$string['targetrole_teacher'] = 'Teachers';
$string['targetrole_manager'] = 'Managers/administrators';
$string['locations'] = 'Display locations';
$string['location_login'] = 'Login page';
$string['location_frontpage'] = 'Front page';
$string['location_dashboard'] = 'Dashboard';
$string['location_mycourses'] = 'My courses';
$string['location_course'] = 'Course page';
$string['startdate'] = 'Start date';
$string['enddate'] = 'End date';

$string['savechanges'] = 'Save changes';
$string['gotocourse'] = 'Go to course';
$string['noticecreated'] = 'Notice created successfully.';
$string['noticeupdated'] = 'Notice updated successfully.';
$string['noticedeleted'] = 'Notice deleted successfully.';
$string['error:nolocations'] = 'Select at least one display location.';
$string['error:dateinvalid'] = 'End date must be greater than or equal to start date.';
$string['error:courseidrequired'] = 'A course must be selected for course-specific notices.';
$string['error:invalidscope'] = 'Invalid scope selected.';
$string['error:invalidtargetrole'] = 'Invalid target role selected.';
$string['error:targetrolerequired'] = 'A target role is required for global notices.';
$string['error:invalidlocation'] = 'Invalid location selected.';
$string['error:grouprequired'] = 'A group must be selected.';
$string['error:invalidgroup'] = 'Invalid group selected.';
$string['error:noticemissing'] = 'The requested notice does not exist.';
$string['error:cannotmanagecourse'] = 'You do not have permission to manage notices in this course.';
$string['error:cannotmanageglobal'] = 'You do not have permission to manage global notices.';

$string['table:title'] = 'Title';
$string['table:id'] = 'Notice ID';
$string['table:scope'] = 'Scope';
$string['table:course'] = 'Course';
$string['table:group'] = 'Group';
$string['table:locations'] = 'Locations';
$string['table:targetrole'] = 'Target role';
$string['table:status'] = 'Status';
$string['table:dates'] = 'Dates';
$string['table:impressions'] = 'Impressions';
$string['table:closes'] = 'Closes';
$string['table:confirmations'] = 'Confirmations';
$string['table:ctr'] = 'CTR';
$string['table:actions'] = 'Actions';
$string['table:reports'] = 'Reports';

$string['report:title'] = 'Notice report';
$string['report:heading'] = 'Report: {$a}';
$string['report:nodata'] = 'No report data found for this notice.';
$string['report:exportcsv'] = 'Export CSV';
$string['report:user'] = 'User';
$string['report:email'] = 'Email';
$string['report:action'] = 'Action';
$string['report:courseid'] = 'Course ID';
$string['report:pageurl'] = 'Page';
$string['report:date'] = 'Date and time';


$string['capability:manageglobalnotices'] = 'Manage global smart notices';
$string['capability:managecoursenotices'] = 'Manage course smart notices';
$string['capability:viewnotices'] = 'View smart notices';

$string['modal:close'] = 'Close notice';
$string['modal:confirm'] = 'Got it';
$string['modal:dialoglabel'] = 'Site notice';
$string['coursenotices'] = 'Course notices';
