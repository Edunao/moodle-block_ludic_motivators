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

require_once dirname(dirname(__DIR__)) . '/config.php';

// setup wish lists of log fields to fetch
$logsu = [
    "login"             => [ "core", "loggedin", "user" ],
    "dashboard"         => [ "core", "viewed", "dashboard" ],
];

$logsuc = [
    "course_pageview"   => [ "core", "viewed", "course" ],
    "quiz_start"        => [ "mod_quiz", "started", "attempt" ],
    "quiz_moduleview"   => [ "mod_quiz", "viewed", "course_module" ],
];

$logsucq = [
    "quiz_review"       => [ "mod_quiz", "reviewed", "attempt" ],
    "quiz_submit"       => [ "mod_quiz", "submitted", "attempt" ],
    "quiz_pageview"     => [ "mod_quiz", "viewed", "attempt" ],
    "quiz_summaryview"  => [ "mod_quiz", "viewed", "attempt_summary" ],
];

// initialise the logs by time container for holding the results
$logsbytime = [];

// Fetch logs that are only user-related
foreach ($logsu as $eventname => $logtype){
    $params = [
        "component" => $logtype[0],
        "action"    => $logtype[1],
        "target"    => $logtype[2],
    ];
    $query = '
        SELECT l.id, l.courseid, u.username, l.timecreated, l.other, l.objecttable, l.objectid
        FROM {logstore_standard_log} l
        JOIN {user} u ON l.userid = u.id
        WHERE component=:component
        AND action=:action
        AND target=:target
        AND u.id > 2
    ';
    $logrecords = $DB->get_records_sql($query, $params);
    foreach($logrecords as $record){
        $time           = $record->timecreated;
        $other          = ($record->other != 'N;') ? unserialize($record->other) : [];
        $newlog         = (object)$other;
        if (!empty($objtable)){
            $newlog->$objtable = $record->objectid;
        }
        $newlog->event  = $eventname;
        $newlog->user   = $record->username;
        $objtable       = $record->objecttable;
        if (array_key_exists($time, $logsbytime)){
            $logsbytime[$time][] = $newlog;
        } else {
            $logsbytime[$time] = [$newlog];
        }
    }
}

// Fetch logs that are user and course related
foreach ($logsuc as $eventname => $logtype){
    $params = [
        "component" => $logtype[0],
        "action"    => $logtype[1],
        "target"    => $logtype[2],
    ];
    $query = '
        SELECT l.id, l.courseid, u.username, c.shortname as coursename, l.timecreated, l.other, l.objecttable, l.objectid
        FROM {logstore_standard_log} l
        JOIN {user} u ON l.userid = u.id
        JOIN {course} c ON l.courseid = c.id
        WHERE component=:component
        AND action=:action
        AND target=:target
        AND u.id > 2
    ';
    $logrecords = $DB->get_records_sql($query, $params);
    foreach($logrecords as $record){
        $time           = $record->timecreated;
        $other          = ($record->other != 'N;') ? unserialize($record->other) : [];
        $newlog         = (object)$other;
        if ($objtable){
            $newlog->$objtable = $record->objectid;
        }
        $newlog->event  = $eventname;
        $newlog->user   = $record->username;
        $newlog->course = $record->coursename;
        $objtable       = $record->objecttable;
        if (array_key_exists($time, $logsbytime)){
            $logsbytime[$time][] = $newlog;
        } else {
            $logsbytime[$time] = [$newlog];
        }
    }
}

