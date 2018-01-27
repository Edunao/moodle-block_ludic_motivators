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
 * Library of functions used by the ludic_motivators block.
 *
 * This contains functions that are called from within the ludic_motivators block only
 * Functions that are also called by core Moodle are in {@link lib.php}
 *
 * @copyright  2018 Edunao SAS (contact@edunao.com)
 * @author     Didier GIBAUD (didier@edunao.com)
 * @package    block_ludic_motivators
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace block_ludic_motivators;
defined('MOODLE_INTERNAL') || die();
// use \block_ludic_motivators\real_store;
// use \block_ludic_motivators\context;

/**
 * This return achievement status for the motivator passed in parameter.
 *
 * @return stdclass the status record.
 *
 */
function get_achievement_status($context) {

    // Get the data from the database and update the status from
    $store = $context->getStore();
    $datas = $store->get_datas();echo "toto";print_r($datas);

    if (empty($datas)) {
        return null;
    }

    print_r($datas);

    foreach ($datas as $key => $data) {
        # code...
    }

    $status = $datas;

    switch ($status->motivator->name) {
        case 'avatar':
            return get_avatar_achievement_status($status);
            break;

        case 'score':
            return get_score_achievement_status($status);
            break;

        case 'goals':
            return get_goals_achievement_status($status);
            break;

        case 'ranking':
            return get_ranking_achievement_status($status);
            break;

        case 'badge':
            return get_badge_achievement_status($status);
            break;

        case 'timer':
            return get_timer_achievement_status($status);
            break;

        case 'progress':
            return get_progress_achievement_status($status);
            break;

        default:
            return false;
            break;
    }
}

function get_avatar_achievement_status(stdClass $status) {

    // Array containing the layer name as presetted
    // except the first two ('calque00','calque01')
    $status->avatar = array(
        'previously_obtained' => array('calque02','calque03','calque04'),
        'newly_obtained' => 'calque04',
    );

    return $status;
}

function get_score_achievement_status(stdClass $status) {

    // Array containing the points obtained
    $status->score = array(
        'previously_obtained' => array(15,12,17),
        'newly_obtained' => 17,
    );

    // Array containing the bonus id as presetted in the configuration file
    $status->bonus = array(
        'previously_obtained' => array(1,3),
        'newly_obtained' => array(2,4),
    );

    return $status;
}

function get_goals_achievement_status(stdClass $status) {

    // Array containing the goals id as presetted in the configuration file
    $status->goals = array(
        'previously_obtained' => array(1,3,5),
        'newly_obtained' => 5,
    );

    return $status;
}

function get_ranking_achievement_status(stdClass $status) {

    // Array containing the best score points obtained in the class
    $status->best_score = array(
        'previously_obtained' => 17,
        'newly_obtained' => 15,
    );

    // Array containing the average points obtained by the class
    $status->class_average = array(
        'previously_obtained' => 13,
        'newly_obtained' => 12.5,
    );

    // Array containing the points obtained par the current user
    $status->user_score = array(
        'previously_obtained' => array(15,12,8),
        'newly_obtained' => 8,
    );

    return $status;
}

function get_badges_achievement_status(stdClass $status) {

    // Array containing the badge id as presetted in the configuration file
    $status->courses_badges = array(
        'previously_obtained' => array(1,2,7),
        'newly_obtained' => 7,
    );

    // Array containing the layer name as presetted in the configuration file
    $status->global_badges = array(
        'previously_obtained' => array('calque01','calque02','calque03'),
        'newly_obtained' => 'calque03',
    );

    return $status;
}

function get_timer_achievement_status(stdClass $status) {

    // Max timing value in minutes
    $status->max_duration_timer = 45;

    // Array containing the timing in seconds
    $status->timer = array(
        'previously_timers' => array(1200,2400,1800),
        'previously_timestamps' => array(123456789,123456789,123456789),
        'current_timer' => 500,
        'current_timestamp' => 123456789,
    );

    return $status;
}

