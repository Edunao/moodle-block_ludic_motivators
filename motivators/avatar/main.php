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

require_once dirname(dirname(__DIR__)) . '/classes/motivators/motivator_base.class.php';

class motivator_avatar extends motivator_base implements motivator {

    public function get_loca_strings(){
        return [
            'name'          => 'Avatar',
            'title'         => 'Représentation de soi',
            'full_title'    => 'Moi même',
            'changes_title' => 'Du nouveau !',
        ];
    }

    public function render($env) {
        // prime a jsdata object with the different tables that we're going to provide to the JS script
        $jsdata = [
            'obtained'       => [],
            'newly_obtained' => [],
            'new_names'      => []
        ];

        // fetch config and associated stat data
        $config     = $env->get_full_config($this->get_short_name());
        $statedata  = $env->get_full_state_data($config);

        // match up the config elements and state data to determine the set of information to pass to the javascript
        foreach ($config as $element){
            $dataname = $element['course'] . '/' . array_keys($element['stats'])[0];
            if (isset($statedata[$dataname])){
                $statevalue = $statedata[$dataname];
                switch ($statevalue){
                case STATE_JUST_ACHIEVED:
                    $jsdata['newly_obtained'][] = $element['motivator']['layer'];
                    // drop through ... don't break here!
                case STATE_ACHIEVED:
                    $jsdata['obtained'][] = $element['motivator']['layer'];
                    break;
                }
            }
        }

        // register the js data
        $env->set_js_init_data($this->get_short_name(), $jsdata);

        // Construct content blocks for rendering
        $imageurl       = $this->image_url('avatar.svg');
        $fullimage      = "<img src='$imageurl' class='avatar svg' id='ludi-avatar-full'/>";
        $changesimage   = "<img src='$imageurl' class='avatar svg' id='ludi-avatar-changes'/>";

        // render the output
        $env->set_block_classes('luditype-avatar');
        $env->render('ludi-main', $this->get_string('full_title'), $fullimage);
        if (!empty($jsdata['newly_obtained'])){
            $env->render('ludi-change', $this->get_string('changes_title'), $changesimage);
        }
    }
}
