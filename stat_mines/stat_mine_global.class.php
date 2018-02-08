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

class log_miner_global extends log_miner_base {

    protected function evaluate_stat($env, $course, $dfn){
        $env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){

        // global stats
        case 'started_course_count':    return $this->evaluate_started_course_count($course, $dfn);
        case 'started_section_count':   return $this->evaluate_started_section_count($course, $dfn);

        default:
            $env->bomb("Unrecognised type in stats definition: " . json_encode($dfn));
        }
    }


    //-------------------------------------------------------------------------
    // global stats

    private function evaluate_started_course_count($course, $dfn){
        // number of courses that this user has started
        // fetch all
        return 0;
    }

    private function evaluate_started_section_count($course, $dfn){
        // number of courses that this user has started
        // fetch all
        return 0;
    }
}
