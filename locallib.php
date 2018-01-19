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

defined('MOODLE_INTERNAL') || die();
use \block_ludic_motivators\real_store;
use \block_ludic_motivators\context;

/**
 * This return achievement status for the moderator passed in parameter.
 *
 * @return stdclass the status record.
 * stdClass->courseId
 * stdClass->moderator
 * stdClass->userId
 * stdClass->quiz_attempts = array(
 *     array(
 *         'quizid'
 *         'attempt'
 *         'layout'
 *         'previouspage'
 *         'currentpage'
 *         'retryanswer'
 *         'state'
 *         'timestart'
 *         'timefinish'
 *         'sumgrades'
 *     )
 * )
 */
function get_achievement_status(context $context, $moderator) {

    // Get the data from the database and update the status from
    // code...
    $store = $context->getStore();print_r($store);
    $datas = $store->get_datas();echo "toto";print_r($datas);

    $status = new stdClass;

    $status->courseId = $context->getCourseId();
    $status->userId = $context->getUser();


    /*$currentpage = optional_param('page', 0, PARAM_INT);
    $previouspage = $datas->quiz_attempts('previouspage');
    $retry_answer = ($currentpage < $previouspage) ? true : false;*/

    /*$status->quiz_attempts = array(
        array(
            'quizid' => '',
            'attempt' => optional_param('attempt', 0, PARAM_INT),
            'layout' => '',
            'previouspage' => '',
            'currentpage' => '',
            'retryanswer' => $retry_answer,
            'state' => '',
            'timestart' => '',
            'timefinish' => '',
            'sumgrades' => '',
        ),
    );*/

    switch ($status->moderator) {
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
 * This store achievement status for the moderator passed in parameter.
 *
 * @return true or false.
 */
function set_achievement_status(context $context, $moderator) {

    // Get the data from the database
    $store = $context->getStore();
    $datas = $store->get_datas();

    if (empty($datas)) {
        $status = init_status($context, $moderator);
    } else {
        $status = $datas;
    }
    //print_r($status);

    // Get the quiz attempt data from the database
    $attempt = optional_param('attempt', 0, PARAM_INT);
    $store = $context->getStore();
    $quiz_attempt = $store->get_quiz_attempt($attempt_id);echo 'itit';print_r($quiz_attempt);


    //$previousStatus = get_achievement_status($context, $moderator);

    //$previousStatus->courseId = $context->getCourseId();
    //$previousStatus->userId = $context->getUser();

    //$currentpage = optional_param('page', 0, PARAM_INT);
    //$previouspage = $previousStatus->quiz_attempts('previouspage');
    //$retry_answer = ($currentpage < $previouspage) ? true : false;

    /*$previousStatus->quiz_attempts = array(
        array(
            'quizid' => '',
            'attempt' => optional_param('attempt', 0, PARAM_INT),
            'layout' => '',
            'previouspage' => '',
            'currentpage' => '',
            'retryanswer' => $retry_answer,
            'state' => '',
            'timestart' => '',
            'timefinish' => '',
            'sumgrades' => '',
        ),
    );*/

    $newValue = array();

    switch ($moderator) {
        case 'avatar':
            $newStatus = set_avatar_achievement_status($status, $newValue);
            break;

        case 'score':
            $newStatus =  set_score_achievement_status($status, $newValue);
            break;

        case 'goals':
            $newStatus =  set_goals_achievement_status($status, $newValue);
            break;

        case 'ranking':
            $newStatus =  set_ranking_achievement_status($status, $newValue);
            break;

        case 'badge':
            $newStatus =  set_badge_achievement_status($status, $newValue);
            break;

        case 'timer':
            $newStatus =  set_timer_achievement_status($status, $newValue);
            break;

        case 'progress':
            $newStatus =  set_progress_achievement_status($status, $newValue);
            break;

        default:
            // code...
            break;
    }

    // Save the new status in the database
    // code...

    return true;
}

function set_avatar_achievement_status(stdClass $status, array $newValue) {

    $status->avatar['previously_obtained'][] = $newValue['newly_obtained'];
    $status->avatar['newly_obtained'] = $newValue['newly_obtained'];

    return true;
}

function set_score_achievement_status(stdClass $status, array $newValue) {

    $status->score['previously_obtained'][] = $newValue['score']['newly_obtained'];
    $status->score['newly_obtained'] = $newValue['score']['newly_obtained'];

    $status->bonus['previously_obtained'][] = $newValue['bonus']['newly_obtained'];
    $status->bonus['newly_obtained'] = $newValue['bonus']['newly_obtained'];

    return true;
}

function set_goals_achievement_status(stdClass $status, array $newValue) {

    $status->goals['previously_obtained'][] = $newValue['newly_obtained'];
    $status->goals['newly_obtained'] = $newValue['newly_obtained'];

    return true;
}

function set_ranking_achievement_status(stdClass $status, array $newValue) {
    $new_score = $newValue['new_score'];

    // Compute and store the new best score according to the new score
    if (get_best_score($status->courseId) < $new_score) {
        $status->best_score['previously_obtained'][] = $new_score;
        $status->best_score['newly_obtained'] = $new_score;
    }

    // Compute and store the new class average according to the new score
    $scores = get_all_scores();
    $scores[] = $new_score;
    $new_class_average = array_sum($scores)/count($scores);
    $status->class_average['previously_obtained'][] = $new_class_average;
    $status->class_average['newly_obtained'] = $new_class_average;

    // Store the new score
    $status->user_score['previously_obtained'][] = $new_score;
    $status->user_score['newly_obtained'] = $new_score;

    return true;
}

function set_badges_achievement_status(stdClass $status, array $newValue) {

    $status->course_badges['previously_obtained'][] = $newValue['newly_obtained'];
    $status->course_badges['newly_obtained'] = $newValue['newly_obtained'];

    // Store the badge new value in the global course
    $courseId = $status->courseId;
    $status->global_badges['previously_obtained'][$courseId][] = $newValue['newly_obtained'];
    $status->global_badges['newly_obtained'][$courseId] = $newValue['newly_obtained'];

    // Array containing the badge id as presetted in the configuration file
    $status->courses_badges = array(
        'previously_obtained' => array(1,2,7),
        'newly_obtained' => 7,
    );

    // Array containing the step indice
    $status->global_badges = array(
        'previously_obtained' => array(1,2,3),
        'newly_obtained' => 3,
    );

    return true;
}

function set_timer_achievement_status(stdClass $status, array $newValue) {

    $status->timer['previously_timers'][] = $newValue['new_timer'];
    $status->timer['previously_timestamps'][] = $newValue['new_timestamp'];
    $status->timer['current_timer'] = $newValue['new_timer'];
    $status->timer['current_timestamp'] = $newValue['new_timestamp'];

    return true;
}

function set_progress_achievement_status(stdClass $status, array $newValue) {

    // Store the progress new value in the current course
    $status->course_progress['previously_obtained'][] = $newValue['newly_obtained'];
    $status->course_progress['newly_obtained'] = $newValue['newly_obtained'];

    // Store the progress new value in the global course
    $courseId = $status->courseId;
    $status->global_progress['newly_obtained'][$courseId] = $newValue['newly_obtained'];

    return true;
}

function get_best_score($courseId) {

    // Get the best score for the $courseId for the class the current user is in
    // Faire un select retournant la valeur max des sumgrades des quiz_attempts
    // pour le quizid où les noms d'élèves ont le même préfixe de classe
    // code...

    if (true) {
        return 18;
    }
}

function get_all_scores($courseId) {

    // Get all the scores obtained for the $courseId for the class the current user is in
    // Faire un select retournant toutes les valeurs des sumgrades des quiz_attempts
    // pour le quizid où les noms d'élèves ont le même préfixe de classe
    // code...

    if (true) {
        return array(18,9,4,6,12,15,10);
    }
}

function init_status(context $context, $moderator) {
    $status = new stdClass;

    $status->courseId = $context->getCourseId();
    $status->userId = $context->getUser();
    $motivator = $context->getMotivatorName();
    $status->$motivator = array();

    $currentpage = optional_param('page', 0, PARAM_INT);
    $status->quiz_attempts[] = array(
            'quizid' => '',
            'attempt' => '',
            'layout' => '',
            'previouspage' => $currentpage,
            'currentpage' => $currentpage,
            'retryanswer' => false,
            'state' => '',
            'timestart' => '',
            'timefinish' => '',
            'sumgrades' => '',
    );

    return $status;
}
