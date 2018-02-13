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

require_once __DIR__ . '/base_classes/execution_environment.interface.php';
require_once __DIR__ . '/stat_mine.class.php';
require_once __DIR__ . '/data_mine.class.php';

/**
*  The goal of this class is to provide issolation from the outside world.
*  It should be possible to implement the different units behind this class as stubs for testing purposes
*/
class execution_environment implements i_execution_environment{

    private $settings           = null;
    private $page               = null;
    private $user               = null;
    private $courseid           = null;
    private $sectionid          = null;
    private $sectionidx         = null;
    private $currentmotivator   = null;
    private $datamine           = null;
    private $statmine           = null;
    private $output             = '';
    private $jsinitdata         = [];
    private $blockclasses       = 'ludi-block';
    private $config             = ['courses' => [], 'elements' => [], 'motivators' => []];
    private $presets            = [];

    public function __construct(\moodle_page $page, $settings) {
        global $DB, $CFG, $USER;

        $this->settings     = $settings;
        $this->page         = $page;
        $userid             = $this->get_setting('userid', 0);
        $this->user         = ($userid === $USER->id) ? $USER : $DB->get_record('user', array('id' => $userid));
        $this->statmine     = new stat_mine($this);

        // load global configuration data
        $configfile     = $CFG->dataroot . '/filedir/ludic-motivators-config.json';
        if (file_exists($configfile)){
            $jsonconfigdata = file_get_contents($configfile);
            $this->config   = json_decode($jsonconfigdata, true);
            $this->bomb_if(!$this->config, 'Failed to JSON decode config file: ' . $configfile);
            $this->bomb_if(!isset($this->config['courses']), '"courses" node not found in config file: ' . $configfile);
            $this->bomb_if(!isset($this->config['elements']), '"elements" node not found in config file: ' . $configfile);
        }

        // determine names of motivator's config files
        $motivator      = $this->get_current_motivator();
        $configfile     = dirname(__DIR__) . '/motivators/' . $motivator->get_short_name() . '/config.json';
        $testdatafile   = dirname(__DIR__) . '/motivators/' . $motivator->get_short_name() . '/testdata.json';

        // load motivator configuration data
        if (file_exists($configfile)){
            $jsonconfigdata = file_get_contents($configfile);
            $motivatorconfig = json_decode($jsonconfigdata, true);
            $this->bomb_if(!$motivatorconfig, 'Failed to JSON decode config file: ' . $configfile);
            $this->config = array_merge_recursive($this->config, $motivatorconfig);
        }
        $this->bomb_if(!isset($this->config['elements']), '"elements" node not found in config files');
        $this->bomb_if(!isset($this->config['courses']), '"courses" node not found in config files');

        // load testing data as required
        if ($this->get_setting('testmode', false) === true){
            if (file_exists($testdatafile)){
                $jsontestdata   = file_get_contents($testdatafile);
                $testdata       = json_decode($jsontestdata, true);
                $this->bomb_if(!$testdata, 'Failed to JSON decode test data file: ' . $testdatafile );
                if (isset($testdata['config'])){
                    $this->config = array_merge_recursive($this->config, $testdata['config']);
                }
                if (isset($testdata['fullpreset'])){
                    $this->presets['fullpreset'] = $testdata['fullpreset'];
                }
                if (isset($testdata['coursepreset'])){
                    $this->presets['coursepreset'] = $testdata['coursepreset'];
                }
                if (isset($testdata['preset'])){
                    $this->presets['preset'] = $testdata['preset'];
                }
            }
        }
    }

    public function bomb($message){
        throw new \Exception($message);
    }

    public function bomb_if($condition,$message){
        if ($condition){
            $this->bomb($message);
        }
    }

    public function get_userid() {
        return $this->user->id;
    }

    public function get_cm_id() {
        return isset($this->page->cm->id) ? $this->page->cm->id : 0;
    }

    public function get_attempt_id() {
        return $this->get_setting('attemptid', 0);
    }

    public function get_course_name() {
        return $this->page->course->shortname;
    }

