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

class stat_mine_section extends stat_mine_base {

    public function evaluate_stat($env, $coursename, $sectionid, $key, $dfn){
        $env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){
        case 'section_progress':        return $this->evaluate_section_progress($env, $coursename, $sectionid, $key, $dfn);
        case 'section_complete':        return $this->evaluate_section_complete($env, $coursename, $sectionid, $key, $dfn);
        case 'section_score':           return $this->evaluate_section_score($env, $coursename, $sectionid, $key, $dfn);
        case 'best_section_score':      return $this->evaluate_best_section_score($env, $coursename, $sectionid, $key, $dfn);
        case 'average_section_score':   return $this->evaluate_average_section_score($env, $coursename, $sectionid, $key, $dfn);
        case 'section_score_rank':      return $this->evaluate_section_score_rank($env, $coursename, $sectionid, $key, $dfn);
        case 'section_score_old_rank':  return $this->evaluate_section_score_old_rank($env, $coursename, $sectionid, $key, $dfn);
        case 'section_scored_users':    return $this->evaluate_section_scored_users($env, $coursename, $sectionid, $key, $dfn);
        case 'section_auto_correct':    return $this->evaluate_section_auto_correct($env, $coursename, $sectionid, $key, $dfn);
        case 'section_correct_run':     return $this->evaluate_section_correct_run($env, $coursename, $sectionid, $key, $dfn);
        }

        // if no match was found then return null to signify that this one was not for us
        return null;

    }


    //-------------------------------------------------------------------------
    // section stats

    private function evaluate_section_progress($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $sectionid);
        return 0;
    }

    private function evaluate_section_complete($env, $coursename, $sectionid, $key, $dfn){
echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';

        // sanity checks
        foreach (['threshold'] as $fieldname){
            $env->bomb_if(! array_key_exists($fieldname, $dfn), "Missing field: $fieldname IN " . json_encode($dfn));
        }

        // lookup the achievement to see if it has already been met
        $userid     = $env->get_userid();
        $datamine   = $env->get_data_mine();
        $result     = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $key, 0);

        // if not previously achieved then check for progress
        if (! $result && $env->is_page_type_in(['my-index', 'mod-quiz-review'])){
            // lookup the data
            $threshold      = $dfn['threshold'];
            $data           = $datamine->get_section_progress($userid, $coursename, $sectionid);
print_object($data);
            $progressValue  = (isset($data->maxgrade) && $data->maxgrade) ? ($data->grade * 100 / $data->maxgrade) : 0;
            if ( $progressValue >= $threshold ){
                $result = 2;
                $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $key, 1);
            }
        }

        return $result;
    }

    private function evaluate_section_score($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $sectionid);
        return 0;
    }

    private function evaluate_best_section_score($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $sectionid);
        return 0;
    }

    private function evaluate_average_section_score($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $sectionid);
        return 0;
    }

    private function evaluate_section_score_rank($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $sectionid);
        return 0;
    }

    private function evaluate_section_score_old_rank($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $sectionid);
        return 0;
    }

    private function evaluate_section_scored_users($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $sectionid);
        return 0;
    }

    private function evaluate_section_auto_correct($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $sectionid);
        return 0;
    }

    private function evaluate_section_correct_run($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $sectionid);
        return 0;
    }
}
