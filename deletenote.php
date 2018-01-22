
<div class="modal show" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="post">
        <input name="studentnoteid" type="hidden" value=<?php echo $editID; ?> >

      <div class="modal-header">
        <h5 class="modal-title">Delete Note</h5>
      </div>
      <div class="modal-body">
        <form method="post">
          <h4>Are you sure you want to delete this note?</h4>
          <input name="studentnoteid" type="hidden" value=<?php echo $editID; ?> >
          <input type="submit" name="delete" value="Yes, delete note" class="btn btn-primary btn-lg"  id="submitbutton" onclick="return validateForm()">
          <input type="submit" name="cancel" value="No, keep note" class="btn btn-lg"  id="cancelbutton">
        </form>
      </div>

    </div>
  </div>
</div>
