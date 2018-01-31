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

require_once __DIR__ . '/execution_environment.interface.php';
require_once dirname(__DIR__) . '/log_miner/log_miner_mdl.class.php';

/**
*  The goal of this class is to provide issolation from the outside world.
*  It should be possible to implement the different units behind this class as stubs for testing purposes
*/
class execution_environment_mdl implements execution_environment{

    private $user;
    private $page;
    private $courseid;
    private $currentmotivator   = null;
    private $miner;
    private $output             = '';
    private $jsinitdata         = [];
    private $blockclasses       = 'ludi-block';
    private $config             = ['courses' => [], 'elements' => [], 'motivators' => []];
    private $presets            = [];

    public function __construct($userid, \moodle_page $page, $testmode) {
        global $DB, $CFG;

        $this->user         = $DB->get_record('user', array('id' => $userid));
        $this->page         = $page;
        $this->coursename   = $page->course->shortname;
        $this->miner        = new log_miner_mdl($this);

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
        $configfile     = \dirname2(__DIR__, 2) . '/motivators/' . $motivator->get_short_name() . '/config.json';
        $testdatafile   = \dirname2(__DIR__, 2) . '/motivators/' . $motivator->get_short_name() . '/testdata.json';

        // load motivator configuration data
        if (file_exists($configfile)){
            $jsonconfigdata = file_get_contents($configfile);
            $motivatorconfig = json_decode($jsonconfigdata, true);
            $this->bomb_if(!$this->config, 'Failed to JSON decode config file: ' . $configfile);
            $this->bomb_if(!isset($this->config['courses']), '"courses" node not found in config file: ' . $configfile);
            $this->bomb_if(!isset($this->config['elements']), '"elements" node not found in config file: ' . $configfile);
            $this->config = array_merge_recursive($this->config, $motivatorconfig);
        }

        // load testing data as required
        if ($testmode === true){
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

    public function get_course_name() {
        return $this->coursename;
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
        $this->set_current_motivator(array_values(motivators::get_instances($this))[0]->get_short_name());
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
        $this->motivator = $motivator;
        $this->write_motivator_selection($name);
    }

    public function get_courses(){
        return $this->config['courses'];
    }

    public function get_full_config($motivatorname){
        // filter the course list to match the requird motivator and course
        $result=[];

        foreach ($this->config['elements'] as $item){
            // check for motivator mismatch
            if ($item['motivator']['type'] != $motivatorname){
                continue;
            }

            // check for course name missmatch
            if ($item['course'] == '*'){
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

    public function get_course_config($motivatorname, $coursename){
        // check whether this is configured in the system as a wildcard course
        $wildcard = array_key_exists($coursename, array_flip($this->config['courses'])) ? '*' : null;

        // filter the course list to match the requird motivator and course
        $result=[];
        foreach ($this->config['elements'] as $item){
            // check for motivator mismatch
            if ($item['motivator']['type'] !== $motivatorname){
                continue;
            }

            // check for course name missmatch
            if ($item['course'] !== $coursename && $item['course'] !== $wildcard){
                continue;
            }

            // check for course name missmatch
            if ($item['course'] == '#'){
                continue;
            }

            // add item to result
            $result[] = $item;
        }
        return $result;
    }

    public function get_motivator_config($motivatorname){
        return isset($this->config['motivators'][$motivatorname]) ? $this->config['motivators'][$motivatorname]: [];
    }

    public function get_presets(){
        return $this->presets;
    }

    public function get_full_state_data($config) {
        // lookout for overrides used for testing
        foreach (['preset', 'fullpreset'] as $overridename){
            if ($this->presets && isset($this->presets[$overridename])){
                $override = optional_param($overridename, null, PARAM_TEXT);
                if ($override && isset($this->presets[$overridename][$override])){
                    return $this->presets[$overridename][$override];
                }
            }
        }
        // default to calculated values
        return $this->miner->get_full_state_data($config);
    }

    public function get_course_state_data($config, $coursename) {
        // lookout for overrides used for testing
        foreach (['preset', 'coursepreset'] as $overridename){
            if ($this->presets && isset($this->presets[$overridename])){
                $override = optional_param($overridename, null, PARAM_TEXT);
                if ($override && isset($this->presets[$overridename][$override])){
                    // expand out any '#' identifiers in the presets
                    $result = [];
                    foreach ($this->presets[$overridename][$override] as $key => $value){
                        $resultkey = preg_replace('/^#/',$coursename,$key);
                        $result[$resultkey] = $value;
                    }
//print_object($result);
                    return $result;
                }
            }
        }
        // default to calculated values
        return $this->miner->get_course_state_data($config, $coursename);
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
        # use a panel div to house the titel and content neetly
        $this->output .= "<div class='ludi-pane $cssclass'>";

        # write the title in a header div
        $this->output .= "<div class='ludi-header'>";
        $this->output .= "<h4 class='ludi-title'>$title</h4>";
        $this->output .= "</div>";

        # write the content in a body div
        $this->output .= "<div class='ludi-body'>";
        $this->output .= "<div class='ludi-content'>$content</div>";
        $this->output .= "</div>";

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
}
