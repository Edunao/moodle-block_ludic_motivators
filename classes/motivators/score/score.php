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
                    'nameOfBonus' => 'Répondre à 3 questions',
                    'valueOfBonus' => '70',
                    'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED,
                ],
                [
                    'nameOfBonus' => 'Terminer un quiz',
                    'valueOfBonus' => '65',
                    'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'nameOfBonus' => 'Réponse à une question en moins de 20 secondes',
                    'valueOfBonus' => '70',
                    'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'nameOfBonus' => 'Objective4',
                    'valueOfBonus' => '65',
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

		// Div block listing all goals with checkboxes next to those that have been achieved
        $output  = '<div id="score-container">
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">The latest score</h4>
                        <div class="score">';
		$output .= '		<span class="score-number">' . $this->preset['previousTotalScore'] . '</span>
							<span class="points">pts</span>';
        $output .= '    </div>
                    </div>';

        // Div block that appears when there are goals that have just been achieved listing these goals
        // Determining if there is at once one goal just achieved
        /*$isGoalsJustAchieved = false;
        foreach ($this->preset['objectives'] as $key => $objective) {
            if ($objective['achievement'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
                $isGoalsJustAchieved = true;
            }
        }

        if ($isGoalsJustAchieved === true) {
        $output .= '<div id="score-container">
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Goals just achieved</h4>
                        <div class="score">
                            <ul id="goals">';
            foreach ($this->preset['objectives'] as $key => $objective) {
                if ($objective['achievement'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
                    $titleObjective = $objective['title'];
                    $output .= "<li><label><input type='checkbox' checked disabled='disabled'>$titleObjective</label></li>";
                }
            }
        $output .= '        </ul>
                        </div>
                    </div>';
        }*/

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
