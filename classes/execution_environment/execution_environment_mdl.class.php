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
class execution_environment_mdl implements execution_environment{

    private $user;
    private $page;
    private $courseid;
    private $currentmotivator = null;
    private $logminer;

    public function __construct($userid, \moodle_page $page) {
        global $DB;
        $this->user     = $DB->get_record('user', array('id' => $userid));
        $this->page     = $page;
        $this->courseid = $page->course->id;
        $this->logminer = new log_miner_mdl($this);
    }

    public function bomb($message){
        throw new \Exception($message);
    }

    public function bomb_if($condition,$message){
        if ($condition){
            $this->bomb($message);
        }
    }

    public function get_user() {
        return $this->user;
    }

    public function get_page() {
        return $this->page;
    }

    public function get_courseid() {
        return $this->courseid;
    }

    public function get_current_motivator(){
        // if we've already identified the current motivator then return the value that we've stored away
        if ($this->currentmotivator){
            return $this->currentmotivator;
        }

        // check to see if the motivator is supplied as a parameter (as in if the debug menu is being used)
        $this->set_current_motivator(optional_param('motivator', null, PARAM_TEXT));
        if ($this->currentmotivator){
            return $this->currentmotivator;
        }

        // try looking up the motivator name in the user's profile
        $this->currentmotivator = $this->lookup_motivator($this->read_motivator_selection());
        if ($this->currentmotivator){
            return $this->currentmotivator;
        }

        // if all else fails fall back to the first valid motivator available
        $this->set_current_motivator(array_keys(motivators::get_instances())[0]);
        return $this->currentmotivator;
    }

    private function lookup_motivator($motivatortype){
        // if motivator not defined then return false
        if (!$motivatortype){
            return null;
        }

        // check that the motivator name that we have is valid
        $motivators = motivators::get_instances();
        return array_key_exists($motivatortype, $motivators)? $motivators[$motivatortype]: null;
    }

    public function set_current_motivator($name){
        $motivator = $this->lookup_motivator($name);
        if (!$motivator){
            return;
        }
        $this->motivator = $motivator;
        $this->write_motivator_selection($name);
    }

    public function get_achievements() {
        return $this->logminer->get_achievements();
    }

    private function read_motivator_selection(){
        global $DB;

        // fetch the identifier of the field used to store user data from the database
        $query    = '
            SELECT uif.id
            FROM {user_info_field} uif
            JOIN {user_info_category} uic ON uif.categoryid = uic.id
            WHERE uic.name="block_ludimotivators" AND uif.shortname="motivator"
        ';
        $fieldid = $DB->get_field_sql($query);

        // sanity check
        $this->bomb_if(!$fieldid, 'Missing user infor field definition');
        $this->bomb_if(!$this->user, 'Missing "USER" object');
        $this->bomb_if(!property_exists($this->user, 'id'), 'Missing "USER->id" property');

        // fetch the data record that holds the options for this course
        $name = $DB->get_field( 'user_info_data', 'data', Array( 'userid'=>$this->user->id, 'fieldid'=>$fieldid ) );

        return ($name? $name: '');
    }

    private function write_motivator_selection($value){
        global $DB;

        // fetch the identifier of the field used to store user data from the database
        $query    = '
            SELECT uif.id
            FROM {user_info_field} uif
            JOIN {user_info_category} uic ON uif.categoryid = uic.id
            WHERE uic.name="block_ludimotivators" AND uif.shortname="motivator"
        ';
        $fieldid = $DB->get_field_sql($value);

        // sanity check
        $this->bomb_if(!$fieldid, 'Missing user infor field definition');
        $this->bomb_if(!$this->user, 'Missing "USER" object');
        $this->bomb_if(!property_exists($this->user, 'id'), 'Missing "USER->id" property');

        // fetch the data record that holds the options for this course
        $name = $DB->get_field('user_info_data', 'data', Array( 'userid'=>$this->user->id, 'fieldid'=>$fieldid));

        // if the name read from DB matches new value then no update is required so we're all done
        if ($name===$value){
            return;
        }

        // if we found no data then return the empty set
        if ($name){
            $DB->set_field('user_info_data', 'data', $value, Array( 'userid'=>$this->user->id, 'fieldid'=>$fieldid ));
        } else {
            $record=[
                'userid'    => $this->user->id,
                'fieldid'   => $fieldid,
                'data'      => $value
            ];
            $DB->insert_record('user_info_data', $record);
        }
    }
}
