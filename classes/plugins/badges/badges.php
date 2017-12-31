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

require_once $CFG->dirroot . '/blocks/ludic_motivators/classes/plugins/plugin_interface.php';

class badges extends iPLugin {

    public function getTitle() {
        return 'Mes badges';
    }

    public function get_content() {
        global $CFG;
        $output = '<div id="badges-container">';
        $output .= '<div class="ludic_motivators-badge"><img src="'.$CFG->wwwroot.'/blocks/ludic_motivators/classes/plugins/badges/pix/badge1.png" title="3 bonnes réponses"/></div>';
        $output .= '<div class="ludic_motivators-badge"><img src="'.$CFG->wwwroot.'/blocks/ludic_motivators/classes/plugins/badges/pix/badge2.png" title="10 bonnes réponses" style="display:none;"/></div>';
        $output .= '</div>';
        return $output;
    }

}