function get_progress_achievement_status(stdClass $status) {

    // Check whether courseId is known (course page) or not (/my page)
    if ($status->courseId !== null) {
        // Array containing the layer name for the step obtained as presetted
        // except the first one ('calque00')
        $status->course_progress = array(
            'previously_obtained' => array('calque04'),
            'newly_obtained' => array('calque04'),
        );
    } else {
        // Array containing the layer name for the step obtained for each branch
        // as presetted except the first one ('calque00')
        $status->global_progress = array(
            // Array containing the layer name
            'previously_obtained' => array(
                'calque07','calque02',
                'calque03','calque01',
                'calque07','calque02',
                'calque03','calque01',
                'calque07','calque02',
                'calque03','calque01',
                'calque07','calque02'
            ),
            'newly_obtained' => array(
                'calque08','calque03',
                'calque04','calque02',
                'calque07','calque03',
                'calque04','calque02',
                'calque08','calque03',
                'calque04','calque02',
                'calque08','calque03'
            ),
        );
    }

    return $status;
}

/**
 * This store achievement status for the motivator passed in parameter.
 *
 * @return true or false.
 */
function set_achievement_status(context $context) {

    // Trying to get the data from the database
    $store = $context->getStore();
    $datas = $store->get_datas();//echo 'tttt';print_r($datas);

    // Check whether it is the first attempt page
    if (empty($datas)) {
        $status = init_status($context);

        // Check whether init is well done
        if (($status = init_status($context)) === null) {
            return null;
        }

        $datas = $store->store_datas($status, $status->quiz['module']['cmid']);print_r($datas);
        return true;
    }

    foreach ($datas as $key => $data) {
        //echo 'foreach';print_r($datas);
    }

    $status = $datas;

    // Get the quiz attempt data from the database
    /*$attempt_id = optional_param('attempt', 0, PARAM_INT);
    $store = $context->getStore();
    $quiz_attempt = $store->get_quiz_attempt($attempt_id);

    // Check whether a retry to answer is attempted
    $retryanswer = true;
    if ($quiz_attempt->currentpage < $datas->previouspage) {
        $retryanswer = true;
    }

    $status->quiz_attempts[] = array(
            'quizid' => $quiz_attempt->quiz,
            'attempt' => $quiz_attempt->attempt,
            'layout' => $quiz_attempt->layout,
            'previouspage' => $quiz_attempt->currentpage,
            'currentpage' => $quiz_attempt->currentpage,
            'retryanswer' => $retryanswer,
            'state' => $quiz_attempt->state,
            'timestart' => $quiz_attempt->timestart,
            'timefinish' => $quiz_attempt->timefinish,
            'sumgrades' => $quiz_attempt->sumgrades,
    );

    switch ($status->motivator['name']) {
        case 'avatar':
            $new_status = set_avatar_achievement_status($status);
            break;

        case 'score':
            $new_status = set_score_achievement_status($status);
            break;

        case 'goals':
            $new_status = set_goals_achievement_status($status);
            break;

        case 'ranking':
            $new_status = set_ranking_achievement_status($status);
            break;

        case 'badge':
            $new_status = set_badge_achievement_status($status);
            break;

        case 'timer':
            $new_status = set_timer_achievement_status($status);
            break;

        case 'progress':
            $new_status = set_progress_achievement_status($status);
            break;

        default:
            // code...
            break;
    }

    // Save the new status in the database
    // code...
    //print_r($new_status);
    $datas = $store->store_datas($new_status, $new_status->module['cmid']);print_r($datas);*/

    return true;
}

function set_avatar_achievement_status(stdClass $status) {
    $status->motivator['status'] = array();

    $status->motivator['status']['previously_obtained'][] = '';
    $status->motivator['status']['newly_obtained'] = '';

    return $status;
}

function set_score_achievement_status(stdClass $status) {
    $status->motivator['score'] = array();
    $status->motivator['bonus'] = array();

    $status->motivator['score']['previously_obtained'][] = '';
    $status->motivator['score']['newly_obtained'] = '';

    $status->motivator['bonus']['previously_obtained'][] = '';
    $status->motivator['bonus']['newly_obtained'] = '';

    return $status;
}

function set_goals_achievement_status(stdClass $status) {
    $status->motivator['status'] = array();

    $status->motivator['status']['previously_obtained'][] = '';
    $status->motivator['status']['newly_obtained'] = '';

    return $status;
}

