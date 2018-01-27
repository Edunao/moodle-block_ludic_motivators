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

require_once __DIR__ . '/classes/motivators.class.php';
require_once __DIR__ . '/classes/execution_environment/execution_environment_mdl.class.php';

use \block_ludic_motivators\motivators;

class block_ludic_motivators extends block_base {

    private $motivator;

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
        // prime a variable to hold the rendered output
        $result='';

        // start with debug render_debug_menus
        $result .= $this->render_debug_menus();

        // add different block content depending on whether we're half way through an activity or not
        $attempt = optional_param('attempt', 0, PARAM_INT);
        if ($attempt){
            $result .= $this->get_content_in_quiz($attempt);
        }else{
            $result .= $this->get_content_default();
        }

        // return the result
        return $result;
    }

    private function render_debug_menus() {
        // lookup the current motivator
        $env = $this->get_execution_environment();
        $currentmotivator  = $env->get_current_motivator();

        // Motivators selector
        $result->text  .= '<div style="margin-bottom:15px;">';
        $result->text  .= '<form id="motivator_form" method="POST">';
        $result->text  .= '<select name="motivator" onChange="document.getElementById(\'motivator_form\').submit()">';
        $motivators     = motivators::get_motivator_names();
        foreach ($motivators as $motivatorid => $name) {
            $selected = ( $currentmotivator == $motivatorid )? 'selected' : '';
            $result->text .= '<option value="' . $motivatorid . '" ' . $selected . '>' . $motivatorvalue . '</option>';
        }
        $result->text   .= '</select>';
        $result->text   .= '</form>';
        $result->text   .= '</div>';

    }

    private function get_content_default($attempt) {
    }

    private function get_content_in_quiz($attempt) {
        global $CFG;

        // lookup the current motivator
        $env = $this->get_execution_environment();
        $currentmotivator  = $env->get_current_motivator();

        $this->motivator= $this->env->get_current_motivator();
        $this->title    = $this->motivator->getTitle();
        $result         = new stdClass;
        $result->footer = '';
        $result->text   = '';

        //
//        $this->page->requires->jquery_plugin('ui-css');
        $env->page_requires_jquery_plugin('ui-css');

        // Require motivator css
        $css_url = '/blocks/ludic_motivators/classes/motivators/css/' . $motivator_name . '.css';
        if (file_exists($CFG->dirroot . $css_url)) {
//            $this->page->requires->css($css_url);
            $env->page_requires_css($css_url);
        }

        // Add motivator HTML
        $result->text .= $this->motivator->get_content();

        return $result;
    }

    private function get_execution_environment() {
        global $USER;
        $userid = optional_param('userid', $USER->id, PARAM_INT);
        $context = new \block_ludic_motivators\execution_environment_mdl($userid, $this->page);
        return $context;
    }
}
