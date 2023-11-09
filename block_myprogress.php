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

/**
 * My Progress block definition class
 *
 * @package    block_myprogress
 * @copyright  2023 e-Learning – Conseils & Solutions <http://www.luiggisansonetti.fr/conseils>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myprogress extends block_base {
    /**
     * Sets the block title
     *
     * @return void
     *
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_myprogress');
    }

    /**
     * Controls the block title based on instance configuration
     *
     * @return bool
     */
    public function specialization() {
        $title = isset($this->config->title) ? trim($this->config->title) : '';
        if (!empty($title)) {
            $this->title = format_string($this->config->title);
        }
    }

    /**
     * Defines where the block can be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'course-view' => true,
            'site' => false,
            'mod' => false,
            'my' => false,
        );
    }

    /**
     * Creates the blocks main content
     *
     * @return \stdClass
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_content() {
        if (isset($this->content)) {
            return $this->content;
        }

        $this->content = new \stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $renderer = $this->page->get_renderer('block_myprogress');

        $contentrenderable = new \block_myprogress\output\block($this->page->course, $this->context, $this->config);
        $this->content->text = $renderer->render($contentrenderable);

        return $this->content;
    }

    /**
     * Allow block instance configuration
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Do any additional initialization you may need at the time a new block instance is created
     *
     * @return boolean
     */
    public function instance_create() {
        $students = get_enrolled_users($this->page->context, 'mod/assign:submit');

        if (!$students) {
            return true;
        }

        $util = new \block_myprogress\util\coursecompletion($this->page->course);

        foreach ($students as $student) {
            $util->update_user_progress($student->id);
        }

        return true;
    }

    /**
     * Delete everything related to this instance if you have been using persistent storage other than the configdata field.
     *
     * @return boolean
     */
    public function instance_delete() {
        global $DB;

        return $DB->delete_records('block_myprogress_course', ['courseid' => $this->page->course->id]);
    }
}
