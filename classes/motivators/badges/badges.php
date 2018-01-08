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

class badges extends iMotivator {

    public function __construct($context) {
        $preset = array(
            'coursesGoals' => [
                [
                    'badgeName' => '3 bonnes réponses',
                    'iconId' => 'course_badge1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'badgeName' => 'Course Goal 2',
                    'iconId' => 'course_badge2',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'badgeName' => '10 bonnes réponses',
                    'iconId' => 'course_badge2',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'badgeName' => 'Course Goal 4',
                    'iconId' => 'course_badge1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'badgeName' => 'Course Goal 5',
                    'iconId' => 'course_badge1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED,
                ]
            ],
            'globalsGoals' => [
                [
                    'layerName' => '1-1',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'layerName' => '1-4',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ],
                [
                    'layerName' => '2-2',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
                ]
            ]
        );
        parent::__construct($context, $preset);
    }

    public function getTitle() {

        return 'Mes badges';
    }

    public function get_content() {
        global $CFG;

        $output  = '<div id="badges-container">';

        // Div block for course goals selected between the achieved and not achieved (opacity:0.3)
        $output .=
            '<div>
                <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Course Goals achieved and not</h4>
                <ul id="course-badge">';
        foreach ($this->preset['coursesGoals'] as $key => $courseBadge) {
            $opacity = ($courseBadge['achievement'] === $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED) ? "0.3" : "1";
            $output .=
                '<li style="opacity:' . $opacity . '">
                    <figure>
                        <img src="' . $CFG->wwwroot . '/blocks/ludic_motivators/classes/motivators/badges/pix/' . $courseBadge['iconId'] . '.png " title="' . $courseBadge['badgeName'] . '"/>
                        <figcaption>' . $courseBadge['badgeName'] . '</figcaption>
                    </figure>
                </li>';
        }
        $output .=
            '   </ul>
            </div>';

        // Div block for course goals that have just been achieved
        $output .=
            '<div>
                <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Course Goals just achieved</h4>
                <ul id="course-badge">';
        foreach ($this->preset['coursesGoals'] as $key => $courseBadge) {
            if ($courseBadge['achievement'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED) {
                $output .=
                '<li>
                    <figure>
                        <img src="' . $CFG->wwwroot . '/blocks/ludic_motivators/classes/motivators/badges/pix/' . $courseBadge['iconId'] . '.png " title="' . $courseBadge['badgeName'] . '"/>
                        <figcaption>' . $courseBadge['badgeName'] . '</figcaption>
                    </figure>
                </li>';
            }
        }
        $output .=
            '   </ul>
            </div>';

        // Div block displaying an SVG image representing the global goals with layers that
        // can be displayed to represent the goals that have been achieved
        $output .=
            '<div id="avatar-container">
                <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Global Goals</h4>';
        $output .=
                '<div /*style="position:relative"*/>
                    <img src="'.$this->image_url('puzzle.svg').'" width="180px" height="180px" class="avatar svg"/>
                </div>';
        $output .=
            '</div>';

        return $output;
    }


    public function getJsParams() {
        $datas = $this->context->store->get_datas();
        $params = array('revealed_pieces' => array());print_r($this->preset['globalsGoals']);

        foreach ($this->preset['globalsGoals'] as $key => $value) {
            if ($value['achievement'] == $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                $params['revealed_pieces'][] = $value['layerName'];
            }
        }

        if (isset($datas->avatar)) {
            $params = $datas->avatar;
        }

        print_r($params['revealed_pieces']);

        return $params;
    }

}
