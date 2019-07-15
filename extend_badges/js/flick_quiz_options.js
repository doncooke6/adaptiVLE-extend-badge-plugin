< script type = 'text/javascript' >
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
//
// @package    local_extend_badges
// @copyright  2015 AdaptiVLE
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
/**
* @module locoa
*/
window.onload = init;

function init() {

    // When a select is changed, look for the quiz based on the course id.
    // Display on the dropdown quiz select.

    alert('in js script');

    $('#id_course').change(function() {
        $('#id_quiz').load('course_getter.php?courseid=' + $('#id_course').val());
    });
}
< /script >
