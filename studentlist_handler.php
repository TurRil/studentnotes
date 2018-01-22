<?php
// Handle your POST data here...
if (isset($_POST['slist'])) { //seems we have a transaction to POST
    $_SESSION[studentlistid]=$_POST['studentid'];    
    header('Location: '.addSession('index.php'));
    return;
}
?>