    public function get_section_idx(){
        // if we haven't got a stored section id then try generating one
        if ($this->sectionidx === null){
            if ($this->page->pagetype == 'course-view-topics'){
                // we're on a course view page and the course-relative section number is provided directly
                $this->sectionidx = $this->get_setting('section',0);
            } else if (isset($this->page->cm->section)) {
                // we're in an activity that is declaring its section id so we need to lookup the corresponding course-relative index
                global $DB;
                $sectionid = $this->page->cm->section;
                $this->sectionidx = $sectionid ? $DB->get_field('course_sections', 'section', ['id' => $sectionid ]) : 0;
            } else {
                // no luck so replace the null with a -1 to avoid wasting times on trying to re-evaluate next time round
                $this->sectionidx = -1;
            }
        }

        // return the stored result
        return $this->sectionidx;
    }

    public function get_section_id() {
        // if we haven't got a stored section id then try generating one
        if ($this->sectionid === null){
            if ($this->page->pagetype == 'course-view-topics'){
                // we're on a course view page and the course-relative section number is provided so lookup the real section id
                global $DB;
                $coursesection = $this->get_setting('section',0);
                $this->sectionid = $coursesection ? $DB->get_field('course_sections', 'id', ['course' => $this->page->course->id, 'section' => $coursesection]) : 0;
            } else if (isset($this->page->cm->section)) {
                // we're in an activity that is declaring its section id so we're in luck
                $this->sectionid = $this->page->cm->section;
            } else {
                // no luck so replace the null with a 0 to avoid wasting times on trying to re-evaluate next time round
                $this->sectionid = 0;
            }
        }

        // return the stored result
        return $this->sectionid;
    }

    public function is_page_type_in($pagetypes){
        $currenttype = $this->page->pagetype;
        foreach ($pagetypes as $type){
            if ($type === $currenttype){
                return true;
            }
        }
        return false;
    }

    public function get_current_motivator(){
        // if we've already identified the current motivator then return the value that we've stored away
        if ($this->currentmotivator){
            return $this->currentmotivator;
        }

        // check to see if the motivator is supplied as a parameter (as in if the debug menu is being used)
        $motivator = $this->get_setting('motivator', null);
        $this->set_current_motivator($motivator);
        if ($this->currentmotivator){
            return $this->currentmotivator;
        }

        // try looking up the motivator name in the user's profile
        $this->currentmotivator = $this->lookup_motivator($this->read_motivator_selection());
        if ($this->currentmotivator){
            return $this->currentmotivator;
        }

        // if all else fails fall back to the first valid motivator available
        $motivators    = array_values(motivators::get_instances($this));
        $motivatoridx  = $this->user->id % count($motivators);
        $motivatorname = $motivators[$motivatoridx]->get_short_name();
        $this->set_current_motivator($motivatorname);
        $this->bomb_if(! $this->currentmotivator, 'Failed to initialise motivator');
        return $this->currentmotivator;
    }

    private function lookup_motivator($motivatortype){
        // if motivator not defined then return false
        if (!$motivatortype){
            return null;
        }

        // check that the motivator name that we have is valid
        $motivators = motivators::get_instances($this);
        return array_key_exists($motivatortype, $motivators)? $motivators[$motivatortype]: null;
    }

    public function set_current_motivator($name){
        $motivator = $this->lookup_motivator($name);
        if (!$motivator){
            return;
        }
        $this->currentmotivator = $motivator;
        $this->write_motivator_selection($name);
    }

    public function get_courses(){
        return $this->config['courses'];
    }

    public function get_global_config($motivatorname){
        // filter the course list to match the requird motivator and course
        $result=[];

        foreach ($this->config['elements'] as $item){
            // check for motivator mismatch
            if ($item['motivator']['type'] != $motivatorname){
                continue;
            }

            // check for course name missmatch
            if ($item['course'] == '*' || $item['course'] == '**'){
                continue;
            }

            // check for course name missmatch
            if ($item['course'] == '#'){
                $newitem = $item;
                foreach($this->config['courses'] as $course){
                    $newitem['course'] = $course;
                    $result[] = $newitem;
                }
                continue;
            }

            // add item to result
            $result[] = $item;
        }
        return $result;
    }

