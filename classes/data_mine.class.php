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
 * @copyright  2018 Edunao SAS (contact@edunao.com)
 * @author     Sadge (daniel@edunao.com)
 * @package    block_ludic_motivators
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_ludic_motivators;
defined('MOODLE_INTERNAL') || die();

require_once __DIR__ . '/base_classes/data_mine_base.class.php';
require_once dirname(dirname(dirname(__DIR__))) . '/question/engine/bank.php';

class data_mine extends data_mine_base {

    private $achievements   = [];
    private $fixedupgrades  = [];  // array indexed by coursename#sectionidx
    private $rawludigrades  = [];  // array indexed by coursename#sectionidx

    /**
     * Basics : flush changes to database (to be called at end of processing to allow inserts to ba batched and suchlike
     */
    public function flush_changes_to_database(){
        $this->flush_achievements();
    }

    /**
     * Quiz Context : get stats for questions in current quiz
     * @return vector of { (done|todo), score }
     */
    protected function fetch_quiz_question_stats($userid, $questionusageid){
        // fetch the latest grade set, calculating new grades as required
        $ludigrades = $this->fetch_raw_ludigrades($userid);

        // compose and return the result
        $result = [];
        foreach($ludigrades as $id=>$record){
            // ignore grades that are not part of the current quiz
            if ($record->questionusageid != $questionusageid){
                continue;
            }
            // setup the result record
            $result[] = (object)[
                'state' => ($score ? (array_key_exists($id, $this->fixedupgrades) ? 2 : 1) : 0),
                'score' => $score,
            ];
        }
        return $result;
//         // Lookup in the database
//         global $DB;
//         $query = '
//             SELECT qas.id,qas.questionattemptid,qa.questionid,qa.maxmark,qas.state,qas.timecreated,qasd.value
//             FROM {question_attempts} qa
//             JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id AND qas.state="complete"
//             LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid=qas.id AND gasd.name="-ludigrade"
//             WHERE qas.userid = :userid AND qa.questionusageid = :questionusageid
//             ORDER BY qas.id
//         ';
//         $params = ['userid' => $userid, 'questionusageid' => $questionusageid];
//         $dbresult = $DB->get_records_sql($query, $params);
//
//         // store the dbresults by question id, overwriting older attemps with newer ones
//         $records = [];
//         foreach ($dbresult as $record){
//             $records[ $record->questionattemptid ] = $record;
//         }
//
//         // iterate over the records fetching scores as required
//         $result = [];
//         $newrecords[];
//         foreach($records as $record){
//             // if we have a pre-calculated score then use it
//             if ($record->value !== null){
//                 $result[] = (object)[
//                     'state' => $record->value ? 1 : 0,
//                     'score' => $record->value,
//                 ];
//                 continue;
//             }
//
//             // calculate the score for the question
//             require_once __DIR__ . '/../../question/engine/bank.php';
//             $question = question_bank::load_question($step->questionid);
//             $query='
//                 SELECT qasd.id,qasd.name,qasd.value
//                 FROM {question_attempt_steps} qas
//                 JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid=qas.id
//                 WHERE qas.questionattemptid = :attemptid ORDER BY qasd.id';
//             $rawqtdata = $DB->get_records_sql($query,['attemptid'=>$step->questionattemptid]);
//             $qtdata=[];
//             foreach($rawqtdata as $item){
//                 if ($item->name[0]===":") continue;
//                 if ($item->name[0]==="-") continue;
//                 $qtdata[$item->name] = $item->value;
//             }
//             $attemptstep = new question_attempt_step($qtdata);
//             $question->apply_attempt_state($attemptstep);
//             list($gradevalue,$gradestate) = $question->grade_response($qtdata);
//
//             // use the result
//             $score = (int)($gradevalue * $step->maxmark)
//             $result[] = (object)[
//                 'state' => $score ? 2 : 0,
//                 'score' => $score,
//             ];
//
//             // queue a record for batched writing to database
//             $newrecords[] = (object)[
//                 'attemptstepid' => $record->questionattemptid,
//                 'name'          => '-ludigrade',
//                 'value'         => $score,
//             ];
//         }
//
//         // write any calculated scores back to the database
//         if ($newrecords){
//             $DB->insert_records('question_attempt_step_data',$newrecords);
//         }
//
//         return $result;
    }

