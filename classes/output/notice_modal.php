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

namespace local_smartnoticespro\output;

use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderable notice modal object.
 *
 * @package   local_smartnoticespro
 * @copyright 2026 Jesus Antonio Jimenez Aviña <antoniomexdf@gmail.com> <antoniojamx@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notice_modal implements renderable, templatable {
    /** @var stdClass */
    protected $notice;

    /**
     * Constructor.
     *
     * @param stdClass $notice
     */
    public function __construct(stdClass $notice) {
        $this->notice = $notice;
    }

    /**
     * Export template context.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        return [
            'modalid' => 'smartnotices-modal-' . $this->notice->id,
            'noticeid' => (int)$this->notice->id,
            'courseid' => !empty($this->notice->courseid) ? (int)$this->notice->courseid : 0,
            'sesskey' => sesskey(),
            'title' => format_string($this->notice->title),
            'showtitle' => empty($this->notice->hidetitle),
            'showconfirm' => !isset($this->notice->confirmenabled) || !empty($this->notice->confirmenabled),
            'message' => format_text($this->notice->message, FORMAT_HTML, ['overflowdiv' => true]),
            'closelabel' => get_string('modal:close', 'local_smartnoticespro'),
            'confirmlabel' => get_string('modal:confirm', 'local_smartnoticespro'),
            'dialoglabel' => get_string('modal:dialoglabel', 'local_smartnoticespro'),
        ];
    }
}
