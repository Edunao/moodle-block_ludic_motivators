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

class avatar extends iMotivator {

	public function __construct($preset) {
		$preset = array(
			'typeBadge' => 'session',
			'session' => [
				['badgeOnSuccessiveAttempt' => '0'],
				['nbquestionOnSuccessiveAttempt' => '1'],
				['badgeAtFirtAttempt' => '2'],
				['badgeAtNAttempt' => '0'],
				['numberattemptAtNAttempt' => '1'],
				['badgeOnSelfCorrection' => '2'],
				['numberattemptOnSelfCorrection' => '0'],
				['courseAchievements' => [
						"runOfFiveGoodAnswers" => 0,
						"tenOfTenGoodAnswers" => 1
					]
				],
				['globalAchievements' => [
						'session1Objectives' => 0,
						'session2Objectives' => 1
					]
				]
			]
		);
		parent::__construct($preset);
	}

	public function getTitle() {

		return 'Découverte';
	}

	public function get_content() {
		$output = '<div id="avatar-container">';
		$output .= '<img src="'.$this->image_url('fractal.jpg').'"width="180px" height="180px" id="avatar-picture"/>';
		$output .= '<img src="'.$this->image_url('puzzle.svg').'" width="180px" height="180px" class="avatar svg"/>';
		$output .= '</div>';
		$output .= '<div><button id="next-piece">Répondre à une question</button></div>';
		$output .= '<div id="congratulation">Congratulation!</div>';

		return $output;
	}

	public function getJsParams() {
		$datas = $this->context->store->get_datas();
		$params = array('revealed_pieces' => array());
		if (isset($datas->avatar)) {
			$params = $datas->avatar;
		}

		return $params;
	}

}
