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

class motivator_ranking extends motivator_base implements i_motivator {

    public function get_loca_strings(){
        return [
            'name'      => 'Ranking',
            'title'     => 'My Ranking',
            'no_rank'   => 'Not available',
            'no_course' => 'Not in a tracked course',
            'bravo'     => 'Bravo!',
            'key_ave'   => 'Average Score',
            'key_best'  => 'Best Score',
            'key_my'    => 'My Score',
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
        $score          = self::lookup_stat($env, $ctxtdata, $coursename, $sectionidx, 'current_score');
        $classbest      = self::lookup_stat($env, $ctxtdata, $coursename, $sectionidx, 'class_best_score');
        $classaverage   = self::lookup_stat($env, $ctxtdata, $coursename, $sectionidx, 'class_average_score');
        $rank           = self::lookup_stat($env, $ctxtdata, $coursename, $sectionidx, 'class_rank');
        $oldrank        = self::lookup_stat($env, $ctxtdata, $coursename, $sectionidx, 'previous_rank');
        $ranksize       = self::lookup_stat($env, $ctxtdata, $coursename, $sectionidx, 'rank_size');

        // prepare to start rendering content
        $env->set_block_classes('luditype-ranking');

        // if we have at least one valid course score then render our ranking, otherwise render the place-holder text
        if ($score || $classbest){
            // prepare the js data
            $jsdata = [
                'userScore'     => $score,
                'classAverage'  => $classaverage,
                'bestScore'     => $classbest,
                'isFirstRank'   => ($score >= $classbest),
                // keys for the columns in the chart
                'keyAveScore'   => $this->get_string('key_ave'),
                'keyBestScore'  => $this->get_string('key_best'),
                'keyMyScore'    => $this->get_string('key_my')

            ];
            $html = '<script>ludiRanking=' . json_encode($jsdata) . ';</script>';

            // render the iframe pane
            $iframeurl = new \moodle_url('/blocks/ludic_motivators/motivators/' . $this->get_short_name() . '/iframe_main.php');
            $html .= '<iframe id="' . $this->get_short_name() . '-iframe" frameBorder="0" src="' . $iframeurl . '"></iframe>';
            $env->render('ludi-main', $this->get_string('title'), $html);

            // consider rendering a Bravo text
            if($score >= $classbest && $oldrank > $rank){
                $env->render('ludi-change', $this->get_string('bravo'), '');
            }
        }else{
            // render a place-holder text
            $env->render('ludi-place-holder', $this->get_string('title'), $this->get_string('no_rank'));
        }
    }

    private static function lookup_stat($env, $statsdata, $coursename, $sectionidx, $statname){
        $statid = $coursename . ($sectionidx > -1 ? "#$sectionidx" : '') . '/000/' . $statname;
        $env->bomb_if(!array_key_exists($statid, $statsdata), "Failed to locate stat: $statid");
        return $statsdata[$statid];
    }
}
