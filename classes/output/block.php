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

namespace block_myprogress\output;

use block_myprogress\util\progress;
use renderable;
use templatable;
use renderer_base;

/**
 * My Progress block renderable class.
 *
 * @package    block_myprogress
 * @copyright  2023 e-Learning – Conseils & Solutions <http://www.luiggisansonetti.fr/conseils>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block implements renderable, templatable {
    protected $course;
    protected $context;
    protected $config;

    /**
     * Class constructor
     *
     * @param $course
     */
    public function __construct($course, $context, $config) {
        $this->course = $course;
        $this->context = $context;
        $this->config = $config;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     *
     * @return array|\stdClass
     *
     * @throws \coding_exception
     *
     * @throws \dml_exception
     */
    public function export_for_template(renderer_base $output) {
        $progressutil = new progress();

        $data = [];

        if (is_null($this->config) || $this->config->showclassaverage) {
            $data['classaverage'] = $progressutil->get_class_average($this->course->id);
            $data['hasclassaverage'] = !empty($data['classaverage']);
        }

        // Teachers can only view class average.
        if (!has_capability('moodle/course:isincompletionreports', $this->context) || is_siteadmin()) {
            return $data;
        }

        $data['userprogress'] = $progressutil->get_user_progress($this->course->id);
        $data['hasuserprogress'] = !empty($data['userprogress']);

        if (is_null($this->config) || $this->config->showgroupaverage) {
            $data['groupsaverage'] = $progressutil->get_groups_average($this->course->id);
            $data['hasgroupsaverage'] = !empty($data['groupsaverage']);
        }

        if (is_null($this->config) || $this->config->showcohortaverage) {
            $data['cohortsaverage'] = $progressutil->get_cohorts_average($this->course->id, null, $this->config->cohorts);
            $data['hascohortsaverage'] = !empty($data['cohortsaverage']);
        }

        return $data;
    }
}
