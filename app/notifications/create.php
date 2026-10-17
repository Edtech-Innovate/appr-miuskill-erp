<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold"></span>Notification's</h5>
</div>
<form role="form" id="form-add-notifications" action="/app/notifications/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>Send to</label>
          <select class="full-width" style="border: transparent;" id="send_to" name="send_to" onchange="removeTable()" >
            <option value="">Choose</option>
            <option value="student">Student's</option>
            <option value="center">Center's</option>
            <option value="all">All</option>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>Heading</label>
          <!-- <input type="text" name="heading" class="form-control" placeholder="ex: Admission" required> -->
          <select class="full-width" style="border: transparent;" id="heading" name="heading" onchange="removeTable()" >
            <option value="">Choose</option>
            <option value="fee">Fee</option>
            <option value="admission">Admission</option>
            <option value="exam">Exam</option>
            <option value="other">Other</option>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group form-group-default required">
          <label>Publiced ON</label>
          <input type="date" name="date" class="form-control" placeholder="ex: J12-12-2023" required>
        </div>
      </div>
    </div>

    <div class="row">
        <div class="form-group form-group-default required">
          <label>Content</label>
          <textarea type="content" name="content" class="form-control" rows="50" cols="50" required>
          </textarea>
        </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label>Attachment*</label>
        <input name="file" type="file" accept=".zip,.rar,.7zip,application/pdf">
      </div>

      <div class="col-md-6" id="logo-view"></div>
    </div>  
  </div>
  <div class="modal-footer clearfix text-end">
    <div class="col-md-4 m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Save</span>
        <span class="hidden-block">
          <i class="pg-icon">tick</i>
        </span>
      </button>
    </div>
  </div>
</form>

<script>
  $(function(){
    $('#form-add-notifications').validate({
      rules: {
        content: {required:true},
        send_to: {required:true},
        heading: {required:true},
        date: {required:true},
      },
      highlight: function (element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function (element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  $("#form-add-notifications").on("submit", function(e){
    if($('#form-add-notifications').valid()){
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
              if(data.status==200){
                $('.modal').modal('hide');
                notification('success', data.message);
              }else{
                $(':input[type="submit"]').prop('disabled', false);
                notification('danger', data.message);
              }
          }
      });
      e.preventDefault();
    }
  });
</script>
