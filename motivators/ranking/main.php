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

class motivator_ranking extends motivator_base implements motivator {

    public function get_loca_strings(){
        return [
            'name'      => 'Ranking',
            'title'     => 'My Ranking',
            'no_rank'   => 'No exercises completed yet',
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
echo "<div style='height:60px'/>";
print_object($this->get_short_name());
print_object($coursename);
print_object($courseconfig);
print_object($coursedata);
        $score          = self::lookup_stat($env, $coursedata, $coursename, 'current_score');
        $classbest      = self::lookup_stat($env, $coursedata, $coursename, 'class_best_score');
        $classaverage   = self::lookup_stat($env, $coursedata, $coursename, 'class_average_score');
        $rank           = self::lookup_stat($env, $coursedata, $coursename, 'class_rank');
        $oldrank        = self::lookup_stat($env, $coursedata, $coursename, 'previous_rank');
        $ranksize       = self::lookup_stat($env, $coursedata, $coursename, 'rank_size');

        // prepare to start rendering content
        $env->set_block_classes('luditype-ranking');

        // if we have at least one valid course score then render our ranking, otherwise render the place-holder text
        if ($score){
            // prepare the js data
            $jsdata = [
                'userScore'      => $score,
                'classAverage'   => $classaverage,
                'bestScore'      => $classbest,
                'isFirstRank'    => ($score >= $classbest)
            ];
            $html = '<script>ludiRanking=' . json_encode($jsdata) . ';</script>';

            // render the iframe pane
            $iframeurl = new \moodle_url('/blocks/ludic_motivators/motivators/' . $this->get_short_name() . '/iframe_main.php');
            $html .= '<iframe id="' . $this->get_short_name() . '-iframe" frameBorder="0" src="' . $iframeurl . '"></iframe>';
            $env->render('ludi-main', $this->get_string('title'), $html);
        }else{
            // render a place-holder text
            $env->render('ludi-place-holder', $this->get_string('title'), $this->get_string('no_rank'));
        }
    }

    private static function lookup_stat($env, $statsdata, $coursename, $statname){
        $statid = $coursename . '/' . $statname;
        $env->bomb_if(!array_key_exists($statid, $statsdata), "Failed to locate stat: $statid");
        return $statsdata[$statid];
    }

//     public function __construct($context) {
//         $preset = array(
//             'maxScore' => 20,
//             'userScore' => 0,
//             'classAverage' => 11,
//             'bestScore' => 20,
//             'userRank' => 3,
//             'numberOfCorrectAnswer' => 4,
//             'numberOfQuestions' => 5,
//             'otherScores' => [12, 18, 16, 15, 3, 8, 14, 18, 13, 15],
//         );
//
//         // Updating preset array when a user score is selected
//         if (($userScore = optional_param('userScore', '', PARAM_TEXT)) !== '') {
//             $scores = $preset['otherScores'];
//             array_push($scores, $userScore);
//             $preset['bestScore'] = max($scores);
//             $preset['classAverage'] = number_format(array_sum($scores) / count($scores), 2);
//             $preset['userScore'] = $userScore;
//         }
//
//         parent::__construct($context, $preset);
//     }
//
//
//     public function getTitle() {
//
//         return 'Mon ranking';
//     }
//
//     function getUserScore() {
//         return $this->preset['userScore'];
//     }
//
//     function getClassAverage() {
//         return $this->preset['classAverage'];
//     }
//
//     function getBestScore() {
//         return $this->preset['bestScore'];
//     }
//
//     function isFirstRank() {
//         if ($this->preset['userScore'] >= $this->preset['bestScore']) {
//             return true;
//         }
//
//         return false;
//     }
//
//     public function get_content() {
//         global $CFG;
//
//         $output  = '<div id="ranking-container">';
//
//         // Div block selecting the points to win selector for the purpose of test
//         $output .= '<div style="margin-bottom:15px;">
//                         <form id="ranking_form" method="POST">
//                             <input id="motivator" name="motivator" type="hidden" value="ranking">
//                             <select name="userScore" onChange="document.getElementById(\'ranking_form\').submit()">
//                                 <option value="" selected>Note à obtenir</option>
//                                 <option value="5">5</option>
//                                 <option value="8">8</option>
//                                 <option value="10">10</option>
//                                 <option value="12">12</option>
//                                 <option value="14">14</option>
//                                 <option value="16">16</option>
//                                 <option value="18">18</option>
//                                 <option value="20">20</option>
//                             </select>
//                         </form>
//                     </div>';
//
//         // Passing to the iFrame the user's score, the class average and the best score
//         $output .= '<script type="text/javascript">
//                         var userScore = ' . $this->getUserScore() . ';
//                         var classAverage = ' . $this->getClassAverage() . ';
//                         var bestScore = ' . $this->getBestScore() . ';
//                         var isFirstRank = ' . var_export($this->isFirstRank(), true) . ';
//                     </script>';
//
//         // Calling the iFrame file generating the bargraph showing the classe average,
//         // the class best and the user's own level
//         $output .= '<iframe id="ranking-iframe" frameBorder="0" src="' .$CFG->wwwroot. '/blocks/ludic_motivators/classes/motivators/ranking/iframe.php"></iframe>';
//
//         $output .= '</div>';
//
//         return $output;
//     }
}
