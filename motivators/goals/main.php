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

require_once \dirname2(__DIR__, 2) . '/classes/motivators/motivator.interface.php';
require_once \dirname2(__DIR__, 2) . '/classes/motivators/motivator_base.class.php';
require_once \dirname2(__DIR__, 2) . '/locallib.php';

class motivator_goals extends motivator_base implements motivator {

    public function get_loca_strings(){
        return [
            'name'          => 'Goals',
            'title'         => 'Mes objectifs',
            'full_title'    => 'All Goals',
            'changes_title' => 'Congratulations !!',
        ];
    }

    public function render($env) {
        // fetch config and associated stat data
        $coursename     = $env->get_course_name();
        $courseconfig   = $env->get_course_config($this->get_short_name(), $coursename);
        $coursedata     = $env->get_course_state_data($courseconfig, $coursename);

        // match up the config elements and state data to determine the set of information to pass to the javascript
        $havechanges = false;
        $goalhtml = '';
        foreach ($courseconfig as $element){
            $dataname = $coursename . '/' . array_keys($element['stats'])[0];
            if (array_key_exists($dataname,$coursedata)){
                $statevalue = $coursedata[$dataname];
                switch ($statevalue){
                case STATE_JUST_ACHIEVED:
                    $cssclasses     = 'ludi-done ludi-new';
                    $checked        = true;
                    $havechanges    = true;
                    break;
                case STATE_ACHIEVED:
                    $cssclasses     = 'ludi-done ludi-old';
                    $checked        = true;
                    break;
                case STATE_NOT_ACHIEVED:
                    $cssclasses     = 'ludi-todo';
                    $checked        = false;
                    break;
                }
                $title      = $element['motivator']['title'];
                $detail     = $element['motivator']['detail'];
                $bulleturl  = new \moodle_url('/blocks/ludic_motivators/motivators/goals/pix/' . ($checked===true ? 'icon_objectif_completed.svg' : 'icon_objectif_uncompleted.svg'));
                $goalhtml   .= "<div class='ludi-goal $cssclasses'>";
//                $goalhtml .= "<input type='checkbox' onclick='event.preventDefault()'" . ($checked===true ? ' checked' : '') . ">";
                $goalhtml   .= "<image src='$bulleturl' class='ludi-bullet'>";
                $goalhtml   .= "<div class='goal-title'>$title</div>";
                $goalhtml   .= "<div class='goal-detail'>$detail</div>";
                $goalhtml   .= "</div>";
            }
        }

        // prepare to start rendering content
        $env->set_block_classes('luditype-goals');

        // render the goal list
        $env->render('ludi-main', $this->get_string('full_title'), '<div class="ludi-goals">' . $goalhtml . '</div>');
        if ($havechanges === true){
            $env->render('ludi-change', $this->get_string('changes_title'), "");
        }
    }
}
