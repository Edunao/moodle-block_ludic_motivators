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

abstract class data_mine_base {

    //---------------------------------------------------------------------------------------------
    // Private data

    private $cache = [];


    //---------------------------------------------------------------------------------------------
    // Quiz Context getters

    /**
     * Quiz Context : get stats for questions in current quiz
     * @return vector of { (done|todo), score }
     */
    public function get_quiz_question_stats($userid/*, $quizid*/){
        // generate an appropriate unique cache key
        $cachekey = __FUNCTION__ . ':' . $userid /*. ':' . $quizid*/;

        // if the item doesn't exist yet in the cache then generate it and store it away
        if (! array_key_exists($cachekey, $this->cache)){
            // lookup the quiz attempt id and if not found then return an empty set
            $questionusageid = optional_param('attempt', 0, PARAM_INT);
            if ( !$questionusageid ){
                return [];
            }
            $this->cache[$cachekey] = $this->fetch_quiz_question_stats($userid, $questionusageid);
//            $this->cache[$cachekey] = $this->fetch_quiz_question_stats($userid, $quizid);
        }

        // return the result
        return $this->cache[$cachekey];
    }

    /**
     * Quiz Context : get times of attampts of current quiz
     * @return vector of { attempt start time, (end-time|null) }
     */
    public function get_quiz_attempt_times($userid, $quizid){
        // generate an appropriate unique cache key
        $cachekey = __FUNCTION__ . ':' . $userid . ':' . $quizid;

        // if the item doesn't exist yet in the cache then generate it and store it away
        if (! array_key_exists($cachekey, $this->cache)){
            // lookup the quiz attempt id and if not found then return an empty set
            $currentattemptid = optional_param('attempt', 0, PARAM_INT);
            if (! $currentattemptid){
                return [];
            }

            $this->cache[$cachekey] = $this->fetch_quiz_attempt_times($userid, $currentattemptid);
        }

        // return the result
        return $this->cache[$cachekey];
    }


    //---------------------------------------------------------------------------------------------
    // Section Context getters

    /**
     * Section Context : get scores for all users for the exercises in the current section
     * @return vector of { user id => score }
     */
    public function get_section_user_scores($sectionid){
        // generate an appropriate unique cache key
        $cachekey = __FUNCTION__ . ':' . $sectionid;

        // if the item doesn't exist yet in the cache then generate it and store it away
        if (! array_key_exists($cachekey, $this->cache)){
            $this->cache[$cachekey] = $this->fetch_section_user_scores($sectionid);
        }

        // return the result
        return $this->cache[$cachekey];
    }

    /**
     * Section Context : get a progress rating for the dsection based on its quiz questions
     * @param  int $userid user identifier
     * @param  int $course course identifier
     * @param  int $sectionid - course-relative section id
     * @return { maxgrade, grade }
     */
    public function get_section_progress($userid, $course, $sectionid){
        // generate an appropriate unique cache key
        $cachekey = __FUNCTION__ . ':' . $userid . ':' . $course . '#' . $sectionid;

        // if the item doesn't exist yet in the cache then generate it and store it away
        if (! array_key_exists($cachekey, $this->cache)){
            $this->cache[$cachekey] = $this->fetch_section_progress($userid, $course, $sectionid);
        }

        // return the result
        return $this->cache[$cachekey];
    }

    /**
     * Section Context : get stats sbout all of the quizzes available and attempted by the user in the section
     * @return vector of { available score, attemps as vector of score }
     */
    public function get_section_quiz_stats($userid, $sectionid){
        // generate an appropriate unique cache key
        $cachekey = __FUNCTION__ . ':' . $userid . ':' . $course . '#' . $sectionid;

        // if the item doesn't exist yet in the cache then generate it and store it away
        if (! array_key_exists($cachekey, $this->cache)){
            $this->cache[$cachekey] = $this->fetch_section_quiz_stats($userid, $sectionid);
        }

        // return the result
        return $this->cache[$cachekey];
    }

    /**
     * Section Context : get scores for all questions answered by the user in the current section in chronological order
     * @return vector of score-fraction
     */
    public function get_section_answer_stats($userid, $sectionid){
        // generate an appropriate unique cache key
        $cachekey = __FUNCTION__ . ':' . $userid . ':' . $course . '#' . $sectionid;

        // if the item doesn't exist yet in the cache then generate it and store it away
        if (! array_key_exists($cachekey, $this->cache)){
            $this->cache[$cachekey] = fetch_section_answer_stats($userid, $sectionid);
        }

        // return the result
        return $this->cache[$cachekey];
    }



    //---------------------------------------------------------------------------------------------
    // Global Context getters
    /**
     * Global Context : Get progress information for all of the sections for the given user
     * @return vector of sectionid => { maxgrade, grade }
     */
    public function get_global_section_progress($userid, $coursenames){
        // generate an appropriate unique cache key
        $cachekey = __FUNCTION__ . ':' . $userid . ':' . json_encode($coursenames);

        // if the item doesn't exist yet in the cache then generate it and store it away
        if (! array_key_exists($cachekey, $this->cache)){
            $this->cache[$cachekey] = fetch_global_section_progress($userid, $coursenames);
        }

        // return the result
        return $this->cache[$cachekey];
    }


    //---------------------------------------------------------------------------------------------
    // Achievement Store getters

