<?php
// Handle your POST data here...
if (isset($_SESSION["rlisttype"])) { //no set up look for the defaults
  $nt=$_SESSION["rlisttype"];
  $ps=date('d F Y', strtotime($_SESSION["rliststart"]));
  $pe=date('d F Y', strtotime($_SESSION["rlistend"]));
} else {
  $nt=-1;
  $ps=date('d F Y', strtotime(date('Y-01-01')));
  $pe=date('d F Y', strtotime(date('Y-12-31')));
}
?>

<form method="post">
  <div class="form-group">
    <label for="type" >Generate list of notes</label>
    <select class="form-control" id="notetype" name="notetype" placeholder="Select Note Type">
      <option  value=-1 disabled <?php echo ($nt<0)?"selected":""?> hidden>Please select note type</option>
      <option value=0 <?php echo ($nt==0)?"selected":"";?>>Extra Time</option>
      <option value=1 <?php echo ($nt==1)?"selected":"";?>>Excused Absence</option>
      <option value=2 <?php echo ($nt==2)?"selected":"";?>>Test Conflict</option>
      <option value=3 <?php echo ($nt==3)?"selected":"";?>>Meeting</option>
    </select>
  </div>
  <div class="form-group" id="daterangeblock">
    <label>Date range</label>
    <div class=row>
      <div class="col-sm-2">
        <input type="text" class="form-control datepick" id=daterangestart name=daterangestart value="<?php echo $ps;?>">
      </div>
      <label class="col-sm-1 col-form-label" >to</label>
      <div class="col-sm-2">
        <input type="text" class="form-control datepick" id=daterangeend name=daterangeend value="<?php echo $pe;?>">
      </div>
    </div>
  </div>

  <input type="submit" name="rlist" value="Show Records" class="btn btn-primary btn-lg">

</form>
<hr/>
<?php
  if (isset($_SESSION["rlisttype"]))
    require "outputreportlist.php";
  unset($_SESSION["rlisttype"]);
?>

<script src="resulttable/resulttable.js"></script>
<script>
function init()
{
  $('.datepick').datepicker({
    dateFormat: "d MM yy",
    changeMonthx: true
  });

  var rt=$(".resulttable");
  if (rt.length>0)
     initResultTable(rt[0]);
}
</script>
