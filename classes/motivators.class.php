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
    private static $instances  = [];

    public static function get_motivator_instances() {
        if (empty(self::$instances)){
            self::identify_motivators();
        }
        return self::$instances;
    }

    public static function get_motivator_names() {
        if (empty(self::$names)){
            self::identify_motivators();
        }
        return self::$names;
    }

    private static function identify_motivators() {
        $direntries = new DirectoryIterator($motivator_path);
        foreach (glob(__DIR__ . '/motivator_*.class.php') as $srcfile) {
            $classname = preg_replace('%.*/(.*).class.php%', '${1}', $srcfile);
            if (!$classname){
                continue;
            }

            // try loading the source file and check that it includes the motivator class that we're expecting
            require_once $srcfile;
            if (!class_exists($classname)){
                continue;
            }

            // store away a new instance of the loaded class in our internal container
            self::$instances[$classname] = new $classname;
            self::$names[$classname] = self::$instances[$classname]->get_name();
        }
    }
}
