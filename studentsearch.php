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
        <button class="btn btn-default" id='studentsearchcancel' type='button'>
          <i class="glyphicon glyphicon-remove">Clear</i>
        </button>
      </div>
    </div>
  </div>
  <div id=studentselectmessage class='info'>Multiple results found, please select correct student.</div>
  <table id="studenttable" class="table table-bordered table-condensed table-striped">
    <thead>
    <tr  class="info"><th class=hidden>id</th><th class=col-md-2>Student ID</th><th>Student Name</th></tr>
    </thead>
    <tbody>
  <?php
    //retreive the list of students  $CONTEXT
    $students=$PDOX->allRowsDie("SELECT lti_user.user_id,displayname,user_key FROM lti_user,lti_membership
        where lti_user.user_id=lti_membership.user_id and context_id={$CONTEXT->id} order by displayname");
    for ($i=0;$i<sizeof($students);$i++)
       echo("<tr><td class=hidden>{$students[$i]["user_id"]}</td><td><a href=# onclick='selectStudent({$i})''>{$students[$i]["user_key"]}</a></td><td><a href=# onclick='selectStudent({$i})'>{$students[$i]["displayname"]}</a></td></tr>");
   ?>
 </tbody></table>
 <div id=studentsearcherror class='alert alert-danger'></div>
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
  $('#studentsearchgo').click(showStudentFilter);
  $('#studentsearchcancel').click(clearStudentFilter);
  showStudentFilter();
  if (auto) studentAutoSubmit=true;
}

function showStudentFilter()
{
   if (selectedStudentID) {
     $('#studentid').val(selectedStudentID);
     $('#studentsearch').val(selectedStudentRef+" - "+selectedStudentName);
     $('#studentsearch').attr('readonly', true);
     $('#studenttable').toggle(false);
     $('#studentsearchgo').toggle(false);
     $('#studentsearchcancel').toggle(true);
     $('#studentsearchcancel').click(clearStudentFilter);
     $('#studentsearcherror').toggle(false);
     $('#studentselectmessage').toggle(false);
  } else {
     $('#studentid').val('');
     var search=$('#studentsearch').val().toLowerCase();
     $('#studenttable').toggle(search!='');
     $('#studentsearchgo').toggle(true);
     $('#studentsearch').attr('readonly', false);
     $('#studentsearchcancel').toggle(false);
     var count=0;
     $('#studenttable tbody tr').each(function (index) {
        var row=$(this);
        var content=row.text();
        if (content.toLowerCase().indexOf(search)===-1)
          row.hide();
       else {
          row.show();
          count+=1;
       }
     });
     if (count==0) {
       $('#studentsearcherror').text("No match for '"+search+"' in student names or IDs enrolled in this course. Please try again.").toggle(true);
       $('#studenttable').toggle(false);
     }
     else
       $('#studentsearcherror').toggle(false);
     $('#studentselectmessage').toggle((count>1)&&(search!=''));

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