function set_ranking_achievement_status(stdClass $status) {
    $status->motivator['best_score'] = array();
    $status->motivator['class_average'] = array();
    $status->motivator['$user_score'] = array();
    $new_score = '';

    // Compute and store the new best score according to the new score
    if (get_best_score($status->courseId) < $new_score) {
        $status->motivator['best_score']['previously_obtained'][] = $new_score;
        $status->motivator['best_score']['newly_obtained'] = $new_score;
    }

    // Compute and store the new class average according to the new score
    $scores = get_all_scores();
    $scores[] = $new_score;
    $new_class_average = array_sum($scores)/count($scores);
    $status->motivator['class_average']['previously_obtained'][] = $new_class_average;
    $status->motivator['class_average']['newly_obtained'] = $new_class_average;

    // Store the new score
    $status->motivator['$user_score']['previously_obtained'][] = $new_score;
    $status->motivator['$user_score']['newly_obtained'] = $new_score;

    return $status;
}

function set_badges_achievement_status(stdClass $status) {
    $status->motivator['course_badges'] = array();
    $status->motivator['global_badges'] = array();

    $status->motivator['course_badges']['previously_obtained'][] = '';
    $status->motivator['course_badges']['newly_obtained'] = '';

    // Store the badge new value in the global course
    $courseId = $status->courseId;
    $status->motivator['global_badges']['previously_obtained'][$courseId][] = '';
    $status->motivator['global_badges']['newly_obtained'][$courseId] = '';

    // Array containing the badge id as presetted in the configuration file
    $status->motivator['course_badges'] = array(
        'previously_obtained' => array(1,2,7),
        'newly_obtained' => 7,
    );

    // Array containing the step indice
    $status->motivator['global_badges'] = array(
        'course_id' => $courseId,
        'previously_obtained' => array(1,2,3),
        'newly_obtained' => 3,
    );

    return $status;
}

function set_timer_achievement_status(stdClass $status) {
    $status->motivator['status'] = array();

    $status->motivator['status']['previously_timers'][] = '';
    $status->motivator['status']['previously_timestamps'][] = '';
    $status->motivator['status']['current_timer'] = '';
    $status->motivator['status']['current_timestamp'] = '';

    return array($timer);
}

function set_progress_achievement_status(stdClass $status) {
    $status->motivator['course_progress'] = array();
    $status->motivator['global_progress'] = array();

    // Store the progress new value in the current course
    $status->motivator['course_progress']['previously_obtained'][] = '';
    $status->motivator['course_progress']['newly_obtained'] = '';

    // Store the progress new value in the global course
    $courseId = $status->courseId;
    $status->motivator['global_progress']['newly_obtained'][$courseId] = '';

    return $status;
}

function get_best_score($cmid) {

    // Get the best score for the $courseId for the class the current user is in
    // Faire un select retournant la valeur max des sumgrades des quiz_attempts
    // pour le quizid où les noms d'élèves ont le même préfixe de classe
    // code...

    if (true) {
        return 18;
    }
}

function get_all_scores($cmid) {

    // Get all the scores obtained for the $courseId for the class the current user is in
    // Faire un select retournant toutes les valeurs des sumgrades des quiz_attempts
    // pour le quizid où les noms d'élèves ont le même préfixe de classe
    // code...

    if (true) {
        return array(18,9,4,6,12,15,10);
    }
}

