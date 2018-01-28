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
    var avatar = {
        init: function (params) {
            console.log('Avatar init', params);

            var that = this;

            this.params = params;

            this.obtained = this.params.obtained;
            this.newly_obtained = this.params.newly_obtained;

            // Convert SVG (<img>) in to raw SVG code (<svg>)
            this.convert_svg('img.svg.avatar');

            // Set visible the layer (items) that are previously and newly obtained
            $.each(this.obtained, function( index, value ) {
                layer = $('#avatar-picture #'+value);
                layer.css('visibility', 'visible');
            });

            $('#avatar-div').css('display', 'block')

            // Set visible the layer (items) that are newly obtained
            $.each(this.newly_obtained, function( index, value ) {
                layer = $('#element-picture #'+value);
                layer.css('visibility', 'visible');
            });

            $('#element-div').css('display', 'block')
        },

        /*
         * Convert SVG (<img>) in to raw SVG code (<svg>)
         */
        convert_svg : function(selector) {

            $(selector).each(function () {
                var $img = $(this);
                var imgID = $img.attr('id');
                var imgClass = $img.attr('class');
                var imgURL = $img.attr('src');

                $.get({url : imgURL, async : false}, function (data) {
                    var $svg = $(data).find('svg');
                    if (typeof imgID !== 'undefined') {
                        $svg = $svg.attr('id', imgID);
                    }
                    if (typeof imgClass !== 'undefined') {
                        $svg = $svg.attr('class', imgClass + ' replaced-svg');
                    }
                    $svg = $svg.removeAttr('xmlns:a');
                    $img.replaceWith($svg);
                });
            });
        },
    };

    return avatar;
});
