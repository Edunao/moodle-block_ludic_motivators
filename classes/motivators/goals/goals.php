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

class goals extends iMotivator {

	public function __construct($context) {
		$preset = array(
			'numberOfObjectives' => 5,
			'titleObjectives' => ['Objective1', 'Objective2', 'Objective3', 'Objective4', 'Objective5'],
			'percentageObjectives' => [70, 65, 70, 65, 75],
			'courseAchievements' => [
				["runOfGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED,
				 "tenOfTenGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED],
				["runOfGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED,
				 "tenOfTenGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED],
				["runOfGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED,
				 "tenOfTenGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED],
				["runOfGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED,
				 "tenOfTenGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED],
				["runOfGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED,
				 "tenOfTenGoodAnswers" => BLOCK_LUDICMOTIVATORS_ACHIEVEMENTSTATE_NOTACQUIRED]
			],
			'globalAchievements' => [
				'session1Objectives' => 0,
				'session2Objectives' => 1
			]
		);
		parent::__construct($context, $preset);
	}

	public function getTitle() {

		return 'Mes objectifs';
	}

	public function get_content() {
		$output = '<div id="goals-container">';
		$output .= '<ul id="goals">';
		$output .= '<li><label><input type="checkbox" checked>Répondre à 3 questions</label></li>';
		$output .= '<li><label><input type="checkbox" checked>Terminer un quiz</label></li>';
		$output .= '<li><label><input type="checkbox">Réponse à une question en moins de 20 secondes</label></li>';
		$output .= '</ul>';
		$output .= '<div><button id="add-goal">Ajouter un objectif</button></div>';
		$output .= '</div>';

		return $output;
	}

}
