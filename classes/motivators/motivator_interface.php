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

define('BLOCK_LUDICMOTIVATORS_PATH', '/blocks/ludic_motivators/classes/motivators/');

abstract class iMotivator {

    const BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED = 0;
    const BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED = 1;
    const BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED = 2;

    protected $context;
    protected $preset;

    public function __construct($context, $preset) {
        $this->context = $context;
        $this->preset = $preset;
    }

    public function getMotivatorName() {

        return (new \ReflectionClass($this))->getShortName();
    }

    public function getTitle() {

        return '';
    }

    public function get_content() {

        return 'You must override this method!';
    }

    public function image_url($image) {
        global $CFG;

        //return __DIR__ . '/' . $this->getMotivatorName() . '/pix/' . $image;
        return $CFG->wwwroot . BLOCK_LUDICMOTIVATORS_PATH . $this->getMotivatorName() . '/pix/' . $image;

    }

    public function getContext() {

        return $this->context;
    }

    public function getJsParams() {

        return array();
    }

}
