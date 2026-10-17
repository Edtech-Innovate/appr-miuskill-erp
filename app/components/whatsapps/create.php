<?php 
  include '../../../includes/db-config.php';
  session_start();
?>

<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add WhatsApp Template</h5>
</div>
<form id="whatsapp_form" method="POST" action="/app/components/whatsapps/store">
  <div class="modal-body">
    
    <div class="form-group row pb-2">
      <div class="col-lg-6">
        <input type="text" autocomplete="off" id="name" name="name" class="form-control" placeholder="Template Name" required/>
      </div>
      <div class="col-lg-6">
        <select name="university" id="university" class="form-control" required>
          <option value="">Choose Vertical</option>
          <?php
            $universities = $conn->query("SELECT ID, CONCAT(Short_Name, ' (', Vertical, ')') as Name FROM Universities WHERE Status = 1");
            while($univeristy = $universities->fetch_assoc()) { ?>
              <option value="<?php echo $univeristy['ID']; ?>"><?php echo $univeristy['Name']; ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
  
    <div class="form-group row pb-2">
      <div class="col-lg-12">
        <select class="form-control custom-select" id="choose_var">
        <option value="">Choose Vaiables</option>
          <option value="{{ student_name }}">Student Name</option>
          <option value="{{ student_id }}">Student ID</option>
          <option value="{{ student_mobile }}">Student's Mobile</option>
          <option value="{{ student_email }}">Student's Email</option>
          <option value="{{ program }}">Program</option>
          <option value="{{ specialization }}">Specialization</option>
          <option value="{{ program_specialization }}">Program (Specialization)</option>
          <option value="{{ counsellor_name }}">Counsellor Name</option>
          <option value="{{ counsellor_contact_number }}">Counsellor Contact No</option>
          <option value="{{ counsellor_email }}">Counsellor Email</option>
        </select>
      </div>
    </div>

    <div class="form-group row pb-2">
      <div class="col-lg-12">
        <textarea id="summernote" class="form-control" name="template" rows="15"></textarea>
      </div>
    </div>

  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" id="submit-button" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Add</span>
        <span class="hidden-block">
          <i class="pg-icon">tick</i>
        </span>
      </button>
    </div>
  </div>
</form>

<script>
  $(document).ready(function() {
    $('#choose_var').change(function() {
      var cursorPos = $('#summernote').prop('selectionStart');
      var v = $('#summernote').val();
      var textBefore = v.substring(0,  cursorPos);
      var textAfter  = v.substring(cursorPos, v.length);
      $('#summernote').val(textBefore + $(this).val() + textAfter);
    });
  });
</script>


<script>
  $(function(){
    $("#whatsapp_form").on("submit", function(e){
      var formData = new FormData(this);
      $.ajax({
        url: this.action,
        type: 'post',
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
          if(data.status==200){
            $('.modal').modal('hide');
            notification('success', data.message);
            $('#tableWhatsapps').DataTable().ajax.reload(null, false);;
          }else{
            notification('danger', data.message);
          }
        }
      });
      e.preventDefault();
    });
  });
</script>

