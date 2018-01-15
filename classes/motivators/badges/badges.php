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
            'coursesBadges' => [
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
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ]
            ],
            'globalBadges' => [
                [
                    'layerName' => 'calque00',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque01',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
                [
                    'layerName' => 'calque02',
                    'achievement' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
                ],
            ],
        );

        // Updating course badges array in the preset array when a badge is selected
        if (($newBadge = optional_param('badge', '', PARAM_TEXT)) !== '') {
            foreach ($preset['coursesBadges'] as $key => $badge) {
                if ($badge['badgeName'] === $newBadge) {
                    $preset['coursesBadges'][$key]['achievement'] = $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED;
                }
            }
        }

        // Updating global badges array in the preset array when a badge is selected
        if (($globalBadge = optional_param('globalBadge', '', PARAM_TEXT)) !== '') {
            foreach ($preset['globalBadges'] as $key => $badge) {
                if ($badge['layerName'] === $globalBadge) {
                    $preset['coursesBadges'][$key]['achievement'] = $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED;
                }
            }
        }
        parent::__construct($context, $preset);
    }

    public function getTitle() {

        return 'Mes badges';
    }

    function getGlobalBadgeList() {
        global $CFG;
        $resultHtml = '';

        foreach ($this->preset['globalBadges'] as $key => $badge) {
            if ($badge['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
                $resultHtml .=
                '<li style="opacity:' . $opacity . '">
                    <figure>
                        <img src="' . $CFG->wwwroot . '/blocks/ludic_motivators/classes/motivators/badges/pix/' . $badge['iconId'] . '.png" title="' . $badge['badgeName'] . '"/>
                        <figcaption>' . $badge['badgeName'] . '</figcaption>
                    </figure>
                </li>';
            }
        }

        return $resultHtml;
    }

    function getGlobalBadgesSelect($selectedBadge){
        $textSelect  = '';
        foreach ($this->preset['globalBadges'] as $key => $badge) {
            if ($badge['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                $selected = $badge['layerName'] == $selectedBadge ? 'selected' : '';
                $textSelect .= '<option value="' . $badge['layerName'] . '" ' . $selected . '>' . $badge['layerName'] . '</option>';
            }
        }

        return $textSelect;
    }

    function getCourseBadgeList() {
        global $CFG;
        $resultHtml = '';

        foreach ($this->preset['coursesBadges'] as $key => $badge) {
            if ($badge['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
                $opacity = ($badge['achievement'] === $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED) ? "0.5" : "1";
                $resultHtml .=
                '<li style="opacity:' . $opacity . '">
                    <figure>
                        <img src="' . $CFG->wwwroot . '/blocks/ludic_motivators/classes/motivators/badges/pix/' . $badge['iconId'] . '.png" title="' . $badge['badgeName'] . '"/>
                        <figcaption>' . $badge['badgeName'] . '</figcaption>
                    </figure>
                </li>';
            }
        }

        return $resultHtml;
    }

    function getJustAchievedBadgeList() {
        global $CFG;
        $resultHtml = '';

        foreach ($this->preset['coursesBadges'] as $key => $badge) {
            if ($badge['achievement'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
                $resultHtml .=
                '<li>
                    <figure>
                        <img src="' . $CFG->wwwroot . '/blocks/ludic_motivators/classes/motivators/badges/pix/' . $badge['iconId'] . '.png" title="' . $badge['badgeName'] . '"/>
                        <figcaption>' . $badge['badgeName'] . '</figcaption>
                    </figure>
                </li>';
            }
        }

        return $resultHtml;
    }

    function getJustAchievedBadgesSelect($selectedBadge){
        $textSelect  = '';
        foreach ($this->preset['coursesBadges'] as $key => $badge) {
            if ($badge['achievement'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                $selected = $badge['badgeName'] == $selectedBadge ? 'selected' : '';
                $textSelect .= '<option value="' . $badge['badgeName'] . '" ' . $selected . '>' . $badge['badgeName'] . '</option>';
            }
        }

        return $textSelect;
    }

    function isNewBadge() {
        return optional_param('badge', '', PARAM_TEXT) !== '';
    }

    function getSVGImage(){
        $fileIndex = count($this->preset['globalBadges']) - 1;
        return $this->image_url('LudiMoodle_pyramide_' . str_pad($fileIndex, 2, 0, STR_PAD_LEFT) . '.svg');
    }

    public function get_content() {
        global $CFG;

        $output  = '<div id="badges-container">';

        // Div block for course goals selected between the achieved and not achieved (opacity:0.3)
        $output .= '<div style="margin-bottom:15px;border:1px solid">
                    <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Course Goals</h4>
                        <ul id="course-badge">'
                            . $this->getCourseBadgeList() .
                        '</ul>
                    </div>';

        // Div block showing the bonus selector for the purpose of test
        $output .= '<div style="margin-bottom:15px;">
                        <form id="badges_form" method="POST">
                            <input id="motivator" name="motivator" type="hidden" value="badges">
                            <select name="badge" onChange="document.getElementById(\'badges_form\').submit()">
                                <option value="" selected>Badges à gagner</option>'
                                . $this->getJustAchievedBadgesSelect(optional_param('badge', '', PARAM_TEXT)) .
                            '</select>
                        </form>
                    </div>';

        // Div block for course badges that have just been achieved
        if ($this->isNewBadge()) {
        $output .= '<div style="margin-bottom:15px;border:1px solid">
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bravo !</h4>
                        <ul id="new-badge">'
                            . $this->getJustAchievedBadgeList() .
                        '</ul>
                    </div>';
        }

        // Div block showing the global badges selector for the purpose of test
        $output .= '<div style="margin-bottom:15px;">
                        <form id="globalBadges_form" method="POST">
                            <input id="motivator" name="motivator" type="hidden" value="badges">
                            <select name="globalBadge" onChange="document.getElementById(\'globalBadges_form\').submit()">
                                <option value="" selected>Badges globaux à gagner</option>'
                                . $this->getGlobalBadgesSelect(optional_param('globalBadge', '', PARAM_TEXT)) .
                            '</select>
                        </form>
                    </div>';

        // Div block displaying an SVG image representing the global badges with layers that
        // can be displayed to represent the goals that have been achieved
        $output  .= '<div id="avatar-div" style="margin-bottom:15px;border:1px solid">
                        <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Avatar</h4>
                        <div id="avatar-container">
                            <img src="' . $this->getSVGImage() . '"width="180px" height="180px" class="avatar svg" id="avatar-picture"/>
                        </div>
                    </div>';

        return $output;
    }


    public function getJsParams() {
        $datas = $this->context->store->get_datas();
        $params = array('previously_obtained' => array());

        foreach ($this->preset['globalBadges'] as $key => $value) {
            if ($value['achievement'] == $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
                $params['previously_obtained'][] = $value['layerName'];
            }
            if ($value['achievement'] == $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED) {
                $params['not_obtained'][] = $value['layerName'];
            }
        }

        if (isset($datas->avatar)) {
            $params = $datas->avatar;
        }

        return $params;
    }

}
