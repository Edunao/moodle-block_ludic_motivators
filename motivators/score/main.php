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

class motivator_score extends motivator_base implements motivator {

    public function __construct(execution_environment $env) {
        parent::__construct($env);
    }

    public function get_loca_strings(){
        return [
            'name'  => 'Score',
            'title' => 'My score'
        ];
    }

    public function get_content() {
        return "";
    }

    public function get_js_data() {
        return [];
    }

//     public function __construct($context) {
//         $preset = array(
//             'previousTotalScore' => 0,
//             'newTotalScore' => 0,
//             'bonuses' => [
//                 [
//                     'nameOfBonus' => 'répondu correctement après 3 tentatives',
//                     'valueOfBonus' => '3',
//                     'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'nameOfBonus' => 'terminé le quiz',
//                     'valueOfBonus' => '2',
//                     'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                 ],
//                 [
//                     'nameOfBonus' => 'répondu à une question en moins de 20 secondes',
//                     'valueOfBonus' => '5',
//                     'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'nameOfBonus' => 'bonus1',
//                     'valueOfBonus' => '1',
//                     'stateOfBonus' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                 ],
//             ],
//             'globalAchievements' => [
//                 'session1Objectives' => 0,
//                 'session2Objectives' => 1
//             ]
//         );
//
//         // Updating bonus array in the preset array when a bonus is selected
//         if (($newBonus = optional_param('bonus', '', PARAM_TEXT)) !== '') {
//             foreach ($preset['bonuses'] as $key => $bonus) {
//                 if ($bonus['nameOfBonus'] === $newBonus) {
//                     $preset['bonuses'][$key]['stateOfBonus'] = $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED;
//                 }
//
//             }
//         }
//
//         // Updating points array in the preset array when new points is selected
//
//         if (($newScore = optional_param('newScore', '', PARAM_TEXT)) !== '') {
//             $preset['previousTotalScore'] = $preset['newTotalScore'];
//             $preset['newTotalScore'] = $newScore;
//         }
//
//         parent::__construct($context, $preset);
//     }
//
//     function getBonusList() {
//         $resultHtml = '';
//         foreach ($this->preset['bonuses'] as $key => $bonus) {
//             if ($bonus['stateOfBonus'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
//                 $resultHtml .= '<li><b>' . $bonus['valueOfBonus'] . ' points</b></li>';
//             }
//         }
//
//         return $resultHtml;
//     }
//
//     function getBonusSelect($selectedBonus){
//
//         $textSelect  = '';
//         foreach ($this->preset['bonuses'] as $key => $bonus) {
//             if ($bonus['stateOfBonus'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
//                 $selected = $bonus['nameOfBonus'] == $selectedBonus ? 'selected' : '';
//                 $textSelect .= '<option value="' . $bonus['nameOfBonus'] . '" ' . $selected . '>' . $bonus['nameOfBonus'] . '</option>';
//             }
//         }
//
//         return $textSelect;
//     }
//
//     function getPreviousScore() {
//         return $this->preset['previousTotalScore'];
//     }
//
//     function getNewScore() {
//         return $this->preset['newTotalScore'];
//     }
//     function isNewBonus() {
//         return optional_param('bonus', '', PARAM_TEXT) !== '';
//     }
//
//     public function get_content() {
//
//         // Div block displaying the latest total score with an animation
//         // showing in first the previous and progressively the new score
//         $output   = '<div id="score-container" style="margin-bottom:15px;border:1px solid">
//                         <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Score</h4>
//                         <div class="score">
//                             <span class="score-number">' . $this->preset['newTotalScore'] . '</span>
//                             <span class="points">pts</span>
//                         </div>
//                     </div>';
//
//         // Div block showing the points to win (or lose) selector for the purpose of test
//         $output .= '<div style="margin-bottom:15px;">
//                         <form id="score_form" method="POST">
//                             <input id="motivator" name="motivator" type="hidden" value="score">
//                             <input id="previousScore" name="previousScore" type="hidden" value='. $this->getPreviousScore() .'>
//                             <select name="newScore" onChange="document.getElementById(\'score_form\').submit()">
//                                 <option value="" selected>Points à gagner</option>
//                                 <option value=' . ($this->getPreviousScore()+1) . '>+1 points</option>
//                                 <option value=' . ($this->getPreviousScore()+2) . '>+2 points</option>
//                                 <option value=' . ($this->getPreviousScore()+3) . '>+3 points</option>
//                                 <option value=' . ($this->getPreviousScore()+4) . '>+4 points</option>
//                                 <option value=' . ($this->getPreviousScore()+5) . '>+5 points</option>
//                                 <option value=' . ($this->getPreviousScore()+6) . '>+6 points</option>
//                             </select>
//                         </form>
//                     </div>';
//
//         // Div block that appears only if the score progresses containing the number of
//         // new points obtained from the last quiz
//         if ($this->preset['newTotalScore'] > $this->preset['previousTotalScore']) {
//             $output .= '<div id="score-container" style="margin-bottom:15px;border:1px solid">
//                             <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bravo !</h4>
//                             <div>
//                                 <ul id="bonus">
//                                     <li>
//                                         <b>'. ($this->getNewScore()-$this->getPreviousScore()) . ' points </b>
//                                     </li>
//                                 </ul>
//                             </div>
//                         </div>';
//         }
//
//         // Div block showing the bonus selector for the purpose of test
//         $output .= '<div style="margin-bottom:15px;">
//                         <form id="bonus_form" method="POST">
//                             <input id="motivator" name="motivator" type="hidden" value="score">
//                             <select name="bonus" onChange="document.getElementById(\'bonus_form\').submit()">
//                                 <option value="" selected>Bonus à gagner</option>'
//                                 . $this->getBonusSelect(optional_param('bonus', 'calque01', PARAM_TEXT)) .
//                             '</select>
//                         </form>
//                     </div>';
//
//         // Div block that appears only on any bonuses obtained with the last quiz
//         if ($this->isNewBonus()) {
//             $output .= '<div id="score-container" style="border:1px solid">
//                             <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bonus</h4>
//                             <div>
//                                 <ul id="bonus">'
//                                     . $this->getBonusList() .
//                                 '</ul>
//                             </div>
//                         </div>';
//         }
//
//         return $output;
//     }
//
//     public function getJsParams() {
//         //$datas = $this->context->store->get_datas();
//         $params = array(
//             'previous_score' => $this->getPreviousScore(),
//             'new_score' => $this->preset['newTotalScore'],
//         );
//
//         if (isset($datas->avatar)) {
//             $params = $datas->avatar;
//         }
//
//         return $params;
//     }
}
