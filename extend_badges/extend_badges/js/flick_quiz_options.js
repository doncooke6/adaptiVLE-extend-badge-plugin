<script type='text/javascript'>
// Standard license block omitted.
/*
 * @package    local_extend_badges
 * @copyright  2015 AdaptiVLE
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

         /**
          * @module locoa
          */
          window.onload = init;

          function init() {

              // When a select is changed, look for the quiz based on the course id
              // and display on the dropdown quiz select

              alert('in js script');

              $('#id_course').change(function() {
                  $('#id_quiz').load('course_getter.php?courseid=' + $('#id_course').val());
              });
          }
</script>