    /**
     * Achievement Store : Getter for global achievement
     * @return achievement value from data mine or $defaultvalue if not found in data mine
     */
    public function get_user_global_achievement($userid, $achievement, $defaultvalue = null){
        $prefix = 'G::';
        return $this->get_user_achievement($userid, $prefix, $achievement, $defaultvalue);
    }

    /**
     * Achievement Store : Getter for course-context achievement
     * @return achievement value from data mine or $defaultvalue if not found in data mine
     */
    public function get_user_course_achievement($userid, $coursename, $achievement, $defaultvalue = null){
        $prefix = 'C:' . $coursename . ':';
        return $this->get_user_achievement($userid, $prefix, $achievement, $defaultvalue);
    }

    /**
     * Achievement Store : Getter for section-context achievement
     * @return achievement value from data mine or $defaultvalue if not found in data mine
     */
    public function get_user_section_achievement($userid, $coursename, $sectionid, $achievement, $defaultvalue = null){
        $prefix = 'S:' . $coursename . '#' . $sectionid . ':';
        return $this->get_user_achievement($userid, $prefix, $achievement, $defaultvalue);
    }

    /**
     * Achievement Store : Getter for quiz-context achievement
     * @return achievement value from data mine or $defaultvalue if not found in data mine
     */
    public function get_user_quiz_achievement($userid, $quizname, $achievement, $defaultvalue = null){
        $prefix = 'Q:' . $quizid . ':';
        return $this->get_user_achievement($userid, $prefix, $achievement, $defaultvalue);
    }

    /**
     * Achievement Store : Getter for global achievement set
     * @return vector of { achievement id => value }
     */
    public function get_user_global_achievements($userid){
        $prefix = 'G::';
        return $this->get_user_achievements($userid, $prefix);
    }

    /**
     * Achievement Store : Getter for course-context achievement set
     * @return vector of { achievement id => value }
     */
    public function get_user_course_achievements($userid, $coursename){
        $prefix = 'C:' . $coursename . ':';
        return $this->get_user_achievements($userid, $prefix);
    }

    /**
     * Achievement Store : Getter for section-context achievement set
     * @return vector of { achievement id => value }
     */
    public function get_user_section_achievements($userid, $coursename, $sectionid){
        $prefix = 'S:' . $coursename . '#' . $sectionid . ':';
        return $this->get_user_achievements($userid, $prefix);
    }

    /**
     * Achievement Store : Getter for quiz-context achievement set
     * @return vector of { achievement id => value }
     */
    public function get_user_quiz_achievements($userid, $quizname){
        $prefix = 'Q:' . $quizid . ':';
        return $this->get_user_achievements($userid, $prefix);
    }


    //---------------------------------------------------------------------------------------------
    // Achievement Store setters

    /**
     * Achievement Store : Setter for global achievement set
     * @return vector of { achievement id => value }
     */
    public function set_user_global_achievement($userid, $achievement, $value){
        $prefix = 'G::';
        $this->set_user_achievements($userid, $prefix, $achievement, $value);
    }

    /**
     * Achievement Store : Setter for course-context achievement set
     * @return vector of { achievement id => value }
     */
    public function set_user_course_achievement($userid, $coursename, $achievement, $value){
        $prefix = 'C:' . $coursename . ':';
        $this->set_user_achievements($userid, $prefix, $achievement, $value);
    }

    /**
     * Achievement Store : Setter for section-context achievement set
     * @return vector of { achievement id => value }
     */
    public function set_user_section_achievement($userid, $coursename, $sectionid, $achievement, $value){
        $prefix = 'S:' . $coursename . '#' . $sectionid . ':';
        $this->set_user_achievements($userid, $prefix, $achievement, $value);
    }

    /**
     * Achievement Store : Setter for quiz-context achievement set
     * @return vector of { achievement id => value }
     */
    public function set_user_quiz_achievement($userid, $quizid, $achievement, $value){
        $prefix = 'Q:' . $quizid . ':';
        $this->set_user_achievements($userid, $prefix, $achievement, $value);
    }


    //---------------------------------------------------------------------------------------------
    // abstract functions for implementation in derived class

    // For batched database write management (optimisation)
    public abstract function flush_changes_to_database();

    // For Quiz Context
    protected abstract function fetch_quiz_question_stats($userid, $questionusageid);
    protected abstract function fetch_quiz_attempt_times($userid, $currentattemptid);

    // For Section Context
    protected abstract function fetch_section_user_scores($course, $sectionid);
    protected abstract function fetch_section_progress($userid, $course, $sectionid);
    protected abstract function fetch_section_quiz_stats($userid, $course, $sectionid);
    protected abstract function fetch_section_answer_stats($userid, $course, $sectionid);

    // For Global Context
    protected abstract function fetch_global_section_progress($userid, $coursenames);

    // For Achievement Store
    protected abstract function get_user_achievements($userid, $prefix);
    protected abstract function set_user_achievements($userid, $prefix, $achievement, $value);


    //---------------------------------------------------------------------------------------------
    // private utilities

    private function get_user_achievement($userid, $prefix, $achievement, $defaultvalue){
        $storedachievements = $this->get_user_achievements($userid, $prefix);
        $key = $prefix . $achievement;
        return array_key_exists($key, $storedachievements) ? $storedachievements[$key] : $defaultvalue;
    }
}
