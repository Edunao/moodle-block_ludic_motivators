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
    private static $instances = [];

    public static function get_instances($env) {
        $result = self::$instances;

        // if we don't have a cached instance table then generate a new one
        if (empty($result)){
            if ($env === false){
                // generate a temporary instance set and don't cache it
                require_once __DIR__ . '/execution_environment/execution_environment_stub.class.php';
                $env = new execution_environment_stub;
                $result = self::identify_motivators($env);
            } else {
                // generate and cache the instance set
                $result = self::identify_motivators($env);
                self::$instances = $result;
            }
        }

        // return the result
        return $result;
    }

    public static function get_names() {
        $result = [];
        $instances = self::get_instances( false );
        foreach ($instances as $classname => $instance){
            $result[$classname] = $instance->get_name();
        }
        return $result;
    }

    private static function identify_motivators($env) {
        $rootpath = dirname(__DIR__) . '/motivators';
        foreach (glob($rootpath . '/*/main.php') as $srcfile) {
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

            // store away a new instance of the loaded class in our internal container
            $result[$classname] = new $classname($env);
        }
        return $result;
    }
}
