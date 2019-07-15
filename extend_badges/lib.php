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
 * Version information
 *
 * @package    local_extend_badges
 * @copyright 2019 AdaptiVLE {@link http://www.adaptivle.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');
 require_once($CFG->dirroot.'/lib/badgeslib.php');

function local_extend_badges_cron() {
     global $DB, $USER;
     // Run query to isolate the badges which require extensions.
    $s = "SELECT id, course, quiz, quiz_grade, badge, timemodified
            FROM {local_badge_requests}
           WHERE  enabled = true";

    if ($rsbadgerequests = $DB->get_records_sql($s)) {

        // For each match.
        foreach ($rsbadgerequests as $recbadgereq) {

            // Look for quiz results mdl_quiz_grade that exceed the grade critria given for the option.=
// New version
            $s = "SELECT u.id AS userid,
                         u.firstname AS Name,
                         u.lastname AS Surname,
                         ROUND(gg.finalgrade,2) AS Score,
                         ROUND(gg.rawgrademax,2) AS MAX,
                         ROUND(gg.finalgrade / gg.rawgrademax * 100 ,2) AS quiz_grade,
                         gi.iteminstance AS quiz
            FROM {course} AS c
            JOIN {context} AS ctx ON c.id = ctx.instanceid
            JOIN {role_assignments} AS ra ON ra.contextid = ctx.id
            JOIN {user} AS u ON u.id = ra.userid
            JOIN {grade_grades} AS gg ON gg.userid = u.id
            JOIN {grade_items} AS gi ON gi.id = gg.itemid
            JOIN {course_categories} AS cc ON cc.id = c.category
            WHERE  gi.courseid = c.id AND
                   gi.itemmodule = 'quiz' AND
                   gi.iteminstance = :quizid AND
                   ROUND(gg.finalgrade / gg.rawgrademax * 100 ,2) > :requiredgrade AND
                   gg.timemodified >= :timemodified";
// End of new version 15th July 2019

            // TODO Limit the quiz issued date  based on the badge issiued request date.
            $parms = array('quizid' => $recbadgereq->quiz,
                            'requiredgrade' => $recbadgereq->quiz_grade,
                            'timemodified' => $recbadgereq->timemodified);
            // For each relevant user.
            if ($rsquizgrades = $DB->get_records_sql($s, $parms)) {
                foreach ($rsquizgrades as $recquizgrade) {
                    // If they have not already been awarded the badge.
                    $s = 'SELECT id FROM {local_badge_request_issued}
                           WHERE userid = :userid
                           AND badgerequestid = :badgereqid';

                     $parms = array('userid' => $recquizgrade->userid, 'badgereqid' => $recbadgereq->id);

                    // For each relevant user.
                    if (!$rsbadgereqs = $DB->get_record_sql($s, $parms)) {
                        // No record of this award - so given the required badge to the user.
                        $newbadge = new badge($recbadgereq->badge);
                        $userid = $recquizgrade->userid;

                        // Before the issuing of the badge.
                        // Second parameter is the open badge support flag - @Lewis 24-6-2019 This is required by the client.
                        echo 'Issued an extended badge for user : ' . $userid;

                        $newbadge->issue($userid, true);
                        // Second parameter is openbadges support - assuming this is not required TODO
                        // After the issuing of the badge.
                        // Issue the badge to them.

                        // Add an audit row to the issue table.
                        // Create a record in the issued tble to show that the user has received the badge.
                        $s = 'INSERT INTO {local_badge_request_issued}  (badgerequestid, userid, issuedate)
                                    VALUES (:badgereqid, :userid, :timecreated)';
                        $parms = array('badgereqid' => $recbadgereq->id,
                                       'userid' => $recquizgrade->userid,
                                       'timecreated' => time());
                        $DB->execute($s, $parms);
                        echo 'Created an extended badge issued record for user  : ' . $userid;
                    } // End of add request.
                }
            }
        } // End of loop through badge requests.
    }
}


function local_extend_badges_extend_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE, $ADMIN;

    if (has_capability('local/extend_badges:admin', context_system::instance())) {
        if ($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
            $stroptionheading = get_string('extendbadges', 'local_extend_badges');
            $url = new moodle_url('/local/extend_badges/index.php', array('id' => $PAGE->course->id));
            $extendbadgesnode = navigation_node::create(
                  $stroptionheading,
                  $url,
                  navigation_node::NODETYPE_LEAF,
                  'extend_badges',
                  'extend_badges',
                  new pix_icon('t/addcontact', $stroptionheading)
            );
            if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
                $extendbadgesnode->make_active();
            }
            $settingnode->add_node($extendbadgesnode);
        }
    }
}

