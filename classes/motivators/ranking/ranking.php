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
            'userScore' => 16,
            'classAverage' => 15,
            'bestScore' => 20,
            'userRank' => 3,
            'numberOfCorrectAnswer' => 4,
            'numberOfQuestions' => 5,
            'courseAchievements' => [
                "runOfFiveGoodAnswers" => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                "tenOfTenGoodAnswers" => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED
            ],
            'globalAchievements' => [
                'session1Objectives' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                'session2Objectives' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED
            ]
        );
        parent::__construct($context, $preset);
    }

    public function getTitle() {

        return 'Mon ranking';
    }

    public function get_content() {
        global $CFG;

        //$output = '<div id="ranking-container">';
        //$output .= '<div class="ranking"/><span class="ranking-number">136</span><span class="points">pts</span></div>';
        //$output .= '</div>';
        //print_r($this->preset['userScore']);
        $output = '<div id="ranking-container">';
        $output .= '<script>var userScore = ' . $this->preset['userScore'] . '</script>';
        $output .= '<script>var classAverage = ' . $this->preset['classAverage'] . '</script>';
        $output .= '<script>var bestScore = ' . $this->preset['bestScore'] . '</script>';
        $output .= '<iframe id="ranking-iframe" frameBorder="0" src="'.$CFG->wwwroot.'/blocks/ludic_motivators/classes/motivators/ranking/iframe.php"></iframe>';
        $output .= '</div>';

        return $output;
    }
}
