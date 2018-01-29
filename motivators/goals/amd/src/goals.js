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

define(['jquery', 'jqueryui', 'core/tree'], function ($, ui, Tree) {
    var goals = {
        init: function () {
//             console.log('Goals init');
//             var that = this;
//
//             $('#add-goal').on('click', function () {
//                 $('<div>Description de l\'objectif:<textarea id="new-goal"></textarea></div>').dialog({
//                     title : 'Ajouter un objectif',
//                     resizable: false,
//                     height: 230,
//                     width: 550,
//                     modal: true,
//                     buttons: {
//                         "Ajouter l\'objectif": function () {
//                             var new_goal = $('#new-goal').val();
//                             $('#goals').append('<li><label><input type="checkbox" />'+new_goal+'</label></li>');
//                             $(this).dialog("close");
//                         },
//                         "Annuler": function () {
//                             $(this).dialog("close");
//                         }
//                     }
//                 });
//             });

        }
    };
    return goals;
});
