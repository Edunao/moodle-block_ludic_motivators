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

class score extends iMotivator {

    public function __construct($context) {
        $preset = array(
            'previousTotalScore' => 10,
            'newTotalScore' => 15,
            'bonuses' => [
                [
                    'nameOfBonus' => 'répondu correctement après 3 tentatives',
                    'valueOfBonus' => '3',
                    'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED,
                ],
                [
                    'nameOfBonus' => 'terminé le quiz',
                    'valueOfBonus' => '2',
                    'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'nameOfBonus' => 'répondu à une question en moins de 20 secondes',
                    'valueOfBonus' => '3',
                    'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'nameOfBonus' => 'bonus1',
                    'valueOfBonus' => '1',
                    'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
            ],
            'globalAchievements' => [
                'session1Objectives' => 0,
                'session2Objectives' => 1
            ]
        );
        parent::__construct($context, $preset);
    }

    public function getTitle() {

        return 'Mon score';
    }

    public function get_content() {
        $output = '<div id="score-container">';
        $output .= '<div class="score"/><span class="score-number">136</span><span class="points">pts</span></div>';
        $output .= '</div>';

        // Div block displaying the latest total score with an animation
        // showing in first the previous and progressively the new score
        $output  = '<div id="score-container">
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">The latest score</h4>
                        <div class="score">';
        $output .= '        <span class="score-number">' . $this->preset['previousTotalScore'] . '</span>
                            <span class="points">pts</span>';
        $output .= '    </div>
                    </div>';

        // Div block that appears only if the score progresses containing the number of
        // new points obtained from the last quiz and any bonuses obtained with the last quiz
        if ($this->preset['newTotalScore'] > $this->preset['previousTotalScore']) {
            $output .= '<div id="score-container">
                            <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">You win</h4>
                            <div>
                                <ul id="bonus">';
            $output .= '            <li><label><input type="checkbox" checked disabled="disabled"><b>' . ($this->preset['newTotalScore']-$this->preset['previousTotalScore']) . ' points</b> depuis le dernier quiz</label></li>';
            foreach ($this->preset['bonuses'] as $key => $bonus) {
                if ($bonus['stateOfBonus'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
                    $output .= '<li><label><input type="checkbox" checked disabled="disabled"><b>' . $bonus['valueOfBonus'] . ' points</b> pour avoir ' . $bonus['nameOfBonus'] . '</label></li>';
                }
            }
            $output .= '        </ul>
                            </div>
                        </div>';
        }

        return $output;
    }


    public function getJsParams() {
        //$datas = $this->context->store->get_datas();
        $params = array(
            'previous_score' => $this->preset['previousTotalScore'],
            'new_score' => $this->preset['newTotalScore'],
        );

        if (isset($datas->avatar)) {
            $params = $datas->avatar;
        }

        return $params;
    }
}
