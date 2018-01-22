<form method="post">

  <?php
    if (isset($_SESSION["studentlistid"])&&($_SESSION["studentlistid"]>0))
       $studentID=$_SESSION["studentlistid"];
    require "studentsearch.php"
  ?>
  <input type="hidden" name="slist" value="1" >
</form>
<hr/>
    <?php
       if (isset($_SESSION["studentlistid"]))
         require "outputstudentnotes.php";
     ?>

<script src="resulttable/resulttable.js"></script>
<script>
function init()
{
  initStudentSearch(true);
  var rt=$(".resulttable");
  if (rt.length>0)
     initResultTable(rt[0]);

  $('form input').on('keypress keyup', function(e) {
        return e.which !== 13;
  });
}

</script>
