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

    function getBonusList() {
        $resultHtml = '';
        foreach ($this->preset['bonuses'] as $key => $bonus) {
            if ($bonus['stateOfBonus'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
                $resultHtml .= '<li><label><b>' . $bonus['valueOfBonus'] . ' points</b></label></li>';
            }
        }

        return $resultHtml;
    }

    public function get_content() {

        // Div block displaying the latest total score with an animation
        // showing in first the previous and progressively the new score
        $output   = '<div id="score-container" style="margin-bottom:15px;border:1px solid">
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Score</h4>
                        <div class="score">
                            <span class="score-number">' . $this->preset['newTotalScore'] . '</span>
                            <span class="points">pts</span>
                        </div>
                    </div>';

        // Div block that appears only if the score progresses containing the number of
        // new points obtained from the last quiz
        if ($this->preset['newTotalScore'] > $this->preset['previousTotalScore']) {
            $output .= '<div id="score-container" style="margin-bottom:15px;border:1px solid">
                            <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bravo !</h4>
                            <div>
                                <ul id="bonus">
                                    <li>
                                        <label><b>'
                                            . ($this->preset['newTotalScore']-$this->preset['previousTotalScore']) . ' points en plus' .
                                        '</b></label>
                                    </li>
                                </ul>
                            </div>
                        </div>';
        }

        // Div block that appears only on any bonuses obtained with the last quiz
        if ($this->preset['newTotalScore'] > $this->preset['previousTotalScore']) {
            $output .= '<div id="score-container" style="border:1px solid">
                            <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bonus</h4>
                            <div>
                                <ul id="bonus">'
                                    . $this->getBonusList() .
                                '</ul>
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
