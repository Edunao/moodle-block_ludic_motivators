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
                    'layerElement' => 'Panda net nu',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'layerName' => 'calque01',
                    'layerElement' => 'Panda flou nu',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque02',
                    'layerElement' => 'Bandeau',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque03',
                    'layerElement' => 'Chapeau',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque04',
                    'layerElement' => 'Pendentif',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'layerName' => 'calque05',
                    'layerElement' => 'Tonneau',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque06',
                    'layerElement' => 'Bambou1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque07',
                    'layerElement' => 'Bambou2',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'layerName' => 'calque08',
                    'layerElement' => 'Short',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque09',
                    'layerElement' => 'Table',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque10',
                    'layerElement' => 'Bol',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'layerName' => 'calque11',
                    'layerElement' => 'Théière',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque12',
                    'layerElement' => 'Arche',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque13',
                    'layerElement' => 'Collier',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque14',
                    'layerElement' => 'Guitare',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque15',
                    'layerElement' => 'Eventail',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
            ],
            'globalAchievements' => [
                        'session1Objectives' => 0,
                        'session2Objectives' => 1
            ]
        );

        // Updating layers array in the preset array when an element is selected
        if (($element = optional_param('element', '', PARAM_TEXT)) !== '') {
            foreach ($preset['layers'] as $key => $layer) {
                if ($layer['layerName'] === $element) {
                    $preset['layers'][$key]['achievement'] = $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED;
                }

            }
        }
        parent::__construct($context, $preset);
    }

    public function getTitle() {

        return 'Découverte';
    }

    function getElementSelect($selectedLayer){
        $textSelect  = '';
        foreach ($this->preset['layers'] as $key => $layer) {
            if ($layer['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                $selected = $layer['layerName'] == $selectedLayer ? 'selected' : '';
                $textSelect .= '<option value="' . $layer['layerName'] . '" ' . $selected . '>' . $layer['layerElement'] . '</option>';
            }
        }

        return $textSelect;
    }

    public function get_content() {
        // Div block showing the image with all of the layers for previously achieved goals unmasked
        $output  = '<div id="avatar-div" style="margin-bottom:15px;border:1px solid">
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Avatar</h4>
                        <div id="avatar-container">
                            <img src="' . $this->image_url('LudiMoodle_avatar.svg') . '"width="180px" height="180px" class="avatar svg" id="avatar-picture"/>
                        </div>
                    </div>';

        // Div block showing the element selector for the purpose of test
        $output .= '<div style="margin-bottom:15px;">
                        <form id="element_form" method="POST">
                            <input id="motivator" name="motivator" type="hidden" value="avatar">
                            <select name="element" onChange="document.getElementById(\'element_form\').submit()">' . $this->getElementSelect(optional_param('element', 'calque01', PARAM_TEXT)) .
                            '</select>
                        </form>
                    </div>';

        // Div block that appears when there are goals that have just been achieved displaying
        // only the layers for the goals that have been newly achieved
        if (optional_param('element', '', PARAM_TEXT) !== '') {
            $output .= '<div id="element-div" style="border:1px solid">
                            <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bravo</h4>
                            <div id="element-container">
                                <img src="' . $this->image_url('LudiMoodle_avatar.svg') . '" width="180px" height="180px" class="avatar svg" id="element-picture"/>
                            </div>
                        </div>';
        }

        return $output;
    }

    public function getJsParams() {
        $datas = $this->context->store->get_datas();
        $params = array();

        //$params['newly_obtained'][] = optional_param('element', 'avatar', PARAM_TEXT);

        foreach ($this->preset['layers'] as $key => $value) {
            if ($value['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED) {
                $params['obtained'][] = $value['layerName'];
            }
            if ($value['achievement'] == $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED) {
                $params['newly_obtained'][] = $value['layerName'];
            }
        }

        if (isset($datas->avatar)) {
            $params = $datas->avatar;
        }

        return $params;
    }

}
