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


 require_once($CFG->dirroot.'/lib/badgeslib.php');

function local_extend_badges_cron() {
     global $DB, $USER;



     // run query to isolate the badges which require extensions
    $s = "SELECT id, course, quiz, quiz_grade, badge, timemodified
            FROM {local_badge_requests}
           WHERE  enabled = true";

    if ($rsbadgerequests = $DB->get_records_sql($s)) {

      // for each match
      foreach ($rsbadgerequests as $recbadgereq) {

          // look for quiz results mdl_quiz_grade that exceed the grade critria given for the option
          //TODO Limit the quiz issued date  based on the badge issiued request date

          $s = 'SELECT id, quiz, userid, grade
                FROM {quiz_grades}
                WHERE quiz = :quizid AND
                      grade >= :requiredgrade AND
                      timemodified >= :timemodified';

              //TODO Limit the quiz issued date  based on the badge issiued request date


          $parms = array('quizid' => $recbadgereq->quiz, 'requiredgrade' => $recbadgereq->quiz_grade, 'timemodified' => $recbadgereq->timemodified);
          // for each relevant user
          if ($rsquizgrades = $DB->get_records_sql($s, $parms)) {
                foreach ($rsquizgrades as $recquizgrade) {
                    // - if they have not already been awarded the badge
                    $s = 'SELECT id FROM {local_badge_request_issued}
                           WHERE userid = :userid
                             AND badgerequestid = :badgereqid';

                    $parms = array('userid' => $recquizgrade->userid, 'badgereqid' => $recbadgereq->id);

                    // for each relevant user
                    if (!$rsbadgereqs = $DB->get_record_sql($s, $parms)) {
                      //  No record of this award - so given the required badge to the user
                      //  and add an audit row to the issue table

                      $newbadge = new badge($recbadgereq->badge);
                      $user = $recquizgrade->userid; // default to logged in user

                      // before the issuing of the badge
                      // Second parameter is the open badge support flag - @Lewis 24-6-2019 This is required by the client,

                      // TODO Externalise this string - if required :)
                      echo 'Issued an extended badge for user : ' . $user;

                      $newbadge->issue($user, true); // second parameter is openbadges support - assuming this is not required TODO
                      // after the issuing of the badge
                      // issue the badge to them

                      // create a record in the issued tble to show that the user has received the badge
                      $s = 'INSERT INTO {local_badge_request_issued}  (badgerequestid, userid, timecreated)
                                  VALUES (:badgereqid, :userid, :timecreated)';
                      $parms = array('badgereqid' => $recbadgereq->id, 'userid' => $recquizgrade->userid, 'timecreated' => time());
                      $DB->execute($s, $parms);

                    } // end of add request
              }
          }
        } // End of loop through badge requests
  }
}


function local_extend_badges_extend_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE;

// TODO Needs to be fixed TODO
  // $cm = $PAGE->cm;
  //   if (!$cm) {
  //       return;
  //   }
  //
  //   $context = $cm->context;
  //   $course = $PAGE->course;
  //
  //   if (!$course) {
  //       return;
  //   }
  //   // Only add this settings item on non-site course pages.
  //   if (!$PAGE->course or $PAGE->course->id == 1) {
  //       return;
  //   }


    //TODO Need this to work for permissions // TODO
    // Only let users with the appropriate capability see this settings item.
    //if (!has_capability('local/extend_badges:admin', context_course::instance($PAGE->course->id))) {
    //    return;
    //}
    // TODO // End of permissions check

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

function get_badgecourse($courseid){
global $DB;

   $s = "SELECT fullname from {course} WHERE id = :course";
   $parms = array("course" => $courseid);

   if ($rscourse = $DB->get_records_sql($s, $parms)) {
      foreach ($rscourse as $reccourse) {
           $coursename = $reccourse->fullname;
        }
      }
       else {
           $coursename = 'Not Located';
        }
   return $coursename;
}

function get_badgequiz($quizid){
  global $DB;

     $s = "SELECT name from {quiz} WHERE id = :quiz";
     $parms = array("quiz" => $quizid);

     if ($rsquiz = $DB->get_records_sql($s, $parms))
      {
        foreach ($rsquiz as $recquiz) {
          $quizname = $recquiz->name;
        }
      }
      else { $quizname = 'Not Located';   }
     return $quizname;
}

function get_badge($badgeid){
  global $DB;

     $s = "SELECT name from {badge} WHERE id = :badge";
     $parms = array("badge" => $badgeid);

     if ($rsbadge = $DB->get_records_sql($s, $parms))
      {
      foreach ($rsbadge as $recbadge) {
        $badgename = $recbadge->name;
          }
         }
       else { $badgename = 'Not Located';
        }
     return $badgename;
   }



    function getcourses($courseid = 0) {
          global $DB;

           $courses = array();

           $s = 'SELECT id, fullname FROM {course}
                  WHERE visible = 1';

           if ($rscourses =  $DB->get_records_sql($s)) {
              foreach($rscourses as $reccourse) {
                 $courses[$reccourse->id] =  $reccourse->fullname;
              }
             return $courses;
          }
       }

        function getquizzes($courseid = 0, $quiz = 0){
          global $DB;

            $quizes = array();

            $s = 'SELECT id, name FROM {quiz}';

            if ($courseid != 0) { // add the specific filter
                $s.= ' WHERE course = :courseid';
                $parms = array('courseid' => $courseid);
            } else {
                    $parms = array();
            }

                if ($rsquizes =  $DB->get_records_sql($s, $parms)) {
                 foreach($rsquizes as $recquiz) {
                    $quizes[$recquiz->id] = $recquiz->name;
                 }

              // if quiz matches the parm then flag this as the selected one
              // add the quiz to the list to be used to populate the dropdown
            return $quizes;
         }
       }


        function getcriteria($crit = 0) {
          global $DB;

           $criteria = array();

           // populate the criteria list from 1 to 0
           for ($i = 1 ; $i < 101 ; $i++) {
             $criteria[$i] =  $i;
           }

         return $criteria;
        }

        function getbadges($courseid = 0, $badgeid = 0) {
          global $DB;

          $badges = array();

          $s = 'SELECT id, name FROM {badge}';

          if ($courseid != 0) { // add the specific filter
              $s.= ' WHERE course = :courseid';
             }

          $parms = array('courseid' => $courseid);

          if ($rsbadges =  $DB->get_records_sql($s,$parms)) {
               foreach($rsbadges as $recbadge) {
                  $badges[$recbadge->id] = $recbadge->name;
               }
          return $badges;
          }
        }

        function add_extend_record($courseid, $quizid, $criteriagradeid ,$badgeid) {
          global $DB;

          $s = 'INSERT INTO {local_badge_requests} (course, quiz, quiz_grade, badge, enabled, timecreated, timemodified)
                     VALUES (:course, :quiz, :criteria, :badge, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())';
                $parms = array('course' => $courseid,
                               'quiz' => $quizid,
                               'criteria' => $criteriagradeid,
                               'badge' =>  $badgeid);
          $DB->execute($s, $parms);
        }

        function update_extend_record($extendid, $courseid, $quizid, $criteriagradeid ,$badgeid) {
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
                               'badge' =>  $badgeid,
                               'extendid' => $extendid);

         // var_dump($s); var_dump($parms);die;
          $DB->execute($s, $parms);

        }
