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

require_once __DIR__ . '/log_miner.interface.php';

abstract class log_miner_base implements log_miner{

    // require derived classes to implement an evaluate_stat() method
    abstract protected function evaluate_stat($env, $course, $dfn);

    // get global state data (independent of the user's current context)
    public function get_global_state_data($env, $config){
        $result=[];
        foreach ($config as $element){
            $elementcourse = $element['course'];
            if (array_key_exists('stats', $element)){
                foreach ($element['stats'] as $key => $dfn){
                    $resultkey = $elementcourse . '/' . $key;
                    $result[$resultkey] = $this->evaluate_stat($elementcourse, $dfn);
                }
            }
        }
        return $result;
    }

    // get contextual state data (based on current course, current section, currentr activity)
    public function get_contextual_state_data($env, $config, $coursename){
        $result=[];
        foreach ($config as $element){
            $elementcourse = ($element['course'] == '*') ? $coursename : $element['course'];
            if (array_key_exists('stats', $element)){
                foreach ($element['stats'] as $key => $dfn){
                    $resultkey = $elementcourse . '/' . $key;
                    $result[$resultkey] = $this->evaluate_stat($elementcourse, $dfn);
                }
            }
        }
        return $result;
    }
}
