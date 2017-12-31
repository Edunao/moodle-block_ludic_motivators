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

require_once $CFG->dirroot . '/blocks/ludic_motivators/classes/context.php';

class block_ludic_motivators extends block_base {

    var $plugin;

    function init() {
        $this->title = get_string('pluginname', 'block_ludic_motivators');
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array('course-view' => true, 'mod' => 'true');
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $CFG;

        $plugin_name = optional_param('plugin', 'puzzle', PARAM_RAW);

        require_once $CFG->dirroot . '/blocks/ludic_motivators/classes/plugins/' . $plugin_name . '/' . $plugin_name . '.php';

        $class_name = '\\block_ludic_motivators\\' . $plugin_name;

        $context = $this->get_context();

        $this->plugin = new $class_name($context);

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->title = $this->plugin->getTitle();
        $this->content         = new stdClass;
        $this->content->footer = '';

        //plugin selector
        $plugins = $this->get_plugins();

        $this->content->text = '<div>';
        $this->content->text .= '<form method="POST">';
        $this->content->text .= '<select name="plugin">';
        foreach ($plugins as $pluginid => $pluginvalue) {
            $selected = $plugin_name == $pluginid ? 'selected' : '';
            $this->content->text .= '<option value="' . $pluginid . '" ' . $selected . '>' . $pluginvalue . '</option>';
        }
        $this->content->text .= '</select>';
        $this->content->text .= '<input type="submit" value="Changer de plugin">';
        $this->content->text .= '</form>';
        $this->content->text .= '</div>';


        $this->page->requires->jquery_plugin('ui-css');

        //require plugin css
        $css_url = '/blocks/ludic_motivators/classes/plugins/' . $plugin_name . '/styles.css';
        if (file_exists($CFG->dirroot . $css_url)) {
            $this->page->requires->css($css_url);
        }

        //add plugin HTML
        $this->content->text .= $this->plugin->get_content();

        return $this->content;
    }

    function get_required_javascript() {
        parent::get_required_javascript();
        $this->page->requires->js_call_amd('block_ludic_motivators/ludic_motivators', 'init', array($this->plugin->getPluginName(), $this->plugin->getJsParams()));
    }

    public function instance_can_be_docked() {
        // The block should only be dockable when the title of the block is not empty and when parent allows docking.
        return (!empty($this->config->title) && parent::instance_can_be_docked());
    }

    function content_is_trusted() {
        return true;
    }

    function get_context() {
        global $USER;
        $userid = optional_param('userid', $USER->id, PARAM_INT);

        $context = new \block_ludic_motivators\context($userid, $this->page);

        return $context;
    }

    function get_plugins() {
        global $CFG;
        $plugin_path = $CFG->dirroot . '/blocks/ludic_motivators/classes/plugins';

        $plugins = array();

        $dir = new DirectoryIterator($plugin_path);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                $plugin_name  = $fileinfo->getFilename();
                $langfile_url = $plugin_path . '/' . $plugin_name . '/lang/en/' . $plugin_name . '.php';
                if (!file_exists($langfile_url)) {
                    print_error('Lang file does not exist : ' . $langfile_url);
                }
                require_once $langfile_url;
                $str_name              = $string['pluginname'];
                $plugins[$plugin_name] = $str_name;
            }
        }

        return $plugins;
    }

}
