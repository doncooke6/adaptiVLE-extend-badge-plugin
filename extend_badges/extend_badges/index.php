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
 // Include config.php.

require_once(__DIR__.'/../../config.php');
require_once($CFG->dirroot.'/local/extend_badges/lib.php');
require_once($CFG->dirroot.'/local/extend_badges/renderer.php');

require_login();

global $DB, $OUTPUT;


$title = get_string('pluginname', 'local_extend_badges');
$heading = get_string('heading', 'local_extend_badges');
$url = new moodle_url('/local/extend_badges/');
$context = context_system::instance();

$PAGE->set_pagelayout('admin');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($heading);

   $s = 'SELECT id, course, quiz, quiz_grade, badge, enabled, timecreated, timemodified
           FROM {local_badge_requests}';
   if ($rs = $DB->get_records_sql($s)) {

      $badge_extends = array();
      $context = (object) [
          'extendbadges' => [],
      ];

      foreach ($rs as $record) {
          // taking the badge extension record and add it to the form data for the template renderer
          $c = new stdClass;
          // $c->id           = $record->id;
          // $c->course       = 'x'. $record->course;
          // $c->quiz         = 'x'. $record->quiz;
          // $c->quiz_grade   = $record->quiz_grade;
          // $c->badge        = $record->badge;
          // $c->enabled      = $record->enabled;
          // $c->timecreated  = $record->timecreated;
          // $c->timemodified = $record->timemodified;

          $c->id           = $record->id;
          $c->course       = get_badgecourse($record->course);
          $c->quiz         = get_badgequiz($record->quiz);
          $c->quiz_grade   = $record->quiz_grade;
          $c->badge        = get_badge($record->badge);
          $c->enabled      = $record->enabled;
          $c->timecreated  = $record->timecreated;
          $c->timemodified = $record->timemodified;

          // Add this one to the end of our list
          $badge_extends[] = $c;
      }

      $context->extendbadges[] = $badge_extends;

      // ## Mustache version
      //echo $OUTPUT->header();
      //echo $OUTPUT->render_from_template('local_extend_badges/badge_extend', $context);
      //echo $OUTPUT->footer();

      echo $OUTPUT->header();
      $bdge = new badge_extend_renderer($PAGE, null);
      $bdge->render_badge_extend_browse($badge_extends);
      $bdge->render_badge_extend_add();
      echo $OUTPUT->footer();
} else {

  //TODO No rows to show - deal with this
  echo $OUTPUT->header();
  $bdge = new badge_extend_renderer;
  $bdge->no_badge_extend_found();
  $bdge->render_badge_extend_add();
  echo $OUTPUT->footer();
}
