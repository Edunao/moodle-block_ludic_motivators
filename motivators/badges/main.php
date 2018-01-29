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

class motivator_badges extends motivator_base implements motivator {

    public function get_loca_strings(){
        return [
            'name'          => 'Badge',
            'title'         => 'Badges',
            'full_title'    => 'Course Badges',
            'changes_title' => 'New Badges',
            'pyramid_title' => 'Acquired Competencies',
        ];
    }

    public function render($env) {
        // prime a jsdata object with the different tables that we're going to provide to the JS script
        $jsdata = [
            'pyramid_layers' => []
        ];

        // fetch config and associated stat data
        $coursename     = $env->get_course_name();
        $courseconfig   = $env->get_course_config($this->get_short_name(), $coursename);
        $coursedata     = $env->get_course_state_data($courseconfig, $coursename);
        $fullconfig     = $env->get_full_config($this->get_short_name());
        $fulldata       = $env->get_full_state_data($fullconfig);

echo "<h1>Full Data</h1>";
// print_object($fullconfig);
// print_object($fulldata);
        // match up the config elements and state data to determine the set of information to pass to the javascript
        foreach ($fullconfig as $element){
            if ($element['motivator']['subtype'] !== 'global'){
                continue;
            }
            $dataname = $element['course'] . '/' . array_keys($element['stats'])[0];
echo "Checking $dataname<br>";
            if (isset($fulldata[$dataname])){
                $statevalue = $fulldata[$dataname];
                switch ($statevalue){
                case STATE_JUST_ACHIEVED:
                case STATE_ACHIEVED:
echo "- achieved<br>";
                    $jsdata['pyramid_layers'][] = $element['motivator']['layer'];
                    break;
                }
            }
        }

        // lookup the course count which we configure via a fake stat
        $env->bomb_if(!isset($fulldata['/course_count']),"/course_count not found in state data");
        $coursecount = $fulldata['/course_count'];

echo "<h1>Course Data</h1>";
// print_object($courseconfig);
// print_object($coursedata);
        $badgeicons = '';
        $newbadgeicons = '';
        // match up the config elements and state data to determine the set of information to pass to the javascript
        foreach ($courseconfig as $element){
            if ($element['motivator']['subtype'] !== 'course'){
                continue;
            }
            $dataname = $coursename . '/' . array_keys($element['stats'])[0];
echo "Checking $dataname<br>";
            if (isset($coursedata[$dataname])){
                $imageurl = $this->image_url($element['motivator']['icon']);
                $statevalue = $coursedata[$dataname];
                switch ($statevalue){
                case STATE_JUST_ACHIEVED:
echo "- newly obtained<br>";
                    $newbadgeicons  .= "<img src='" . $imageurl . "_actif.svg' class='ludi-badge ludi-new'/>";
                    $badgeicons     .= "<img src='" . $imageurl . "_actif.svg' class='ludi-badge ludi-new'/>";
                    break;
                case STATE_ACHIEVED:
echo "- obtained<br>";
                    $badgeicons     .= "<img src='" . $imageurl . "_actif.svg' class='ludi-badge ludi-old'/>";
                    break;
                case STATE_NOT_ACHIEVED:
echo "- not obtained<br>";
                    $badgeicons     .= "<img src='" . $imageurl . "_inactif.svg' class='ludi-badge ludi-todo'/>";
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

        // prepare to start rendering content
        $env->set_block_classes('luditype-badges');

        // render the badge images
        $env->render($this->get_string('full_title'), '<div class="ludi-course-badges">' . $badgeicons . '</div>');
        if (!empty($newbadgeicons)){
            $env->render($this->get_string('changes_title'), '<div class="ludi-course-badges">' . $newbadgeicons . '</div>');
        }

        // render the pyramid image
        $pyramidname    = sprintf("pyramide_%02d.svg", $coursecount);
        $imageurl       = $this->image_url($pyramidname);
        $imagehtml      = "<div class='ludi-pyramid-container'><img src='$imageurl' class='svg' id='ludi-pyramid'/></div>";
        $env->render($this->get_string('pyramid_title'), $imagehtml);
    }


//     public function __construct($context) {
//
//         // Updating course badges array in the preset array when a badge is selected
//         if (($newBadge = optional_param('badge', '', PARAM_TEXT)) !== '') {
//             foreach ($preset['coursesBadges'] as $key => $badge) {
//                 if ($badge['badgeName'] === $newBadge) {
//                     $preset['coursesBadges'][$key]['stat'] = $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED;
//                 }
//             }
//         }
//
//         // Updating global badges array in the preset array when a badge is selected
//         if (($globalBadge = optional_param('globalBadge', 0, PARAM_TEXT)) !== 0) {
//             $i = 1;
//             do {
//                 $preset['globalBadges'][$i]['stat'] = $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED;
//                 $i++;
//             } while ($i <= $globalBadge);
//         }
//
//         parent::__construct($context, $preset);
//     }
//
//     public function getTitle() {
//
//         return 'Mes badges';
//     }
//
//     function getGlobalBadgeList() {
//         global $CFG;
//         $resultHtml = '';
//
//         foreach ($this->preset['globalBadges'] as $key => $badge) {
//             if ($badge['stat'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
//                 $resultHtml .=
//                 '<li style="opacity:' . $opacity . '">
//                     <figure>
//                         <img src="' . $CFG->wwwroot . '/blocks/ludic_motivators/classes/motivators/badges/pix/' . $badge['iconId'] . '.png" title="' . $badge['badgeName'] . '"/>
//                         <figcaption>' . $badge['badgeName'] . '</figcaption>
//                     </figure>
//                 </li>';
//             }
//         }
//
//         return $resultHtml;
//     }
//
//     function getGlobalBadgesSelect($selectedBadge){
//         $globalBadges = $this->preset['globalBadges'];
//         $textSelect  = '<option value="' . $selectedBadge . '" selected> Etape ' . $selectedBadge . '</option>';
//         foreach ($globalBadges as $key => $badge) {
//             if ($badge['stat'] === $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED) {
//                 $textSelect .= '<option value="' . $key . '""> Etape ' . $key . '</option>';
//             }
//         }
//
//         return $textSelect;
//     }
//
//     function getCourseBadgeList() {
//         global $CFG;
//         $resultHtml = '';
//
//         foreach ($this->preset['coursesBadges'] as $key => $badge) {
//             if ($badge['stat'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
//                 $opacity = ($badge['stat'] === $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED) ? "0.5" : "1";
//                 $resultHtml .=
//                 '<li style="opacity:' . $opacity . '">
//                     <figure>
//                         <img src="' . $CFG->wwwroot . '/blocks/ludic_motivators/classes/motivators/badges/pix/' . $badge['iconId'] . '.png" title="' . $badge['badgeName'] . '"/>
//                         <figcaption>' . $badge['badgeName'] . '</figcaption>
//                     </figure>
//                 </li>';
//             }
//         }
//
//         return $resultHtml;
//     }
//
//     function getJustAchievedBadgeList() {
//         global $CFG;
//         $resultHtml = '';
//
//         foreach ($this->preset['coursesBadges'] as $key => $badge) {
//             if ($badge['stat'] === $this::BLOCK_LUDICMOTIVATORS_STATE_JUSTACHIEVED){
//                 $resultHtml .=
//                 '<li>
//                     <figure>
//                         <img src="' . $CFG->wwwroot . '/blocks/ludic_motivators/classes/motivators/badges/pix/' . $badge['iconId'] . '.png" title="' . $badge['badgeName'] . '"/>
//                         <figcaption>' . $badge['badgeName'] . '</figcaption>
//                     </figure>
//                 </li>';
//             }
//         }
//
//         return $resultHtml;
//     }
//
//     function getJustAchievedBadgesSelect($selectedBadge){
//         $textSelect  = '';
//         foreach ($this->preset['coursesBadges'] as $key => $badge) {
//             if ($badge['stat'] !== $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
//                 $selected = $badge['badgeName'] == $selectedBadge ? 'selected' : '';
//                 $textSelect .= '<option value="' . $badge['badgeName'] . '" ' . $selected . '>' . $badge['badgeName'] . '</option>';
//             }
//         }
//
//         return $textSelect;
//     }
//
//     function isNewBadge() {
//         return optional_param('badge', '', PARAM_TEXT) !== '';
//     }
//
//     function getSVGImage(){
//         $fileIndex = optional_param('globalBadge', 0, PARAM_TEXT);
//         $fileIndex = $fileIndex == 14 ? $fileIndex : $fileIndex+1;
//
//         return $this->image_url('LudiMoodle_pyramide_' . str_pad($fileIndex, 2, 0, STR_PAD_LEFT) . '.svg');
//     }
//
//     public function get_loca_strings(){
//         return [
//             'name'  => 'Badges',
//             'title' => 'Course Goals'
//         ];
//     }
//
//     public function get_content() {
//         global $CFG;
//
//         $output  = '<div id="badges-container">';
//
//         // Div block for course goals selected between the achieved and not achieved (opacity:0.3)
//         $output .= '<div style="margin-bottom:15px;border:1px solid">
//                     <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Course Goals</h4>
//                         <ul id="course-badge">'
//                             . $this->getCourseBadgeList() .
//                         '</ul>
//                     </div>';
//
//         // Div block showing the bonus selector for the purpose of test
//         $output .= '<div style="margin-bottom:15px;">
//                         <form id="badges_form" method="POST">
//                             <input id="motivator" name="motivator" type="hidden" value="badges">
//                             <select name="badge" onChange="document.getElementById(\'badges_form\').submit()">
//                                 <option value="" selected>Badges à gagner</option>'
//                                 . $this->getJustAchievedBadgesSelect(optional_param('badge', '', PARAM_TEXT)) .
//                             '</select>
//                         </form>
//                     </div>';
//
//         // Div block for course badges that have just been achieved
//         if ($this->isNewBadge()) {
//         $output .= '<div style="margin-bottom:15px;border:1px solid">
//                         <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Bravo !</h4>
//                         <ul id="new-badge">'
//                             . $this->getJustAchievedBadgeList() .
//                         '</ul>
//                     </div>';
//         }
//
//         // Div block showing the global badges selector for the purpose of test
//         $output .= '<div style="margin-bottom:15px;">
//                         <form id="globalBadges_form" method="POST">
//                             <input id="motivator" name="motivator" type="hidden" value="badges">
//                             <select name="globalBadge" onChange="document.getElementById(\'globalBadges_form\').submit()">
//                                 <option value="0" selected>Badges globaux à gagner</option>'
//                                 . $this->getGlobalBadgesSelect(optional_param('globalBadge', 0, PARAM_TEXT)) .
//                             '</select>
//                         </form>
//                     </div>';
//
//         // Div block displaying an SVG image representing the global badges with layers that
//         // can be displayed to represent the goals that have been achieved
//         $output  .= '<div id="avatar-div" style="margin-bottom:15px;border:1px solid">
//                         <h4 style="background-color: #6F5499;color: #CDBFE3;text-align: center;">Avatar</h4>
//                         <div id="avatar-container">
//                             <img src="' . $this->getSVGImage() . '"width="180px" height="180px" class="avatar svg" id="avatar-picture"/>
//                         </div>
//                     </div>';
//
//         return $output;
//     }
//
//
//     public function get_js_params() {
//         $datas = $this->context->store->get_datas();
//         $params = array('previously_obtained' => array());
//
//         foreach ($this->preset['globalBadges'] as $key => $value) {
//             if ($value['stat'] == $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED) {
//                 $params['previously_obtained'][] = $value['layerName'];
//             }
//             if ($value['stat'] == $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED) {
//                 $params['not_obtained'][] = $value['layerName'];
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









//         $preset = array(
//             'coursesBadges' => [
//                 [
//                     'badgeName' => '3 bonnes réponses',
//                     'iconId' => 'course_badge1',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                 ],
//                 [
//                     'badgeName' => 'Course Goal 2',
//                     'iconId' => 'course_badge2',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'badgeName' => '10 bonnes réponses',
//                     'iconId' => 'course_badge2',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                 ],
//                 [
//                     'badgeName' => 'Course Goal 4',
//                     'iconId' => 'course_badge1',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'badgeName' => 'Course Goal 5',
//                     'iconId' => 'course_badge1',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ]
//             ],
//             'globalBadges' => [
//                 [
//                     'layerName' => 'calque00',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_PREVIOUSLYACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque01',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque02',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque03',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque04',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque05',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque06',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque07',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque08',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque09',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque10',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque11',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque12',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque13',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//                 [
//                     'layerName' => 'calque14',
//                     'stat' => $this::BLOCK_LUDICMOTIVATORS_STATE_NOTACHIEVED,
//                 ],
//             ],
//         );
