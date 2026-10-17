<?php if(isset($_GET['university_id'])){
  require '../../../includes/db-config.php';
  $university_id = intval($_GET['university_id']);
  ?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Add <span class="semi-bold">Scheme</span></h6>
  </div>
  <form role="form" id="form-add-schemes" action="/app/components/schemes/store" method="POST">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="ex: A-22">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Fee Structure</label>
            <select class="full-width" style="border: transparent;" id="fee_structures" name="fee_structures[]" multiple>
              <?php
                $fee_structures = $conn->query("SELECT ID, Name FROM Fee_Structures WHERE University_ID = $university_id AND Status = 1");
                while($fee_structure = $fee_structures->fetch_assoc()) { ?>
                  <option value="<?php echo $fee_structure['ID']; ?>"><?php echo $fee_structure['Name']; ?></option>
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

      $('#form-add-schemes').validate({
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

    $("#form-add-schemes").on("submit", function(e){
      if($('#form-add-schemes').valid()){
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
<?php } ?>
