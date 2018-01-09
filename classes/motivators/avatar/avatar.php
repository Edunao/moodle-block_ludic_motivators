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

    public function __construct($context) {
        $preset = array(
            'questionsSet' => [
                'nbOfQuestions' => '8',
                'currentQuestion' => '3',
                'percentToPass' => '70',
                'quizState' => 'Notdone | InProgress | Completed',
                'svgFileName' => 'puzzle.svg',
                'nbOfLayers' => 16,
            ],
            'layers' => [
                [
                    'layerName' => '1-1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'layerName' => '1-2',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'layerName' => '1-3',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => '1-4',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => '2-1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => '2-2',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => '2-3',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => '2-4',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
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

        return 'Découverte';
    }

    public function get_content() {
        // Div block showing the image with all of the layers for previously achieved goals unmasked
        $output = '<div id="avatar-container">';
        $output .= '<img src="'.$this->image_url('fractal.jpg').'"width="180px" height="180px" id="avatar-picture"/>';
        $output .= '</div>';

        // Div block that appears when there are goals that have just been achieved displaying
        // only the layers for the goals that have been newly achieved
        $output .= '<div id="avatar-container">';
        $output .= '<img src="'.$this->image_url('puzzle.svg').'" width="180px" height="180px" class="avatar svg"/>';
        $output .= '</div>';
        $output .= '<div><button id="next-piece">Répondre à une question</button></div>';
        $output .= '<div id="congratulation">Congratulations !</div>';

        return $output;
    }

    public function getJsParams() {
        $datas = $this->context->store->get_datas();
        $params = array('revealed_pieces' => array());

        foreach ($this->preset['layers'] as $key => $value) {
            if ($value['achievement'] == $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                $params['revealed_pieces'][] = $value['layerName'];
            }
        }

        if (isset($datas->avatar)) {
            $params = $datas->avatar;
        }

        return $params;
    }

}
