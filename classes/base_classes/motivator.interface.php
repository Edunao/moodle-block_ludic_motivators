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

define('block_ludic_motivators\STATE_NO_LONGER_ACHIEVABLE', -2); // eg: perfect run no longer achievable as an error has been made
define('block_ludic_motivators\STATE_NOT_YET_ACHIEVABLE', -1);   // eg: auto-correct not-yet available as no errors have been made
define('block_ludic_motivators\STATE_NOT_ACHIEVED', 0);
define('block_ludic_motivators\STATE_ACHIEVED', 1);
define('block_ludic_motivators\STATE_JUST_ACHIEVED', 2);

interface i_motivator {
    public function get_loca_strings();
    public function render($env);
}
