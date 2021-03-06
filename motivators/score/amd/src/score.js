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

define(['jquery', 'core/tree'], function ($, Tree) {
    var score = {
        init: function (params) {
            console.log('Score init', params);

            /*var that = this;

            this.params = params;

            this.previous_score = this.params.previous_score;
            this.new_score = this.params.new_score;

            $({someValue: this.previous_score}).animate({someValue: this.new_score}, {
                duration: 1000,
                easing:'swing', // can be anything
                step: function() { // called on every steps
                    $('.score-number').text(Math.ceil(this.someValue));
                }
            });*/

        }
    };
    return score;
});