    /**
     * Quiz Context : get time taken so far in an active quiz attempt
     * @return time_now - attempt_start_time
     */
    protected function fetch_quiz_attempt_duration($attemptid){
        // Lookup in the database
        global $DB;
        $starttime = $DB->get_field('quiz_attempts', 'timestart', ['id' => $attemptid]);

        return time() - $starttime;
    }

    /**
     * Quiz Context : get times of completed attempts of current quiz
     * @return vector of { attempt start time => end-time }
     */
    protected function fetch_quiz_completion_times($userid, $cmid){
        // Lookup in the database
        global $DB;
        $query = '
            SELECT qa.id, qa.timestart, qa.timefinish
            FROM {modules} m
            JOIN {course_modules} cm on cm.module=m.id
            JOIN {quiz_attempts} qa on qa.quiz = cm.instance
            WHERE m.name = "quiz"
            AND cm.id = :cmid
            AND qa.userid = :userid
            AND qa.timefinish > 0
            ORDER BY qa.id
        ';
        $sqlresult = $DB->get_records_sql($query, ['userid' => $userid, 'cmid' => $cmid]);
        $result = [];
        foreach ($sqlresult as $record){
            $result[$record->timestart] = max(0, ($record->timefinish - $record->timestart));
        }

        return $result;
    }

    /**
     * Section Context : get scores for all users for the exercises in the current section
     * @return vector of { user id => score }
     */
    protected function fetch_section_user_scores($course, $sectionid){
        // fetch the raw data from db
        global $DB;
        $query='
            SELECT qug.userid, sum(qug.grade) AS grade
            FROM (
                SELECT qa.id, qa.quiz, qa.userid, max(qa.sumgrades) AS grade
                FROM {course} c
                JOIN {course_sections} cs ON cs.course = c.id
                JOIN {course_modules} cm ON cm.section = cs.id
                JOIN {modules} m ON cm.module=m.id
                JOIN {quiz_attempts} qa on qa.quiz = cm.instance
                WHERE c.shortname = :course
                AND cs.section = :sectionid
                AND m.name="quiz"
                GROUP BY qa.quiz, qa.userid
            ) AS qug
            GROUP BY qug.userid
        ';
        $sqlresult = $DB->get_records_sql($query, ['course' =>$course, 'sectionid' => $sectionid]);

        // sedtup and return the result
        $result = [];
        foreach($sqlresult AS $userid => $record){
            $result[$userid] = $record->grade;
        }
        return $result;
    }

    /**
     * Section Context : get a progress rating for the section based on its quiz questions
     * @return { maxgrade, grade }
     */
    protected function fetch_section_progress($userid, $course, $sectionid){
        $query='
            SELECT sum(qg.maxgrade) AS maxgrade, sum(qg.grade) AS grade
            FROM (
                SELECT q.id, q.sumgrades AS maxgrade, max(qa.sumgrades) AS grade
                FROM {course} c
                JOIN {course_sections} cs ON cs.course = c.id
                JOIN {course_modules} cm ON cm.section = cs.id
                JOIN {modules} m ON cm.module=m.id
                JOIN {quiz} q ON q.id = cm.instance
                LEFT JOIN {quiz_attempts} qa ON qa.quiz = cm.instance AND qa.userid = :userid
                WHERE c.shortname = :course
                AND cs.section = :sectionid
                AND m.name="quiz"
                GROUP BY qa.quiz
            ) AS qg
        ';
        global $DB;
        $result = $DB->get_record_sql($query, ['userid' => $userid, 'course' => $course, 'sectionid' => $sectionid]);

        // return the result as-is
        return $result;
    }

