<?php

class badge_extend_renderer extends renderer_base {
public function render_badge_extend_browse($arrBadgeExtends) {
  global $CFG, $OUTPUT;
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
';

  // <a href="{{{togglelink}}}">
  //          {{#toggletarget}}
  //          {{#pix}}i/show, moodle, {{#str}} enable, moodle {{/str}}{{/pix}}
  //          {{/toggletarget}}

              if ($badgedets->enabled == 1) {
                echo '<td>';
                $editurl = $CFG->wwwroot .'/local/extend_badges/toggle_badge_active.php?badgeid=' . $badgedets->id ;
                echo $OUTPUT->action_icon($editurl, new pix_icon('t/hide', 'Enabled'));
                echo '</td>';
              } else {
                echo '<td>';
                $editurl = $CFG->wwwroot .'/local/extend_badges/toggle_badge_active.php?badgeid=' . $badgedets->id ;
                echo $OUTPUT->action_icon($editurl, new pix_icon('t/show', 'Disabled'));
                echo '</td>';
              }

  //          {{^toggletarget}}
  //          {{#pix}}i/hide, moodle, {{#str}} disable, moodle {{/str}}{{/pix}}
  //          {{/toggletarget}}
  //        </a>';'
  //

            echo '<td>';
            $editurl = $CFG->wwwroot .'/local/extend_badges/update_badge_extend.php?editid=' . $badgedets->id ;
            echo $OUTPUT->action_icon($editurl, new pix_icon('t/edit', 'Edit'));
            echo '</td>';


            echo '<td>';
            $editurl = $CFG->wwwroot .'/local/extend_badges/delete_extend_badge.php?id=' . $badgedets->id ;
            echo $OUTPUT->action_icon($editurl, new pix_icon('t/delete', 'Delete'));
            echo '</td>';

           echo '</form></td>
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
