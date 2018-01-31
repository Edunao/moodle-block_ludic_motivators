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


google.charts.load('current', {'packages': ['gauge', 'corechart']});
google.charts.setOnLoadCallback(drawColumnChart);

function drawColumnChart() {
    var colors = ['red', 'orange', '#eee', 'green'];
    var params = parent.ludiTimer.past_times;
    var data = new google.visualization.DataTable();

    // Declare columns
    data.addColumn('string', 'Element');    // Implicit domain column.
    data.addColumn('number', 'Timing');     // Implicit data column.
    data.addColumn({type:'string', role: 'style'});

    params.forEach(function(element, index) {
        data.addRow([(index+1).toString(), params[index], colors[index]]);
    });

    var view = new google.visualization.DataView(data);
    view.setColumns([0, 1,
        {calc: "stringify",
            sourceColumn: 1,
            type: "string",
            role: "annotation"},
        2]
    );

    var options = {
//        title: parent.ludiTimer.past_times_key,
        width: 190,
        height: 300,
        bar: {groupWidth: "95%"},
        legend: {position: "none"}
    };
    var chart = new google.visualization.ColumnChart(document.getElementById("column_div"));
    chart.draw(view, options);
}
