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

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * My Progress block configuration form definition
 *
 * @package    block_myprogress
 * @copyright  2023 e-Learning – Conseils & Solutions <http://www.luiggisansonetti.fr/conseils>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_myprogress_edit_form extends block_edit_form {

    /**
     * Form definition
     *
     * @param mixed $mform
     * @return void
     */
    public function specific_definition($mform) {
        $mform->addElement('header', 'displayinfo', get_string('settings'));

        $mform->addElement('text', 'config_title', get_string('blocktitle', 'block_myprogress'));
        $mform->setDefault('config_title', get_string('pluginname', 'block_myprogress'));
        $mform->addRule('config_title', null, 'required', null, 'client');
        $mform->setType('config_title', PARAM_TEXT);

        $options = [
            0 => get_string('no'),
            1 => get_string('yes'),
        ];

        $mform->addElement('select', 'config_showclassaverage', get_string('showclassaverage', 'block_myprogress'), $options);
        $mform->setDefault('config_showclassaverage', 1);
        $mform->setType('config_showclassaverage', PARAM_INT);

        $mform->addElement('select', 'config_showgroupaverage', get_string('showgroupaverage', 'block_myprogress'), $options);
        $mform->setDefault('config_showgroupaverage', 1);
        $mform->setType('config_showgroupaverage', PARAM_INT);

        $mform->addElement('select', 'config_showcohortaverage', get_string('showcohortaverage', 'block_myprogress'), $options);
        $mform->setDefault('config_showcohortaverage', 1);
        $mform->setType('config_showcohortaverage', PARAM_INT);

        $cohortsutil = new \block_myprogress\util\cohort();
        $cohorts = $cohortsutil->get_all();

        $mform->addElement('autocomplete', 'config_cohorts', get_string('cohortselection', 'block_myprogress'), $cohorts);
        $mform->getElement('config_cohorts')->setMultiple(true);
        $mform->hideIf('config_cohorts', 'config_showcohortaverage', 0);

        if ($cohorts) {
            $mform->getElement('config_cohorts')->setSelected(array_keys($cohorts));
        }
    }
}
