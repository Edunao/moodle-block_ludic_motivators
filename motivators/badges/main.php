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

require_once dirname(dirname(__DIR__)) . '/classes/base_classes/motivator.interface.php';
require_once dirname(dirname(__DIR__)) . '/classes/base_classes/motivator_base.class.php';

class motivator_badges extends motivator_base implements i_motivator {

    public function get_loca_strings(){
        return [
            'name'          => 'Badge',
            'title'         => 'Badges',
            'full_title'    => 'Badges',
            'changes_title' => 'New Badges',
            'pyramid_title' => 'Acquired Competencies',
        ];
    }

    public function render($env) {
        // prime a jsdata object with the different tables that we're going to provide to the JS script
        $jsdata = [
            'global_done' => []
        ];

        // fetch config and associated stat data
        $systemconfig   = $env->get_motivator_config($this->get_short_name());
        $coursename     = $env->get_course_name();
        $sectionidx     = $env->get_section_idx();
        $ctxtconfig     = $env->get_contextual_config($this->get_short_name(), $coursename, $sectionidx);
        $ctxtdata       = $env->get_contextual_state_data($ctxtconfig, $coursename, $sectionidx);
        $globalconfig   = $env->get_global_config($this->get_short_name());
        $globaldata     = $env->get_global_state_data($globalconfig);

        // perform a few sanity tests on config data
        foreach(['global_images', 'global_layers'] as $nodename){
            $env->bomb_if(!isset($systemconfig[$nodename]),'Node not found in configuration: ' . $nodename);
        }

        // match up the config elements and state data to determine the set of information to pass to the javascript
        $idx = 0;
        foreach ($globalconfig as $element){
            if ($element['motivator']['subtype'] !== 'global'){
                continue;
            }
            $dataname = $element['course'] . '/' . array_keys($element['stats'])[0];
            if (isset($globaldata[$dataname])){
                $statevalue = $globaldata[$dataname];
                switch ($statevalue){
                case STATE_JUST_ACHIEVED:
                case STATE_ACHIEVED:
                    $jsdata['global_done'][] = $systemconfig['global_layers'][$idx];
                    ++$idx;
                    break;
                }
            }
        }

        // lookup the course count which we configure via a fake stat
        $env->bomb_if(!isset($globaldata['/context_count']),"/context_count not found in state data");
        $contextcount = $globaldata['/context_count'];

        $badgeicons = '';
        $newbadges = [];
        // match up the config elements and state data to determine the set of information to pass to the javascript
        foreach ($ctxtconfig as $element){
            if ($element['motivator']['subtype'] !== 'local'){
                continue;
            }
            $dataname = $coursename . ($sectionidx > -1 ? "#$sectionidx" : '') . '/' . array_keys($element['stats'])[0];
            if (array_key_exists($dataname,$ctxtdata)){
                $title      = $element['motivator']['title'];
                $imageurl   = $this->image_url($element['motivator']['icon']);
                $statevalue = $ctxtdata[$dataname];
                switch ($statevalue){
                case STATE_JUST_ACHIEVED:
                    $newbadges[$title]  = "<img src='" . $imageurl . "_actif.svg' class='ludi-badge ludi-new'/>";
//                    $badgeicons         .= "<img src='" . $imageurl . "_actif.svg' class='ludi-badge ludi-new'/>";
                    break;
                case STATE_ACHIEVED:
                    $badgeicons         .= "<div class='ludi-named-badge'>";
                    $badgeicons         .= "<img src='" . $imageurl . "_actif.svg' class='ludi-badge ludi-old'/>";
                    $badgeicons         .= "<div class='ludi-badge-name ludi-old'>$title</div>";
                    $badgeicons         .= "</div>";
                    break;
                case STATE_NOT_ACHIEVED:
                    $badgeicons         .= "<div class='ludi-named-badge'>";
                    $badgeicons         .= "<img src='" . $imageurl . "_inactif.svg' class='ludi-badge ludi-todo'/>";
                    $badgeicons         .= "<div class='ludi-badge-name ludi-old'>$title</div>";
                    $badgeicons         .= "</div>";
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
        if (!empty($badgeicons)){
            $env->render('ludi-main ludi-detail', $this->get_string('full_title'), '<div class="ludi-course-badges">' . $badgeicons . '</div>');
        }
        foreach ($newbadges as $title => $icon){
            $env->render('ludi-change ludi-detail', $title, '<div class="ludi-course-badges">' . $icon . '</div>');
        }

        // render the global image
        $imgidx         = min($contextcount, count($systemconfig['global_images']));
        $imgname        = sprintf($systemconfig['global_images'][$imgidx]);
        $imageurl       = $this->image_url($imgname);
        $imagehtml      = "<div class='ludi-overview-container'><img src='$imageurl' class='svg' id='ludi-overview'/></div>";
        $env->render('ludi-main ludi-overview', $this->get_string('pyramid_title'), $imagehtml);
    }
}
