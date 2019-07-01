<?php
require_once("../../config.php");
global $DB;

// Get the parameter
$courseid = optional_param('courseid',  0,  PARAM_INT);

// If courseid exists
if($courseid) {

    // Do your query
    $query = 'SELECT * FROM {quiz} WHERE course = ' . $courseid;
    $quiz_arr = $DB->get_records_sql($query, null,  $limitfrom=0,  $limitnum=0);

    // echo your results, loop the array of objects and echo each one
    //echo "<option value='0'>All Students</option>";
    foreach ($quiz_arr as $quiz) {
        echo "<option value=".$quiz->id.">" . $quiz->name . "</option>";
    }

}
