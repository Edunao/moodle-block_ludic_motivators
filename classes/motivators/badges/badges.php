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

class badges extends iMotivator {

	public function __construct($context) {
		$preset = array(
			'typeBadge' => 'session',
			'session' => [
				'badgeOnSuccessiveAttempt' => 'badge1',
				'nbquestionOnSuccessiveAttempt' => 5,
				'badgeAtFirtAttempt' => 'badge2',
				'badgeAtNAttempt' => 3,
				'numberattemptAtNAttempt' => 1,
				'badgeOnSelfCorrection' => 'badge3',
				'numberattemptOnSelfCorrection' => 1,
				'courseAchievements' => [
					"runOfFiveGoodAnswers" => 0,
					"tenOfTenGoodAnswers" => 1
				],
				'globalAchievements' => [
					'session1Objectives' => 0,
					'session2Objectives' => 1
				]
			],
			'theme' => [
				'badgeOnSuccessiveAttempt' => '0',
				'nbquestionOnSuccessiveAttempt' => '1',
				'badgeAtFirtAttempt' => '2',
				'badgeAtNAttempt' => '0',
				'numberattemptAtNAttempt' => '1',
				'badgeOnSelfCorrection' => '2',
				'numberattemptOnSelfCorrection' => '0',
				'courseAchievements' => [
					"runOfFiveGoodAnswers" => 0,
					"tenOfTenGoodAnswers" => 1
				],
				'globalAchievements' => [
					'session1Objectives' => 0,
					'session2Objectives' => 1
				]
			]
		);
		parent::__construct($context, $preset);
	}

	public function getTitle() {

		return 'Mes badges';
	}

	public function get_content() {
		global $CFG;

		$output = '<div id="badges-container">';
		$output .= '<div class="ludic_motivators-badge"><img src="'.$CFG->wwwroot.'/blocks/ludic_motivators/classes/motivators/badges/pix/badge1.png" title="3 bonnes réponses"/></div>';
		$output .= '<div class="ludic_motivators-badge"><img src="'.$CFG->wwwroot.'/blocks/ludic_motivators/classes/motivators/badges/pix/badge2.png" title="10 bonnes réponses" style="display:none;"/></div>';
		$output .= '</div>';

		return $output;
	}

}