    public function get_contextual_config($motivatorname, $coursename, $sectionidx){
        // check whether this is configured in the system as a wildcard course
        $sectionkey = $coursename . '#' . $sectionidx;
        $patterns = array_key_exists($coursename, array_flip($this->config['courses'])) ? ['*', '**', $coursename, $sectionkey] : [$coursename, $sectionkey];

        // filter the course list to match the requird motivator and course
        $result=[];
        foreach ($this->config['elements'] as $idx => $item){
            // check for motivator mismatch
            if ($item['motivator']['type'] !== $motivatorname){
                continue;
            }

            // check for course name missmatch
            if (! array_key_exists($item['course'], array_flip($patterns))){
                continue;
            }

            // add item to result
            $newitem = $item;
            if ($newitem['course'][0] === '*' && array_key_exists('stats', $newitem)){
                $newitem['stats'] = [];
                foreach ($item['stats'] as $statid => &$stat){
                    $newstatid = sprintf('%03d/%s', $idx, $statid);
                    $newitem['stats'][$newstatid] = $stat;
                }
            }
            $result[] = $newitem;
        }
        return $result;
    }

    public function get_motivator_config($motivatorname){
        return isset($this->config['motivators'][$motivatorname]) ? $this->config['motivators'][$motivatorname]: [];
    }

    public function get_presets(){
        return $this->presets;
    }

    public function get_data_mine(){
        if (! $this->datamine){
            $this->datamine = new data_mine;
        }
        return $this->datamine;
    }

    public function get_global_state_data($config) {
        // lookout for overrides used for testing
        foreach (['preset', 'fullpreset'] as $overridename){
            if ($this->presets && isset($this->presets[$overridename])){
                $override = $this->get_setting($overridename, null);
                if ($override && isset($this->presets[$overridename][$override])){
                    return $this->presets[$overridename][$override];
                }
            }
        }
        // default to calculated values
        return $this->statmine->get_global_state_data($this, $config);
    }

    public function get_contextual_state_data($config, $coursename) {
        // lookout for overrides used for testing
        foreach (['preset', 'coursepreset'] as $overridename){
            if ($this->presets && isset($this->presets[$overridename])){
                $override = $this->get_setting($overridename, null);
                if ($override && isset($this->presets[$overridename][$override])){
                    // expand out any '#' identifiers in the presets
                    $result = [];
                    foreach ($this->presets[$overridename][$override] as $key => $value){
                        $resultkey = preg_replace('/^#/',$coursename,$key);
                        $result[$resultkey] = $value;
                    }
                    return $result;
                }
            }
        }
        // default to calculated values
        return $this->statmine->get_contextual_state_data($this, $config, $coursename, $this->get_section_idx());
    }

    private function read_motivator_selection(){
        global $DB;

        // fetch the identifier of the field used to store user data from the database
        $query    = '
            SELECT uif.id
            FROM {user_info_field} uif
            JOIN {user_info_category} uic ON uif.categoryid = uic.id
            WHERE uic.name="block_ludic_motivators" AND uif.shortname="motivator"
        ';
        $fieldid = $DB->get_field_sql($query);

        // sanity check
        $this->bomb_if(!$fieldid, 'Missing user info field definition');
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
            WHERE uic.name="block_ludic_motivators" AND uif.shortname="motivator"
        ';
        $fieldid = $DB->get_field_sql($query);

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

    public function page_requires_jquery_plugin($pluginname){
        $this->page->requires->jquery_plugin($pluginname);
    }

    public function page_requires_css($cssurl){
        $this->page->requires->css($cssurl);
    }

    public function set_block_classes($classes) {
        $this->blockclasses .= " $classes";
    }

    public function render($cssclass,$title,$content){
        // use a panel div to house the titel and content neetly
        $this->output .= "<div class='ludi-pane $cssclass'>";

        // write the title in a header div
        $this->output .= "<div class='ludi-header'>";
        $this->output .= "<h4 class='ludi-title'>$title</h4>";
        $this->output .= "</div>";

        // write the content in a body div
        if ($content){
            $this->output .= "<div class='ludi-body'>";
            $this->output .= "<div class='ludi-content'>$content</div>";
            $this->output .= "</div>";
        }

        $this->output .= "</div>";
    }

    public function get_rendered_output(){
        return "
            <div class='$this->blockclasses'>
            $this->output
            </div>
        ";
    }

    public function set_js_init_data($motivatorname, $data){
        $this->jsinitdata = [$motivatorname, $data];
    }

    public function get_js_init_data(){
        return $this->jsinitdata;
    }

    private function get_setting($name, $defaultvalue){
        $result = isset($this->settings->$name) ? $this->settings->$name : $defaultvalue;
        return $result;
    }
}
