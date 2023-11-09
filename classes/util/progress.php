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
 * Progress class
 *
 * @package    block_myprogress
 * @copyright  2023 e-Learning – Conseils & Solutions <http://www.luiggisansonetti.fr/conseils>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class progress {
    /**
     * Get user progress in a course
     *
     * @param $courseid
     * @param $userid
     *
     * @return int
     *
     * @throws \dml_exception
     */
    public function get_user_progress($courseid, $userid = null) {
        global $USER, $DB;

        if (!$userid) {
            $userid = $USER->id;
        }

        $record = $DB->get_record('block_myprogress_course', ['courseid' => $courseid, 'userid' => $userid]);

        if (!$record) {
            return 0;
        }

        return $record->progress;
    }

    /**
     * Get course average progress
     *
     * @param $courseid
     *
     * @return false|float|int
     *
     * @throws \dml_exception
     */
    public function get_class_average($courseid) {
        global $DB;

        $sql = 'SELECT AVG(progress) as average
                FROM {block_myprogress_course}
                WHERE courseid = :courseid';

        $record = $DB->get_record_sql($sql, ['courseid' => $courseid]);

        if (!$record) {
            return 0;
        }

        return floor($record->average);
    }

    /**
     * Get user groups average progress
     *
     * @param $courseid
     * @param $userid
     *
     * @return array|false
     *
     * @throws \dml_exception
     */
    public function get_groups_average($courseid, $userid = null) {
        global $USER, $DB;

        if (!$userid) {
            $userid = $USER->id;
        }

        $usergroups = groups_get_user_groups($courseid, $userid);

        if (!$usergroups) {
            return false;
        }

        $groups = [];
        foreach ($usergroups[0] as $usergroup) {
            $sql = 'SELECT g.id, g.name, AVG(cp.progress) as average
                    FROM {groups} g
                    INNER JOIN {groups_members} gm ON gm.groupid = g.id
                    LEFT JOIN {block_myprogress_course} cp ON cp.userid = gm.userid AND cp.courseid = :courseid
                    WHERE g.id = :groupid';

            $record = $DB->get_record_sql($sql, ['courseid' => $courseid, 'groupid' => $usergroup]);

            $groups[] = [
                'name' => $record->name,
                'average' => floor($record->average),
            ];
        }

        return $groups;
    }

    /**
     * Get user cohorts average progress
     *
     * @param $courseid
     * @param $userid
     *
     * @return array
     *
     * @throws \dml_exception
     */
    public function get_cohorts_average($courseid, $userid = null, $cohortstoshow = null) {
        global $USER, $DB;

        if (!$userid) {
            $userid = $USER->id;
        }

        $sql = 'SELECT c.*
                FROM {cohort} c
                INNER JOIN {cohort_members} cm ON c.id = cm.cohortid
                WHERE cm.userid = :userid AND c.visible = 1';

        $usercohorts = $DB->get_records_sql($sql, ['userid' => $userid]);

        if (!$usercohorts) {
            return [];
        }

        if (!is_null($cohortstoshow)) {
            $usercohorts = array_filter($usercohorts, function($cohort) use ($cohortstoshow) {
                return in_array($cohort->id, $cohortstoshow);
            });
        }

        if (empty($usercohorts)) {
            return [];
        }

        $cohorts = [];
        foreach ($usercohorts as $usercohort) {
            $sql = 'SELECT c.id, c.name, AVG(cp.progress) as average
                    FROM {cohort} c
                    INNER JOIN {cohort_members} cm ON c.id = cm.cohortid
                    LEFT JOIN {block_myprogress_course} cp ON cp.userid = cm.userid AND cp.courseid = :courseid
                    WHERE c.id = :cohortid';

            $record = $DB->get_record_sql($sql, ['cohortid' => $usercohort->id, 'courseid' => $courseid]);

            $cohorts[] = [
                'name' => $record->name,
                'average' => floor($record->average),
            ];
        }

        return $cohorts;
    }
}
