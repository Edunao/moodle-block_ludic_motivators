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

require_once dirname(__DIR__, 2) . '/classes/motivators/motivator.interface.php';
require_once dirname(__DIR__, 2) . '/classes/motivators/motivator_base.class.php';
require_once dirname(__DIR__, 2) . '/locallib.php';

class motivator_progress extends motivator_base implements motivator {

    public function get_loca_strings(){
        return [
            'name'  => 'Progress',
            'title' => 'My progress'
        ];
    }

    public function render($env) {
    }

//     public function __construct($context) {
//         $preset = array(
//             'branches' => [
//                 [
//                     'courseId' => '1',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                     'layers' => [
//                         [
//                             'layerName' => 'calque00',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque01',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque02',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque03',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque04',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque05',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque06',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque07',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque08',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                     ]
//                 ],
//                 [
//                     'courseId' => '2',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                     'layers' => [
//                         [
//                             'layerName' => 'calque00',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque01',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque02',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque03',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque04',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque05',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque06',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque07',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque08',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                     ]
//                 ],
//                 [
//                     'courseId' => '3',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                     'layers' => [
//                         [
//                             'layerName' => 'calque00',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                         ],
//                     ]
//                 ],
//                 [
//                     'courseId' => '4',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                     'layers' => [
//                         [
//                             'layerName' => 'calque00',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque01',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                         ],
//                         [
//                             'layerName' => 'calque02',
//                             'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                         ],
//                     ]
//                 ],
//             ]
//         );
//
//         // Updating course layers array in the preset array when a branch is obtained
//         if (($branchParam = optional_param('branch', 0, PARAM_TEXT)) !== 0) {
//             // Check whether the courseid param is a valid course id (#0)
//             if (($courseId = optional_param('courseid', 0, PARAM_TEXT)) !== 0) {
//                 // Check whether the course is presetted in the course layers array
//                 foreach ($preset['branches'] as $branchKey => $branch) {
//                     if ($branch['courseId'] == $courseId) {
//                         $preset['branches'][$branchKey]['layers'][$branchParam]['stat'] = $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED;
//                     }
//
//                 }
//             }
//         } else {
//             if (($layerParam = optional_param('layer', 0, PARAM_TEXT)) !== 0) {
//                 for ($i=0; $i <= $layerParam ; $i++) {
//                     $branch = floor($i/9);
//                     $layer = $i % 9;
//                     $preset['branches'][$branch]['layers'][$layer]['stat'] = $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED;
//                     if (!isset($preset['branches'][$branch]['layers'][$layer]['layerName'])){
//                         $preset['branches'][$branch]['layers'][$layer]['layerName'] = 'calque' . str_pad($layer, 2, '0', STR_PAD_LEFT);
//                     }
//                 }
//             }
//         }
//
//
//
//         parent::__construct($context, $preset);
//     }
//
//     /*
//      * This determines whether or not the current page is /my/ page
//      *
//      * @return true or false
//      */
//     function isMyPage() {
//         $arrayPathUrl = explode('/', $_SERVER['REQUEST_URI']);
//         if ($arrayPathUrl[count($arrayPathUrl) - 2] === 'my') {
//             return true;
//         }
//
//         return false;
//     }
//
//     /*
//      * This return an array containing the layer name of the goals previously achieved
//      *
//      * @return an array
//      */
//     function getRevealedLayer(array $layers, $state){
//         $revealedLayers = array();
//
//         foreach ($layers as $key => $layer) {
//             if ($layer['stat'] === $state) {
//                 $revealedLayers = $layer['layerName'];
//             }
//         }
//
//         return $revealedLayers;
//     }
//
//     function getBranchOptionsSelect($courseId, $selectedLayer){
//         $textSelect  = '';
//         foreach ($this->preset['branches'] as $courseKey => $branch) {
//             if ($branch['courseId'] === $courseId) {
//                 foreach ($branch['layers'] as $layerKey => $layer) {
//                     $selected = ($layerKey == $selectedLayer) ? 'selected' : '';
//                     $textSelect .= '<option value="' . $layerKey . '" ' . $selected . '>Calque ' . $layerKey . '</option>';
//                 }
//             }
//         }
//
//         return $textSelect;
//     }
//
//     function getTreeOptionsSelect($courseId, $selectedLayer){
//         $textSelect  = '<option value="" selected>Sélectionnez une branche</option>';
//         for ($branch=0; $branch <15; $branch++) {
//             $layer = 0;
//             $layerId = ($branch*9) + $layer;
//             $selected = ($layerId == $selectedLayer) ? 'selected' : '';
//             $textSelect .= '<option value="' . $layerId . '" ' . $selected . '>Branche ' . $branch . ' - Calque ' . $layer . '</option>';
//         }
//
//         return $textSelect;
//     }
//
//     public function get_loca_strings(){
//         return [
//             'name'  => 'Progress',
//             'title' => 'My progress'
//         ];
//     }
//
//     public function get_content() {
//
//         // The view for the /my/ page shows an image of trees with 8 optional layers per course
//         // (making 14 x 8 = 112 optional layers in all)
//         if ($this->isMyPage()) {
//             $layerParam = optional_param('layer', '', PARAM_TEXT);
//             $courseId = optional_param('courseId', 0, PARAM_TEXT);
//
//             // Div block showing the tree items selector for the purpose of test
//             $output  = '<div style="margin-bottom:15px;">
//                             <form id="branch_form" method="POST">
//                                 <input id="motivator" name="motivator" type="hidden" value="progress">
//                                 <input id="courseid" name="courseid" type="hidden" value="' . $courseId .'">
//                                 <select name="layer" onChange="document.getElementById(\'branch_form\').submit()">'
//                                     . $this->getTreeOptionsSelect($courseId, $layerParam) .
//                                 '</select>
//                             </form>
//                         </div>';
//
//             $output .= '<div id="branch-div" style="border:1px solid">
//                             <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Tree</h4>
//                             <div id="progress-container">
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_01.svg').'" class="avatar svg" id="branch-picture1"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_02.svg').'" class="avatar svg" id="branch-picture2"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_03.svg').'" class="avatar svg" id="branch-picture3"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_04.svg').'" class="avatar svg" id="branch-picture4"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_05.svg').'" class="avatar svg" id="branch-picture5"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_06.svg').'" class="avatar svg" id="branch-picture6"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_07.svg').'" class="avatar svg" id="branch-picture7"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_08.svg').'" class="avatar svg" id="branch-picture8"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_09.svg').'" class="avatar svg" id="branch-picture9"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_10.svg').'" class="avatar svg" id="branch-picture10"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_11.svg').'" class="avatar svg" id="branch-picture11"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_12.svg').'" class="avatar svg" id="branch-picture12"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_13.svg').'" class="avatar svg" id="branch-picture13"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_branche_14.svg').'" class="avatar svg" id="branch-picture14"/>
//                                 <img src="'.$this->image_url('LudiMoodle_arbre_troncs.svg').'" class="avatar svg"/>
//                             </div>
//                         </div>';
//
//         // The view within a course shows a tree branch as an SVG with 8 optional layers.
//         // The progress value (0..8) determines which of the layers will be hidden and which revealed
//         } else {
//             $courseId = $this->context->getCourseId();
//             $branchParam = optional_param('branch', 0, PARAM_TEXT);
//
//             // Div block showing the branch items selector for the purpose of test
//             $output  = '<div style="margin-bottom:15px;">
//                             <form id="branch_form" method="POST">
//                                 <input id="motivator" name="motivator" type="hidden" value="progress">
//                                 <input id="courseid" name="courseid" type="hidden" value="' . $courseId .'">
//                                 <select name="branch" onChange="document.getElementById(\'branch_form\').submit()">'
//                                     . $this->getBranchOptionsSelect($courseId, $branchParam) .
//                                 '</select>
//                             </form>
//                         </div>';
//
//             $output .= '<div id="branch-div" style="border:1px solid">
//                             <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bravo !</h4>
//                             <div id="branch-container">
//                                 <img src="' . $this->image_url('LudiMoodle_branche_1.svg') . '" width="180px" height="180px" class="avatar svg" id="branch-picture1"/>
//                             </div>
//                         </div>';
//         }
//
//         return $output;
//     }
//
//     public function getJsParams() {
//         $datas = $this->context->store->get_datas();
//         $params = array('revealed_layers' => array());
//
//         // The view for the /my/ page shows an image of trees with 8 optional layers per course
//         // (making 14 x 8 = 112 optional layers in all)
//         if ($this->isMyPage()) {
//             foreach ($this->preset['branches'] as $key => $branch) {
//                 $params['revealed_layers'][] = $this->getRevealedLayer($branch['layers'], $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED);
//             }
//
//         // The view within a course shows a tree branch as an SVG with 8 optional layers.
//         // The progress value (0..8) determines which of the layers will be hidden and which revealed
//         } else {
//             foreach ($this->preset['branches'] as $key => $branch) {
//                 if ($branch['courseId'] === $this->context->getCourseId()) {
//                     $params['revealed_layers'][] = $this->getRevealedLayer($branch['layers'], $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED);
//                 }
//             }
//         }
//
//         if (isset($datas->avatar)) {
//             $params = $datas->avatar;
//         }
//
//         return $params;
//     }
}