    /**
     * Section Context : get stats sbout all of the quizzes available and attempted by the user in the section
     * @return vector of { available score, attemps as vector of score }
     */
    protected function fetch_section_quiz_stats($userid, $course, $sectionid){
        $query='
            SELECT COALESCE(qa.id,-q.id) AS idx, q.id, qa.attempt, q.sumgrades AS maxgrade, qa.sumgrades AS grade, qa.state, qa.timestart, qa.timemodified, qa.timefinish
            FROM {course} c
            JOIN {course_sections} cs ON cs.course = c.id
            JOIN {course_modules} cm ON cm.section = cs.id
            JOIN {modules} m ON cm.module=m.id
            JOIN {quiz} q ON q.id = cm.instance
            LEFT JOIN {quiz_attempts} qa ON qa.quiz = cm.instance AND qa.userid = :userid
            WHERE c.shortname = :course
            AND cs.section = :sectionid
            AND m.name="quiz"
        ';
        global $DB;
        $sqlresult = $DB->get_records_sql($query, ['userid' => $userid, 'course' => $course, 'sectionid' => $sectionid]);

        // compose and return the result container ...
        $result = [];
        foreach ($sqlresult as $record){
            // get hold of the record for this quiz
            $quizid  = $record->id;
            if (! array_key_exists($quizid, $result)){
                $result[$quizid] = (object)[
                    'maxgrade'  => $record->maxgrade,
                    'attempts'  => [],
                    'times'     => [],
                    'grades'    => [],
                ];
            }
            $resultrecord = &$result[$quizid];

            // if there are no attempts for this quiz then skip on
            if ($record->state === null){
                continue;
            }

            // store away the attempt record
            $attempt = $record->attempt;
            $resultrecord->attempts[$attempt] = (object)[
                'state'     => $record->state,
                'starttime' => $record->timestart,
                'duration'  => $record->timemodified - $record->timestart,
            ];

            // if the attempt is not completed and graded then continue
            if ($record->state != 'finished'){
                continue;
            }

            // store away finished quiz stats
            $resultrecord->times[$attempt]  = $record->timefinish - $record->timestart;
            $resultrecord->grades[$attempt] = $record->grade;
        }

        // return the result
        return $result;
    }

    /**
     * Section Context : get scores for all questions answered by the user in the current section in chronological order
     * @return vector of score-fraction
     */
    protected function fetch_section_answer_stats($userid, $course, $sectionid){
        // fetch the latest grade set, calculating new grades as required
        $ludigrades = $this->fetch_raw_ludigrades($userid, $course, $sectionid);

        // construct and return the result
        $result = [];
        foreach ($ludigrades as $record){
            $result[] = $record->maxgrade ? ( $record->grade / $record->maxgrade ) : 0;
        }
        return $result;
    }

    /**
     * Global Context : Get progress information for all of the sections for the given user
     * @return vector of sectionid => { maxgrade, grade }
     */
    protected function fetch_global_section_progress($userid, $coursenames){
        $coursenamestr = '"' . join('","', $coursenames) . '"';
        $query='
            SELECT section, sum(maxgrade) AS maxgrade, sum(grade) AS grade
            FROM (
                SELECT q.id, cm.section, q.sumgrades AS maxgrade, max(qa.sumgrades) AS grade
                FROM {modules} m
                JOIN {course_modules} cm on cm.module=m.id
                JOIN {course} c on c.id = cm.course
                JOIN {quiz} q on q.id = cm.instance
                LEFT JOIN {quiz_attempts} qa on qa.quiz = cm.instance AND qa.userid = :userid
                WHERE m.name="quiz" AND c.shortname in (' . $coursenamestr . ')
                GROUP BY cm.instance
            ) AS qg
            GROUP BY section
        ';
        global $DB;
        $result = $DB->get_records_sql($query, ['userid' => $userid]);

        // return the result as-is
        return $result;
    }


