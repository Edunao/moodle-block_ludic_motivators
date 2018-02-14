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

    private $rawscore   = null;  // raw score as laoded from achievements table (-1 for section with no quizzes)
    private $oldscore   = null;  // score as laoded from achievements table clamped to >= 0
    private $newscore   = null;  // score as calculated dynamically
    private $allscores  = null;

    public function evaluate_stat($env, $coursename, $sectionid, $key, $dfn){
        $env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){
        case 'section_progress':        return $this->evaluate_section_progress($env, $coursename, $sectionid, $key, $dfn);
        case 'section_complete':        return $this->evaluate_section_complete($env, $coursename, $sectionid, $key, $dfn);
        case 'section_score':           return $this->evaluate_section_score($env, $coursename, $sectionid, $key, $dfn);
        case 'section_is_scored':       return $this->evaluate_section_is_scored($env, $coursename, $sectionid, $key, $dfn);
        case 'section_score_gain':      return $this->evaluate_section_score_gain($env, $coursename, $sectionid, $key, $dfn);
        case 'user_section_score':      return $this->evaluate_user_section_score($env, $coursename, $sectionid, $key, $dfn);
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
            $progressValue  = (isset($data->maxgrade) && $data->maxgrade > 0) ? ($data->grade * 100 / $data->maxgrade) : 0;
            if ( $progressValue >= $threshold ){
                $result = STATE_JUST_ACHIEVED;
                $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_ACHIEVED);
            }
        }

        return $result;
    }

    private function evaluate_section_is_scored($env, $coursename, $sectionid, $key, $dfn){
        // call utility method to load calculate and store score values
        $this->analyse_section_score($env, $coursename, $sectionid, $key, $dfn);

        // return true if section contains at least 1 quiz (if not then rawscore will be -1)
        return $this->rawscore >= 0;
    }

    private function evaluate_section_score($env, $coursename, $sectionid, $key, $dfn){
        // call utility method to load calculate and store score values
        $this->analyse_section_score($env, $coursename, $sectionid, $key, $dfn);

        // return the latest score
        return round($this->newscore, 1);
    }

    private function evaluate_section_score_gain($env, $coursename, $sectionid, $key, $dfn){
        // call utility method to load calculate and store score values
        $this->analyse_section_score($env, $coursename, $sectionid, $key, $dfn);

        // return the difference
        return round($this->newscore - $this->oldscore, 1);
    }

    private function analyse_section_score($env, $coursename, $sectionid, $key, $dfn){
        // if we've already calculated the score then just return what we have
        if ($this->newscore !== null){
            return $this->newscore;
        }

        // lookup the old score value in the achievement table
        $userid         = $env->get_userid();
        $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
        $datamine       = $env->get_data_mine();
        $this->rawscore = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, -1);
        $this->oldscore = max($this->rawscore, 0);
        $this->newscore = $this->oldscore;

        // if this is a key course page then recalculate the score
        if ($env->is_page_type_in(['mod-quiz-review', 'course-view-topics'])){
            $this->newscore = 0;
            $this->rawscore = -1;
            $sectiondata = $datamine->get_section_quiz_stats($userid, $coursename, $sectionid);
            foreach ($sectiondata as $quizdata){
                $bestgrade = 0;
                foreach ($quizdata->grades as $grade){
                    $bestgrade = max($bestgrade, $grade);
                }
                $this->newscore += $bestgrade;
                $this->rawscore = $this->newscore;
            }
            // record the new score in the achievements table for use next time
            $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $this->rawscore);
        }
    }

    private function evaluate_user_section_score($env, $coursename, $sectionid, $key, $dfn){
        $userid = $env->get_userid();
        $scores = $this->fetch_user_section_scores($env, $coursename, $sectionid);
        return array_key_exists($userid, $scores) && $scores[$userid] != null ? $scores[$userid] : 0;
    }

    private function evaluate_best_section_score($env, $coursename, $sectionid, $key, $dfn){
        $scores = $this->fetch_user_section_scores($env, $coursename, $sectionid);
        return $scores ? round(max($scores, 1)) : 0;
    }

    private function evaluate_average_section_score($env, $coursename, $sectionid, $key, $dfn){
        $scores = $this->fetch_user_section_scores($env, $coursename, $sectionid);
        $sum    = 0;
        foreach($scores as $score){
            $sum += $score;
        }
        return empty($scores) ? 0 : round($sum / count($scores), 1);
    }

    private function evaluate_section_score_rank($env, $coursename, $sectionid, $key, $dfn){
        // call sister method to load calculate and store rank values
        $this->fetch_user_section_scores($env, $coursename, $sectionid);

        return $this->newrank;
    }

    private function evaluate_section_score_old_rank($env, $coursename, $sectionid, $key, $dfn){
        // call sister method to load calculate and store rank values
        $this->fetch_user_section_scores($env, $coursename, $sectionid);

        return $this->oldrank;
    }

    private function evaluate_section_scored_users($env, $coursename, $sectionid, $key, $dfn){
        $userid = $env->get_userid();
        $scores = $this->fetch_user_section_scores($env, $coursename, $sectionid);
        return count($scores) + (array_key_exists($userid, $scores) && $scores[$userid] != null ? 0 : 1);
    }

    private function fetch_user_section_scores($env, $coursename, $sectionid){
        if ($this->allscores === null){
            // fetch and cache the user scores for all users who have completed exercises in this section
            $datamine           = $env->get_data_mine();
            $scores             = $datamine->get_section_user_scores($coursename, $sectionid);
            $this->allscores    = $scores;

            // fetch and cache the old rank value
            $userid             = $env->get_userid();
            $achievement        = $env->get_current_motivator()->get_short_name() . '/rank';
            $this->oldrank      = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, 0);

            // calculate and stoe away the new rank value
            $ownscore = array_key_exists($userid, $scores) ? $scores[$userid] : 0;
            $this->newrank = 1;
            foreach($scores as $score){
                $this->newrank += ($score > $ownscore) ? 1 : 0;
            }
            $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $this->newrank);
        }
        return $this->allscores;
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
        foreach (['min', 'threshold'] as $fieldname){
            $env->bomb_if(! array_key_exists($fieldname, $dfn), "Missing field: $fieldname IN " . json_encode($dfn));
        }
        $targetrunlength = $dfn['threshold'];
        $minrunlength    = $dfn['min'];

        // lookup the achievement to see if it has already been met
        $userid         = $env->get_userid();
        $achievement    = $env->get_current_motivator()->get_short_name() . '/' . $key;
        $datamine       = $env->get_data_mine();
        $result         = $datamine->get_user_section_achievement($userid, $coursename, $sectionid, $achievement, STATE_NOT_YET_ACHIEVABLE);

        // if not previously achieved then check for progress
        if ($result < STATE_ACHIEVED && $env->is_page_type_in(['mod-quiz-attempt', 'mod-quiz-summary', 'mod-quiz-view'])){
            // assume that we're not yet achievable until we have at least one answer attempt behind us
            $result = STATE_NOT_YET_ACHIEVABLE;
            $value  = STATE_NOT_YET_ACHIEVABLE;

            // look for a long enough run
            $grades = $datamine->get_section_answer_stats($userid, $coursename, $sectionid);
            $count  = 0;
            foreach ($grades as $grade){
                if ($grade == 1){
                    ++$count;
                    if ($count >= $targetrunlength){
                        $result = STATE_JUST_ACHIEVED;
                        $value  = STATE_ACHIEVED;
                        break;
                    } else if ($count >= $minrunlength){
                        $result = STATE_NOT_ACHIEVED;
                        $value  = STATE_NOT_ACHIEVED;
                    }
                } else {
                    $count = 0;
                }
            }
            // store away the current state in the achievements container
            $datamine->set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $value);
        }

        return $result;
    }
}
