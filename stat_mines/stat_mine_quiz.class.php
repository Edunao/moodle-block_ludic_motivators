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
 * @copyright  2018 Edunao SAS (contact@edunao.com)
 * @author     Sadge (daniel@edunao.com)
 * @package    block_ludic_motivators
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_ludic_motivators;
defined('MOODLE_INTERNAL') || die();

require_once dirname(__DIR__) . '/classes/base_classes/stat_mine_base.class.php';

class stat_mine_quiz extends stat_mine_base {

    public function evaluate_stat($env, $coursename, $sectionid, $key, $dfn){
        $env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){
        case 'past_quiz_times':         return $this->evaluate_past_quiz_times($env, $coursename, $sectionid, $key, $dfn);
        case 'past_quiz_time':          return $this->evaluate_past_quiz_time($env, $coursename, $sectionid, $key, $dfn);
        case 'quiz_time':               return $this->evaluate_quiz_time($env, $coursename, $sectionid, $key, $dfn);
        case 'quiz_score':              return $this->evaluate_quiz_score($env, $coursename, $sectionid, $key, $dfn);
        case 'quiz_score_gain':         return $this->evaluate_quiz_score_gain($env, $coursename, $sectionid, $key, $dfn);
        case 'quiz_auto_correct':       return $this->evaluate_quiz_auto_correct($env, $coursename, $sectionid, $key, $dfn);
        case 'quiz_correct_run':        return $this->evaluate_quiz_correct_run($env, $coursename, $sectionid, $key, $dfn);
        }

