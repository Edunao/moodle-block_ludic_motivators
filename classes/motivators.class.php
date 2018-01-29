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

class motivators{
    private static $motivatorclasses = [];

    public static function get_instances() {
        if (!self::$motivatorclasses){
            self::init();
        }

        // generate and return an array of motivator instances
        $result = [];
        foreach (self::$motivatorclasses as $classname){
            $instance = new $classname;
            $result[$instance->get_short_name()] = $instance;
        }
        return $result;
    }

    public static function get_names() {
        $instances = self::get_instances();
        $result = [];
        foreach ($instances as $shortname => $instance){
            $result[$shortname] = $instance->get_name();
        }
        return $result;
    }

    private static function init() {
        $rootpath   = dirname(__DIR__) . '/motivators';
        $filespec   = $rootpath . '/*/main.php';
        $srcfiles   = glob($filespec);
        foreach ($srcfiles as $srcfile) {
            $shortname = preg_replace('%.*/(.*)/main.php%', '${1}', $srcfile);
            $classname = "block_ludic_motivators\\motivator_" . $shortname;
            if (!$classname){
                continue;
            }

            // try loading the source file and check that it includes the motivator class that we're expecting
            require_once $srcfile;
            if (!class_exists($classname)){
                continue;
            }

            // the class exists so register it
            self::$motivatorclasses[] = $classname;
        }

        // sanity check
        if (!self::$motivatorclasses){
            throw new \Excpetion('No motivator types found!');
        }
    }
}
