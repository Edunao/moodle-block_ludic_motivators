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
            console.log('Progress init: ', params);

            // Convert SVG (<img>) in to raw SVG code (<svg>)
            ludicMotivators.convert_svg('img.svg');

            // unhide selected layers
            for ( imageId in params.revealed_layers ){
                layerName = params.revealed_layers[ imageId ];
                if ( layerName !== "" ){
                    layerSelector = $( '#' + imageId + ' #' + layerName );
                    layerSelector.css( 'visibility', 'visible' );
                }
                // unhide the image
                $( '#' + imageId ).slideDown();
            }
        },
    };

    return avatar;
});

