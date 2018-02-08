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

// include all of the common stuff that we need in all motivator type implementations
require_once dirname(__DIR__) . '/motivators/motivator.interface.php';
require_once dirname(__DIR__) . '/execution_environment/execution_environment.interface.php';
require_once dirname(dirname(__DIR__)) . '/locallib.php';

abstract class motivator_base {

    public function get_class_name() {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function get_short_name() {
        return preg_replace('/motivator_/','',$this->get_class_name());
    }

    public function get_string($stringid) {
        return \get_string( $this->get_short_name() . '.' . $stringid, 'block_ludic_motivators');
    }

    public function get_name() {
        return $this->get_string('name');
    }

    public function get_strings() {
        $result = [];
        foreach ($this->get_loca_strings() as $stringid){
            $result[$stringid] = $this->get_string($stringid);
        }
        return $result;
    }

    public function image_url($image) {
        $blockname = basename(dirname(dirname(__DIR__)));
        return new \moodle_url("/blocks/$blockname/motivators/" . $this->get_short_name() . "/pix/$image");
    }
}
