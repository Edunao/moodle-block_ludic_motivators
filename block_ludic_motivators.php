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

defined('MOODLE_INTERNAL') || die();

require_once __DIR__ . '/classes/motivators.class.php';
require_once __DIR__ . '/classes/execution_environment.class.php';

use \block_ludic_motivators\motivators;

class block_ludic_motivators extends block_base {
    private $testmode   = false;
    private $env        = null;

    public function init() {
        global $USER, $CFG;
        // enable or disable testing morde
        $this->testmode = (isset($CFG->ludimoodledebug) && $CFG->ludimoodledebug) ? true : false;
        if ($this->testmode){
            echo "<h1><br><br>Running in Test Mode</h1>";
        }
        // set the block title
        $this->title = get_string('pluginname', 'block_ludic_motivators');
    }

    public function has_config() {
        return false;
    }

    public function applicable_formats() {
        return array('course-view' => true, 'mod' => true, 'my' => true);
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function instance_can_be_docked() {
        // The block should only be dockable when the title of the block is not empty and when parent allows docking.
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }

    public function content_is_trusted() {
        return true;
    }

    public function get_required_javascript() {
        parent::get_required_javascript();

        // retrieve a handle to the execution environment, initialising it on the fly if required, and get hold of the js data (if there is any)
        $env = $this->get_execution_environment();
        $jsdata = $env->get_js_init_data();

        // call to global init js
        $this->page->requires->js_call_amd(
                'block_ludic_motivators/ludic_motivators',
                'init'
        );

        // if there is no js data then we're done
        if (empty($jsdata)){
            return;
        }

        // add an intialisation call for the motivator
        $this->page->requires->js_call_amd(
            'block_ludic_motivators/ludic_motivators',
            'init_motivator',
            $jsdata
        );

        // flush any environment changes to the database
        $env->get_data_mine()->flush_changes_to_database();
    }

    public function get_content() {
        global $CFG;

        // if we've already generated the content then no need to do it again
        if ($this->content){
            return $this->content;
        }

        // retrieve a handle to the execution environment, initialising it on the fly if required
        $env = $this->get_execution_environment();

        // lookup the current motivator and set the block title
        $motivator    = $env->get_current_motivator();
//        $this->title  = $motivator->get_string('title');

        // Ensure that required external JS plugins are loaded
        $env->page_requires_jquery_plugin('ui-css');

        // Include motivator css
        $css_url = '/blocks/ludic_motivators/motivators/' . $motivator->get_short_name() . '/styles.css';
        if (file_exists($CFG->dirroot . $css_url)) {
            $env->page_requires_css($css_url);
        }

        // prime a variable to hold the rendered content
        $result         = new stdClass;
        $result->footer = '';
        $result->text   = '';

        // start with debug render_debug_menus
        if ($this->testmode){
            $result->text .= $this->render_debug_menus($env);
        }

        // add different block content depending on whether we're half way through an activity or not
        $motivator->render($env);
        $result->text .= $env->get_rendered_output();

        // flush any environment changes
        $env->get_data_mine()->flush_changes_to_database();

        // cache the result for reuse as required and return it
        $this->content = $result;
        return $result;
    }

    private function get_execution_environment() {
        if (!$this->env){
            global $USER;
            $settings = [
                'userid'    => optional_param('userid', $USER->id, PARAM_INT),
                'attemptid' => optional_param('attempt', 0, PARAM_INT),
                'section'   => optional_param('section',0,PARAM_INT),
                'testmode'  => $this->testmode,
            ];
            if ($this->testmode){
                $settings += [
                    'motivator' => optional_param('motivator', null, PARAM_TEXT),
                    'preset'       => optional_param( 'preset'       , null, PARAM_TEXT),
                    'fullpreset'   => optional_param( 'fullpreset'   , null, PARAM_TEXT),
                    'coursepreset' => optional_param( 'coursepreset' , null, PARAM_TEXT),
                ];
            }
            $this->env = new \block_ludic_motivators\execution_environment($this->page, (object)$settings);
        }
        return $this->env;
    }

    private function render_debug_menus($env) {
        global $COURSE;

        // lookup the current motivator
        $currentmotivator   = $env->get_current_motivator()->get_short_name();

        // prime result accumulator
        $result = '';
        $result .= '<div class="ludi-pane">';
        $result .= '<div class="ludi-header">';
        $result .= '<h4>Debug Menus</h4>';
        $result .= '</div>';

        $result .= '<div class="ludi-body">';
        $result .= '<form id="motivator_form" method="GET">';

        // if we're in a course then add its id to the form
        if ($_SERVER['PHP_SELF'] === '/course/view.php'){
            $result .= '<input type="hidden" name="id" value="' . $COURSE->id . '">';
        }

        // Render motivators selector
        $result .= '<div class="ludi-debug-menu">';
        $result .= '<select name="motivator" onChange="submit()">';
        $motivators = motivators::get_names();
        foreach ($motivators as $motivatorid => $name) {
            $selected = ( $currentmotivator == $motivatorid )? ' selected' : '';
            $result .= '<option value="' . $motivatorid . '"' . $selected . '>' . $name . '</option>';
        }
        $result .= '</select>';
        $result .= '</div>';

        // Load presets for the current motivator
        foreach ($env->get_presets() as $presettype => $presets){
            $currentpreset  = optional_param($presettype, null, PARAM_TEXT);

            // Render preset selector
            $result .= '<div class="ludi-debug-menu">';
            $result .= '<select name="' . $presettype . '" onChange="submit()">';
            $selected = ($currentpreset === null)? ' selected' : '';
            $result .= '<option value="">-- real values --</option>';
            foreach ($presets as $name => $presetdata) {
                $selected = ( $name === $currentpreset)? ' selected' : '';
                $result .= '<option value="' . $name . '"' . $selected . '>' . $name . '</option>';
            }
            $result .= '</select>';
            $result .= '</div>';
        }

        // terminate the form
        $result .= '</form>';
        $result .= '</div>';
        $result .= '</div>';
        return $result;
    }
}
