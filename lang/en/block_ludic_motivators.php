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

$string['ludic_motivators:addinstance']     = 'Add a new Ludic Motivators block';
$string['newludic_motivatorsblock']         = '(new Ludic Motivators block)';
$string['pluginname']                       = 'Ludic Motivators';

// loadup the string tables for the motivators
require_once __DIR__ . '/../../motivators/motivators.class.php';
foreach (\block_ludic_motivators\motivators::get_instances() as $motivator){
    foreach($motivator->get_loca_strings() as $stringid => $value){
        $string[$motivator->get_short_name() . '.' . $stringid] = $value;
    }
}
