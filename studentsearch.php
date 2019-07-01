  <div id="studentblock">
  <div class="form-group">
    <label for="student">Student name or ID</label>
    <div class="input-group">
      <input type="hidden" class="form-control" id=studentid name=studentid>
      <input type="text" class="form-control" placeholder="Search" id=studentsearch>
      <div class="input-group-btn">
        <button class="btn btn-default" id='studentsearchgo' type='button'>
          <i class="glyphicon glyphicon-search">Search</i>
        </button>
      </div>
    </div>
  </div>
  <div id=studentselectmessage class='info' style="display:none">Multiple results found, please select correct student.</div>
  <table id="studenttable" class="table table-bordered table-condensed table-striped" style="display:none">
    <thead>
    <tr  class="info"><th class=hidden>id</th><th>Student Name</th></tr>
    </thead>
    <tbody>
  <?php
    //retreive the list of students  $CONTEXT
    $students=$PDOX->allRowsDie("SELECT lti_user.user_id,lti_user.displayname, JSON_UNQUOTE(ifnull(JSON_EXTRACT(lti_user.`json`,'$.sourcedId'), LOWER(SUBSTRING(lti_user.email, 1, LOCATE('@', lti_user.email) - 1)))) as eid, user_key FROM lti_user,lti_membership
        where lti_user.user_id=lti_membership.user_id and context_id={$CONTEXT->id} order by displayname");
    for ($i=0;$i<sizeof($students);$i++)
       echo("<tr><td class=hidden>{$students[$i]["user_id"]}</td><td><a href=# data-rid='{$i}' data-uid='{$students[$i]["user_id"]}' data-eid='{$students[$i]["eid"]}'>{$students[$i]["displayname"]} ({$students[$i]["eid"]})</a></td></tr>");
   ?>
 </tbody></table>
 <div id=studentsearcherror class='alert alert-danger' style="display: none"></div>
</div>
<script>

var selectedStudentID='';
var selectedStudentRef='';
var selectedStudentName='';
var validationsFailed=false;
var studentAutoSubmit=false;

<?php
  if (isset($studentID)) {
     $result=$PDOX->RowDie("select user_key,displayname from lti_user where user_id={$studentID}");
     $studentName=$result['displayname'];
     $studentRef=$result['user_key'];
     echo "selectedStudentID={$studentID};";
     echo "selectedStudentRef='{$studentRef}';";
     echo "selectedStudentName='{$studentName}';";
  }
?>

function initStudentSearch(auto)
{
  $('#studentsearchgo').click(function() {if (selectedStudentID) clearStudentFilter(); else showStudentFilter();});
  $('#studentsearch').keydown(function() { if (selectedStudentID) clearStudentFilter();}); //type anything in and clear the setting
  showStudentFilter();
  if (auto) studentAutoSubmit=true;
}

function showStudentFilter()
{
   if (selectedStudentID) {
     $('#studentid').val(selectedStudentID);
     $('#studentsearch').val(selectedStudentName);
     $('#studenttable').toggle(false);
     $('#studentselectmessage').toggle(false);
     $('#studentsearcherror').toggle(false);
  } else {
      $('#studentid').val('');
      var search=$('#studentsearch').val().toLowerCase();
      $('#studenttable').toggle(search!='');

      var r = $('#studenttable tbody tr a').filter(function(i, el){
          var a = $(el);
          return (a.text().toLowerCase().includes(search) || a.data('eid').toLowerCase().includes(search));
      });

      $('#studenttable tbody tr a').parent().parent().hide();
      if (r.length > 0) {
        r.parent().parent().show();
        $('#studentsearcherror').toggle(false);
      } else {
        $('#studentsearcherror').text("No match for '"+search+"' in student enrolled in this course. Please try again.").toggle(true);
        $('#studenttable').toggle(false);
      }

    $('#studentselectmessage').toggle((r.length>1)&&(search!=''));
  }
}

function clearStudentFilter()
{
  selectedStudentID=0;
  $('#studentsearch').val("");
  showStudentFilter();
}


function selectStudent(i)
{
  var row=$('#studenttable tbody tr:eq('+i+')')
  selectedStudentID=row.children().eq(0).text();
  selectedStudentRef=row.children().eq(1).text();
  selectedStudentName=row.children().eq(2).text();
  showStudentFilter();
  if (studentAutoSubmit)
    $('form').submit();
}

</script>
