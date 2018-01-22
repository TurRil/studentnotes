<?php
  $editID=$_SESSION['editID'];
  $nt=-1;
  $studentID=0;
  $et=-1;
  $at=-1;
  $notetext="";
  $psd="";
  $ped="";
  $psh=-1;
  $peh=-1;
  $privatetext=0;
  $isowned=0;

  if ($editID>0) {
    //load the old note
    $row=$PDOX->rowDie("select student_id,updated_at,created_at,period_start,period_end,extra_time,note_type,absence_type,note_text,private_text,creator_id
       from {$p}student_note where student_note_id={$editID}");
    $nt=intval($row['note_type']);
    $studentID=$row['student_id'];
    $et=$row['extra_time'];
    $at=$row['absence_type'];
    $notetext=$row['note_text'];
    $privatetext=$row['private_text'];
    $psd=date('d F Y', strtotime($row['period_start']));
    $ped=date('d F Y', strtotime($row['period_end']));
    $psh=date('H', strtotime($row['period_start']));
    $peh=date('H', strtotime($row['period_end']));
    $isowned=($row['creator_id']==$USER->id);
  } else $editID=0;
?>
<form method="post">
  <input name="studentnoteid" type="hidden" value=<?php echo $editID; ?> >
  <div class="form-group">
    <label for="type">Student note type</label>
    <select class="form-control" id="notetype" name="notetype" <?php echo $editID?"disabled":""; ?> >
      <option value=-1 <?php echo ($nt<0)?"selected":""?> hidden>Please select note type</option>
      <option value=0 <?php echo ($nt==0)?"selected":""?>>Extra time</option>
      <option value=1 <?php echo ($nt==1)?"selected":""?>>Excused absence</option>
      <option value=2 <?php echo ($nt==2)?"selected":""?>>Test conflict</option>
      <option value=3 <?php echo ($nt==3)?"selected":""?>>Meeting</option>
    </select>
  </div>
  <?php require "studentsearch.php" ?>
  <div class="form-group" id="extratimeblock">
    <label for="type" >Extra time allocation</label>
    <select class="form-control" id="extratime" name="extratime">
      <option  value=-1 disabled <?php echo ($et>0)?"selected":""?> hidden>Please select extra time</option>
      <option value=0 <?php echo ($et==0)?"selected":""?>>0 minutes per hour</option>
      <option value=10 <?php echo ($et==10)?"selected":""?>>10 minutes per hour</option>
      <option value=15<?php echo ($et==15)?"selected":""?>>15 minutes per hour</option>
      <option value=20<?php echo ($et==20)?"selected":""?>>20 minutes per hour</option>
    </select>
  </div>

  <div class="form-group" id="absenceblock">
    <label>Type of absence</label>
    <select class="form-control" id="absencetype"  name="absencetype">
      <option  value=-1 disabled <?php echo ($at<0)?"selected?":""?> hidden>Select type</option>
      <option value=0 <?php echo ($at==0)?"selected":""?>>Leave of Absence</option>
      <option value=1 <?php echo ($at==1)?"selected":""?>>Religious</option>
      <option value=2 <?php echo ($at==2)?"selected":""?>>Medical</option>
      <option value=3 <?php echo ($at==3)?"selected":""?>>Compassionate</option>
      <option value=4 <?php echo ($at==4)?"selected":""?>>Sports and activities</option>
      <option value=5 <?php echo ($at==6)?"selected":""?>>Test clash</option>
      <option value=6 <?php echo ($at==6)?"selected":""?>>Other</option>
    </select>
    <label >Reason</label>
    <input type="text" class="form-control" id="absencereason" name="absencereason" value="<?php echo $notetext;?>">
  </div>


  <div class="form-group" id="daterangeblock">
    <label >Date range</label>
    <div class="row">
      <div class="col-sm-2">
        <input type="text" class="form-control datepick" id=daterangestart name=daterangestart value="<?php echo $psd;?>">
      </div>
      <label class="col-sm-2 col-form-label" >to</label>
      <div class="col-sm-2">
        <input type="text" class="form-control datepick" id=daterangeend name=daterangeend value="<?php echo $ped;?>">
      </div>
    </div>
  </div>

  <div id="conflictblock">
    <div class="form-group" >
      <label>Date of conflict</label>
      <input type="test" class="form-control datepick" id="conflictdate" name="conflictdate" value="<?php echo $psd;?>">
    </div>
    <div class="form-group" >
      <label>Conflicting time period</label>
      <div class="row">
        <div class="col-sm-2" >
          <select class="form-control" id="conflicttimestart" name="conflicttimestart">
            <option  value=-1 disabled <?php echo ($psh<0)?"selected":""?> hidden>Select start time</option>
            <option value=6 <?php echo ($psh==6)?"selected":""?> >06:00</option>
            <option value=7 <?php echo ($psh==7)?"selected":""?> >07:00</option>
            <option value=8 <?php echo ($psh==8)?"selected":""?> >08:00</option>
            <option value=9 <?php echo ($psh==9)?"selected":""?> >09:00</option>
            <option value=10 <?php echo ($psh==10)?"selected":""?> >10:00</option>
            <option value=11 <?php echo ($psh==11)?"selected":""?> >11:00</option>
            <option value=12 <?php echo ($psh==12)?"selected":""?> >12:00</option>
            <option value=13 <?php echo ($psh==13)?"selected":""?> >13:00</option>
            <option value=14 <?php echo ($psh==14)?"selected":""?> >14:00</option>
            <option value=15 <?php echo ($psh==15)?"selected":""?> >15:00</option>
            <option value=16 <?php echo ($psh==16)?"selected":""?> >16:00</option>
            <option value=17 <?php echo ($psh==17)?"selected":""?> >17:00</option>
            <option value=18 <?php echo ($psh==18)?"selected":""?> >18:00</option>
            <option value=19 <?php echo ($psh==19)?"selected":""?> >19:00</option>
            <option value=20 <?php echo ($psh==20)?"selected":""?> >20:00</option>
            <option value=21 <?php echo ($psh==21)?"selected":""?> >21:00</option>
            <option value=22 <?php echo ($psh==22)?"selected":""?> >22:00</option>
            <option value=23 <?php echo ($psh==23)?"selected":""?> >23:00</option>
          </select>
        </div>
        <label class="col-sm-2 col-form-label">to</label>
        <div class="col-sm-2" >
          <select class="form-control" id="conflicttimeend" name="conflicttimeend">
            <option  value=-1 disabled  <?php echo ($peh<0)?"selected":""?>  hidden>Select end time</option>
            <option value=6 <?php echo ($peh==6)?"selected":""?> >06:00</option>
            <option value=7 <?php echo ($peh==7)?"selected":""?> >07:00</option>
            <option value=8 <?php echo ($peh==8)?"selected":""?> >08:00</option>
            <option value=9 <?php echo ($peh==9)?"selected":""?> >09:00</option>
            <option value=10 <?php echo ($peh==10)?"selected":""?> >10:00</option>
            <option value=11 <?php echo ($peh==11)?"selected":""?> >11:00</option>
            <option value=12 <?php echo ($peh==12)?"selected":""?> >12:00</option>
            <option value=13 <?php echo ($peh==13)?"selected":""?> >13:00</option>
            <option value=14 <?php echo ($peh==14)?"selected":""?> >14:00</option>
            <option value=15 <?php echo ($peh==15)?"selected":""?> >15:00</option>
            <option value=16 <?php echo ($peh==16)?"selected":""?> >16:00</option>
            <option value=17 <?php echo ($peh==17)?"selected":""?> >17:00</option>
            <option value=18 <?php echo ($peh==18)?"selected":""?> >18:00</option>
            <option value=19 <?php echo ($peh==19)?"selected":""?> >19:00</option>
            <option value=20 <?php echo ($peh==20)?"selected":""?> >20:00</option>
            <option value=21 <?php echo ($peh==21)?"selected":""?> >21:00</option>
            <option value=22 <?php echo ($peh==22)?"selected":""?> >22:00</option>
            <option value=23 <?php echo ($peh==23)?"selected":""?> >23:00</option>
          </select>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label>Reason for conflict (optional)</label>
      <textarea type="text" class="form-control" id=conflictreason name=conflictreason rows=5><?php echo $notetext;?></textarea>
    </div>
  </div>


  <div class="form-group" id="specialneedsblock">
    <label for="type">Special needs (optional)</label>
    <textarea type="text" class="form-control" id=specialneeds name=specialneeds rows=5><?php echo $notetext;?></textarea>
  </div>


  <div id="meetingblock">
    <div class="form-group">
      <label>Date of meeting</label>
      <input type="text" class="form-control datepick" id="meetingdate" name="meetingdate" value="<?php echo $psd;?>">
    </div>
    <div class="form-group">
      <label for="type">Reason for meeting</label>
      <textarea type="text" class="form-control" id="meetingtext" name="meetingtext" rows=5><?php echo $notetext;?></textarea>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="1" <?php echo $privatetext?"checked":"";?> id="privatetext" name="privatetext">
      <label class="form-check-label" for="privatetext">
        This note is private and will only be displayed to the original creator.
      </label>
    </div>
  </div>

  <input type="submit" name="save" value="Save Note" class="btn btn-primary btn-lg"  id="submitbutton" onclick="return validateForm()">
  <input type="submit" name="cancel" value="Cancel" class="btn btn-lg"  id="cancelbutton">
</form>

<script>

function init()
{
  showControlsByType();
  $('#notetype').change(showControlsByType);
  $('.datepick').datepicker({
    dateFormat: "d MM yy",
    changeMonthx: true
  });

  initStudentSearch();
}

function showControlsByType()
{
  var type=$('#notetype').val();
  $('#studentblock').toggle(type>=0);
  $('#submitbutton').toggle(type>=0);
  $('#cancelbutton').toggle(type>=0);
  $('#extratimeblock').toggle(type==0);
  $('#specialneedsblock').toggle(type==0);
  $('#daterangeblock').toggle((type==0)||(type==1));
  $('#absenceblock').toggle(type==1);
  $('#conflictblock').toggle(type==2);
  $('#meetingblock').toggle(type==3);
}


function setValidationText(parentid,text)
{
  var p=$('#'+parentid);
  p.find('.validationtext').remove();
  if (text) {
    var valtext=$('<p>').addClass("form-text validationtext alert alert-danger").text(text);
    p.append(valtext);
    validationsFailed=true;
  }
}

function clearAllValidations()
{
  $('.validationtext').remove();
  validationsFailed=false;
}

function validateForm()
{
  clearAllValidations();
  if (!selectedStudentID)
    setValidationText('studentblock','Please select a student');
  var nt=Number($('#notetype').val());
  switch (nt) {
    case 0:
      if ($('#extratime').val()<0)  setValidationText('extratimeblock','Please select an extra time allowance.');
      if ($('#daterangestart').val()>$('#daterangeend').val())  setValidationText('daterangeblock','End date must be before start date');
      if ($('#daterangeend').val()=="")  setValidationText('daterangeblock','Please enter an end date.');
      if ($('#daterangestart').val()=="")  setValidationText('daterangeblock','Please enter a start date.');
      break;
    case 1:
      if ($('#absencetype').val()<0)  setValidationText('absenceblock','Please select a type of absence');
      if ($('#daterangestart').val()>$('#daterangeend').val())  setValidationText('daterangeblock','End date must be before start date');
      if ($('#daterangeend').val()=="")  setValidationText('daterangeblock','Please enter an end date.');
      if ($('#daterangestart').val()=="")  setValidationText('daterangeblock','Please enter a start date.');
    break;
    case 2:
      if ($('#conflicttimestart').val()>$('#conflicttimeend').val()) setValidationText('conflictblock','Start time must be before end time.');
      if (($('#conflicttimestart').val()<0)||($('#conflicttimeend').val()<0)) setValidationText('conflictblock','Please select the time range of the conflict.');
      if ($('#conflictdate').val()=="")  setValidationText('conflictblock','Please select a date for the conflict.');
      break;
    case 3:
      if ($('#meetingdate').val()=="")  setValidationText('meetingblock','Please select the date of the meeting.');
      break;

  }
  if (validationsFailed) return false;
  //enable note ttype select so that it posts
  $("#notetype").prop( "disabled", false );
  return true;
}
</script>
