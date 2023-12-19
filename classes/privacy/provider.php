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

/**
 * Privacy api class.
 * @package    block_myprogress
 * @copyright  2023 e-Learning – Conseils & Solutions <http://www.luiggisansonetti.fr/conseils>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_myprogress\privacy;

defined('MOODLE_INTERNAL') || die();

use context_block;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\{approved_contextlist, approved_userlist, contextlist, userlist};

/**
 * Class provider
 * @package block_myprogress
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns metadata.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'block_myprogress',
            [
                'userid' => 'privacy:metadata:block_myprogress:userid',
                'courseid' => 'privacy:metadata:block_myprogress:courseid',
                'progress' => 'privacy:metadata:block_myprogress:progress',
            ],
            'privacy:metadata'
        );
        return $collection;
    }

   /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist $contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $sql = 'SELECT DISTINCT ctx.id
        FROM {block_myprogress_course} bmc
        JOIN {context} ctx
            ON ctx.instanceid = bmc.userid
            AND ctx.contextlevel = :contextlevel
        WHERE bmc.userid = :userid';

        $params = ['userid' => $userid, 'contextlevel' => CONTEXT_USER];

        $contextlist = new contextlist();
        $contextlist->add_from_sql($sql, $params);
        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param   userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_user) {
            return;
        }

        $params = [
            'contextid' => $context->id,
            'contextuser' => CONTEXT_BLOCK,
        ];

        $sql = "SELECT bmc.userid as userid
                  FROM {block_myprogress_course} bmc
                  JOIN {context} ctx
                       ON ctx.instanceid = bmc.userid
                       AND ctx.contextlevel = :contextuser
                 WHERE ctx.id = :contextid";

        $userlist->add_from_sql('userid', $sql, $params);
    }

   /**
     * Get records related to this plugin and user.
     *
     * @param  int $userid The user ID
     * @return array An array of records.
     * @throws \dml_exception
     */
    protected static function get_records($userid) {
        global $DB;

        return $DB->get_records('block_myprogress_course', ['userid' => $userid]);
    }


    /**
     * Export all user data for the specified user, in the specified contexts, using the supplied exporter instance.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        $myprogressdata = [];
        $results = static::get_records($contextlist->get_user()->id);
        foreach ($results as $result) {
            $myprogressdata[] = (object) [
                'courseid' => $result->courseid,
                'progress' => $result->progress,
            ];
        }
        if (!empty($myprogressdata)) {
            $data = (object) [
                'progress' => $myprogressdata,
            ];
            \core_privacy\local\request\writer::with_context($contextlist->current())->export_data([
                get_string('pluginname', 'block_myprogress')], $data);
        }
    }

	
    /**
     * Delete all personal data for all users in the specified context.
     *
     * @param context $context Context to delete data from.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;
        if ($context instanceof \context_user) {
            static::delete_data($context->instanceid);
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contextlist to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
       static::delete_data($contextlist->get_user()->id);
    }

   /**
     * Delete data related to a userid.
     *
     * @param  int $userid The user ID
     * @throws \dml_exception
     */
    protected static function delete_data($userid) {
        global $DB;

        $DB->delete_records('block_myprogress_course', ['userid' => $userid]);
    }

   /**
     * Delete multiple users within a single context.
     *
     * @param   approved_userlist $userlist The approved context and user information to delete information for.
     * @throws \dml_exception
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        $context = $userlist->get_context();

        if ($context instanceof \context_user) {
            static::delete_data($context->instanceid);
        }
    }
}
