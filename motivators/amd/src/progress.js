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
            console.log('Progress init', params);

            var that = this;

            this.params = params;

            this.revealed_layers = this.params.revealed_layers;

            // Convert SVG (<img>) in to raw SVG code (<svg>)
            this.convert_svg('img.svg.avatar');

            // Set visible the layer (items) that are newly obtained
            /*$.each(this.revealed_layers, function( branchId, arrayLayer ) {
                branchId = branchId+1;
                if (arrayLayer.length == 0) {
                    console.log('branche vide');
                    return true;
                } else {
                    $.each(arrayLayer, function (layerId, layerName) {
                        layerSelector = $('#branch-picture'+branchId+' #'+layerName);
                        console.log('layer=',layerSelector);
                        layerSelector.css('visibility', 'visible');
                    });
                }
            });*/

            // Set visible only the layers (items) that are newly obtained
            $.each(this.revealed_layers, function( branchId, layerName ) {
                branchId = branchId+1;
                if (layerName.length == 0) {
                    console.log('branche vide');
                    return true;
                } else {
                    layerSelector = $('#branch-picture'+branchId+' #'+layerName);
                    console.log('layer=',layerSelector);
                    layerSelector.css('visibility', 'visible');
                }
            });

            $('#branch-div').css('display', 'block')
        },

        convert_svg : function(selector) {
            //convert svg pictures
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

