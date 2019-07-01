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
$badgerequestid = required_param('badgeid', PARAM_INT);

global $DB;

       $s = 'SELECT enabled
             FROM {local_badge_requests}
             WHERE id = :badgeid';

       $parms = array('badgeid' => $badgerequestid);

       if ($rec = $DB->get_record_sql($s,$parms))  {
           if ($rec->enabled) {
               $newenabled = 0;
           } else {
              $newenabled = 1;
           }
           $s = 'UPDATE {local_badge_requests}
                 SET enabled = :newenabled
                 WHERE id = :badgeid';

           $parms = array('newenabled' => $newenabled , 'badgeid' => $badgerequestid);
           // update the record toggling the value
           $DB->execute($s, $parms);
       }

 // get the value of the enabled field for the badge and toggle /**
 // Rerender the index page - TODO Refactor to do this in ajax - not page refresh
   header("Location: " . $CFG->wwwroot.'/local/extend_badges/index.php');
