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

require_once $CFG->dirroot . '/blocks/ludic_motivators/classes/stores/store_interface.php';

class real_store extends \block_ludic_motivators\iStore {

    public function store_datas($datas, $cmid = 0) {
        global $DB;

        if ($stored_datas = $DB->get_record(
                'ludic_motivators_states',
                array(
                    'course' => $this->courseid,
                    'userid' => $this->userid,
                    'cmid' => $cmid))) {
            $stored_datas->datas        = json_encode($datas);
            $stored_datas->timemodified = time();
            return $DB->update_record('ludic_motivators_states', $stored_datas);
        }
        else {

            $stored_datas = new \stdClass;

            $stored_datas->course       = $this->courseid;
            $stored_datas->cmid         = $cmid;
            $stored_datas->userid       = $this->userid;
            $stored_datas->datas        = json_encode($datas);
            $stored_datas->timemodified = time();

            return $DB->insert_record('ludic_motivators_states', $stored_datas);
        }
    }

    public function get_datas() {
        global $DB;

        if ($datas = $DB->get_record(
                'ludic_motivators_states',
                array(
                    'course' => $this->courseid,
                    'userid' => $this->userid)
            )) {
            return json_decode($datas->datas);
        }
        return null;
    }

    public function get_quiz_attempt($attempt_id) {
        global $DB;

        if ($datas = $DB->get_record('quiz_attempts', array('id' => $attempt_id, 'userid' => $this->userid))) {
            return $datas;
        }
        return null;
    }

    public function get_quiz_info($quiz_id) {
        global $DB;

        if ($datas = $DB->get_record('quiz', array('id' => $quiz_id))) {
            return $datas;
        }
        return null;
    }

    public function get_cm_quiz($quiz_id) {
        global $DB;

        if ($datas = $DB->get_record('course_modules', array('course' => $this->courseid, 'instance' => $quiz_id))) {
            return $datas;
        }
        return null;
    }
}
