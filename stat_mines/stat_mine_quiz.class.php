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
        case 'past_quiz_times':         return $this->evaluate_past_quiz_times($env, $dfn);
        case 'past_quiz_time':          return $this->evaluate_past_quiz_time($env, $dfn);
        case 'quiz_time':               return $this->evaluate_quiz_time($env, $dfn);
        case 'quiz_score':              return $this->evaluate_quiz_score($env, $dfn);
        case 'quiz_score_gain':         return $this->evaluate_quiz_score_gain($env, $dfn);
        case 'quiz_auto_correct':       return $this->evaluate_quiz_auto_correct($env, $dfn);
        case 'quiz_correct_run':        return $this->evaluate_quiz_correct_run($env, $dfn);
        }

        // if no match was found then return null to signify that this one was not for us
        return null;
    }


    //-------------------------------------------------------------------------
    // quiz stats

    /**
    * @return vector of past attempt times for the given quiz
    */
    private function evaluate_past_quiz_times($env, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_attempt_times($userid);
        return [];
    }

    /**
    * @return nth past attempt times for the given quiz, where 'n' counts backwards in time, so n=1 is most recent past attempt
    */
    private function evaluate_past_quiz_time($env, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_attempt_times($userid);
        return 0;
    }

    /**
    * @return time in seconds since the start of the current quiz attempt
    */
    private function evaluate_quiz_time($env, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_attempt_times($userid);
        return 0;
    }

    /**
    * @return current quiz score
    */
    private function evaluate_quiz_score($env, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_question_stats($userid);
        return 0;
    }

    /**
    * @return quiz points acquired since last check
    */
    private function evaluate_quiz_score_gain($env, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_question_stats($userid);
        return 0;
    }

    /**
    * @return true if the student has auto-corrrected themself in the current quiz
    */
    private function evaluate_quiz_auto_correct($env, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_question_stats($userid);
        return 0;
    }

    /**
    * @return true if the student has complteed the quiz with correct answers to all questions on the first attempt
    */
    private function evaluate_quiz_perfect($env, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_question_stats($userid);
        return 0;
    }

    /**
    * @return true if the student has completed a given number of questions in a run correctly, without a single error
    */
    private function evaluate_quiz_correct_run($env, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $data           = $datamine->get_quiz_question_stats($userid);
        return 0;
    }
}