// Fetch logs that are user, course and question related
foreach ($logsucq as $eventname => $logtype){
    $params = [
        "component" => $logtype[0],
        "action"    => $logtype[1],
        "target"    => $logtype[2],
    ];
    $query = '
        SELECT l.id, l.courseid, u.username, c.shortname as coursename, l.timecreated, l.other, l.objecttable, l.objectid
        FROM {logstore_standard_log} l
        JOIN {user} u ON l.userid = u.id
        JOIN {course} c ON l.courseid = c.id
        WHERE component=:component
        AND action=:action
        AND target=:target
        AND u.id > 2
    ';
    $logrecords = $DB->get_records_sql($query, $params);
    // extract the quiz identifiers
    $quizids = [];
    foreach($logrecords as $record){
        $other              = unserialize($record->other);
        $quizid             = $other['quizid'];
        $record->quizid     = $quizid;
        $quizids[$quizid]   = $quizid;
        unset($other['quizid']);
    }

    // lookup the quiz records from the database
    $query = '
        SELECT q.id, q.name, cs.section
        FROM {quiz} q
        JOIN {course_modules} cm ON cm.instance = q.id
        JOIN {modules} m ON cm.module = m.id
        JOIN {course_sections} cs ON cm.section = cs.id
        WHERE m.name = "quiz"
        AND q.id in (' . join(',', $quizids) . ')
    ';
    $quizrecords = $DB->get_records_sql($query);

    // combine the logs and quiz info into result records
    foreach($logrecords as $record){
        $time   = $record->timecreated;
        $quizid = $record->quizid;
        $quiz   = $quizrecords[$quizid];
        $newlog = (object)([
            "event"     => $eventname,
            "user"      => $record->username,
            "course"    => $record->coursename,
            "section"   => $quiz->section,
            "quiz"      => $quiz->name,
        ]);
//        ] + $other);
        $objtable = $record->objecttable;
        if ($objtable){
            $newlog->$objtable = $record->objectid;
        }
        if (array_key_exists($time, $logsbytime)){
            $logsbytime[$time][] = $newlog;
        } else {
            $logsbytime[$time] = [$newlog];
        }
    }
}

// mine the mdl_ludic_motivator_achievements table
$query = '
    SELECT a.id, a.timestamp, u.username, a.achievement as eventcode, a.value
    FROM {ludic_motivator_achievements} a
    JOIN {user} u ON u.id = a.user
    WHERE value > 0
';
$achievementrecords = $DB->get_records_sql($query);
foreach($achievementrecords as $record){
    $time       = $record->timestamp;
    $eventcode  = $record->eventcode;
    preg_match('%.:([^#:]*)#?([^:]*):([^/]*)/(.*)%', $eventcode, $parts);
    $coursename = $parts[1];
    $sectionidx = $parts[2];
    $motivator  = $parts[3];
    $item       = $parts[4];
    $newlog = (object)[
        "event"     => $motivator . '_update',
        "user"      => $record->username,
        "course"    => $coursename,
        "section"   => $sectionidx,
        "property"  => $item,
        "value"     => $record->value,
    ];
    if (array_key_exists($time, $logsbytime)){
        $logsbytime[$time][] = $newlog;
    } else {
        $logsbytime[$time] = [$newlog];
    }
}

// fetch quiz results
$query = '
    SELECT qa.id, q.name as quizname, u.username, c.shortname as coursename, cs.section, qa.attempt, qa.timestart, qa.timefinish, qa.timemodified
    FROM {quiz_attempts} qa
    JOIN {user} u ON u.id = qa.userid
    JOIN {quiz} q ON q.id = qa.quiz
    JOIN {course} c ON c.id = q.course
    JOIN {course_modules} cm ON cm.instance = q.id
    JOIN {modules} m ON cm.module = m.id
    JOIN {course_sections} cs ON cm.section = cs.id
    WHERE u.id > 2
    AND m.name = "quiz"
