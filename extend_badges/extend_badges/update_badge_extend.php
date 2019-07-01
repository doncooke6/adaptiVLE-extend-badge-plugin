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

 /** Include config */
 require_once(dirname(__FILE__) . '/../../config.php');

 require_once($CFG->dirroot . '/local/extend_badges/lib.php');
 require_once($CFG->dirroot . '/local/extend_badges/badge_extend_form.php');

$badgerequestid = optional_param('editid', 0, PARAM_INT);
$reentrycourseid = optional_param('courseid', 0, PARAM_INT);

// Retrieve and populate the values from the badge extension request
if ($badgerequestid != 0) {
   global $DB;

   $s = 'SELECT id, course, quiz, quiz_grade, badge, enabled, timecreated, timemodified
           FROM {local_badge_requests}
           WHERE id = :badgeextendid';
  $parms = array('badgeextendid' => $badgerequestid);

  if ($recbadgeext = $DB->get_record_sql($s, $parms)) {


     $courseid = $recbadgeext->course;
     $quizid = $recbadgeext->quiz;
     $badgeid = $recbadgeext->badge;
     $criteria = $recbadgeext->quiz_grade;
    }
  }
  else {
    $badgerequestid = 0;
    $courseid = 0;
    $quizid = 0;
    $badgeid = 0;
    $criteria = 0;
  }

  // On reentry (via js callback) if course has changed - then change course to limit quiz dropdown
  if ($reentrycourseid !== 0) {
     $courseid = $reentrycourseid;
  }

require_login();

$title = get_string('pluginname', 'local_extend_badges');
$heading = get_string('heading', 'local_extend_badges');
$url = new moodle_url('/local/extend_badges/');
$context = context_system::instance();

$PAGE->set_pagelayout('admin');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($heading);

$PAGE->requires->jquery();

$formparams = new stdClass;
$mform = new local_badge_extend_form($badgerequestid, $courseid, $quizid, $badgeid, $criteria);

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form

  header("Location: " . $CFG->wwwroot.'/local/extend_badges/index.php');

} else if ($fromform = $mform->get_data()) {
  //In this case you process validated data. $mform->get_data() returns data posted in form.

  update_extend_record($fromform->badgeextendid, $fromform->course, $fromform->quiz, $fromform->criteriagrade ,$fromform->badgetobeissued);
  header("Location: " . $CFG->wwwroot.'/local/extend_badges/index.php');

} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.
  $toform = new stdClass;

  //Set default data (if any)
  $mform->set_data($toform);
  //displays the form
  echo $OUTPUT->header();
  $mform->display();
}
