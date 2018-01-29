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

require_once dirname(__DIR__, 5) . '/config.php';
?>

<html style="overflow: hidden">
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
                    ['Temps', {v: 0, f: '00:00'}]
                ]);

                var options = {
                    width: 190, height: 190,
                    greenFrom: parent.timingAttempts[(parent.timingAttempts.length-1)], greenTo: 45,
                    minorTicks: 5, max:45,
                    majorTicks : ['0','5','10','15','20','25','30','35','40','45']
                };

                var chart = new google.visualization.Gauge(document.getElementById('timer_div'));

                chart.draw(data, options);

                value = 0;
                timerId = setInterval(function () {
                    value = Math.max(0, value + 1);
                    data.setValue(0, 1, value/60);

                    // Formatting minutes and seconds for displaying digit
                    seconds = value % 60;
                    minutes = Math.floor(value / 60);
                    secondsFormatted = ("0" + seconds).slice(-2);
                    minutesFormatted = ("0" + minutes).slice(-2);
                    timeFormatted = minutesFormatted + ':' + secondsFormatted;
                    data.setFormattedValue(0, 1, timeFormatted);
                    chart.draw(data, options);

                    // Stop the timer after more than 2700 seconds (45 minutes)
                    if (value >= 2700) {
                        clearInterval(timerId);
                    }

                }, 1000);
            }

            function drawColumnChart() {
                var colors = ['red', 'orange', '#eee', 'green'];
                var params = parent.timingAttempts;
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
        <div id="timer_div" style="height: 190px;"></div>
        <div id="column_div" style="height: 300px;"></div>
    </body>
</html>
