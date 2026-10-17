<?php if(isset($_GET['id'])){
  require '../../../includes/db-config.php';
  $id = intval($_GET['id']);
  $scheme = $conn->query("SELECT ID, Name, University_ID, Fee_Structure FROM Schemes WHERE ID = $id");
  if($scheme->num_rows>0){
    $scheme = mysqli_fetch_assoc($scheme);
    $alloted_fee_structures = !empty($scheme['Fee_Structure']) ? json_decode($scheme['Fee_Structure'], true) : array();
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Edit <span class="semi-bold">Scheme</span></h6>
  </div>
  <form role="form" id="form-edit-schemes" action="/app/components/schemes/update" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo $scheme['Name'] ?>" placeholder="ex: Sem">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Fee Structure</label>
            <select class="full-width" style="border: transparent;" id="fee_structures" name="fee_structures[]" multiple>
              <?php
                $fee_structures = $conn->query("SELECT ID, Name FROM Fee_Structures WHERE University_ID = ".$scheme['University_ID']);
                while($fee_structure = $fee_structures->fetch_assoc()) { ?>
                  <option value="<?php echo $fee_structure['ID']; ?>" <?php echo !empty($alloted_fee_structures) && in_array($fee_structure['ID'], $alloted_fee_structures) ? 'selected' : '' ?>><?php echo $fee_structure['Name']; ?></option>
              <?php } ?>
            </select>
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

  <script>
    $(function(){
      $("#fee_structures").select2({
        placeholder: "Choose",
        allowClear: true
      });

      $('#form-edit-schemes').validate({
        rules: {
          name: {required:true},
          'fee_structures[]':{required:true}
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

    $("#form-edit-schemes").on("submit", function(e){
      if($('#form-edit-schemes').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('university_id', '<?=$scheme['University_ID']?>');
        formData.append('id', '<?=$scheme['ID']?>');
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
              $('#tableSchemes').DataTable().ajax.reload(null, false);
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
