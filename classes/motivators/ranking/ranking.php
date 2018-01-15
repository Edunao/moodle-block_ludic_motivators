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

require_once dirname( __DIR__ ) . '/motivator_interface.php';

class ranking extends iMotivator {

    public function __construct($context) {
        $preset = array(
            'maxScore' => 20,
            'userScore' => 0,
            'classAverage' => 11,
            'bestScore' => 20,
            'userRank' => 3,
            'numberOfCorrectAnswer' => 4,
            'numberOfQuestions' => 5,
            'otherScores' => [12, 18, 16, 15, 3, 8, 14, 18, 13, 15],
        );

        // Updating preset array when a user score is selected
        if (($userScore = optional_param('userScore', '', PARAM_TEXT)) !== '') {
            $scores = $preset['otherScores'];
            array_push($scores, $userScore);
            $preset['bestScore'] = max($scores);
            $preset['classAverage'] = number_format(array_sum($scores) / count($scores), 2);
            $preset['userScore'] = $userScore;
        }

        parent::__construct($context, $preset);
    }


    public function getTitle() {

        return 'Mon ranking';
    }

    function getUserScore() {
        return $this->preset['userScore'];
    }

    function getClassAverage() {
        return $this->preset['classAverage'];
    }

    function getBestScore() {
        return $this->preset['bestScore'];
    }

    function isFirstRank() {
        if ($this->preset['userScore'] >= $this->preset['bestScore']) {
            return true;
        }

        return false;
    }

    public function get_content() {
        global $CFG;

        $output  = '<div id="ranking-container">';

        // Div block selecting the points to win selector for the purpose of test
        $output .= '<div style="margin-bottom:15px;">
                        <form id="ranking_form" method="POST">
                            <input id="motivator" name="motivator" type="hidden" value="ranking">
                            <select name="userScore" onChange="document.getElementById(\'ranking_form\').submit()">
                                <option value="" selected>Note à obtenir</option>
                                <option value="5">5</option>
                                <option value="8">8</option>
                                <option value="10">10</option>
                                <option value="12">12</option>
                                <option value="14">14</option>
                                <option value="16">16</option>
                                <option value="18">18</option>
                                <option value="20">20</option>
                            </select>
                        </form>
                    </div>';

        // Passing to the iFrame the user's score, the class average and the best score
        $output .= '<script type="text/javascript">
                        var userScore = ' . $this->getUserScore() . ';
                        var classAverage = ' . $this->getClassAverage() . ';
                        var bestScore = ' . $this->getBestScore() . ';
                        var isFirstRank = ' . var_export($this->isFirstRank(), true) . ';
                    </script>';

        // Calling the iFrame file generating the bargraph showing the classe average,
        // the class best and the user's own level
        $output .= '<iframe id="ranking-iframe" frameBorder="0" src="' .$CFG->wwwroot. '/blocks/ludic_motivators/classes/motivators/ranking/iframe.php"></iframe>';

        $output .= '</div>';

        return $output;
    }
}
