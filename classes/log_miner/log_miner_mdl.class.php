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
 * @copyright  2017 Edunao SAS (contact@edunao.com)
 * @author     Adrien JAMOT (adrien@edunao.com)
 * @package    block_ludic_motivators
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_ludic_motivators;
defined('MOODLE_INTERNAL') || die();

require_once __DIR__ . '/log_miner.interface.php';

class log_miner_mdl implements log_miner {
    private $env;
    private $achievements   = [];
//     protected $courseid;
//     protected $userid;

    public function __construct(execution_environment $env) {
//     public function __construct(, $userid, \moodle_page $page) {

//         global $USER;
//         $this->courseid     = $courseid;
//         $this->userid       = empty($userid) ? $USER->id : $userid;
        $this->env      = $env;
    }

    public function get_full_state_data(){
    }

    public function get_course_state_data($courseid){
    }

    public function get_achievements(){
        if (! $this->achievements){
            $this->calculate_achievements();
        }
        return $this->achievements();
    }

    protected function calculate_achievements(){
        // load achievements description file
        // for each achievement
            // if not applicable to the current course then skip it
            // delegate to the appropriate data processing routine to evaluate the achievement
            // store the result in the results container
    }

//     public function store_datas($datas, $cmid = 0) {
//         global $DB;
//
//         if ($stored_datas = $DB->get_record(
//                 'ludic_motivators_states',
//                 array(
//                     'course' => $this->courseid,
//                     'userid' => $this->userid,
//                     'cmid' => $cmid))) {
//             $stored_datas->datas        = json_encode($datas);
//             $stored_datas->timemodified = time();
//             return $DB->update_record('ludic_motivators_states', $stored_datas);
//         }
//         else {
//
//             $stored_datas = new \stdClass;
//
//             $stored_datas->course       = $this->courseid;
//             $stored_datas->cmid         = $cmid;
//             $stored_datas->userid       = $this->userid;
//             $stored_datas->datas        = json_encode($datas);
//             $stored_datas->timemodified = time();
//
//             return $DB->insert_record('ludic_motivators_states', $stored_datas);
//         }
//     }
//
//     public function get_datas() {
//         global $DB;
//
//         if ($datas = $DB->get_record(
//                 'ludic_motivators_states',
//                 array(
//                     'course' => $this->courseid,
//                     'userid' => $this->userid)
//             )) {
//             return json_decode($datas->datas);
//         }
//         return null;
//     }
//
//     public function get_quiz_attempt($attempt_id) {
//         global $DB;
//
//         if ($datas = $DB->get_record('quiz_attempts', array('id' => $attempt_id, 'userid' => $this->userid))) {
//             return $datas;
//         }
//         return null;
//     }
//
//     public function get_quiz_info($quiz_id) {
//         global $DB;
//
//         if ($datas = $DB->get_record('quiz', array('id' => $quiz_id))) {
//             return $datas;
//         }
//         return null;
//     }
//
//     public function get_cm_quiz($quiz_id) {
//         global $DB;
//
//         if ($datas = $DB->get_record('course_modules', array('course' => $this->courseid, 'instance' => $quiz_id))) {
//             return $datas;
//         }
//         return null;
//     }
}
