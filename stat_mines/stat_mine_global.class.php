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

class stat_mine_global extends stat_mine_base {

    public function evaluate_stat($env, $coursename, $sectionid, $key, $dfn){
        $env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){
        case 'started_course_count':    return $this->evaluate_started_course_count($course, $dfn);
        case 'started_section_count':   return $this->evaluate_started_section_count($course, $dfn);
        }

        // if no match was found then return null to signify that this one was not for us
        return null;
    }


    //-------------------------------------------------------------------------
    // global stats

    // number of courses that this user has started
    private function evaluate_started_course_count($course, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $coursenames    = $env->get_course_names();
        $progressdata   = $datamine->fetch_global_course_progress($userid, $coursenames);

        // calculate the result
        $result = 0;
        foreach ($progressdata as $record){
            $result += (($record->grade != null) ? 1 : 0);
        }

        // store away as an achievement
        $datamine->set_user_global_achievement($userid, 'started_courses', $result);

        // TODO : Evaluate this stat on login or on quiz submitted and just return the achievement value otherwise

        return $result;
    }

    // number of sections that this user has started
    private function evaluate_started_section_count($course, $dfn){
        $datamine       = $env->get_data_mine();
        $userid         = $env->get_userid();
        $coursenames    = $env->get_course_names();
        $progressdata   = $datamine->fetch_global_section_progress($userid, $coursenames);

        // calculate the result
        $result = 0;
        foreach ($progressdata as $record){
            $result += (($record->grade != null) ? 1 : 0);
        }

        // store away as an achievement
        $datamine->set_user_global_achievement($userid, 'started_sections', $result);

        // TODO : Evaluate this stat on login or on quiz submitted and just return the achievement value otherwise

        return $result;
    }
}
