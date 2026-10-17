<?php if(isset($_GET['id'])){
  require '../../../includes/db-config.php';
  $id = intval($_GET['id']);
  $admission_session = $conn->query("SELECT ID, Name, Scheme, University_ID FROM Admission_Sessions WHERE ID = $id");
  if($admission_session->num_rows>0){
    $admission_session = $admission_session->fetch_assoc();
    $allotedSchemes = json_decode($admission_session['Scheme'], true);
    $dates = array();
    foreach($allotedSchemes['dates'] as $key=>$value){
      $dates[$key] = date("d-m-Y", strtotime($value));
    }
?>
  <!-- Modal -->
  <link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Edit <span class="semi-bold">Admission Sessions</span></h6>
  </div>
  <form role="form" id="form-edit-admission-sessions" action="/app/components/admission-sessions/update" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo $admission_session['Name'] ?>" placeholder="ex: Jan-22">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Scheme</label>
            <select class="full-width" style="border: transparent;" onchange="getSchemeDate()" id="scheme" name="scheme[]" multiple>
              <option value="">Choose</option>
              <?php
                $schemes = $conn->query("SELECT ID, Schemes.Name FROM Schemes WHERE University_ID = ".$admission_session['University_ID']);
                while($scheme = $schemes->fetch_assoc()) { ?>
                  <option value="<?=$scheme['ID']?>" <?php echo in_array($scheme['ID'], $allotedSchemes['schemes']) ? 'selected' : '' ?>><?=$scheme['Name']?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
      <div id="dates">
        
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
  <script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>
  <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script>
    $(function(){
      $("#scheme").select2({
        placeholder: 'Choose',
        allowClear: true
      })

      $('#form-edit-admission-sessions').validate({
        rules: {
          name: {required:true},
          'scheme[]': {required:true},
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

    function getSchemeDate(){
      $("#dates").html('');
      var values = JSON.parse('<?=json_encode($dates)?>');
      var schemeIds = $('#scheme').val();
      var names = $('#scheme option:selected').toArray().map(item => item.text)
      for(var i = 0; i < schemeIds.length; i++){
        $("#dates").append('<div class="row"><div class="col-md-12 d-flex justify-content-between">\
          <div class="m-t-10"><b>'+names[i]+'</b></div>\
          <div>\
            <div class="form-group form-group-default required">\
              <label>Start Date</label>\
              <input type="text" name="start_date['+schemeIds[i]+']" value="'+values[schemeIds[i]]+'" id="start_date_'+i+'" class="form-control" placeholder="ex: dd-mm-yyyy" required>\
            </div>\
          </div>\
        </div></div>');

        $("#start_date_"+i).mask("99-99-9999");
        $('#start_date_'+i).datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true
        });
      }
    }

    getSchemeDate();

    $("#form-edit-admission-sessions").on("submit", function(e){
      if($('#form-edit-admission-sessions').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('university_id', '<?=$admission_session['University_ID']?>');
        formData.append('id', '<?=$admission_session['ID']?>');
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
              $('#tableAdmissionSessions').DataTable().ajax.reload(null, false);
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
<?php }} ?>
