<?php
  if(isset($_GET['university_id'])){
    require '../../../includes/db-config.php';

    $university_id = intval($_GET['university_id']);

    $max = $conn->query("SELECT Max(Min_Duration) as Duration FROM Sub_Courses WHERE University_ID = ".$university_id);
    $max = $max->fetch_assoc(); 
    $max = $max['Duration'];

    $modes = $conn->query("SELECT GROUP_CONCAT(Name SEPARATOR '/') as Mode FROM Modes WHERE University_ID = ".$university_id." GROUP BY University_ID");
    $modes = $modes->fetch_assoc();
    $modes = $modes['Mode'];
  }

  
?>
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h6>Add <span class="semi-bold">Fee Dropdowns</span></h6>
</div>
  
<form role="form" id="form-add-fee-dropdown" action="/app/components/fee-dropdowns/store" method="POST">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Reg Fee" required>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default">
          <label>Admission Type <i>(optional)</i></label>
          <select class="full-width" style="border: transparent;" name="admission_type">
            <option value="">Choose</option>
            <?php $admission_types = $conn->query("SELECT ID, Name FROM Admission_Types WHERE University_ID = ".$university_id);
              while($admission_type = $admission_types->fetch_assoc()){
                echo '<option value="'.$admission_type['ID'].'">'.$admission_type['Name'].'</option>';
              }
            ?>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Duration <i>(optional)</i></label>
          <select class="full-width" style="border: transparent;" name="duration">
            <option value="">Choose</option>
            <?php for($i=1; $i<=$max; $i++){ ?>
              <option value="<?=$i?>"><?=$i?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>
    
    <div class="row mt-1">
      <div class="col-md-12">
        <div class="row mt-3 mb-2">
          <div class="col-md-6 font-weight-bold">Fee Heads</div>
          <div class="col-md-6 font-weight-bold"><?=$modes?></div>
        </div>
        <?php $ids = array(); $fee_structures = $conn->query("SELECT ID, Name, Fee_Applicable_ID FROM Fee_Structures WHERE Is_Constant = 1 AND University_ID = ".$university_id); 
          while($fee_structure = $fee_structures->fetch_assoc()){ $ids[] = $fee_structure['ID']; ?>
            <div class="row d-flex justify-content-between">
              <div class="col-md-6">
                <div class="mb-4 mt-1 form-group">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="structure[]" value="<?=$fee_structure['ID']?>" id="checkbox-fee-structure-<?=$fee_structure['ID']?>">
                    <label class="custom-control-label" for="checkbox-fee-structure-<?=$fee_structure['ID']?>"><?=$fee_structure['Name']?></label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <?php if(in_array($fee_structure['Fee_Applicable_ID'],[1,2])){ ?>
                  <select class=" full-width semester" name="semesters[<?=$fee_structure['ID']?>][]" placeholder="Select" id="semester_<?=$fee_structure['ID']?>" multiple>
                    <?php for($i=1; $i<=$max; $i++){ ?>
                      <option value="<?=$i?>"><?=$i?></option>
                    <?php } ?>
                  </select>
                <?php } ?>
              </div>
            </div>
        <?php } ?>
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
    <?php foreach($ids as $id){ ?>
      $("#semester_<?=$id?>").select2({
        placeholder:"Choose",
        allowClear: true
      });
    <?php } ?>
  })

  $("#form-add-fee-dropdown").validate();
  
  $('#form-add-fee-dropdown').submit(function(e){
    e.preventDefault();    
    var formData = new FormData(this);
    formData.append('university_id', '<?=$university_id?>');
    if($('#form-add-fee-dropdown').valid()){
      $(':input[type="submit"]').prop('disabled', true);
      $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        success: function(data){
          if(data.status){
            $(".modal").modal('hide');
            notification('success', data.message);
            $('#tableFeeDropDowns').DataTable().ajax.reload(null, false);
          }else{
            $(':input[type="submit"]').prop('disabled', false);
            notification('danger', data.message);
          }
        },
        error: function(){
          toastr.error('Please try again after sometime!');
          $(':input[type="submit"]').prop('disabled', false);
        }
      });
    }
  });
</script>
