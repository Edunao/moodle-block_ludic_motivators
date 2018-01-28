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
require_once __DIR__ . '/classes/execution_environment/execution_environment_mdl.class.php';

use \block_ludic_motivators\motivators;

class block_ludic_motivators extends block_base {

    private $motivator;
    private $testmode = true;

    public function init() {
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
        $this->page->requires->js_call_amd(
            'block_ludic_motivators/ludic_motivators',
            'init',
            array(
                $this->motivator->get_motivator_name(),
                $this->motivator->get_js_params()
            )
        );
    }

    public function get_content() {
        // setup the execution environment
        $env = $this->get_execution_environment();

        // prime a variable to hold the rendered output
        $result         = new stdClass;
        $result->footer = '';
        $result->text   = '';

        // start with debug render_debug_menus
        if ($this->testmode===true){
            $result->text .= $this->render_debug_menus($env);
        }

        // add different block content depending on whether we're half way through an activity or not
        $attempt = optional_param('attempt', 0, PARAM_INT);
        if ($attempt){
            $result->text .= $this->get_content_in_quiz($env, $attempt);
        }else{
            $result->text .= $this->get_content_default($env);
        }

        // return the result
        return $result;
    }

    private function render_debug_menus($env) {
        // lookup the current motivator
        $currentmotivator   = $env->get_current_motivator();

        // prime result accumulator
        $result = '';

        // Render motivators selector
        $result  .= '<div style="margin-bottom:15px;">';
        $result  .= '<form id="motivator_form" method="GET">';
//        $result  .= '<select name="motivator" onChange="document.getElementById(\'motivator_form\').submit()">';
        $result  .= '<select name="motivator" onChange="submit()">';
        $motivators = motivators::get_names();
        foreach ($motivators as $motivatorid => $name) {
            $selected = ( $currentmotivator == $motivatorid )? ' selected' : '';
            $result .= '<option value="' . $motivatorid . '"' . $selected . '>' . $name . '</option>';
        }
        $result   .= '</select>';
        $result   .= '</form>';
        $result   .= '</div>';

        // Load presets for the current motivator
//         $presetfile     = __DIR__ . '/motivators/' . $currentmotivator . '/testdata.json';
//         $jsonpresets    = file_get_contents( $presetfile );
//         $presets        = json_decode( $jsonpresets );
        foreach ($env->get_presets() as $presettype => $presets){
            $currentpreset  = optional_param($presettype, null, PARAM_TEXT);

            // Render preset selector
            $result  .= '<div style="margin-bottom:15px;">';
            $result  .= '<form id="motivator_form" method="GET">';
//            $result  .= '<select name="motivator" onChange="document.getElementById(\'motivator_form\').submit()">';
            $result  .= '<select name="preset" onChange="submit()">';
            foreach ($presets as $name => $presetdata) {
                $selected = ( $name === $currentpreset)? ' selected' : '';
                $result .= '<option value="' . $name . '"' . $selected . '>' . $name . '</option>';
            }
            $result   .= '</select>';
            $result   .= '</form>';
            $result   .= '</div>';
        }
        return $result;
    }

    private function get_content_default($env) {
    }

    private function get_content_in_quiz($env, $attempt) {
        global $CFG;

        // lookup the current motivator and set the block title
        $this->motivator    = $env->get_current_motivator();
        $this->title        = $this->motivator->get_string('title');

        // Ensure that required external JS plugins are loaded
        $env->page_requires_jquery_plugin('ui-css');

        // Include motivator css
        $css_url = '/blocks/ludic_motivators/motivators/' . $motivator_name . '/styles.css';
        if (file_exists($CFG->dirroot . $css_url)) {
            $env->page_requires_css($css_url);
        }

        // Add motivator HTML
        return $this->motivator->get_content();
    }

    private function get_execution_environment() {
        global $USER;
        $userid = optional_param('userid', $USER->id, PARAM_INT);
        $context = new \block_ludic_motivators\execution_environment_mdl($userid, $this->page, $this->testmode);
        return $context;
    }
}
