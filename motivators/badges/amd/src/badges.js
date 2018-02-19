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
    var badges = {
        init: function (params) {
            console.log('Badges init', params);

            // Convert SVG (<img>) in to raw SVG code (<svg>)
            ludicMotivators.convert_svg('.ludi-body img.svg');

            // Set visible the layer (badges) that are previously obtained
            $.each(params.global_done, function( index, value ) {
                layer = $('#ludi-overview #'+value);
                layer.css('visibility', 'visible');
            });

            // unhide the image
            $('#ludi-overview').slideDown();
        },
    };

    return badges;
});

