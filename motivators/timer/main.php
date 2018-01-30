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

require_once dirname(__DIR__, 2) . '/classes/motivators/motivator.interface.php';
require_once dirname(__DIR__, 2) . '/classes/motivators/motivator_base.class.php';
require_once dirname(__DIR__, 2) . '/locallib.php';

class motivator_timer extends motivator_base implements motivator {

    public function get_loca_strings(){
        return [
            'name'          => 'Timer',
            'title'         => 'Best Times',
            'first_attempt' => 'This is your first attempt at this exercise. You will be able to retry the exercise again later to try to improve your best time',
            'history_title' => 'Attempt history',
        ];
    }

    public function render($env) {
        // fetch config and associated stat data
        $coursename     = $env->get_course_name();
        $courseconfig   = $env->get_course_config($this->get_short_name(), $coursename);
        $coursedata     = $env->get_course_state_data($courseconfig, $coursename);

        // lookup base properties that should always always exist
        $statnames      = $coursename . '/time';
        $env->bomb_if(!array_key_exists($statnames, $coursedata), "Failed to locate stat: $statnames");
        $timetodate     = $coursedata[$statnames];

        // match up the config elements and state data to determine the set of information to pass to the javascript
        $pasttimes = [];
        foreach ($courseconfig as $element){
            $elementtype = $element['motivator']['subtype'];
            if($elementtype != 'past_time'){
                continue;
            }
            $index = $element['motivator']['index'];
            $statname = $coursename . '/' . array_keys($element['stats'])[0];
            $pasttimes[$index] = array_key_exists($statname, $coursedata)? $coursedata[$statname]: 0;
        }
        $env->bomb_if(empty($pasttimes), "Failed to locate any past_times stats");

        // prepare to start rendering content
        $env->set_block_classes('luditype-timer');

        // if we have at least one valid past time value then rendder it otherwise render the place-holder text
        if ($pasttimes[0]){
            // prepare the js data
            $jsdata = [
                'time_to_date'      => $timetodate,
                'past_times'        => $pasttimes,
                'past_times_key'    => $this->get_string('history_title')
            ];
            $timerhtml = '<script>ludiTimer=' . json_encode($jsdata) . ';</script>';

            // render the timer pane
            $iframeurl = new \moodle_url('/blocks/ludic_motivators/motivators/timer/iframe_main.php');
            $timerhtml .= '<iframe id="timer-iframe" frameBorder="0" src="'.$iframeurl.'"></iframe>';
            $env->render('ludi-main', $this->get_string('title'), $timerhtml);
        }else{
            // render a place-holder text
            $env->render('ludi-place-holder', $this->get_string('title'), $this->get_string('first_attempt'));
        }
    }
}
