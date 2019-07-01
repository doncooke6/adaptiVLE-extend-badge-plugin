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
 * @package   local_extend_badges
 * @copyright 2019 AdaptiVLE {@link http://www.adaptivle.co.uk}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /** Include config */
 require_once(dirname(__FILE__) . '../../../config.php');
 require_login();


 /** Include config */
 require_once(dirname($CFG->dirroot . '/local/extend_badges/badge_extend_form.php');
// get the form module
$badgeform = new local_badge_extend_form;
echo 'Form loaded .. ';exit;
// show the select list for all courses
var_dump($badgeform->getcourses());

// show the select list for one courses
var_dump($badgeform->getquizes());

// show select list of all badges
var_dump($badgeform->getbadges());
?>
