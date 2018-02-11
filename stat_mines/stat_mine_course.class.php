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

class stat_mine_course extends stat_mine_base {

    public function evaluate_stat($env, $coursename, $sectionid, $key, $dfn){
        $env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){
        case 'course_progress':         return $this->evaluate_course_progress($env, $course, $dfn);
        case 'course_complete':         return $this->evaluate_course_complete($env, $course, $dfn);
        case 'course_score':            return $this->evaluate_course_score($env, $course, $dfn);
        case 'best_course_score':       return $this->evaluate_best_course_score($env, $course, $dfn);
        case 'average_course_score':    return $this->evaluate_average_course_score($env, $course, $dfn);
        case 'course_score_rank':       return $this->evaluate_course_score_rank($env, $course, $dfn);
        case 'course_score_old_rank':   return $this->evaluate_course_score_old_rank($env, $course, $dfn);
        case 'course_scored_users':     return $this->evaluate_course_scored_users($env, $course, $dfn);
        case 'course_auto_correct':     return $this->evaluate_course_auto_correct($env, $course, $dfn);
        case 'course_correct_run':      return $this->evaluate_course_correct_run($env, $course, $dfn);
        }

        // if no match was found then return null to signify that this one was not for us
        return null;
    }


    //-------------------------------------------------------------------------
    // course stats

    private function evaluate_course_progress($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }

    private function evaluate_course_complete($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }

    private function evaluate_course_score($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }

    private function evaluate_best_course_score($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }

    private function evaluate_average_course_score($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }

    private function evaluate_course_score_rank($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }

    private function evaluate_course_score_old_rank($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }

    private function evaluate_course_scored_users($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }

    private function evaluate_course_auto_correct($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }

    private function evaluate_course_correct_run($env, $course, $dfn){
        $datamine = $env->get_data_mine();
        return 0;
    }
}
