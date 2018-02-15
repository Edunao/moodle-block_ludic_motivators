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

class motivator_score extends motivator_base implements i_motivator {

    public function get_loca_strings(){
        return [
            'name'          => 'Score',
            'title'         => 'Score',
            'full_title'    => 'Score',
            'changes_title' => 'Points Gagnés',
            'no_course'     => 'Not available',
            'not_scored'    => 'Not available'
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
            $env->render('ludi-place-holder', $this->get_string('title'), $this->get_string('no_course'));
            return;
        }

        // lookup base properties that should always always exist
        $sectionkey         = $coursename . ($sectionidx > -1 ? "#$sectionidx": '');
        $statnamescore      = $sectionkey . '/000/score';
        $statnamenewscore   = $sectionkey . '/000/new_score';
        $statnamenewscored  = $sectionkey . '/000/scored';
        foreach ([$statnamescore, $statnamenewscore] as $dataname){
            $env->bomb_if(!array_key_exists($dataname, $ctxtdata), "Failed to locate stat: $dataname");
        }
        $score      = $ctxtdata[$statnamescore];
        $newscore   = $ctxtdata[$statnamenewscore];
        $scored     = $ctxtdata[$statnamenewscored];

        // if this is not a scored section then don't display the score
        if (! $scored){
            $env->render('ludi-place-holder', $this->get_string('title'), $this->get_string('not_scored'));
            return;
        }

        // match up the config elements and state data to determine the set of information to pass to the javascript
        $totalbonus = 0;
        $newbonus   = 0;
        $bonuses    = [];
        foreach ($ctxtconfig as $element){
            $elementtype = $element['motivator']['subtype'];
            if($elementtype != 'bonus'){
                continue;
            }
            $dataname = $sectionkey . '/' . (array_keys($element['stats'])[0]);
            if (!array_key_exists($dataname,$ctxtdata)){
                continue;
            }
            $bonusvalue = $element['motivator']['bonus'];
            $statevalue = $ctxtdata[$dataname];
            switch ($statevalue){
            case STATE_JUST_ACHIEVED:
                $newbonus   += $bonusvalue;
                $bonustitle = $element['motivator']['title'];
                $bonuses[$bonustitle] = $bonusvalue;
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
            $newscorehtml .= '<span class="ludi-score-new">+ ' . $newscore . '</span>';
            $env->render('ludi-change', $this->get_string('changes_title'), $newscorehtml);
            foreach ($bonuses as $title =>$value){
                $bonushtml = '<span class="ludi-score-bonus">+ ' . $value . '</span>';
                $env->render('ludi-change ludi-bonus', $title, $bonushtml);
            }
        }

    }
}