';
$quizattemptrecords = $DB->get_records_sql($query);
foreach ($quizattemptrecords as $record){
    // extract key event elements
    $newlog = new \StdClass;
    $newlog->user       = $record->username;
    $newlog->course     = $record->coursename;
    $newlog->section    = $record->section;
    $newlog->attempt    = $record->attempt;
    $newlog->quiz       = $record->quizname;

    // start with the start of quiz event
    $time               = $record->timestart;
    $newlog->event      = "quiz_attempt_started";
    if (array_key_exists($time, $logsbytime)){
        $logsbytime[$time][] = clone $newlog;
    } else {
        $logsbytime[$time] = [clone $newlog];
    }

    if ($record->timefinish > $record->timestart){
        // add and 'end' log
        $time               = $record->timefinish;
        $newlog->event      = "quiz_attempt_finished";
        if (array_key_exists($time, $logsbytime)){
            $logsbytime[$time][] = clone $newlog;
        } else {
            $logsbytime[$time] = [clone $newlog];
        }
    }else{
        // add an 'abandonned' log
        $time               = $record->timemodified;
        $newlog->event      = "quiz_attempt_unfinished";
        if (array_key_exists($time, $logsbytime)){
            $logsbytime[$time][] = clone $newlog;
        } else {
            $logsbytime[$time] = [clone $newlog];
        }
    }
}

$query = '
    SELECT qas.id, q.name as quizname, u.username, c.shortname as coursename, cs.section, qa.attempt, GREATEST(qa.timefinish, qa.timemodified) as endtime, a.slot, qas.sequencenumber , qas.state
    FROM {quiz_attempts} qa
    JOIN {user} u ON u.id = qa.userid
    JOIN {quiz} q ON q.id = qa.quiz
    JOIN {course} c ON c.id = q.course
    JOIN {course_modules} cm ON cm.instance = q.id
    JOIN {modules} m ON cm.module = m.id
    JOIN {course_sections} cs ON cm.section = cs.id
    JOIN {question_usages} qu ON qu.id=qa.uniqueid
    JOIN {question_attempts} a ON a.questionusageid=qu.id
    JOIN {question_attempt_steps} qas ON questionattemptid=a.id
    WHERE u.id > 2
    AND m.name = "quiz"
    AND qas.state != "todo"
';
$questionattemptrecords = $DB->get_records_sql($query);
foreach ($questionattemptrecords as $record){
    // extract key event elements
    $time               = $record->endtime;
    $newlog             = new \StdClass;
    $newlog->event      = "question_" . $record->state;
    $newlog->user       = $record->username;
    $newlog->course     = $record->coursename;
    $newlog->section    = $record->section;
    $newlog->attempt    = $record->attempt;
    $newlog->quiz       = $record->quizname;
    $newlog->question   = $record->slot;
    $newlog->sequence   = $record->sequencenumber;

    // start with the start of quiz event
    if (array_key_exists($time, $logsbytime)){
        $logsbytime[$time][] = clone $newlog;
    } else {
        $logsbytime[$time] = [clone $newlog];
    }
}

$times = array_keys($logsbytime);
sort($times);

// setup target path and file name
$path = "{$CFG->dataroot}/filedir/trace_logs/";
$tgtFile = "$path/".time().".gz";
if (! file_exists($path)){
    mkdir($path, 0777, true);
}

// generate the output file
$outputStr = '';
foreach ($times as $time){
    foreach ($logsbytime[$time] as $log){
        $username = $log->user;
        $action = $log->event;
        $elements = (array)$log;
        unset($elements['user']);
        unset($elements['event']);
        $outputStr .= "$time ; $username ; $action ; " . ($elements ? json_encode($elements) : '') . "\n";
    }
}
file_put_contents( 'compress.zlib://' . $tgtFile, $outputStr );

// shut access to courses
$DB->execute('UPDATE {course_sections} SET visible=0 WHERE course>2');
purge_all_caches();

// Upload to server for storage
$ch = curl_init('http://ludimoodle-demo.proto.edunao.com/store_logs.php');
$cfile = new CURLFile($tgtFile, 'application/gzip','ludimoodle_trace.txt.gz');
$data = array('ludilog' => $cfile);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_exec($ch);

// dump the output file
header('Content-Description: File Transfer');
header("Content-Type: application/gzip");
header("Content-disposition: attachment; filename=\"ludimoodle_trace.log.gz\"");
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($tgtFile));
readfile($tgtFile);
