<?php
include '../../../includes/db-config.php';
session_start();
?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

<div class="modal-header">
  <h5>Add Email Template</h5>
</div>
<form id="emails_form" method="POST" action="/app/components/emails/store" enctype="multipart/form-data">
  <div class="modal-body">

    <div class="form-group row pb-2">
      <div class="col-lg-6">
        <input type="text" autocomplete="off" id="name" name="name" class="form-control" placeholder="Template Name" required />
      </div>
      <div class="col-lg-6">
        <input type="text" autocomplete="off" id="subject" name="subject" class="form-control" placeholder="Subject" required />
      </div>
    </div>

    <div class="form-group row pb-2">
      <div class="col-lg-6">
        <select name="university" id="university" class="form-control" required>
          <option value="">Vertical</option>
          <?php
          $universities = $conn->query("SELECT ID, CONCAT(Short_Name, ' (', Vertical, ')') as Name FROM Universities WHERE Status = 1");
          while ($university = $universities->fetch_assoc()) { ?>
            <option value="<?php echo $university['ID']; ?>"><?php echo $university['Name']; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="col-lg-6">
        <select class="form-control custom-select" id="choose_var_email">
          <option value="">Choose Vaiables</option>
          <option value="{{ student_name }}">Student Name</option>
          <option value="{{ student_id }}">Student ID</option>
          <option value="{{ student_mobile }}">Student's Mobile</option>
          <option value="{{ student_email }}">Student's Email</option>
          <option value="{{ student_password }}">Student's Password for LMS</option>
          <option value="{{ program }}">Program</option>
          <option value="{{ specialization }}">Specialization</option>
          <option value="{{ university_name }}">University Name</option>
          <option value="{{ university_vertical }}">University Vertical</option>
          <option value="{{ contact_information }}">Contact Information</option>
        </select>
      </div>
    </div>

    <div class="form-group row pb-2">
      <div class="col-lg-12">
        <textarea id="summernote" name="template"></textarea>
      </div>
    </div>

    <div class="form-group row pb-2">
      <div class="col-lg-12">
        <label class="control-label">Attachments <i class="text-muted">(optional)</i></label>
        <input type="file" accept="application/msword, .doc, .docx, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/jpg, image/jpeg, image/png, image/gif" class="form-control" name="attachments[]" multiple>
      </div>
      <i class="text-muted">Only png, jpg, jpeg, pdf, doc(x), ppt(x), xls(s), txt file supported. File size cannot be more than 2MB, and max 4 files can be uploaded.</i>
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
    $("#choose_var_email").change(function() {
      var choose_variab = $('#choose_var_email option:selected').val();
      $('#summernote').summernote('editor.saveRange');
      $('#summernote').summernote('editor.focus');
      $('#summernote').summernote('editor.insertText', choose_variab);
    })
  });
  $("#choose_var_email").change();
</script>

<script>
  $(function() {
    $('#summernote').summernote({
      placeholder: 'Template',
      tabsize: 2,
      height: 360,
      callbacks: {
        onImageUpload: function(files, editor, welEditable) {
          for (var i = files.length - 1; i >= 0; i--) {
            sendFile(files[i], this);
          }
        }
      }
    });
  })

  function sendFile(file, el) {
    var form_data = new FormData();
    form_data.append('file', file);
    $.ajax({
      data: form_data,
      type: "POST",
      url: '/app/components/emails/files/store',
      cache: false,
      contentType: false,
      processData: false,
      success: function(url) {
        $(el).summernote('editor.insertImage', url);
      }
    });
  }
</script>



<script>
  $(function() {
    $("#emails_form").on("submit", function(e) {
      var formData = new FormData(this);
      $.ajax({
        url: this.action,
        type: 'post',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            notification('success', data.message);
            $('#tableEmails').DataTable().ajax.reload(null, false);;
          } else {
            toastr.error(data.message);
          }
        }
      });
      e.preventDefault();
    });
  });
</script>