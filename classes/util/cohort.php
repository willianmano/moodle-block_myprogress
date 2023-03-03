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
 * Cohort utility class
 *
 * @package    block_myprogress
 * @copyright  2023 e-Learning – Conseils & Solutions <http://www.luiggisansonetti.fr/conseils>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cohort {
    /**
     * Get all site cohorts
     *
     * @return array|false
     *
     * @throws \dml_exception
     */
    public function get_all() {
        global $DB;

        $cohorts = $DB->get_records('cohort', ['visible' => 1]);

        if (!$cohorts) {
            return false;
        }

        $data = [];
        foreach ($cohorts as $cohort) {
            $data[$cohort->id] = $cohort->name;
        }

        return $data;
    }
}
