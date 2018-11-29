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
    ludicMotivators = {
        init: function (motivator, params) {
            var that = this;

            // block display
            this.resize_block();
            $(window).resize(function () {
                that.resize_block();
            });

            require(['/blocks/ludic_motivators/motivators/' + motivator + '/amd/src/' + motivator + '.js'], function (motivator) {
                motivator.init(params);
            });
        },
        resize_block: function () {

            // small screens => Display the block before the page content on small screens
            if (window.innerWidth <= 1199) {

                $('body').addClass('small-screen');
                var quizblock = $('#mod_quiz_navblock');
                var sidecolumnblocks = $('#block-region-side-pre .block:visible');

                // 2 blocks => flex display
                if (quizblock.length > 0 && sidecolumnblocks.length == 2) {
                    $('#block-region-side-pre').css('display', 'flex');
                    quizblock.css('width', '35%');
                    quizblock.css('margin-right', '3%');
                    $('.block_ludic_motivators').css('width', '62%');
                }
            }
            // big screens => normal display
            else {
                $('body').removeClass('small-screen');
            }

        },

        /*
         * Convert SVG (<img>) in to raw SVG code (<svg>)
         */
        convert_svg: function (selector) {
            $(selector).each(function () {
                var $img = $(this);
                var imgID = $img.attr('id');
                var imgClass = $img.attr('class');
                var imgURL = $img.attr('src');
                console.log("cleaning svg: ", imgURL);

                $.get({url: imgURL, async: false}, function (data) {
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

    return ludicMotivators;
});

