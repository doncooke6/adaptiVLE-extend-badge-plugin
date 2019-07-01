<?php

class badge_extend_renderer {
public function render_badge_extend_browse($arrBadgeExtends) {
  global $CFG;
   echo '  <div>
  <table width ="100%">
  <tr><th width="20%">Course</th><th width = "20%">Quiz</th><th width = "10%">Criteria</th><th width = "20%">Badge</th></tr>
';

foreach($arrBadgeExtends as $badgedets) {

    echo '
     <tr>
       <td>' . $badgedets->course . '</td>
       <td>'. $badgedets->quiz .'</td>
       <td>'. $badgedets->quiz_grade . ' %</td>
       <td>'. $badgedets->badge .'</td>
       <td>
      <!--   <a href="{{{togglelink}}}">
           {{#toggletarget}}
           {{#pix}}i/show, moodle, {{#str}} enable, moodle {{/str}}{{/pix}}
           {{/toggletarget}}
           {{^toggletarget}}
           {{#pix}}i/hide, moodle, {{#str}} disable, moodle {{/str}}{{/pix}}
           {{/toggletarget}}
         </a>
         -->
        <td>
           <form action="' . $CFG->wwwroot .'/local/extend_badges/update_badge_extend.php?editid=' . $badgedets->id .'" method="get">
           <button type="submit" name="editsubmit" value="editsubmit" class="btn-link">Edit</button>
           <input type="hidden" id="editid" name="editid" value="' . $badgedets->id. '">
           </form>
        </td>
        <td>

           <form action="' . $CFG->wwwroot .'/local/extend_badges/delete_extend_badge.php' . '" method="post">
           <button type="submit" name="deletesubmit" value="deletesubmit" class="btn-link">Delete</button>
           <input type="hidden" id="id" name="id" value="' . $badgedets->id . '">
           </form></td>
      </tr>';
    }

    echo '</table></div>';
  }


 public function render_badge_extend_add()
 {
   global $CFG;
  echo '<p></p><p></p><div>
         <form action="' . $CFG->wwwroot .'/local/extend_badges/add_badge_extend.php' . '" method="get">
         <button type="submit">Add a new badge extension</button></form>
    </div>';
 }

 public function no_badge_extend_found() {
     echo '<p>No badge extension requests found</p>';
 }
}
