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

/**
*  The goal of this class is to provide issolation from the outside world.
*  It should be possible to implement the different units behind this class as stubs for testing purposes
*/
interface execution_environment{
    public function bomb($message);
    public function bomb_if($condition,$message);
//    public function get_user();
//    public function get_page();
    public function get_userid();
    public function get_courseid();
    public function get_motivator_name();
    public function set_motivator_name($name);
    public function get_achievements();
}

