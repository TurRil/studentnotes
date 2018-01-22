<?php
// Handle your POST data here...

if (isset($_POST['cancel'])) {
  $_SESSION['deleteID']=0;
  header('Location: '.addSession('index.php'));
  return;
}
if (isset($_POST['delete'])) { //kill this note
    $noteID=$_SESSION['deleteID'];
    $PDOX->queryDie("Delete  from {$p}student_note where student_note_id={$noteID}");
    $_SESSION['success'] = "Note Deleted.";
    $_SESSION['deleteID']=0;
    header('Location: '.addSession('index.php'));
    return;
}
 ?>