function init_status(context $context) {
    $status = new stdClass;

    // Return whether an attempt is not in progress
    if (($attempt_id = optional_param('attempt', 0, PARAM_INT)) === 0) {
        return null;
    }

    $store = $context->getStore();
    $status->user = $context->getUser();

    // Initialize status motivator
    $status->motivator = array(
        'status' => '',
        'name' => $context->getMotivatorName(),
    );

    // Get the quiz attempt data from the database
    $quiz_attempt = $store->get_quiz_attempt($attempt_id);
    $status->quiz['attempt'] = array(
        //'quizid' => $quiz_attempt->quiz,
        'attempt' => $quiz_attempt->attempt,
        'layout' => $quiz_attempt->layout,
        'previouspage' => $quiz_attempt->currentpage,
        'currentpage' => $quiz_attempt->currentpage,
        'retryanswer' => false,
        'state' => $quiz_attempt->state,
        'timestart' => $quiz_attempt->timestart,
        'timefinish' => $quiz_attempt->timefinish,
        'sumgrades' => $quiz_attempt->sumgrades,
    );

    // Get the quiz info from the database
    $quiz = $store->get_quiz_info($quiz_attempt->quiz);
    $status->quiz['info'] = array(
        'course_id' => $context->getCourseId(),
        'quiz_id' => $quiz->id,
        'name' => $quiz->name,
        'sumgrades' => $quiz->sumgrades,
        'grade' => $quiz->grade,
    );

    // Get the course module info from the database
    //$cmid = $attempt_id = optional_param('cmid', 0, PARAM_INT);print_r($cmid);
    $cm = $store->get_cm_quiz($quiz_attempt->quiz);
    $status->quiz['module'] = array(
        'cmid' => $cm->id,
        'section' => $cm->section,
    );

    switch ($status->motivator['name']) {
        case 'avatar':
            $new_status = init_avatar_achievement_status($status);
            break;

        case 'score':
            $new_status = init_score_achievement_status($status);
            break;

        case 'goals':
            $new_status = init_goals_achievement_status($status);
            break;

        case 'ranking':
            $new_status = init_ranking_achievement_status($status);
            break;

        case 'badge':
            $new_status = init_badge_achievement_status($status);
            break;

        case 'timer':
            $new_status = init_timer_achievement_status($status);
            break;

        case 'progress':
            $new_status = init_progress_achievement_status($status);
            break;

        default:
            // code...
            break;
    }

    return $new_status;
}

function init_avatar_achievement_status(stdClass $status) {

    $status->motivator['status']['previously_obtained'][] = '';
    $status->motivator['status']['newly_obtained'] = '';

    return $status;
}

function init_score_achievement_status(stdClass $status) {

    $status->motivator['score']['previously_obtained'][] = '';
    $status->motivator['score']['newly_obtained'] = '';

    $status->motivator['bonus']['previously_obtained'][] = '';
    $status->motivator['bonus']['newly_obtained'] = '';

    return $status;
}

function init_goals_achievement_status(stdClass $status) {

    $status->motivator['status']['previously_obtained'][] = '';
    $status->motivator['status']['newly_obtained'] = '';

    return $status;
}

function init_ranking_achievement_status(stdClass $status) {
    $new_score = '';

    // Compute and store the new best score according to the new score
    if (get_best_score($status->courseId) < $new_score) {
        $status->motivator['best_score']['previously_obtained'][] = $new_score;
        $status->motivator['best_score']['newly_obtained'] = $new_score;
    }

    // Compute and store the new class average according to the new score
    $scores = get_all_scores();
    $scores[] = $new_score;
    $new_class_average = array_sum($scores)/count($scores);
    $status->motivator['class_average']['previously_obtained'][] = $new_class_average;
    $status->motivator['class_average']['newly_obtained'] = $new_class_average;

    // Store the new score
    $status->motivator['$user_score']['previously_obtained'][] = $new_score;
    $status->motivator['$user_score']['newly_obtained'] = $new_score;

    return $status;
}

function init_badges_achievement_status(stdClass $status) {

    $status->motivator['course_badges']['previously_obtained'][] = '';
    $status->motivator['course_badges']['newly_obtained'] = '';

    // Store the badge new value in the global course
    $courseId = $status->courseId;
    $status->motivator['global_badges']['previously_obtained'][$courseId][] = '';
    $status->motivator['global_badges']['newly_obtained'][$courseId] = '';

    // Array containing the badge id as presetted in the configuration file
    $status->motivator['course_badges'] = array(
        'previously_obtained' => array(1,2,7),
        'newly_obtained' => 7,
    );

    // Array containing the step indice
    $status->motivator['global_badges'] = array(
        'course_id' => $courseId,
        'previously_obtained' => array(1,2,3),
        'newly_obtained' => 3,
    );

    return $status;
}

function init_timer_achievement_status(stdClass $status) {

    $status->motivator['status']['previously_timers'][] = '';
    $status->motivator['status']['previously_timestamps'][] = '';
    $status->motivator['status']['current_timer'] = '';
    $status->motivator['status']['current_timestamp'] = '';

    return array($timer);
}

function init_progress_achievement_status(stdClass $status) {

    // Store the progress new value in the current course
    $status->motivator['course_progress']['previously_obtained'][] = '';
    $status->motivator['course_progress']['newly_obtained'] = '';

    // Store the progress new value in the global course
    $courseId = $status->courseId;
    $status->motivator['global_progress']['newly_obtained'][$courseId] = '';

    return $status;
}