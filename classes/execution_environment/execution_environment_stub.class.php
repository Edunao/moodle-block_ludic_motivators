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
 * @copyright  2017 Edunao SAS (contact@edunao.com)
 * @author     Adrien JAMOT (adrien@edunao.com)
 * @package    block_ludic_motivators
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_ludic_motivators;
defined('MOODLE_INTERNAL') || die();

/**
*  The goal of this class is to provide issolation from the outside world.
*  It should be possible to implement the different units behind this class as stubs for testing purposes
*/
class execution_environment_stub implements execution_environment{
    //-------------------------------------------------------------------------
    // logging and error management

    public function bomb($message) {}
    public function bomb_if($condition,$message) {}

    //-------------------------------------------------------------------------
    // Moodle context

    public function get_userid() {}
    public function get_coursename() {}

    //-------------------------------------------------------------------------
    // Motivator management

    public function get_current_motivator() {}
    public function set_current_motivator($name) {}

    //-------------------------------------------------------------------------
    // Config management

    public function get_presets() {}
    public function get_full_config($motivatorname) {}
    public function get_course_config($motivatorname, $coursename) {}

    //-------------------------------------------------------------------------
    // Evaluation of achievements and suchlike for use by motivators

    public function get_full_state_data() {}
    public function get_course_state_data() {}

    //-------------------------------------------------------------------------
    // rendering

    public function page_requires_jquery_plugin($pluginname) {}
    public function page_requires_css($cssurl) {}
    public function set_block_classes($classes) {}
    public function render($title,$content) {}
    public function get_rendered_output() {}
    public function get_js_init_data() {}
}
