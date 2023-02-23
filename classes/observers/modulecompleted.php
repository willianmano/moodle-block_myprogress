<?php
// This file is part of My Progress block for Moodle - http://moodle.org/
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

namespace block_myprogress\observers;

use core\event\base as baseevent;

/**
 * Module completed event observer class.
 *
 * @package    block_myprogress
 * @copyright  2023 e-Learning – Conseils & Solutions <http://www.luiggisansonetti.fr/conseils>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class modulecompleted {
    /**
     * Handle the module completed event to recalculate user's course progress
     *
     * @param baseevent $event
     * @return void
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function observer(baseevent $event) {
        global $DB;

        if (!is_enrolled($event->get_context(), $event->relateduserid)) {
            return;
        }

        // Avoid calculate progress for teachers, admins, anyone who can edit course.
        if (has_capability('moodle/course:update', $event->get_context(), $event->relateduserid)) {
            return;
        }

        $course = $DB->get_record('course', ['id' => $event->courseid], '*', MUST_EXIST);

        $util = new \block_myprogress\util\coursecompletion($course);

        $util->update_user_progress($event->relateduserid);
    }
}