        // if no match was found then return null to signify that this one was not for us
        return null;
    }


    //-------------------------------------------------------------------------
    // quiz stats

    /**
    * @return vector of past attempt times for the given quiz
    */
    private function evaluate_past_quiz_times($env, $coursename, $sectionid, $key, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_attempt_times($userid);
        return [];
    }

    /**
    * @return nth past attempt times for the given quiz, where 'n' counts backwards in time, so n=1 is most recent past attempt
    */
    private function evaluate_past_quiz_time($env, $coursename, $sectionid, $key, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_attempt_times($userid);
        return 0;
    }

    /**
    * @return time in seconds since the start of the current quiz attempt
    */
    private function evaluate_quiz_time($env, $coursename, $sectionid, $key, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_attempt_times($userid);
        return 0;
    }

    /**
    * @return current quiz score
    */
    private function evaluate_quiz_score($env, $coursename, $sectionid, $key, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_question_stats($userid);
        return 0;
    }

    /**
    * @return quiz points acquired since last check
    */
    private function evaluate_quiz_score_gain($env, $coursename, $sectionid, $key, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_question_stats($userid);
        return 0;
    }

    /**
    * @return state reflecting whether the student has completed the quiz with correct answers to all questions on the first attempt
    */
    private function evaluate_quiz_perfect($env, $coursename, $sectionid, $key, $dfn){
//         // lookup the achievement to see if it has already been met
//         $userid         = $env->get_userid();
//         $cmid           = $env->get_cm_id();
//         $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
//         $datamine       = $env->get_data_mine();
//         $result         = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_NOT_ACHIEVED);
//
//         // if not previously achieved then check for progress
//         $achievable = ($result === STATE_NOT_YET_ACHIEVABLE) || ($result === STATE_NOT_ACHIEVED);
//         if ($achievable && $env->is_page_type_in(['mod-quiz-review', 'course-view-topics'])){
//             $sectiondata = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
// $quizdata = $datamine->get_quiz_question_stats($userid);
//             $result  = $sectiondata? STATE_NO_LONGER_ACHIEVABLE : STATE_NOT_YET_ACHIEVABLE;
//             $value   = $sectiondata? STATE_NO_LONGER_ACHIEVABLE : STATE_NOT_YET_ACHIEVABLE;
//             foreach ($sectiondata as $quizdata){
//                 if (empty($quizdata->grades)){
//                     // some quizes still to be done so there's still a chance of getting there if we're not there yet
//                     $result = STATE_NOT_ACHIEVED;
//                     $value  = STATE_NOT_ACHIEVED;
//                     continue;
//                 }
//                 $attempts       = array_keys($quizdata->grades);
//                 $firstattempt   = $attempts[0];
//                 $firstgrade     = $quizdata->grades[$firstattempt];
//                 $maxgrade       = $quizdata->maxgrade;
//                 if ($firstgrade == $maxgrade){
//                     // this one's perfect so we're there !
//                     $result = STATE_JUST_ACHIEVED;
//                     $value  = STATE_ACHIEVED;
//                     break;
//                 }
//             }
//             $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $value);
//         }

        return $result;
    }

    /**
    * @return state reflecting whether the student has auto-corrrected themself in the current quiz
    */
    private function evaluate_quiz_auto_correct($env, $coursename, $sectionid, $key, $dfn){
//         // lookup the achievement to see if it has already been met
//         $userid         = $env->get_userid();
//         $cmid           = $env->get_cm_id();
//         $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
//         $datamine       = $env->get_data_mine();
//         $result         = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_NOT_ACHIEVED);
//
//         // if not previously achieved then check for progress
//         $achievable = (($result === STATE_NOT_YET_ACHIEVABLE) || ($result === STATE_NOT_ACHIEVED));
//         if ($achievable && $env->is_page_type_in(['mod-quiz-review', 'course-view-topics'])){
//             $result     = STATE_NOT_ACHIEVED;
//             $value      = STATE_NOT_ACHIEVED;
//             $quizdata   = $datamine->get_quiz_stats($userid, $cmid);
//             // use a do ... while (false) construct to make a breakable code block
//             do {
//                 if (empty($quizdata->grades)){
//                     // quiz still to be done so there's still a chance of getting there
//                     $result     = STATE_NOT_YET_ACHIEVABLE;
//                     $value      = STATE_NOT_YET_ACHIEVABLE;
//                     break;
//                 }
//                 $attempts       = array_keys($quizdata->grades);
//                 $firstattempt   = $attempts[0];
//                 $firstgrade     = $quizdata->grades[$firstattempt];
//                 $maxgrade       = $quizdata->maxgrade;
//                 if ($firstgrade == $maxgrade){
//                     // the quiz was perfect so no chance of an auto-correct
//                     $result = STATE_NO_LONGER_ACHIEVABLE;
//                     $value  = STATE_NO_LONGER_ACHIEVABLE;
//                     break;
//                 }
//                 // look for corrected attempts
//                 foreach($quizdata->grades as $grade){
//                     if ($grade === $maxgrade){
//                         // this one's good so we're there !
//                         $result = STATE_JUST_ACHIEVED;
//                         $value  = STATE_ACHIEVED;
//                         break 2;
//                     }
//                 }
//             } while (false);
//
//             $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $value);
//         }

        return $result;
    }

    /**
    * @return state reflecting whether the student has completed a given number of questions in a run correctly, without a single error
    */
    private function evaluate_quiz_correct_run($env, $coursename, $sectionid, $key, $dfn){
//         // sanity checks
//         foreach (['min', 'threshold'] as $fieldname){
//             $env->bomb_if(! array_key_exists($fieldname, $dfn), "Missing field: $fieldname IN " . json_encode($dfn));
//         }
//         $targetrunlength = $dfn['threshold'];
//         $minrunlength    = $dfn['min'];
//
//         // lookup the achievement to see if it has already been met
//         $userid         = $env->get_userid();
//         $cmid           = $env->get_cm_id();
//         $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
//         $datamine       = $env->get_data_mine();
//         $result         = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_NOT_YET_ACHIEVABLE);
//
//         // if not previously achieved then check for progress
//         if ($result < STATE_ACHIEVED && $env->is_page_type_in(['mod-quiz-attempt', 'mod-quiz-summary', 'mod-quiz-view'])){
//             // assume that we're not yet achievable until we have at least one answer attempt behind us
//             $result = STATE_NOT_YET_ACHIEVABLE;
//             $value  = STATE_NOT_YET_ACHIEVABLE;
//
//             // look for a long enough run
//             $grades = $datamine->get_quiz_answer_stats($userid, $cmid);
//             $count  = 0;
//             foreach ($grades as $grade){
//                 if ($grade == 1){
//                     ++$count;
//                     if ($count >= $targetrunlength){
//                         $result = STATE_JUST_ACHIEVED;
//                         $value  = STATE_ACHIEVED;
//                         break;
//                     } else if ($count >= $minrunlength){
//                         $result = STATE_NOT_ACHIEVED;
//                         $value  = STATE_NOT_ACHIEVED;
//                     }
//                 } else {
//                     $count = 0;
//                 }
//             }
//             // store away the current state in the achievements container
//             $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $value);
//         }

        return $result;
    }
}
