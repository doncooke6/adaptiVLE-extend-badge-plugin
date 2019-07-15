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
require_once("../../config.php");

require_login();
global $DB;

// Get the parameter.
$courseid = optional_param('courseid',  0,  PARAM_INT);

// If courseid exists.
if ($courseid) {

    // Do your query.
    $query = 'SELECT * FROM {quiz} WHERE course = ' . $courseid;
    $quizarr = $DB->get_records_sql($query, null,  $limitfrom = 0,  $limitnum = 0);

    // Echo your results, loop the array of objects and echo each one.
    foreach ($quizarr as $quiz) {
        echo "<option value=".$quiz->id.">" . $quiz->name . "</option>";
    }

}
