<?php
// Handle your POST data here...

if (isset($_POST['cancel'])) {
  $_SESSION['success'] = "Changes to note discarded";
  $_SESSION['editID']=0;
  header('Location: '.addSession('index.php'));
  return;
}
if (isset($_POST['save'])) { //seems we have a transaction to POST
    $nt=$_POST['notetype'];
    $noteID=$_POST['studentnoteid'];
    $data=array(":CONID"=>$CONTEXT->id,
                ":CID"=>$USER->id,
                ":SID"=>$_POST['studentid'],
                ":NT"=>$nt,
                ":ETT"=>$_POST['extratime'],
                ":PS"=>date('Y-m-d', strtotime($_POST['daterangestart'])),
                ":PE"=>date('Y-m-d', strtotime($_POST['daterangeend'])),
                ":AT"=>$_POST['absencetype'],
                ":PT"=>0,
                ":LTEXT"=>""
              );
    switch ($nt) {
      case 0:
        $data[':LTEXT']=$_POST['specialneeds'];
        break;
      case 1:
        $data[':LTEXT']=$_POST['absencereason'];
        break;
      case 2:
        $data[':PS']=date('Y-m-d', strtotime($_POST['conflictdate'])).' '.$_POST['conflicttimestart'].":00:00";
        $data[':PE']=date('Y-m-d', strtotime($_POST['conflictdate'])).' '.$_POST['conflicttimeend'].":00:00";
        $data[':LTEXT']=$_POST['conflictreason'];
        break;
      case 3:
        $data[':PS']=date('Y-m-d', strtotime($_POST['meetingdate']));
        $data[':PE']=date('Y-m-d', strtotime($_POST['meetingdate']));
        $data[':LTEXT']=$_POST['meetingtext'];
        $data[":PT"]=$_POST['privatetext'];
        break;
    }
    if ($noteID>0) {
      $data[':NID']=$noteID;
      $PDOX->queryDie("Update  {$p}student_note set
          context_id=:CONID,creator_id=:CID,student_id=:SID,updated_at=NOW(),period_start=:PS,period_end=:PE,
          extra_time=:ETT,note_type=:NT,absence_type=:AT,note_text=:LTEXT,private_text=:PT where student_note_id=:NID",
          $data
      );
      $_SESSION['success'] = "Note Updated";
    } else { //new note so insert
      $PDOX->queryDie("INSERT INTO {$p}student_note
          (context_id,creator_id,student_id,updated_at,created_at,period_start,period_end,
          extra_time,note_type,absence_type,note_text,private_text)
          VALUES ( :CONID,:CID, :SID, NOW(),NOW(),:PS,:PE,:ETT,:NT,:AT,:LTEXT,:PT )",
          $data
      );
      $_SESSION['success'] = "Note Created";
    }
    header('Location: '.addSession('index.php'));
    return;
}
 ?>
