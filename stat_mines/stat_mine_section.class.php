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

class log_miner_section extends log_miner_base {

    private function evaluate_stat($env, $course, $dfn){
        $env->bomb_if(!array_key_exists('type', $dfn), 'No type found in stats definition: ' . json_encode($dfn));
        switch($dfn['type']){

        // section stats
        case 'section_progress':        return $this->evaluate_section_progress($course, $section, $dfn);
        case 'section_complete':        return $this->evaluate_section_complete($course, $section, $dfn);
        case 'section_score':           return $this->evaluate_section_score($course, $section, $dfn);
        case 'best_section_score':      return $this->evaluate_best_section_score($course, $section, $dfn);
        case 'average_section_score':   return $this->evaluate_average_section_score($course, $section, $dfn);
        case 'section_score_rank':      return $this->evaluate_section_score_rank($course, $section, $dfn);
        case 'section_score_old_rank':  return $this->evaluate_section_score_old_rank($course, $section, $dfn);
        case 'section_scored_users':    return $this->evaluate_section_scored_users($course, $section, $dfn);
        case 'section_auto_correct':    return $this->evaluate_section_auto_correct($course, $section, $dfn);
        case 'section_correct_run':     return $this->evaluate_section_correct_run($course, $section, $dfn);

        default:
            $env->bomb("Unrecognised type in stats definition: " . json_encode($dfn));
        }
    }


    //-------------------------------------------------------------------------
    // section stats

    private function evaluate_section_progress($course, $section, $dfn){
        return 0;
    }

    private function evaluate_section_complete($course, $section, $dfn){
        return 0;
    }

    private function evaluate_section_score($course, $section, $dfn){
        return 0;
    }

    private function evaluate_best_section_score($course, $section, $dfn){
        return 0;
    }

    private function evaluate_average_section_score($course, $section, $dfn){
        return 0;
    }

    private function evaluate_section_score_rank($course, $section, $dfn){
        return 0;
    }

    private function evaluate_section_score_old_rank($course, $section, $dfn){
        return 0;
    }

    private function evaluate_section_scored_users($course, $section, $dfn){
        return 0;
    }

    private function evaluate_section_auto_correct($course, $section, $dfn){
        return 0;
    }

    private function evaluate_section_correct_run($course, $section, $dfn){
        return 0;
    }
}
