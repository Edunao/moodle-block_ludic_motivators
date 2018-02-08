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

require_once __DIR__ . '/log_miner_base.class.php';

class log_miner_quiz extends log_miner_base {

    protected function evaluate_stat($env, $course, $dfn){
        $env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){
        case 'past_quiz_times':         return $this->evaluate_past_quiz_times($dfn);
        case 'past_quiz_time':          return $this->evaluate_past_quiz_time($dfn);
        case 'quiz_time':               return $this->evaluate_quiz_time($dfn);
        case 'quiz_score':              return $this->evaluate_quiz_score($dfn);
        case 'quiz_score_gain':         return $this->evaluate_quiz_score_gain($dfn);
        case 'quiz_auto_correct':       return $this->evaluate_quiz_auto_correct($dfn);
        case 'quiz_correct_run':        return $this->evaluate_quiz_correct_run($dfn);
        }

        // if no match was found then return null to signify that this one was not for us
        return null;
    }


    //-------------------------------------------------------------------------
    // quiz stats

    private function evaluate_past_quiz_times($dfn){
        return [];
    }

    private function evaluate_past_quiz_time($dfn){
        return 0;
    }

    private function evaluate_quiz_time($dfn){
        return 0;
    }

    private function evaluate_quiz_score($dfn){
        return 0;
    }

    private function evaluate_quiz_score_gain($dfn){
        return 0;
    }

    private function evaluate_quiz_auto_correct($dfn){
        return 0;
    }

    private function evaluate_quiz_correct_run($dfn){
        return 0;
    }


    //-------------------------------------------------------------------------
    // quiz data fetching


}
