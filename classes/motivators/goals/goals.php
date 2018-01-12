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

class goals extends iMotivator {

    public function __construct($context) {
        $preset = array(
            'objectives' => [
                [
                    'title' => 'Répondre à 3 questions',
                    'percentToPass' => '70',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED,
                ],
                [
                    'title' => 'Terminer un quiz',
                    'percentToPass' => '65',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'title' => 'Réponse à une question en moins de 20 secondes',
                    'percentToPass' => '70',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'title' => 'Objective4',
                    'percentToPass' => '65',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'title' => 'Objective5',
                    'percentToPass' => '75',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ]
            ],
            'globalAchievements' => [
                [
                    'title' => 'session1Objectives',
                    'percentToPass' => '70',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'title' => 'session2Objectives',
                    'percentToPass' => '65',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'title' => 'session3Objectives',
                    'percentToPass' => '70',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
            ]
        );
        parent::__construct($context, $preset);
    }

    public function getTitle() {

        return 'Mes objectifs';
    }

    function isGoalsJustAchieved() {
        foreach ($this->preset['objectives'] as $key => $objective) {
            if ($objective['achievement'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
                return true;
            }
        }

        return false;
    }

    function getPreviouslyAchievedGoals() {
        $textHtml = '';
        foreach ($this->preset['objectives'] as $key => $objective) {
            if ($objective['achievement'] != $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED){
                $textHtml .= '<li>' . $objective['title'] . '</li>';
            }
        }

        return $textHtml;

    }

    function getJustAchievedGoals() {
        $textHtml = '';
        foreach ($this->preset['objectives'] as $key => $objective) {
            if ($objective['achievement'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
                $textHtml .= "<li>" . $objective['title'] . "</li>";
            }
        }

        return $textHtml;
    }

    public function get_content() {

        // Div block listing all goals with checkboxes next to those that have been achieved
        $output  = '<div id="goals-container">
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Objectifs</h4>
                        <div>
                            <ul id="goals">'
                            . $this->getPreviouslyAchievedGoals() .
                            '</ul>
                        </div>
                    </div>';

        // Div block that appears when there are goals that have just been achieved listing these goals
        // Determining if there is at once one goal just achieved
        if ($this->isGoalsJustAchieved() === true) {
            $output .= '<div id="goals-container">
                            <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bravo !</h4>
                            <div>
                                <ul id="goals">'
                                    . $this->getJustAchievedGoals() .
                                '</ul>
                            </div>
                        </div>';
        }

        return $output;
    }

}
