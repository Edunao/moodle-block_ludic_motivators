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

class motivator_timer extends motivator_base implements motivator {

    public function get_loca_strings(){
        return [
            'name'  => 'Timer',
            'title' => 'My Time'
        ];
    }

    public function render($env) {
    }

//     public function __construct($context) {
//         $preset = array(
//             'introductionMessage' => 'Bravo, tu as réussi, maintenant avec un chrono, essaie de faire de ton mieux',
//             'maxDurationTimer' => 90,
//             'maxNumberAttempts' => 5,
//             'numberPreviousAttempts' => 3,
//             'timingAttempts' => [40, 60, 30],
//             'globalAchievements' => [
//                 'session1Objectives' => 0,
//                 'session2Objectives' => 1
//             ]
//         );
//         parent::__construct($context, $preset);
//     }
//
//     public function get_content() {
//         global $CFG;
//
//         $output = '';
//
//         if ($this->preset['numberPreviousAttempts'] != 0) {
//             $output = '<div id="timer-container">';
//
//             // Passing to the iFrame the timestamps and the last attempts timing
//             $output .= '<script type="text/javascript">' . PHP_EOL;
//             $output .= '    var timingAttempts = ' . json_encode($this->preset['timingAttempts']) . ';' . PHP_EOL;
//             $output .= '    var timestamp = ' . time() . ';' . PHP_EOL;
//             $output .= '</script>';
//
//             // Calling the iFrame file generating the gauge and the bargraph showing the classe average,
//             // the class best and the user's own level
//             $output .= '<iframe id="timer-iframe" frameBorder="0" src="'.$CFG->wwwroot.'/blocks/ludic_motivators/classes/motivators/timer/iframe.php"></iframe>';
//             $output .= '</div>';
//         }
//
//         return $output;
//     }
//
//         public function getJsParams() {
//         $datas = $this->context->store->get_datas();
//         $params = array();
//
//         $params['timingAttempts'] = $this->preset['timingAttempts'];
//         $params['timestamp'] = time();
//
//         if (isset($datas->avatar)) {
//             $params = $datas->avatar;
//         }
//
//         return $params;
//     }
}
