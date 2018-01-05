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

class progress extends iMotivator {

	public function __construct($context) {
		$preset = array(
			'introductionMessage' => 'Bravo, tu as réussi, maintenant avec un chrono, essaie de faire de ton mieux',
			'maxDurationTimer' => 90,
			'numberAttempts' => 3,
			'attemptsTiming' => [40, 60, 30],
			'courseAchievements' => [
				"runOfFiveGoodAnswers" => 0,
				"tenOfTenGoodAnswers" => 1
			],
			'globalAchievements' => [
				'session1Objectives' => 0,
				'session2Objectives' => 1
			]
		);
		parent::__construct($context, $preset);
	}

	public function getTitle() {

		return 'My progress';
	}

	public function get_content() {
		$output = '<div id="progress-container">';
		$output .= '<div class="progress"/><span class="progress-number">136</span><span class="points">pts</span></div>';
		$output .= '</div>';

		return $output;
	}
}