function get_badgecourse($courseid) {
    global $DB;

    $s = "SELECT fullname from {course} WHERE id = :course";
    $parms = array("course" => $courseid);

    if ($rscourse = $DB->get_records_sql($s, $parms)) {
        foreach ($rscourse as $reccourse) {
            $coursename = $reccourse->fullname;
        }
    } else {
         $coursename = 'Not Located';
    }
    return $coursename;
}

function get_badgequiz($quizid) {
    global $DB;

     $s = "SELECT name from {quiz} WHERE id = :quiz";
     $parms = array("quiz" => $quizid);

    if ($rsquiz = $DB->get_records_sql($s, $parms)) {
        foreach ($rsquiz as $recquiz) {
            $quizname = $recquiz->name;
        }
    } else {
        $quizname = 'Not Located';
    }
    return $quizname;
}

function get_badge($badgeid) {
    global $DB;

    $s = "SELECT name from {badge} WHERE id = :badge";
    $parms = array("badge" => $badgeid);

    if ($rsbadge = $DB->get_records_sql($s, $parms)) {
        foreach ($rsbadge as $recbadge) {
            $badgename = $recbadge->name;
        }
    } else {
        $badgename = 'Not Located';
    }
    return $badgename;
}



function getcourses($courseid = 0) {
    global $DB;
    $courses = array();
    $s = 'SELECT id, fullname FROM {course}
            WHERE visible = 1';

    if ($rscourses = $DB->get_records_sql($s)) {
        foreach ($rscourses as $reccourse) {
              $courses[$reccourse->id] = $reccourse->fullname;
        }
        return $courses;
    }
}

function getquizzes($courseid = 0, $quiz = 0) {
    global $DB;

    $quizes = array();

    $s = 'SELECT id, name FROM {quiz}';

    if ($courseid != 0) { // Add the specific filter.
        $s .= ' WHERE course = :courseid';
        $parms = array('courseid' => $courseid);
    } else {
        $parms = array();
    }

    if ($rsquizes = $DB->get_records_sql($s, $parms)) {
        foreach ($rsquizes as $recquiz) {
            $quizes[$recquiz->id] = $recquiz->name;
        }

        // If quiz matches the parm then flag this as the selected one.
        // Add the quiz to the list to be used to populate the dropdown.
        return $quizes;
    }
}


function getcriteria($crit = 0) {
    global $DB;

    $criteria = array();

    // Populate the criteria list from 1 to 0.
    for ($i = 1; $i < 101; $i++) {
        $criteria[$i] = $i;
    }
    return $criteria;
}

function getbadges($courseid = 0, $badgeid = 0) {
    global $DB;

    $badges = array();

    $s = 'SELECT id, name FROM {badge}';

    if ($courseid != 0) { // Ad the specific filter.
        $s .= ' WHERE course = :courseid';
    }

    $parms = array('courseid' => $courseid);

    if ($rsbadges = $DB->get_records_sql($s, $parms)) {
        foreach ($rsbadges as $recbadge) {
             $badges[$recbadge->id] = $recbadge->name;
        }
        return $badges;
    }
}

function add_extend_record($courseid, $quizid, $criteriagradeid, $badgeid) {
    global $DB;

    $s = 'INSERT INTO {local_badge_requests} (course, quiz, quiz_grade, badge, enabled, timecreated, timemodified)
             VALUES (:course, :quiz, :criteria, :badge, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';
    $parms = array('course' => $courseid,
                       'quiz' => $quizid,
                       'criteria' => $criteriagradeid,
                       'badge' => $badgeid);
     $DB->execute($s, $parms);
}

function update_extend_record($extendid, $courseid, $quizid, $criteriagradeid, $badgeid) {
    global $DB;

    $s = "UPDATE {local_badge_requests}
           SET course = :course,
              quiz = :quiz,
              quiz_grade = :criteria,
              badge = :badge,
              timemodified =  UNIX_TIMESTAMP()
        WHERE id = :extendid";

    $parms = array('course' => $courseid,
                       'quiz' => $quizid,
                       'criteria' => $criteriagradeid,
                       'badge' => $badgeid,
                       'extendid' => $extendid);

    $DB->execute($s, $parms);

}
