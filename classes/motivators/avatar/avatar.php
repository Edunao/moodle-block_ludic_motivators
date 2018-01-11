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
                    'layerName' => 'calque00',
                    'layerElement' => 'panda',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'layerName' => 'calque01',
                    'layerElement' => 'panda1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque02',
                    'layerElement' => 'bandeau',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque03',
                    'layerElement' => 'chapeau',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque04',
                    'layerElement' => 'pendentif',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque05',
                    'layerElement' => 'tonneau',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque06',
                    'layerElement' => 'bambou1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque07',
                    'layerElement' => 'bambou2',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque08',
                    'layerElement' => 'short',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque09',
                    'layerElement' => 'table',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque10',
                    'layerElement' => 'bol',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque11',
                    'layerElement' => 'theiere',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque12',
                    'layerElement' => 'arche',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque13',
                    'layerElement' => 'collier',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque14',
                    'layerElement' => 'guitare',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque15',
                    'layerElement' => 'eventail',
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

    function getElementSelect($selectedLayer){

        $textSelect  = '';
        foreach ($this->preset['layers'] as $key => $layer) {
            $selected = $layer['layerName'] == $selectedLayer ? 'selected' : '';
            $textSelect .= '<option value="' . $layer['layerName'] . '" ' . $selected . '>' . $layer['layerElement'] . '</option>';
        }
    }

    public function get_content() {
        // Div block showing the image with all of the layers for previously achieved goals unmasked
        $output  = '<div>
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Avatar</h4>
                        <div id="avatar-container">
                            <img src="' . $this->image_url('LudiMoodle_avatar.svg') . '"width="180px" height="180px" class="avatar svg" id="avatar-picture"/>
                        </div>
                    </div>';

        // Div block that appears when there are goals that have just been achieved displaying
        // only the layers for the goals that have been newly achieved
        echo $this->getElementSelect('calque01');
        $output .= '<div>
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Tree</h4>
                        <form id="motivator_form" method="POST">
                            <select name="motivator" onChange="document.getElementById(\'motivator_form\').submit()">
                            </select>
                        </form>
                        <div id="element-container">
                            <img src="' . $this->image_url('LudiMoodle_avatar.svg') . '" width="180px" height="180px" class="avatar svg" id="element-picture"/>
                        </div>
                    </div>';
        //$output .= '<div><button id="next-piece">Répondre à une question</button></div>';
        $output .= '<div id="congratulation">Bravo !</div>';

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
