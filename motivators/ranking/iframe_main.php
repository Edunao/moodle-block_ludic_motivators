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

require_once dirname(__DIR__, 5)  . '/config.php';
global $CFG;
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="<?=$CFG->wwwroot?>/blocks/ludic_motivators/classes/motivators/css/ranking.css">
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['gauge', 'corechart']});
            google.charts.setOnLoadCallback(drawCharts);

            function drawCharts() {
                drawScoreChart();
            }

            function drawScoreChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Element', 'Score', {role: 'style'}],
                    ['Average score', parent.classAverage, 'red'],
                    ['Best score', parent.bestScore, 'orange'],
                    ['My Score', parent.userScore, '#eee'],
                ]);

                var view = new google.visualization.DataView(data);
                view.setColumns([0, 1,
                    {calc: "stringify",
                        sourceColumn: 1,
                        type: "string",
                        role: "annotation"},
                    2]);

                var options = {
                    title: "Course Ranking",
                    width: 200,
                    height: 280,
                    bar: {groupWidth: "95%"},
                    legend: {position: "none"}
                };
                var chart = new google.visualization.ColumnChart(document.getElementById("bargraph_div"));
                chart.draw(view, options);
            }
        </script>
    </head>
    <body>
        <div class="bargraph" id="bargraph_div"></div>

        <div id="ranking-bravo">
            <h4 style="background-color:#6F5499;color:#CDBFE3;text-align:center;margin:20 0 0 0">Bravo !</h4>
        </div>

    </body>

    <script type="text/javascript">
        console.log(parent.isFirstRank);
        if (parent.isFirstRank) {
            document.getElementById('ranking-bravo').style.display = 'block';
        }
    </script>
</html>