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

class stat_mine{
    private static $mineclasses = [];
    private $instances = [];

    public function get_global_state_data($env, $config){
        $mines = get_instances();
        $result=[];

        // for each configuration item
        foreach ($config as $element){
            $elementcourse = $element['course'];
            if (array_key_exists('stats', $element)){

                // for each stat required by the configuration item
                foreach ($element['stats'] as $key => $dfn){

                    // for each stat evaluator
                    foreach ($mines as $mine){
                        $statvlaue = $mine->evaluate_stat($elementcourse, $dfn);

                        // if the evaluator gave us a value for the stat then use it (otherwise iterate)
                        if ($statvlaue !== null){
                            $resultkey = $elementcourse . '/' . $key;
                            $result[$resultkey] = $statvlaue;
                            continue 2;
                        }
                    }

                    // if no match was found for the stat that we're trying to evaluate then cry about it
                    $env->bomb("Unrecognised type in stats definition: " . json_encode($dfn));
                }
            }
        }

        return $result;
    }

    public function get_contextual_state_data($env, $config, $coursename){
        $mines = self::get_instances();
        $result=[];

        // for each configuration item
        foreach ($config as $element){
            $elementcourse = ($element['course'] == '*') ? $coursename : $element['course'];
            if (array_key_exists('stats', $element)){

                // for each stat required by the configuration item
                foreach ($element['stats'] as $key => $dfn){

                    // for each stat evaluator
                    foreach ($mines as $mine){
                        $statvlaue = $mine->evaluate_stat($elementcourse, $dfn);

                        // if the evaluator gave us a value for the stat then use it (otherwise iterate)
                        if ($statvlaue !== null){
                            $resultkey = $elementcourse . '/' . $key;
                            $result[$resultkey] = $statvlaue;
                            continue 2;
                        }
                    }

                    // if no match was found for the stat that we're trying to evaluate then cry about it
                    $env->bomb("Unrecognised type in stats definition: " . json_encode($dfn));
                }
            }
        }

        return $result;
    }

    protected static function get_instances() {
        // if we already have the instances that we need then just return them
        if (!empty($this->instances)){
            return $this->instances;
        }

        // if the class hasn't been initialised yet then do it now
        if (!self::$mineclasses){
            self::init();
        }

        // return an array of new mine instances
        foreach (self::$mineclasses as $classname){
            $mine = new $classname;
            $this->instances[] = $mine;
        }
        return $this->instances;
    }

    private static function init() {
        $rootpath   = dirname(__DIR__) . '/stat_mines';
        $filespec   = $rootpath . '/stat_mine_*.class.php';
        $srcfiles   = glob($filespec);
        foreach ($srcfiles as $srcfile) {
            $classname = preg_replace('%.*/(.*).php%', '${1}', $srcfile);

            // try loading the source file and check that it includes the log mine class that we're expecting
            require_once $srcfile;
            if (!class_exists($classname)){
                continue;
            }

            // the class exists so register it
            self::$mineclasses[] = $classname;
        }

        // sanity check
        if (!self::$mineclasses){
            throw new \Excpetion('No log mine types found!');
        }
    }
}