    private function fetch_raw_ludigrades($userid, $coursename, $sectionidx){
        // make sure the fetch is only run the once - this is a big query so not to be run more often thn necessary
        $sectionkey = $coursename . '#' . $sectionidx;
        if (array_key_exists($sectionkey, $this->rawludigrades)){
            return $this->rawludigrades[$sectionkey];
        }


        // make sure that grade table has been fixed up as required
        $this->fixup_ludigrades($userid, $coursename, $sectionidx);

        // fetch the appropriate grade data from sql
        global $DB;
        $query = '
            SELECT qasd.id, c.shortname as coursename, qa.id as attemptid, qa.questionusageid, qa.questionid, qa.maxmark as maxgrade, qasd.value as grade
            FROM {course} c
            JOIN {course_modules} cm ON cm.course = c.id
            JOIN {course_sections} cs ON cs.id = cm.section
            JOIN {modules} m ON cm.module=m.id
            JOIN {context} ctxt ON ctxt.instanceid = cm.id AND ctxt.contextlevel = 70
            JOIN {question_usages} qu ON qu.contextid = ctxt.id
            JOIN {question_attempts} qa ON qa.questionusageid = qu.id
            JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id AND qas.userid = :userid AND qas.state = "complete"
            JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid = qas.id AND qasd.name = "-ludigrade"
            WHERE c.shortname=:course
            AND cs.section=:section
            AND m.name="quiz"
            ORDER BY qasd.id
        ';
        $this->rawludigrades[$sectionkey] = $DB->get_records_sql($query,['userid' => $userid, 'course' => $coursename, 'section' => $sectionidx]);

        // return the result
        return $this->rawludigrades[$sectionkey];
    }

    private function fixup_ludigrades($userid, $coursename, $sectionidx){
        // put in a database request fo the set of attempt step data fields for completed questions that have yet to be ludigraded
        global $DB;
        $query = '
            SELECT qasd.id, l.coursename, l.questionid, l.maxgrade, l.attemptstepid, qas.questionattemptid, qasd.name, qasd.value
            FROM (
                SELECT qa.id, c.shortname as coursename, qa.questionid, qa.maxmark as maxgrade, qas.id as attemptstepid
                FROM {course} c
                JOIN {course_modules} cm ON cm.course = c.id
                JOIN {course_sections} cs ON cs.id = cm.section
                JOIN {modules} m ON cm.module=m.id
                JOIN {context} ctxt ON ctxt.instanceid = cm.id AND ctxt.contextlevel=70
                JOIN {question_usages} qu ON qu.contextid = ctxt.id
                JOIN {question_attempts} qa ON qa.questionusageid = qu.id
                JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id AND qas.state="complete"
                LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid=qas.id AND qasd.name="-ludigrade"
                WHERE c.shortname=:course
                AND cs.section=:section
                AND m.name="quiz"
                AND qas.userid = :userid
                AND qasd.id is null
                GROUP BY qa.id
            ) l
            JOIN {question_attempt_steps} qas ON qas.questionattemptid = l.id
            JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid=qas.id
            ORDER BY qas.id
        ';
        $sqlresult = $DB->get_records_sql($query, ['userid'=>$userid, 'course' => $coursename, 'section' => $sectionidx]);

        // organise the data records by question attempt
        $attemptdata = [];
        $attemptrecords = [];
        foreach ($sqlresult as $record){
            // ignore non-question-type data
            if ($record->name[0]===":" || $record->name[0]==="-"){
                continue;
            }
            // store the field away as required
            $attempt = $record->attemptstepid;
            if (!array_key_exists($attempt, $attemptdata)){
                $attemptdata[$attempt] = [$record->name => $record->value];
                $attemptrecords[$attempt] = $record;
            } else {
                $attemptdata[$attempt][$record->name] = $record->value;
            }
        }

        // iterate over the problem attempts, grading them and preparing new records for DB application
        $newrecords = [];
        foreach ($attemptdata as $attemptid => $qtdata){
            $questionid = $attemptrecords[$attemptid]->questionid;
            $maxgrade   = $attemptrecords[$attemptid]->maxgrade;

            // calculate the score for the question
            $question       = \question_bank::load_question($questionid);
            $attemptstep    = new \question_attempt_step($qtdata);
            $question->apply_attempt_state($attemptstep);
            list($gradefraction, $gradestate) = $question->grade_response($qtdata);
            $newgrade = $gradefraction * $maxgrade;

            // queue a record for batched writing to database
            $newrecords[] = (object)[
                'attemptstepid' => $attemptid,
                'name'          => '-ludigrade',
                'value'         => $newgrade,
            ];

            // cache the set of question attempts that have had their grades updated just now
            $this->fixedupgrades[$attemptid] = $newgrade;
        }

        // write any calculated scores back to the database
        if ($newrecords){
            $DB->insert_records('question_attempt_step_data', $newrecords);
        }
    }

