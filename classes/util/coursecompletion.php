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

namespace block_myprogress\util;

/**
 * Course completion class
 *
 * @package    block_myprogress
 * @copyright  2023 e-Learning – Conseils & Solutions <http://www.luiggisansonetti.fr/conseils>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecompletion {
    protected $course;

    /**
     * Class constructor
     *
     * @param $course
     */
    public function __construct($course) {
        $this->course = $course;
    }

    /**
     * Update the course progress of a user in the course
     *
     * @param int $userid
     *
     * @return void
     */
    public function update_user_progress(int $userid): void {
        $progress = $this->get_course_progress($userid);

        $this->insert_or_update_user_course_progress($userid, $progress);
    }

    /**
     * Insert or update the user progress in the course progress table
     *
     * @param $userid
     * @param $progress
     *
     * @return bool|int
     *
     * @throws \dml_exception
     */
    public function insert_or_update_user_course_progress($userid, $progress) {
        global $DB;

        $courseprogress = $DB->get_record('block_myprogress_course', ['courseid' => $this->course->id, 'userid' => $userid]);

        if ($courseprogress) {
            $courseprogress->progress = $progress;
            $courseprogress->timemodified = time();

            return $DB->update_record('block_myprogress_course', $courseprogress);
        }

        $courseprogress = new \stdClass();
        $courseprogress->courseid = $this->course->id;
        $courseprogress->userid = $userid;
        $courseprogress->progress = $progress;
        $courseprogress->timecreated = time();
        $courseprogress->timemodified = time();

        return $DB->insert_record('block_myprogress_course', $courseprogress);
    }

    /**
     * Get calculated user progress in the course
     *
     * @param int $userid
     *
     * @return int
     */
    public function get_course_progress(int $userid): int {
        $progresspercentage = \core_completion\progress::get_course_progress_percentage($this->course, $userid);

        if (!is_null($progresspercentage)) {
            return floor($progresspercentage);
        }

        return 0;
    }
}
