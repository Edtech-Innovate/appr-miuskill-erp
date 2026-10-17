<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
    <i class="pg-icon">close</i>
  </button>
  <h5><span class="semi-bold">Upload Marksheet</span></h5>
</div>

<form role="form" id="form-add-marksheet" action="/app/upload-marksheet/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Marksheet Name</label>
          <input type="text" name="file_name" class="form-control" placeholder="e.g., Department of Engineering & Technology" required>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>File (PDF only)</label>
          <input type="file" name="file_path" class="form-control-file" accept="application/pdf" required>
        </div>
      </div>
    </div>
  </div>

  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 mt-2">
      <button type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Save</span>
        <span class="hidden-block"><i class="pg-icon">tick</i></span>
      </button>
    </div>
  </div>
</form>

<script>
  $(function () {
    $('#form-add-marksheet').validate({
      rules: {
        file_name: { required: true },
        file_path: { required: true }
      },
      highlight: function (element) {
        $(element).closest('.form-group').addClass('has-error');
      },
      unhighlight: function (element) {
        $(element).closest('.form-group').removeClass('has-error');
      }
    });

    $('#form-add-marksheet').on('submit', function (e) {
      e.preventDefault();

      const fileInput = $('input[name="file_path"]')[0];
      const file = fileInput.files[0];

      // Check for PDF MIME type
      if (!file || file.type !== 'application/pdf') {
        notification('danger', 'Only PDF files are allowed.');
        return;
      }

      if ($(this).valid()) {
        const form = this;
        const formData = new FormData(form);
        $(':input[type="submit"]').prop('disabled', true);

        $.ajax({
          url: form.action,
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function (response) {
            if (response.status === 200) {
              $('.modal').modal('hide');
              notification('success', response.message);
              $('#upload_marksheet-table').DataTable().ajax.reload(null, false);
            } else {
              $(':input[type="submit"]').prop('disabled', false);
              notification('danger', response.message || 'Something went wrong.');
            }
          },
          error: function () {
            $(':input[type="submit"]').prop('disabled', false);
            notification('danger', 'Server error. Please try again.');
          }
        });
      }
    });
  });
</script>
