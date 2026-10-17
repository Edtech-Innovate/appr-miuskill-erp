<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
    $alloted = array();
    $course = $conn->query("SELECT Department_ID, University_ID FROM Courses WHERE ID = $id");
    $course = $course->fetch_assoc();
    $allotedDepartments = !empty($course['Department_ID']) ? json_decode($course['Department_ID'], true) : [];
    $university_id = $course['University_ID'];
    
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Allot <span class="semi-bold"></span>Departments</h5>
  </div>
  <form role="form" id="form-allot-departments" action="/app/courses/allot" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <?php 
          $departments = $conn->query("SELECT ID, Name FROM Departments WHERE University_ID = $university_id");
          while($department = $departments->fetch_assoc()){ ?>
            <div class="row">
              <div class="form-check complete">
                <input type="checkbox" id="allot-university-<?=$department['ID']?>" <?php print in_array($department['ID'], $allotedDepartments) ? 'checked' : '' ?> name="allot[]" value="<?=$department['ID']?>">
                <label for="allot-university-<?=$department['ID']?>" class="font-weight-bold">
                  <?=$department['Name']?>
                </label>  
              </div>
            </div>  
        <?php }
        ?>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span>Allot</span>
          <span class="hidden-block">
            <i class="pg-icon">tick</i>
          </span>
        </button>
      </div>
    </div>
  </form>

  <script>
    $(function(){
      $('#form-allot-departments').validate({
        rules: {
          allot: {required:true}
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

    $("#form-allot-departments").on("submit", function(e){
      if($('#form-allot-departments').valid()){
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?=$id?>');
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
                $('#courses-table').DataTable().ajax.reload(null, false);
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
