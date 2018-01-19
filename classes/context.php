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

require_once $CFG->libdir . '/grouplib.php';
require_once $CFG->dirroot . '/blocks/ludic_motivators/classes/stores/real_store.php';

class context {

    var $user;
    var $page;
    var $courseid;
    var $motivator;
    var $store;

    public function __construct($userid, \moodle_page $page) {
        global $DB;
        $this->user      = $DB->get_record('user', array('id' => $userid));
        $this->page      = $page;
        $this->courseid  = $page->course->id;
        $this->motivator = optional_param('motivator', 'avatar', PARAM_TEXT);

        //$this->groups = groups_get_user_groups($this->courseid, $this->user->id);

        $this->store = new real_store($this->courseid, $userid);
    }

    public function getUser() {
        return $this->user;
    }

    public function getPage() {
        return $this->page;
    }

    public function getCourseId() {
        return $this->courseid;
    }

    public function getMotivatorName() {
        return $this->motivator;
    }

    public function getStore() {
        return $this->store;
    }
}
