<?php if(isset($_GET['university_id'])){
  require '../../../includes/db-config.php';
?>
  <link href="../../assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Add <span class="semi-bold">Late Fee</span></h6>
  </div>
  <form role="form" id="form-add-late-fees" action="/app/components/late-fees/store" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Fee</label>
            <input type="number" min="0" step="100" name="fee" class="form-control" placeholder="ex: 500">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Admission Session</label>
            <select class="full-width" style="border: transparent;" id="admission_session" name="admission_session[]" multiple>
              <option value="">Choose</option>
              <?php
                $admission_sessions = $conn->query("SELECT ID, Admission_Sessions.Name FROM Admission_Sessions WHERE Admission_Sessions.Status = 1 AND University_ID = ".$_GET['university_id']."");
                while($admission_session = $admission_sessions->fetch_assoc()) { ?>
                  <option value="<?=$admission_session['ID']?>"><?=$admission_session['Name']?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Start Date</label>
            <input type="tel" name="start_date" id="start_date" class="form-control" placeholder="dd-mm-yyyy" onchange="setEndDate(this.value);" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default">
            <label>End Date <i>(optional)</i></label>
            <input type="tel" name="end_date" id="end_date" class="form-control" placeholder="dd-mm-yyyy">
          </div>
        </div>
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

  <script src="../../assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script type="text/javascript" src="../../assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>
  
  <script type="text/javascript">
    $("#admission_session").select2();
    $("#start_date").mask("99-99-9999");
    $("#end_date").mask("99-99-9999");

    $('#start_date').datepicker({
      format: 'dd-mm-yyyy',
      autoclose: true,
      startDate: '+0d'
    });

    function setEndDate(value){
      $('#end_date').datepicker('remove');
      $('#end_date').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        startDate: new Date(value.split("-").reverse().join("-"))
      });
    }

    function setEndTime(value){
      $("#end_time").attr('min', value);
    }
  </script>

  <script>
    $(function(){
      $('#form-add-late-fees').validate({
        rules: {
          fee: {required:true},
          "admission_session[]": {required:true},
          start_date: {required:true},
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

    $("#form-add-late-fees").on("submit", function(e){
      if($('#form-add-late-fees').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('university_id', '<?=$_GET['university_id']?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache:false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function(data) {
            if(data.status){
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#tableLateFees').DataTable().ajax.reload(null, false);
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
<?php } ?>
