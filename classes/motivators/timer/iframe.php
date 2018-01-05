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

require_once '../../../../../config.php';
?>

<html>
    <head>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['gauge', 'corechart']});
            google.charts.setOnLoadCallback(drawCharts);

            function drawCharts() {
                drawGauge();
                drawColumnChart();
            }

            function drawGauge() {

                var data = google.visualization.arrayToDataTable([
                    ['Label', 'Value'],
                    ['Temps', 100]
                ]);

                var options = {
                    width: 400, height: 120,
                    redFrom: 0, redTo: 25,
                    yellowFrom: 25, yellowTo: 50,
                    greenFrom: 75, greenTo: 100,
                    minorTicks: 5
                };

                var chart = new google.visualization.Gauge(document.getElementById('timer_div'));

                chart.draw(data, options);

                value = 100;
                setInterval(function () {
                    value = Math.max(0, value - 1);
                    data.setValue(0, 1, value);
                    chart.draw(data, options);

                }, 500);
            }

            function drawColumnChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Element', 'Density', {role: 'style'}],
                    ['1', 8.94, 'red'],
                    ['2', 10.49, 'orange'],
                    ['3', 19.30, '#eee'],
                    ['4', 21.45, 'green']
                ]);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                    {calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation"},
                    2]);

                var options = {
                    title: "Progression des tentatives",
                    width: 200,
                    height: 300,
                    bar: {groupWidth: "95%"},
                    legend: {position: "none"}
                };
                var chart = new google.visualization.ColumnChart(document.getElementById("column_div"));
                chart.draw(view, options);
            }
        </script>
    </head>
    <body>
        <div id="timer_div" style="height: 120px;"></div>
        <div id="column_div" style="height: 300px;"></div>
    </body>
</html>
