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
require_once dirname(dirname(__DIR__)) . '/locallib.php';

class motivator_progress extends motivator_base implements i_motivator {

    public function get_loca_strings(){
        return [
            'name'  => 'Progress',
            'title' => 'My progress'
        ];
    }

    public function render($env) {
        // fetch config and associated stat data
        $courses        = $env->get_courses();
        $systemconfig   = $env->get_motivator_config($this->get_short_name());

        // perform a few sanity tests on config data
        $env->bomb_if(empty($courses),'No course list has been defined');
        foreach(['course_layers', 'global_background_images', 'global_course_images', 'local_course_images'] as $nodename){
            $env->bomb_if(!isset($systemconfig[$nodename]),'Node not found in configuration: ' . $nodename);
        }
        $env->bomb_if(count($systemconfig['global_course_images']) < count($courses), 'Too few global_course_image definitions found for course list');

        // prime the jsdata and images result containers
        $jsdata         = [];
        $images         = [];
        $backgrounds    = [];

        // lookup the current course & section to see if we're in a tracked course page or not
        $coursename     = $env->get_course_name();
        $courseindex    = array_search($coursename, $courses);
        $sectionid      = ($courseindex === false) ? 0 : $env->get_section_id();

        if ($sectionid){
            // deal with the detail view ...

            // lookup and evaluate the contextual configuration
            $ctxtconfig     = $env->get_contextual_config($this->get_short_name(), $coursename, $sectionid);
            $ctxtdata       = $env->get_contextual_state_data($ctxtconfig, $coursename, $sectionid);
            $progress       = $ctxtdata[$coursename . '/current_progress'];

            // determine the local image file name
            $localimages    = $systemconfig['local_course_images'];
            $localimageidx  = $courseindex % count($localimages);
            $localimage     = $localimages[$localimageidx];

            // determine the progression step
            $best       = -1;
            $layername  = '';
            foreach ($systemconfig['course_layers'] as $lyr => $threshold){
                if ($threshold > $best && $threshold <= $progress){
                    $best       = $threshold;
                    $layername  = $lyr;
                }
            }

            // store away the jsdata and image list
            $images = ['ludi_course_image' => $localimage];
            $jsdata = ['ludi_course_image' => $layername];
        } else {
            // deal with the global view ...

            // lookup and evaluate the global configuration
            $globalconfig   = $env->get_global_config($this->get_short_name());
print_object($globalconfig);
            $globaldata     = $env->get_global_state_data($globalconfig);
            $backgrounds    = $systemconfig['global_background_images'];

            // iterate over the
            for ($i=0; $i < count($courses); ++$i){
                // lookup the course basics
                $course         = $courses[$i];
                $progresskey    = $course . '/progress';
                $progress       = array_key_exists($progresskey, $globaldata) ? $globaldata[$progresskey] : 0;
                $courseimages   = $systemconfig['global_course_images'];
                $courseimage    = $courseimages[$i];

                // determine the progression step
                $best       = -1;
                $layername  = '';
                foreach ($systemconfig['course_layers'] as $lyr => $threshold){
                    if ($threshold > $best && $threshold <= $progress){
                        $best       = $threshold;
                        $layername  = $lyr;
                    }
                }

                // store away the jsdata and image list
                $imageid = sprintf('ludi_progress_part_%02d', $i);
                $images[$imageid] = $courseimage;
                $jsdata[$imageid] = $layername;
            }
        }

        // render the output
        $env->set_block_classes('luditype-progress');
        $html = '';
        foreach ($backgrounds as $filename){
            $imageurl = $this->image_url($filename);
            $html .= "<img src='" . $imageurl . "' class='ludi-progress-background ludi-progress-image'/>";
        }
        foreach ($images as $imageid => $filename){
            $imageurl = $this->image_url($filename);
            $html .= "<img src='" . $imageurl . "' class='svg ludi-progress-image' id='$imageid'/>";
        }
        $env->render('ludi-progress', $this->get_string('title'), $html);

        // register the js data
        $env->set_js_init_data($this->get_short_name(), ['revealed_layers' => $jsdata ]);

    }
}
