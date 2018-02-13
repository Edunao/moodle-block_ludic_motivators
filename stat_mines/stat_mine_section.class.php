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
        case 'section_perfect':         return $this->evaluate_section_perfect($env, $coursename, $sectionid, $key, $dfn);
        }

        // if no match was found then return null to signify that this one was not for us
        return null;

    }


    //-------------------------------------------------------------------------
    // section stats

    private function evaluate_section_progress($env, $coursename, $sectionid, $key, $dfn){
        // lookupt the achievement value
        $userid         = $env->get_userid();
        $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
        $datamine       = $env->get_data_mine();
        $progress       = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, 0);

        if ($env->is_page_type_in(['my-index', 'mod-quiz-review']) || $progress === null){
            $data           = $datamine->get_section_progress($userid, $coursename, $sectionid);
            $grade          = ($data? $data->grade: 0);
            $maxgrade       = ($data? $data->maxgrade: 0);
            $progress       = $maxgrade ? (int)(100 * $grade / $maxgrade) : 0;
            $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $progress);
        }

        return $progress;
    }

    private function evaluate_section_complete($env, $coursename, $sectionid, $key, $dfn){
        // sanity checks
        foreach (['threshold'] as $fieldname){
            $env->bomb_if(! array_key_exists($fieldname, $dfn), "Missing field: $fieldname IN " . json_encode($dfn));
        }

        // lookup the achievement to see if it has already been met
        $userid         = $env->get_userid();
        $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
        $datamine       = $env->get_data_mine();
        $result         = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, 0);

        // if not previously achieved then check for progress
        if ($result === STATE_NOT_ACHIEVED && $env->is_page_type_in(['my-index', 'mod-quiz-review'])){
            // lookup the data
            $threshold      = $dfn['threshold'];
            $data           = $datamine->get_section_progress($userid, $coursename, $sectionid);
            $progressValue  = (isset($data->maxgrade) && $data->maxgrade) ? ($data->grade * 100 / $data->maxgrade) : 0;
            if ( $progressValue >= $threshold ){
                $result = STATE_JUST_ACHIEVED;
                $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_ACHIEVED);
            }
        }

        return $result;
    }

    private function evaluate_section_score($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $coursename, $sectionid);
        return 0;
    }

    private function evaluate_best_section_score($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $coursename, $sectionid);
        return 0;
    }

    private function evaluate_average_section_score($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $coursename, $sectionid);
        return 0;
    }

    private function evaluate_section_score_rank($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $coursename, $sectionid);
        return 0;
    }

    private function evaluate_section_score_old_rank($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $coursename, $sectionid);
        return 0;
    }

    private function evaluate_section_scored_users($env, $coursename, $sectionid, $key, $dfn){
        echo __FUNCTION__ . "($coursename, $sectionid, " . json_encode($dfn) . ')<br>';
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
//         $data           = $datamine->get_section_user_scores($sectionid);
//         $data           = $datamine->get_section_progress($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
//         $data           = $datamine->get_section_answer_stats($userid, $coursename, $sectionid);
        return 0;
    }

    private function evaluate_section_auto_correct($env, $coursename, $sectionid, $key, $dfn){
        // lookup the achievement to see if it has already been met
        $userid         = $env->get_userid();
        $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
        $datamine       = $env->get_data_mine();
        $result         = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_NOT_ACHIEVED);

        // if not previously achieved then check for progress
        $achievable = (($result === STATE_NOT_YET_ACHIEVABLE) || ($result === STATE_NOT_ACHIEVED));
        if ($achievable && $env->is_page_type_in(['mod-quiz-review', 'course-view-topics'])){
            $sectiondata    = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
            $result         = STATE_NOT_YET_ACHIEVABLE;
            $value          = STATE_NOT_YET_ACHIEVABLE;
            foreach ($sectiondata as $quizdata){
                if (empty($quizdata->grades)){
                    // some quizes still to be done so there's still a chance of getting there if we're not there yet
                    continue;
                }
                $attempts       = array_keys($quizdata->grades);
                $firstattempt   = $attempts[0];
                $firstgrade     = $quizdata->grades[$firstattempt];
                $maxgrade       = $quizdata->maxgrade;
                if ($firstgrade == $maxgrade){
                    // the quiz was perfect so no chance of an auto-correct
                    continue;
                }
                // look for corrected attempts
                foreach($quizdata->grades as $grade){
                    if ($grade === $maxgrade){
                        // this one's good so we're there !
                        $result = STATE_JUST_ACHIEVED;
                        $value  = STATE_ACHIEVED;
                        break 2;
                    }
                }
                // the quiz was not perfect so there's a chance of an auto-correct
                $result = STATE_NOT_ACHIEVED;
                $value  = STATE_NOT_ACHIEVED;
            }
            $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $value);
        }

        return $result;
    }

    private function evaluate_section_correct_run($env, $coursename, $sectionid, $key, $dfn){
        // sanity checks
        foreach (['threshold'] as $fieldname){
            $env->bomb_if(! array_key_exists($fieldname, $dfn), "Missing field: $fieldname IN " . json_encode($dfn));
        }
        $targetrunlength = $dfn['threshold'];

        // lookup the achievement to see if it has already been met
        $userid         = $env->get_userid();
        $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
        $datamine       = $env->get_data_mine();
        $result         = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_NOT_ACHIEVED);

        // if not previously achieved then check for progress
        if ($result === STATE_NOT_ACHIEVED && $env->is_page_type_in(['mod-quiz-attempt', 'mod-quiz-summary'])){
            // look for a long enough run
            $grades = $datamine->get_section_answer_stats($userid, $coursename, $sectionid);
            $count  = 0;
            foreach ($grades as $grade){
                if ($grade == 1){
                    ++$count;
                    if ($count >= $targetrunlength){
                        $result = STATE_JUST_ACHIEVED;
                        $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_ACHIEVED);
                        break;
                    }
                } else {
                    $count = 0;
                }
            }
        }

        return $result;
    }

    private function evaluate_section_perfect($env, $coursename, $sectionid, $key, $dfn){
        // lookup the achievement to see if it has already been met
        $userid         = $env->get_userid();
        $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
        $datamine       = $env->get_data_mine();
        $result         = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_NOT_ACHIEVED);

        // if not previously achieved then check for progress
        $achievable = ($result === STATE_NOT_YET_ACHIEVABLE) || ($result === STATE_NOT_ACHIEVED);
        if ($achievable && $env->is_page_type_in(['mod-quiz-review', 'course-view-topics'])){
            $sectiondata = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
            $result  = $sectiondata? STATE_NO_LONGER_ACHIEVABLE : STATE_NOT_YET_ACHIEVABLE;
            $value   = $sectiondata? STATE_NO_LONGER_ACHIEVABLE : STATE_NOT_YET_ACHIEVABLE;
            foreach ($sectiondata as $quizdata){
                if (empty($quizdata->grades)){
                    // some quizes still to be done so there's still a chance of getting there if we're not there yet
                    $result = STATE_NOT_ACHIEVED;
                    $value  = STATE_NOT_ACHIEVED;
                    continue;
                }
                $attempts       = array_keys($quizdata->grades);
                $firstattempt   = $attempts[0];
                $firstgrade     = $quizdata->grades[$firstattempt];
                $maxgrade       = $quizdata->maxgrade;
                if ($firstgrade == $maxgrade){
                    // this one's perfect so we're there !
                    $result = STATE_JUST_ACHIEVED;
                    $value  = STATE_ACHIEVED;
                    break;
                }
            }
            $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $value);
        }

        return $result;
    }
}
