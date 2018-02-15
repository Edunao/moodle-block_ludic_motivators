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

require_once dirname(dirname(__DIR__)) . '/classes/base_classes/motivator_base.class.php';

class motivator_timer extends motivator_base implements i_motivator {

    public function get_loca_strings(){
        return [
            'name'          => 'Timer',
            'title'         => 'Timer',
            'time_title'    => 'Current Time',
            'first_attempt' => 'Times are not shown until an exercise has been completed at least once',
            'no_course'     => 'Not in a tracked course',
            'history_title' => 'Attempt History',
            'key_time'      => 'Temps',
        ];
    }

    public function render($env) {
        // fetch config and associated stat data
        $coursename     = $env->get_course_name();
        $sectionidx     = $env->get_section_idx();
        $ctxtconfig     = $env->get_contextual_config($this->get_short_name(), $coursename, $sectionidx);
        $ctxtdata       = $env->get_contextual_state_data($ctxtconfig, $coursename, $sectionidx);

        // if the course isn't in the courses list then display a placeholder message and drop out
        if (!$ctxtdata){
            $env->render('ludi-place-holder', $this->get_string('name'), $this->get_string('no_course'));
            return;
        }

        // lookup base properties that should always always exist
        $sectionkey     = $coursename . ($sectionidx > -1 ? "#$sectionidx" : '') . '/000';

        $statname       = $sectionkey . '/time';
        $env->bomb_if(!array_key_exists($statname, $ctxtdata), "Failed to locate stat: $statname");
        $timetodate     = $ctxtdata[$statname];

        $statname       = $sectionkey . '/past_times';
        $env->bomb_if(!array_key_exists($statname, $ctxtdata), "Failed to locate stat: $statname");
        $pasttimes      = $ctxtdata[$statname];

        // prepare to start rendering content
        $env->set_block_classes('luditype-' . $this->get_short_name());

        // if we have at least one valid past time value then render it otherwise render the place-holder text
        if (!empty($pasttimes)){
            // prepare the js data
            $jsdata = [
                'time_to_date'      => $timetodate,
                'past_times'        => array_values($pasttimes),
                'key_time'          => $this->get_string('key_time'),
            ];

            // if this is a question attempt page then render the first iframe pane
            if ($env->is_page_type_in(['mod-quiz-attempt'])){
                $html = '<script>ludiTimer=' . json_encode($jsdata) . ';</script>';
                $iframeurl = new \moodle_url('/blocks/ludic_motivators/motivators/' . $this->get_short_name() . '/iframe_time.php');
                $html .= '<iframe id="' . $this->get_short_name() . '-iframe-time" frameBorder="0" src="' . $iframeurl . '"></iframe>';
                $env->render('ludi-main', $this->get_string('time_title'), $html);
            }

            // render the second iframe pane
            $html = '<script>ludiTimer=' . json_encode($jsdata) . ';</script>';
            $iframeurl = new \moodle_url('/blocks/ludic_motivators/motivators/' . $this->get_short_name() . '/iframe_history.php');
            $html .= '<iframe id="' . $this->get_short_name() . '-iframe-history" frameBorder="0" src="' . $iframeurl . '"></iframe>';
            $env->render('ludi-main', $this->get_string('history_title'), $html);
        }else{
            // render a place-holder text
            if ($env->is_page_type_in(['mod-quiz-attempt', 'mod-quiz-summary', 'mod-quiz-view'])){
                $env->render('ludi-place-holder', $this->get_string('name'), $this->get_string('first_attempt'));
            } else {
                $env->render('ludi-place-holder', $this->get_string('name'), $this->get_string('no_course'));
            }
        }
    }
}
