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
            'branches' => [
                [
                    'courseId' => '1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                    'layers' => [
                        [
                            'layerName' => 'calque00',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                        [
                            'layerName' => 'calque01',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque02',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque03',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque04',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque05',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque06',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque07',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque08',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                    ]
                ],
                [
                    'courseId' => '2',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                    'layers' => [
                        [
                            'layerName' => 'calque00',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                        [
                            'layerName' => 'calque01',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque02',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque03',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque04',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque05',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque06',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque07',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'calque08',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                    ]
                ],
                [
                    'courseId' => '3',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                    'layers' => [
                        [
                            'layerName' => 'calque00',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                    ]
                ],
                [
                    'courseId' => '4',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                    'layers' => [
                        [
                            'layerName' => 'calque00',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                        [
                            'layerName' => 'calque01',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                        [
                            'layerName' => 'calque02',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],                    ]
                ],
            ]
        );

        // Updating course layers array in the preset array when a branch is obtained
        if (($branchParam = optional_param('branch', 0, PARAM_TEXT)) !== 0) {
            // Check whether the courseid param is a valid course id (#0)
            if (($courseId = optional_param('courseid', 0, PARAM_TEXT)) !== 0) {
                // Check whether the course is preset in the course layers array
                foreach ($preset['branches'] as $branchKey => $branch) {
                    if ($branch['courseId'] == $courseId) {
                        $preset['branches'][$branchKey]['layers'][$branchParam]['achievement'] = $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED;
                    }

                }
            }
        }

        parent::__construct($context, $preset);
    }

    public function getTitle() {

        return 'My progress';
    }

    /*
     * This determines whether or not the current page is /my/ page
     *
     * @return true or false
     */
    function isMyPage() {
        $arrayPathUrl = explode('/', $_SERVER['REQUEST_URI']);
        if ($arrayPathUrl[count($arrayPathUrl) - 2] === 'my') {
            return true;
        }

        return false;
    }

    /*
     * This return an array containing the layer name of the goals previously achieved
     *
     * @return an array
     */
    function getRevealedLayer(array $layers){
        //$datas = $this->context->store->get_datas();
        //$params = array('revealed_pieces' => array());

        foreach ($layers as $key => $layer) {
            if ($layer['achievement'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED) {
                $revealedPieces[] = $layer['layerName'];
            }
        }

        return $revealedPieces;
    }

    function getBranchOptionsSelect($courseId, $selectedLayer){
        $textSelect  = '';
        foreach ($this->preset['branches'] as $key => $branch) {
            if ($branch['courseId'] === $courseId) {
                foreach ($branch['layers'] as $layerKey => $layer) {
                    //if ($layer['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                        $selected = $layerKey == $selectedLayer ? 'selected' : '';
                        $textSelect .= '<option value="' . $layerKey . '" ' . $selected . '>Calque ' . $layerKey . '</option>';
                    //}
                }
            }
        }

        return $textSelect;
    }

    function getTreeOptionsSelect($selectedLayer){
        $textSelect  = '';
        foreach ($this->preset['branches'] as $key => $branch) {
            if ($branch['courseId'] === $courseId) {echo $selectedLayer;
                foreach ($branch['layers'] as $layerKey => $layer) {echo $layerKey;
                    if ($layer['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                        $selected = $layerKey == $selectedLayer ? 'selected' : '';
                        $textSelect .= '<option value="' . $layerKey . '" ' . $selected . '>Calque ' . $layerKey . '</option>';
                    }
                }
            }
        }

        return $textSelect;
    }

    public function get_content() {


        // The view for the /my/ page shows an image of trees with 8 optional layers per course
        // (making 14 x 8 = 112 optional layers in all)
        if ($this->isMyPage()) {
            $treeParam = optional_param('tree', 0, PARAM_TEXT);

            // Div block showing the tree items selector for the purpose of test
            $output  = '<div style="margin-bottom:15px;">
                            <form id="branch_form" method="POST">
                                <input id="motivator" name="motivator" type="hidden" value="progress">
                                <input id="motivator" name="motivator" type="hidden" value="' . $courseId .'">
                                <select name="branch" onChange="document.getElementById(\'branch_form\').submit()">'
                                    . $this->getBranchOptionsSelect($treeParam) .
                                '</select>
                            </form>
                        </div>';

            $output .= '<div>
                            <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Tree</h4>
                            <div id="progress-container">
                                <img src="'.$this->image_url('LudiMoodle_arbre_tronc_1.svg').'" width="180px" height="180px" class="avatar svg"/>
                            </div>
                        </div>';

        // The view within a course shows a tree branch as an SVG with 8 optional layers.
        // The progress value (0..8) determines which of the layers will be hidden and which revealed
        } else {
            $courseId = $this->context->getCourseId();echo $courseId;
            $branchParam = optional_param('branch', 0, PARAM_TEXT);//echo $branchParam;

            // Div block showing the branch items selector for the purpose of test
            $output  = '<div style="margin-bottom:15px;">
                            <form id="branch_form" method="POST">
                                <input id="motivator" name="motivator" type="hidden" value="progress">
                                <input id="courseid" name="courseid" type="hidden" value="' . $courseId .'">
                                <select name="branch" onChange="document.getElementById(\'branch_form\').submit()">'
                                    . $this->getBranchOptionsSelect($courseId, $branchParam) .
                                '</select>
                            </form>
                        </div>';

            $output .= '<div id="branch-div" style="border:1px solid">
                            <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bravo !</h4>
                            <div id="branch-container">
                                <img src="' . $this->image_url('LudiMoodle_branche_1.svg') . '" width="180px" height="180px" class="avatar svg" id="branch-picture"/>
                            </div>
                        </div>';
        }

        return $output;
    }

    public function getJsParams() {
        $datas = $this->context->store->get_datas();
        $params = array('revealed_layers' => array());

        // The view for the /my/ page shows an image of trees with 8 optional layers per course
        // (making 14 x 8 = 112 optional layers in all)
        if ($this->isMyPage()) {
            foreach ($this->preset['branches'] as $key => $branch) {
                //if ($branch['achievement'] == $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                $params['revealed_pieces'] = array_merge(
                    $params['revealed_pieces'],
                    $this->getRevealedLayer($branch['layers'])
                );
                //}
            }

        // The view within a course shows a tree branch as an SVG with 8 optional layers.
        // The progress value (0..8) determines which of the layers will be hidden and which revealed
        } else {
            foreach ($this->preset['branches'] as $key => $branch) {
                if ($branch['courseId'] === $this->context->getCourseId()) {
                    $params['revealed_layers'] = $this->getRevealedLayer($branch['layers']);
                }
            }
        }

        if (isset($datas->avatar)) {
            $params = $datas->avatar;
        }

        return $params;
    }
}
