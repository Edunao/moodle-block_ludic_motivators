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

class log_miner_course extends log_miner_base {

    protected function evaluate_stat($env, $course, $dfn){
        $env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){

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

        default:
            $env->bomb("Unrecognised type in stats definition: " . json_encode($dfn));
        }
    }


    //-------------------------------------------------------------------------
    // course stats

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
}
