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
require_once(dirname(__FILE__) . '/../../../config.php');

require_once($CFG->dirroot.'/lib/badgeslib.php');
// Lets start with a known user and a known badge and issue the badge to the userid

require_login();


$testBadgeID = 1; // the badge for our test

$testbadge = new badge($testBadgeID);
$testuser =  4; // default to logged in user

// before the issuing of the badge
$testbadge->issue($testuser, false);
// after the issuing of the badge
