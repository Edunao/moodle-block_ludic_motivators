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
                            'layerName' => 'Etape0',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape1',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape2',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape3',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape4',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape5',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape6',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape7',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape8',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                    ]
                ],
                [
                    'courseId' => '2',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                    'layers' => [
                        [
                            'layerName' => 'Etape0',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape1',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape2',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape3',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape4',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape5',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape6',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape7',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                        [
                            'layerName' => 'Etape8',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                        ],
                    ]
                ],
                [
                    'courseId' => '3',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                    'layers' => [
                        [
                            'layerName' => '1-3',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                    ]
                ],
                [
                    'courseId' => '4',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                    'layers' => [
                        [
                            'layerName' => '2-4',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                        [
                            'layerName' => '3-4',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],
                        [
                            'layerName' => '4-4',
                            'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                        ],                    ]
                ],
            ]
        );
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
    function getRevealedPieces(array $layers){
        //$datas = $this->context->store->get_datas();
        //$params = array('revealed_pieces' => array());

        foreach ($layers as $key => $layer) {
            if ($layer['achievement'] == $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                $revealedPieces[] = $layer['layerName'];
            }
        }

        return $revealedPieces;
    }

    public function get_content() {
        // The view for the /my/ page shows an image of trees with 8 optional layers per course
        // (making 14 x 8 = 112 optional layers in all)
        if ($this->isMyPage()) {
            $output  = '<div>
                            <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Tree</h4>
                            <div id="progress-container">';
            $output .= '        <img src="'.$this->image_url('tree.svg').'" width="180px" height="180px" class="avatar svg"/>';
            $output .= '    </div>
                        </div>';
            $output .= '<div><button id="next-stage">Répondre à une question</button></div>';
            $output .= '<div id="congratulation">Congratulations !</div>';

        // The view within a course shows a tree branch as an SVG with 8 optional layers.
        // The progress value (0..8) determines which of the layers will be hidden and which revealed
        } else {
            $output  = '<div>
                            <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Branch</h4>
                            <div id="progress-container">';
            $output .= '        <img src="'.$this->image_url('branch.svg').'" width="180px" height="180px" class="avatar svg"/>';
            $output .= '    </div>
                        </div>';
            $output .= '<div><button id="next-stage">Répondre à une question</button></div>';
            $output .= '<div id="congratulation">Congratulations !</div>';
        }

        return $output;
    }

    public function getJsParams() {
        $datas = $this->context->store->get_datas();
        $params = array('revealed_pieces' => array());

        // The view for the /my/ page shows an image of trees with 8 optional layers per course
        // (making 14 x 8 = 112 optional layers in all)
        if ($this->isMyPage()) {
            foreach ($this->preset['branches'] as $key => $branch) {
                //if ($branch['achievement'] == $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                $params['revealed_pieces'] = array_merge(
                    $params['revealed_pieces'],
                    $this->getRevealedPieces($branch['layers'])
                );
                //}
            }

        // The view within a course shows a tree branch as an SVG with 8 optional layers.
        // The progress value (0..8) determines which of the layers will be hidden and which revealed
        } else {
            foreach ($this->preset['branches'] as $key => $branch) {
                if ($branch['courseId'] === $this->context->getCourseId()) {
                    $params['revealed_pieces'] = $this->getRevealedPieces($branch['layers']);
                }
            }
        }

        if (isset($datas->avatar)) {
            $params = $datas->avatar;
        }

        return $params;
    }
}
