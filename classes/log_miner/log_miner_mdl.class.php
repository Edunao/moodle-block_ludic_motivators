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
require_once dirname(__DIR__) . '/motivators/motivator.interface.php';

class log_miner_mdl implements log_miner {
    private $env;

    public function __construct(execution_environment $env) {
        $this->env      = $env;
    }

    public function get_full_state_data($config){
        $result=[];
        foreach ($config as $element){
            $elementcourse = $element['course'];
            if (array_key_exists('stats', $element)){
                foreach ($element['stats'] as $key => $dfn){
                    $resultkey = $elementcourse . '/' . $key;
                    $result[$resultkey] = $this->evaluate_stat($elementcourse, $dfn);
                }
            }
        }
        return $result;
    }

    public function get_course_state_data($config, $coursename){
        $result=[];
        foreach ($config as $element){
            $elementcourse = ($element['course'] == '*') ? $coursename : $element['course'];
            if (array_key_exists('stats', $element)){
                foreach ($element['stats'] as $key => $dfn){
                    $resultkey = $elementcourse . '/' . $key;
                    $result[$resultkey] = $this->evaluate_stat($elementcourse, $dfn);
                }
            }
        }
        return $result;
    }

    private function evaluate_stat($course, $dfn){
        $this->env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){

        // global stats
        case 'started_course_count':    return $this->evaluate_started_course_count($course, $dfn);

        // course stats
        case 'course_progress':         return $this->evaluate_course_progress($course, $dfn);
        case 'course_complete':         return $this->evaluate_course_complete($course, $dfn);
        case 'course_score':            return $this->evaluate_course_score($course, $dfn);
        case 'best_course_score':       return $this->evaluate_best_course_score($course, $dfn);
        case 'average_course_score':    return $this->evaluate_average_course_score($course, $dfn);
        case 'course_score_rank':       return $this->evaluate_course_score_rank($course, $dfn);
        case 'course_score_old_rank':   return $this->evaluate_course_score_old_rank($course, $dfn);
        case 'course_scored_users':     return $this->evaluate_course_scored_users($course, $dfn);
        case 'course_auto_correct':     return $this->evaluate_course_auto_correct($course, $dfn);
        case 'course_correct_run':      return $this->evaluate_course_correct_run($course, $dfn);

        // quiz stats
        case 'past_quiz_time':          return $this->evaluate_past_quiz_time($course, $dfn);
        case 'quiz_time':               return $this->evaluate_quiz_time($course, $dfn);
        case 'quiz_score':              return $this->evaluate_quiz_score($course, $dfn);
        case 'quiz_score_gain':         return $this->evaluate_quiz_score_gain($course, $dfn);
        case 'quiz_auto_correct':       return $this->evaluate_quiz_auto_correct($course, $dfn);
        case 'quiz_correct_run':        return $this->evaluate_quiz_correct_run($course, $dfn);

        default:
            $this->env->bomb("Unrecognised type in stats definition: " . json_encode($dfn));
        }
    }

    private function evaluate_started_course_count($course, $dfn){
        return 0;
    }

    private function evaluate_course_progress($course, $dfn){
        return 0;
    }

    private function evaluate_course_complete($course, $dfn){
        return 0;
    }

    private function evaluate_course_score($course, $dfn){
        return 0;
    }

    private function evaluate_best_course_score($course, $dfn){
        return 0;
    }

    private function evaluate_average_course_score($course, $dfn){
        return 0;
    }

    private function evaluate_course_score_rank($course, $dfn){
        return 0;
    }

    private function evaluate_course_score_old_rank($course, $dfn){
        return 0;
    }

    private function evaluate_course_scored_users($course, $dfn){
        return 0;
    }

    private function evaluate_course_auto_correct($course, $dfn){
        return 0;
    }

    private function evaluate_course_correct_run($course, $dfn){
        return 0;
    }

    private function evaluate_past_quiz_time($course, $dfn){
        return 0;
    }

    private function evaluate_quiz_time($course, $dfn){
        return 0;
    }

    private function evaluate_quiz_score($course, $dfn){
        return 0;
    }

    private function evaluate_quiz_score_gain($course, $dfn){
        return 0;
    }

    private function evaluate_quiz_auto_correct($course, $dfn){
        return 0;
    }

    private function evaluate_quiz_correct_run($course, $dfn){
        return 0;
    }


//     public function get_stats(){
//         if (! $this->stats){
//             $this->calculate_stats();
//         }
//         return $this->stats();
//     }
//
//     protected function calculate_stats(){
//         // load stats description file
//         // for each stat
//             // if not applicable to the current course then skip it
//             // delegate to the appropriate data processing routine to evaluate the stat
//             // store the result in the results container
//     }

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