    /**
     * Private methods for use by Achievement Store getters
     */
    protected function get_user_achievements($userid, $prefix){
        // grab the achievements set for the current user, fetching from DB if required
        $userachievements = $this->load_user_achievements($userid);
        $allachievements = $userachievements->changes + $userachievements->cache;

        // filter the achievements container to only include results matching prefix
        $result = [];
        foreach ($allachievements as $key => $value){
            if (substr($key, 0, strlen($prefix)) === $prefix){
                $result[$key] = (int)$value;
            }
        }

        // return the result
        return $result;
    }

    /**
     * Private methods for use by Achievement Store setters
     */
    protected function set_user_achievements($userid, $prefix, $achievement, $value){
        // grab the achievements set for the current user, fetching from DB if required
        $userachievements = $this->load_user_achievements($userid);

        // store away the achievement in the appropriate array for future use
        $userachievements->changes[$prefix . $achievement] = $value;
    }

    /**
     * Private methods for use by Achievement Store getters & setters
     */
    private function load_user_achievements($userid){
        // grab the achievements set for the current user, fetching from DB if required
        if (array_key_exists($userid, $this->achievements)){
            return $this->achievements[$userid];
        }

        // fetch achievements from the database, ordering by id in order to ensure that more recent values overwrite older ones in subsequent processing
        global $DB;
        $dbresult = $DB->get_records('ludic_motivator_achievements', ['user' => $userid], 'id');

        // construct associative array of achievement names to values, storing internally for future reuse
        $userachievements = (object)[ 'cache' => [], 'changes' => [] ];
        $achievementcache = &$userachievements->cache;
        foreach ($dbresult as $record){
            $achievementcache[ $record->achievement ] = $record->value;
        }

        // store and return the result
        $this->achievements[$userid] = $userachievements;
        return $userachievements;
    }

    /**
     * Achievement Store : Flush any new achievement values to DB
     */
    private function flush_achievements(){
        global $DB;
        $timenow = time();
        $inserts = [];
        foreach ($this->achievements AS $userid => $userachievements){
            foreach ($userachievements->changes AS $key => $value){
                // ignore entries that haven't changed
                if (array_key_exists($key, $userachievements->cache) && $userachievements->cache[$key] == $userachievements->changes[$key]){
                        continue;
                }
                // queue up the insertion for bulk application at the end
                $inserts[] = (object)[
                    'user'          => $userid,
                    'achievement'   => $key,
                    'value'         => $value,
                    'timestamp'     => $timenow,
                ];
            }
            // cleanup the user record to avoid re-injection of the same data on subsequent flushes
            $userachievements->cache = $userachievements->changes + $userachievements->cache;
            $userachievements->changes = [];
        }

        // if we have new records then flush them to the database
        if ($inserts){
            $DB->insert_records('ludic_motivator_achievements', $inserts);
        }
    }
}
