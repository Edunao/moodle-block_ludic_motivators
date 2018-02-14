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
google.charts.setOnLoadCallback(drawGauge);

function drawGauge() {

    var data = google.visualization.arrayToDataTable([
        ['Label', 'Value'],
        [parent.ludiTimer.key_time, {v: 0, f: '00:00'}]
    ]);

    var pastTimes = parent.ludiTimer.past_times;
    var bestTime = pastTimes[0];
    for (var i = 1; i < pastTimes.length; i++) {
        if ( pastTimes[i] === 0 ){
            continue;
        }
        bestTime = Math.min( bestTime, pastTimes[i] );
    }
    bestTime /= 60;
    graphMax = Math.round( 2 * bestTime );
    var options = {
        width: 190,
        height: 190,
        max: graphMax,
        yellowFrom: bestTime, yellowTo: 1.5 * bestTime,
        redFrom: 1.5 * bestTime, redTo: 2 * bestTime,
        minorTicks: 5,
    };

    var chart = new google.visualization.Gauge(document.getElementById('timer_div'));

    chart.draw(data, options);

    var value = parent.ludiTimer.time_to_date;
    timerId = setInterval(function () {
        value = Math.max(0, value + 1);
        data.setValue(0, 1, Math.min(graphMax, value/60));

        // Formatting minutes and seconds for displaying digit
        var seconds = value % 60;
        var minutes = Math.floor(value / 60);
        var secondsFormatted = ("0" + seconds).slice(-2);
        var minutesFormatted = ("0" + minutes).slice(-2);
        var timeFormatted = minutesFormatted + ':' + secondsFormatted;
        data.setFormattedValue(0, 1, timeFormatted);
        chart.draw(data, options);
    }, 1000);
}
