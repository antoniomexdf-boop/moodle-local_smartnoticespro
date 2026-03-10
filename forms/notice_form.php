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

namespace local_smartnoticespro\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use context_course;
use local_smartnoticespro\local\manager;
use moodleform;

/**
 * Notice form.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notice_form extends moodleform {
    /**
     * Form definition.
     *
     * @return void
     */
    public function definition(): void {
        global $DB;

        $mform = $this->_form;
        $data = $this->_customdata;

        $courseid = (int)($data['courseid'] ?? 0);
        $canmanageglobal = !empty($data['canmanageglobal']);

        $mform->addElement('hidden', 'id', (int)($data['id'] ?? 0));
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'title', get_string('title', 'local_smartnoticespro'), ['size' => 60]);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');

        $editoroptions = [
            'maxfiles' => 0,
            'maxbytes' => 0,
            'trusttext' => false,
            'noclean' => false,
            'context' => $courseid > 0 ? context_course::instance($courseid) : \context_system::instance(),
        ];
        $mform->addElement('editor', 'message_editor', get_string('message', 'local_smartnoticespro'), ['rows' => 8], $editoroptions);
        $mform->setType('message_editor', PARAM_RAW);
        $mform->addRule('message_editor', null, 'required', null, 'client');

        $mform->addElement('advcheckbox', 'hidetitle', get_string('hidetitle', 'local_smartnoticespro'));
        $mform->setDefault('hidetitle', 0);

        $mform->addElement('advcheckbox', 'confirmenabled', get_string('confirmenabled', 'local_smartnoticespro'));
        $mform->setDefault('confirmenabled', 1);

        $mform->addElement('advcheckbox', 'active', get_string('active', 'local_smartnoticespro'));
        $mform->setDefault('active', 1);

        if ($canmanageglobal) {
            $mform->addElement('select', 'scope', get_string('scope', 'local_smartnoticespro'), manager::get_scope_options());
            $mform->setType('scope', PARAM_ALPHANUMEXT);

            $courses = [0 => get_string('none')];
            $records = $DB->get_records_menu('course', null, 'fullname ASC', 'id,fullname');
            foreach ($records as $id => $fullname) {
                $courses[(int)$id] = format_string($fullname);
            }
            $mform->addElement('autocomplete', 'courseid', get_string('course', 'local_smartnoticespro'), $courses);
            $mform->setType('courseid', PARAM_INT);
            $mform->hideIf('courseid', 'scope', 'neq', manager::SCOPE_COURSE);
        } else {
            $mform->addElement('hidden', 'scope', manager::SCOPE_COURSE);
            $mform->setType('scope', PARAM_ALPHANUMEXT);
            $mform->addElement('hidden', 'courseid', $courseid);
            $mform->setType('courseid', PARAM_INT);
            $groups = groups_get_all_groups($courseid, 0, 0, 'g.id,g.name');
            if (!empty($groups)) {
                $groupoptions = [0 => get_string('allgroups', 'local_smartnoticespro')];
                foreach ($groups as $group) {
                    $groupoptions[(int)$group->id] = format_string($group->name);
                }
                $mform->addElement('select', 'groupid', get_string('targetgroup', 'local_smartnoticespro'), $groupoptions);
                $mform->setType('groupid', PARAM_INT);
                $mform->addElement('hidden', 'hasgroups', 1);
                $mform->setType('hasgroups', PARAM_INT);
            } else {
                $mform->addElement('hidden', 'groupid', 0);
                $mform->setType('groupid', PARAM_INT);
                $mform->addElement('hidden', 'hasgroups', 0);
                $mform->setType('hasgroups', PARAM_INT);
            }
        }

        if ($canmanageglobal) {
            $targetroleoptions = ['' => get_string('targetrole_select', 'local_smartnoticespro')] + manager::get_target_role_options();
            $mform->addElement('select', 'targetroles', get_string('targetroles', 'local_smartnoticespro'), $targetroleoptions);
            $mform->setType('targetroles', PARAM_ALPHANUMEXT);
            $mform->setDefault('targetroles', '');
            $mform->hideIf('targetroles', 'scope', 'eq', manager::SCOPE_COURSE);
        } else {
            $mform->addElement('hidden', 'targetroles', manager::ROLE_ALL);
            $mform->setType('targetroles', PARAM_ALPHANUMEXT);
        }

        if ($canmanageglobal) {
            $locations = manager::get_location_options();
            foreach ($locations as $value => $label) {
                $field = 'location_' . $value;
                $mform->addElement('advcheckbox', $field, $label);
                $mform->setType($field, PARAM_INT);
            }
            $mform->hideIf('location_login', 'scope', 'eq', manager::SCOPE_COURSE);
            $mform->hideIf('location_frontpage', 'scope', 'eq', manager::SCOPE_COURSE);
            $mform->hideIf('location_dashboard', 'scope', 'eq', manager::SCOPE_COURSE);
            $mform->hideIf('location_mycourses', 'scope', 'eq', manager::SCOPE_COURSE);
        } else {
            $mform->addElement('hidden', 'location_course', 1);
            $mform->setType('location_course', PARAM_INT);
            $mform->addElement('hidden', 'location_login', 0);
            $mform->setType('location_login', PARAM_INT);
            $mform->addElement('hidden', 'location_frontpage', 0);
            $mform->setType('location_frontpage', PARAM_INT);
            $mform->addElement('hidden', 'location_dashboard', 0);
            $mform->setType('location_dashboard', PARAM_INT);
            $mform->addElement('hidden', 'location_mycourses', 0);
            $mform->setType('location_mycourses', PARAM_INT);
        }

        $mform->addElement('date_time_selector', 'timestart', get_string('startdate', 'local_smartnoticespro'), ['optional' => true]);
        $mform->addElement('date_time_selector', 'timeend', get_string('enddate', 'local_smartnoticespro'), ['optional' => true]);

        $this->add_action_buttons(true, get_string('savechanges', 'local_smartnoticespro'));
    }

    /**
     * Extra validation.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if (!empty($data['timestart']) && !empty($data['timeend']) && $data['timeend'] < $data['timestart']) {
            $errors['timeend'] = get_string('error:dateinvalid', 'local_smartnoticespro');
        }

        $locations = 0;
        foreach (array_keys(manager::get_location_options()) as $location) {
            if (!empty($data['location_' . $location])) {
                $locations++;
            }
        }
        if ($locations === 0 && ($data['scope'] ?? '') !== manager::SCOPE_COURSE) {
            $errors['location_login'] = get_string('error:nolocations', 'local_smartnoticespro');
        }

        if (($data['scope'] ?? '') === manager::SCOPE_COURSE && empty($data['courseid'])) {
            $errors['courseid'] = get_string('error:courseidrequired', 'local_smartnoticespro');
        }

        if (($data['scope'] ?? '') === manager::SCOPE_COURSE && isset($data['groupid']) && (int)$data['groupid'] < 0) {
            $errors['groupid'] = get_string('error:invalidgroup', 'local_smartnoticespro');
        }

        if (($data['scope'] ?? '') === manager::SCOPE_GLOBAL && empty($data['targetroles'])) {
            $errors['targetroles'] = get_string('error:targetrolerequired', 'local_smartnoticespro');
        }

        return $errors;
    }
}
