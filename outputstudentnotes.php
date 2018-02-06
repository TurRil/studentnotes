
<?php
  //retreive the list of students
  $SID=$_SESSION["studentlistid"];
  $student=$PDOX->rowDie("SELECT displayname,user_key FROM lti_user where user_id={$SID}");

  function  describeNoteType($nt) {
    switch ($nt) {
      case 0: return "Extra Time";
      case 1: return "Excused Absence";
      case 2: return "Test Conflict";
      case 3: return "Meeting";
    }
    return "unknown type";
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

  function allowNoteEdit($note)
  {
    global $USER;
    $isprivate=($note['creator_id']!=$USER->id)&&($note['note_type']==3)&&($note['private_text']);
    return !$isprivate;
  }

  function describeNote($note) {
    $nt=$note["note_type"];
    switch ($nt) {
      case 0:  return $note["extra_time"]." minutes: ".$note["note_text"];
      case 1:  if ($note["absence_type"]==6) return describeAbsenceType($note["absence_type"]).": {$note['note_text']}";
               else return describeAbsenceType($note["absence_type"]);
      case 2:  return $note["note_text"];
      case 3:
         global $USER;
         $isprivate=($note['creator_id']!=$USER->id)&&($note['note_type']==3)&&($note['private_text']);
         if ($isprivate) return "This meeting is private";
         else return $note["note_text"];
    }
    return "unknown type";
  }

  function describeNotePeriod($note) {
    $nt=$note["note_type"];
    $ps=date('d M Y', strtotime($note['period_start']));

    switch ($nt) {
      case 0:
      case 1: $pe=date('d M Y', strtotime($note['period_end']));
              return "{$ps} - {$pe}";
      case 2: $psh=date('H:00', strtotime($note['period_start']));
              $peh=date('H:00', strtotime($note['period_end']));
              return "{$ps} {$psh}-{$peh}";
      case 3:
          return "{$ps}";
    }
    return "unknown type";
  }
?>
<div class="resulttable">
    <table class='table table-bordered table-condensed table-striped wraptext'>
      <thead><tr class=info><th>Action</th><th>Note type</th><th>Date Range</th><th>Description</th><th>Created</th><th>Last modified</th></tr></thead>
      <tbody>

  <?php
    $notes=$PDOX->allRowsDie("SELECT student_note_id,creator_id,student_note.created_at,student_note.updated_at,period_start,period_end,extra_time,note_type,absence_type,note_text,private_text,lti_user.displayname as creatorname
      FROM student_note,lti_user where context_id={$CONTEXT->id} and creator_id=user_id and
      student_id={$SID} order by note_type,created_at");


    for ($i=0;$i<sizeof($notes);$i++) {
      $crdate=date('d M Y', strtotime($notes[$i]['created_at']));
      $moddate=date('d M Y', strtotime($notes[$i]['updated_at']));

       $but="<a href='.?modalEditID=".$notes[$i]['student_note_id']."'>Edit&nbsp;&nbsp;&nbsp;&nbsp;</button><br/>
            <a href='.?deleteID=".$notes[$i]['student_note_id']."'>Delete</button>";
       if (!allowNoteEdit($notes[$i])) $but="private";
       echo("<tr><td>{$but}</td><td>".describeNoteType($notes[$i]["note_type"])."</td><td>".describeNotePeriod($notes[$i])."</td><td class=wraptext>".describeNote($notes[$i])."</td><td>{$notes[$i]['creatorname']} <br/>{$crdate}</td><td>{$notes[$i]['creatorname']} <br/>{$moddate}</td></tr>");
    }
   ?>
 </tbody></table>
 </div>
