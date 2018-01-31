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

require_once dirname(dirname(__DIR__)) . '/classes/motivators/motivator.interface.php';
require_once dirname(dirname(__DIR__)) . '/classes/motivators/motivator_base.class.php';
require_once dirname(dirname(__DIR__)) . '/locallib.php';

class motivator_score extends motivator_base implements motivator {

    public function get_loca_strings(){
        return [
            'name'  => 'Score',
            'title' => 'Score',
            'full_title'    => 'Score',
            'changes_title' => 'New Points',
            'bonus_title' => 'Bonus !!',
            'no_course' => 'Not in a tracked course',
        ];
    }

    public function render($env) {
        // fetch config and associated stat data
        $coursename     = $env->get_course_name();
        $courseconfig   = $env->get_course_config($this->get_short_name(), $coursename);
        $coursedata     = $env->get_course_state_data($courseconfig, $coursename);

        // if the course isn't in the courses list then display a placeholder message and drop out
        if (!$coursedata){
            $env->render('ludi-place-holder', $this->get_string('title'), $this->get_string('no_course'));
            return;
        }

        // lookup base properties that should always always exist
        $statnamescore      = $coursename . '/score';
        $statnamenewscore   = $coursename . '/new_score';
        foreach ([$statnamescore, $statnamenewscore] as $dataname){
            $env->bomb_if(!array_key_exists($dataname, $coursedata), "Failed to locate stat: $dataname");
        }
        $score      = $coursedata[$statnamescore];
        $newscore   = $coursedata[$statnamenewscore];

        // match up the config elements and state data to determine the set of information to pass to the javascript
        $totalbonus = 0;
        $newbonus   = 0;
        foreach ($courseconfig as $element){
            $elementtype = $element['motivator']['subtype'];
            if($elementtype != 'bonus'){
                continue;
            }
            $dataname = $coursename . '/' . array_keys($element['stats'])[0];
            if (!array_key_exists($dataname,$coursedata)){
                continue;
            }
            $bonusvalue = $element['motivator']['bonus'];
            $statevalue = $coursedata[$dataname];
            switch ($statevalue){
            case STATE_JUST_ACHIEVED:
                $newbonus   += $bonusvalue;
            case STATE_ACHIEVED:
                $totalbonus += $bonusvalue;
                break;
            }
        }

        // prepare to start rendering content
        $env->set_block_classes('luditype-score');

        // render the score pane
        $scorehtml = '';
        $scorehtml .= '<div class="ludi-score-pane">';
        $scorehtml .= '<span class="ludi-score-total">' . ( $score + $totalbonus ) . '</span>';
        $scorehtml .= '</div>';
        $env->render('ludi-main', $this->get_string('full_title'), $scorehtml);

        // render the new points pane
        if ($newscore){
            $newscorehtml = '';
            $newscorehtml .= '<div class="ludi-score-pane">';
            $newscorehtml .= '<span class="ludi-score-new">+ ' . $newscore . '</span>';
            // add bonus points as required
            if ($newbonus){
                $newscorehtml .= '&nbsp;&nbsp;';
                $newscorehtml .= '<span class="ludi-score-bonus">+' . $newbonus . '</span>';
            }
            $newscorehtml .= '</div>';
            $env->render('ludi-change', $this->get_string('changes_title'), $newscorehtml);
            if ($newbonus){
                $env->render('ludi-change ludi-bonus', $this->get_string('bonus_title'), '');
            }
        }

    }
}
