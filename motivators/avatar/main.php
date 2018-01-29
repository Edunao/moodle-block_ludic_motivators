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

require_once dirname(__DIR__, 2) . '/classes/motivators/motivator_base.class.php';

class motivator_avatar extends motivator_base implements motivator {

//    public function __construct(execution_environment $env) {
//         // Initialisation du status du modérateur
//         set_achievement_status($context);
//
//
//         // Updating layers array in the preset array when an element is selected
//         if (($element = optional_param('element', '', PARAM_TEXT)) !== '') {
//             foreach ($preset['layers'] as $key => $layer) {
//                 if ($layer['layerName'] === $element) {
//                     $preset['layers'][$key]['achievement'] = $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED;
//                 }
//
//             }
//         }
//        parent::__construct($env);
//    }

//     function getElementSelect($selectedLayer){
//         $textSelect  = '';
//         foreach ($this->preset['layers'] as $key => $layer) {
//             if ($layer['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
//                 $selected = $layer['layerName'] == $selectedLayer ? 'selected' : '';
//                 $textSelect .= '<option value="' . $layer['layerName'] . '" ' . $selected . '>' . $layer['layerElement'] . '</option>';
//             }
//         }
//
//         return $textSelect;
//     }

    public function get_loca_strings(){
        return [
            'name'          => 'Avatar',
            'title'         => 'Représentation de soi',
            'full_title'    => 'Moi même',
            'changes_title' => 'Du nouveau !',
        ];
    }

    public function render($env) {
//         // Div block showing the image with all of the layers for previously achieved goals unmasked
//         $output  = '<div id="avatar-div" style="margin-bottom:15px;border:1px solid">
//                         <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Avatar</h4>
//                         <div id="avatar-container">
//                             <img src="' . $this->image_url('LudiMoodle_avatar.svg') . '"width="180px" height="180px" class="avatar svg" id="avatar-picture"/>
//                         </div>
//                     </div>';

//         // Div block showing the element selector for the purpose of test
//         $output .= '<div style="margin-bottom:15px;">
//                         <form id="element_form" method="POST">
//                             <input id="motivator" name="motivator" type="hidden" value="avatar">
//                             <select name="element" onChange="document.getElementById(\'element_form\').submit()">' . $this->getElementSelect(optional_param('element', 'calque01', PARAM_TEXT)) .
//                             '</select>
//                         </form>
//                     </div>';

//         // Div block that appears when there are goals that have just been achieved displaying
//         // only the layers for the goals that have been newly achieved
//         if (optional_param('element', '', PARAM_TEXT) !== '') {
//             $output .= '<div id="element-div" style="border:1px solid">
//                             <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bravo</h4>
//                             <div id="element-container">
//                                 <img src="' . $this->image_url('LudiMoodle_avatar.svg') . '" width="180px" height="180px" class="avatar svg" id="element-picture"/>
//                             </div>
//                         </div>';
//         }

//         return $output;
//    }
//
//    private function get_js_data( /* $env */ ) {
        // prime a jsdata object with the different tables that we're going to projide to the JS script
        $jsdata = [
            'obtained'       => [],
            'newly_obtained' => [],
            'new_names'      => []
        ];

        // fetch config and associated achievement data
        $config     = $env->get_full_config($this->get_short_name());
        $statedata  = $env->get_full_state_data($config);

echo "<h1>state data</h1>";

        // match up the config elements and state data to determine the set of information to pass to the javascript
        foreach ($config as $element){
            $dataname = $element['course'] . '/done';
echo "Checking $dataname<br>";
            if (isset($statedata[$dataname])){
                $statevalue = $statedata[$dataname];
                switch ($statevalue){
                case 2:
echo "- newly obtained<br>";
                    $jsdata['newly_obtained'][] = $element['motivator']['layer'];
                    // drop through ... don't break here!
                case 1:
echo "- obtained<br>";
                    $jsdata['obtained'][] = $element['motivator']['layer'];
                    break;
                }
            }
        }

        // register the js data
        $env->set_js_init_data($this->get_short_name(), $jsdata);

        // Construct content blocks for rendering
        $imageurl       = $this->image_url('avatar.svg');
        $fullimage      = "<img src='$imageurl' class='avatar svg' id='ludi-avatar-full'/>";
        $changesimage   = "<img src='$imageurl' class='avatar svg' id='ludi-avatar-changes'/>";

        // render the output
//        $env->set_block_classes('show-changes');
        $env->render($this->get_string('full_title'), $fullimage);
        if (!empty($jsdata['newly_obtained'])){
            $env->render($this->get_string('changes_title'), $changesimage);
        }

// //        $datas = $this->context->store->get_datas();
//         $params = array();
//
//         //$params['newly_obtained'][] = optional_param('element', 'avatar', PARAM_TEXT);
//
//         foreach ($this->preset['layers'] as $key => $value) {
//             if ($value['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED) {
//                 $params['obtained'][] = $value['layerName'];
//             }
//             if ($value['achievement'] == $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED) {
//                 $params['newly_obtained'][] = $value['layerName'];
//             }
//         }
//
//         if (isset($datas->avatar)) {
//             $params = $datas->avatar;
//         }
//
//         return $params;
    }

}











//         // Lecture du fichier de configuration
//         $preset = array(
//             'questionsSet' => [
//                 'nbOfQuestions' => '8',
//                 'currentQuestion' => '3',
//                 'percentToPass' => '70',
//                 'quizState' => 'Notdone | InProgress | Completed',
//                 'svgFileName' => 'puzzle.svg',
//                 'nbOfLayers' => 16,
//             ],
//             'layers' => [
//                 [
//                     'layerName' => 'calque00',
//                     'layerElement' => 'Panda net nu',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque01',
//                     'layerElement' => 'Panda flou nu',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque02',
//                     'layerElement' => 'Bandeau',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque03',
//                     'layerElement' => 'Chapeau',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque04',
//                     'layerElement' => 'Pendentif',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque05',
//                     'layerElement' => 'Tonneau',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque06',
//                     'layerElement' => 'Bambou1',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque07',
//                     'layerElement' => 'Bambou2',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque08',
//                     'layerElement' => 'Short',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque09',
//                     'layerElement' => 'Table',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque10',
//                     'layerElement' => 'Bol',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque11',
//                     'layerElement' => 'Théière',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque12',
//                     'layerElement' => 'Arche',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque13',
//                     'layerElement' => 'Collier',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque14',
//                     'layerElement' => 'Guitare',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque15',
//                     'layerElement' => 'Eventail',
//                     'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//             ],
//             'globalAchievements' => [
//                         'session1Objectives' => 0,
//                         'session2Objectives' => 1
//             ]
//         );
