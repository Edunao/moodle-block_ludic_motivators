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

require_once \dirname2(__DIR__, 2) . '/classes/motivators/motivator.interface.php';
require_once \dirname2(__DIR__, 2) . '/classes/motivators/motivator_base.class.php';
require_once \dirname2(__DIR__, 2) . '/locallib.php';

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
            'pyramid_done' => []
        ];

        // fetch config and associated stat data
        $coursename     = $env->get_course_name();
        $courseconfig   = $env->get_course_config($this->get_short_name(), $coursename);
        $coursedata     = $env->get_course_state_data($courseconfig, $coursename);
        $fullconfig     = $env->get_full_config($this->get_short_name());
        $fulldata       = $env->get_full_state_data($fullconfig);

        // match up the config elements and state data to determine the set of information to pass to the javascript
        foreach ($fullconfig as $element){
            if ($element['motivator']['subtype'] !== 'global'){
                continue;
            }
            $dataname = $element['course'] . '/' . array_keys($element['stats'])[0];
            if (isset($fulldata[$dataname])){
                $statevalue = $fulldata[$dataname];
                switch ($statevalue){
                case STATE_JUST_ACHIEVED:
                case STATE_ACHIEVED:
                    $jsdata['pyramid_done'][] = $element['motivator']['layer'];
                    break;
//                 default:
//                     $jsdata['pyramid_todo'][] = $element['motivator']['layer'];
//                     break;
                }
            }
        }

        // lookup the course count which we configure via a fake stat
        $env->bomb_if(!isset($fulldata['/course_count']),"/course_count not found in state data");
        $coursecount = $fulldata['/course_count'];

        $badgeicons = '';
        $newbadgeicons = '';
        // match up the config elements and state data to determine the set of information to pass to the javascript
        foreach ($courseconfig as $element){
            if ($element['motivator']['subtype'] !== 'course'){
                continue;
            }
            $dataname = $coursename . '/' . array_keys($element['stats'])[0];
            if (array_key_exists($dataname,$coursedata)){
                $imageurl = $this->image_url($element['motivator']['icon']);
                $statevalue = $coursedata[$dataname];
                switch ($statevalue){
                case STATE_JUST_ACHIEVED:
                    $newbadgeicons  .= "<img src='" . $imageurl . "_actif.svg' class='ludi-badge ludi-new'/>";
                    $badgeicons     .= "<img src='" . $imageurl . "_actif.svg' class='ludi-badge ludi-new'/>";
                    break;
                case STATE_ACHIEVED:
                    $badgeicons     .= "<img src='" . $imageurl . "_actif.svg' class='ludi-badge ludi-old'/>";
                    break;
                case STATE_NOT_ACHIEVED:
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
        $env->render('ludi-main', $this->get_string('full_title'), '<div class="ludi-course-badges">' . $badgeicons . '</div>');
        if (!empty($newbadgeicons)){
            $env->render('ludi-change', $this->get_string('changes_title'), '<div class="ludi-course-badges">' . $newbadgeicons . '</div>');
        }

        // render the pyramid image
        $pyramidname    = sprintf("pyramide_%02d.svg", $coursecount);
        $imageurl       = $this->image_url($pyramidname);
        $imagehtml      = "<div class='ludi-pyramid-container'><img src='$imageurl' class='svg' id='ludi-pyramid'/></div>";
        $env->render('ludi-main', $this->get_string('pyramid_title'), $imagehtml);
    }
}
