<?php
// Handle your POST data here...
if (isset($_POST['rlist'])) { //seems we have a transaction to POST
    $_SESSION["rlisttype"]=$_POST['notetype'];
    $_SESSION["rliststart"]=date('Y-m-d', strtotime($_POST['daterangestart']));
    $_SESSION["rlistend"]=date('Y-m-d', strtotime($_POST['daterangeend']));
    header('Location: '.addSession('index.php'));
    return;
}
?>
