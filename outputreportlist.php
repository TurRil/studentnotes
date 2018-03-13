<?php


  function outputHeader() {
    $nt=$_SESSION["rlisttype"];
    switch ($nt) {
      case 0: echo "<tr class=info><th>Actions</th><th>Student name</th><th>Allocation</th><th>Valid Dates</th><th>Special Needs</th><th>Created</th><th>Last Modified</th></tr>";
              return;
      case 1: echo "<tr class=info><th>Actions</th><th>Student name</th><th>Reason for absence</th><th>Dates absent</th><th>Created</th><th>Last modified</th></tr>";
              return;
      case 2: echo "<tr class=info><th>Actions</th><th>Student name</th><th>Date</th><th>Time period</th><th>Reason</th><th>Created</th><th>Last modified</th></tr>";
              return;
      case 3: echo "<tr class=info><th>Actions</th><th>Student name</th><th>Date</th><th>Reason</th><th>Created</th><th>Last modified</th></tr>";
              return;
    }

  }



  function outputRow($note) {
    global $USER;
    $nt=$_SESSION["rlisttype"];
    $ps=date('d M Y', strtotime($note['period_start']));
    $pe=date('d M Y', strtotime($note['period_end']));
    $psh=date('H:00', strtotime($note['period_start']));
    $peh=date('H:00', strtotime($note['period_end']));

    $but="<a href='.?modalEditID=".$note['student_note_id']."'>Edit&nbsp;&nbsp;&nbsp;&nbsp;</button><br/>
          <a href='.?deleteID=".$note['student_note_id']."'>Delete</button>";
    $crdate=date('d M Y', strtotime($note['created_at']));
    $moddate=date('d M Y', strtotime($note['updated_at']));

    $meetbut=$but;
    $notetext=$note['note_text'];
    $isprivate=($note['creator_id']!=$USER->id)&&($note['private_text']);
    if ($isprivate) {
      $notetext="This meeting is private.";
      $meetbut="private";
    }

    switch ($nt) {
      case 0: echo "<tr><td>{$but}</td><td>{$note['displayname']}</td><td>{$note['extra_time']} min/hour</td><td>{$ps} - {$pe}</td><td class=wraptext>{$note['note_text']}<td>{$note['creatorname']} <br/>{$crdate}</td><td>{$note['creatorname']} <br/>{$moddate}</td></tr>";
              return;
      case 1: $abreason=describeAbsenceType($note['absence_type']);
              $abtext=(($note['absence_type']==6)?(": ".$note['note_text']):"");
              echo "<tr><td>{$but}</td><td>{$note['displayname']}</td><td class=wraptext>{$abreason}{$abtext}</td><td>{$ps} - {$pe}</td><td>{$note['creatorname']} <br/>{$crdate}</td><td>{$note['creatorname']} <br/>{$moddate}</td></tr>";
              return;
      case 2: echo "<tr><td>{$but}</td><td>{$note['displayname']}</td><td>{$ps}</td><td>{$psh} - {$peh}</td><td class=wraptext>{$note['note_text']}</td><td>{$note['creatorname']}<br/>{$crdate}</td><td>{$note['creatorname']} <br/>{$moddate}</td></tr>";
              return;
      case 3: echo "<tr><td>{$meetbut}</td><td>{$note['displayname']}</td><td>{$ps}</td><td class=wraptext>{$notetext}</td><td>{$note['creatorname']} <br/>{$crdate}</td><td>{$note['creatorname']} <br/>{$moddate}</td></tr>";
              return;
    }
  }


  function  describeAbsenceType($at) {
    switch ($at) {
      case 0: return "Leave of Absence";
      case 1: return "Religious";
      case 2: return "Medical";
      case 3: return "Compassionate";
      case 4: return "Sport and Activities";
      case 5: return "Test Clash";
      case 6: return "Other";
    }
    return "unknown type";
  }



?>
 <div class="resulttable">

    <table class='table table-bordered table-condensed table-striped wraptext'>
      <thead><?php outputHeader()?></thead>
      <tbody>

  <?php
    //retreive the list of students
    $notes=$PDOX->allRowsDie("SELECT student_note_id,creator_id,student_note.created_at,student_note.updated_at,period_start,period_end,extra_time,note_type,absence_type,note_text,private_text,
      U.displayname as displayname,U.user_key as user_key,C.displayname as creatorname
      FROM student_note,lti_user U,lti_user C where student_id=U.user_id and creator_id=C.user_id and
      context_id={$CONTEXT->id} and
      note_type={$_SESSION['rlisttype']} and
      period_start<'{$_SESSION['rlistend']}' and
      period_end>'{$_SESSION['rliststart']}'
      order by created_at DESC,displayname,period_start");

    for ($i=0;$i<sizeof($notes);$i++)
       outputRow($notes[$i]);
   ?>
      </tbody>
    </table>
 </div>
