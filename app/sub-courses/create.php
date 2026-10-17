<link href="../../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="../../assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold">Specialization</span></h5>
</div>
<form role="form" id="form-add-sub-course" action="/app/sub-courses/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <!-- University & Course -->
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Vertical</label>
          <select class="full-width" style="border: transparent;" id="university_id" name="university_id" onchange="getDetails(this.value);">
            <option value="">Choose</option>
            <?php
            require '../../includes/db-config.php';
            session_start();
            $university_query = $_SESSION['Role'] != 'Administrator' ? " AND ID =" . $_SESSION['university_id'] : '';
            $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name FROM Universities WHERE ID IS NOT NULL $university_query");
            while ($university = $universities->fetch_assoc()) { ?>
              <option value="<?= $university['ID'] ?>"><?= $university['Name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Program</label>
          <select class="full-width" style="border: transparent;" id="course" name="course" onchange="getDepartments(this.value)">
            <option value="">Choose</option>
            
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Department</label>
          <select class="full-width" style="border: transparent;" id="department" name="department">
            <option value="">Choose</option>
            
          </select>
        </div>
      </div>
    </div>

    <!-- Name -->
    <div class=" row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Name</label>
          <input type="text" name="name" class="form-control" placeholder="ex: Mechanical Engineering" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Short Name</label>
          <input type="text" name="short_name" class="form-control" placeholder="ex: ME" required>
        </div>
      </div>
    </div>

    <!-- Scheme & Mode -->
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default required">
          <label>Mode</label>
          <select class="full-width" style="border: transparent;" id="mode" name="mode">

          </select>
        </div>
      </div>
    </div>

    <!-- Duration -->
    <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Min Duration</label>
          <input type="tel" name="min_duration" id="min_duration" class="form-control" placeholder="ex: 8" onkeypress="return isNumberKey(event);" onkeyup="getAdmissionTypes()" required>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Max Duration</label>
          <input type="tel" name="max_duration" class="form-control" placeholder="ex: 12" onkeypress="return isNumberKey(event)" required>
        </div>
      </div>
    </div>

    <div id="admission_types">

    </div>
     <div class="row">
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Fee For Counsellor</label>
          <input type="tel" name="counsellor_fee" class="form-control" placeholder="ex: 12" onkeypress="return isNumberKey(event)" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Fee For Sub-Counsellor</label>
          <input type="tel" name="sub_counsellor_fee" class="form-control" placeholder="ex: 12" onkeypress="return isNumberKey(event)" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Fee For Center</label>
          <input type="tel" name="center_fee" class="form-control" placeholder="ex: 12" onkeypress="return isNumberKey(event)" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Fee For Center(Can Create Sub-Center)</label>
          <input type="tel" name="coordinator_fee" class="form-control" placeholder="ex: 12" onkeypress="return isNumberKey(event)" required>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Fee For Sub-Center</label>
          <input type="tel" name="sub_center_fee" class="form-control" placeholder="ex: 12" onkeypress="return isNumberKey(event)" required>
        </div>
      </div>
        <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>University Fee</label>
          <input type="text" name="University_fee" class="form-control" placeholder="Enter University Fee ex 1200.00" onkeypress="return isNumberKey(event)" required>
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
<script type="text/javascript" src="../../assets/plugins/select2/js/select2.full.min.js"></script>

<script>
  function disableOptions(id, value){
    console.log(value);
    // $("#eligibilities_optional>option[value='"+value+"']").prop('disabled', true);
  }

  function getDepartments(id){
    $.ajax({
      url: '/app/sub-courses/departments?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#department').html(data);
      }
    })
  }

  function getDetails(id) {
    $.ajax({
      url: '/app/sub-courses/courses?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#course').html(data);
      }
    });

    $.ajax({
      url: '/app/sub-courses/schemes?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#scheme').html(data);
      }
    });

    $.ajax({
      url: '/app/sub-courses/modes?id=' + id,
      type: 'GET',
      success: function(data) {
        $('#mode').html(data);
      }
    });
  }

  function getAdmissionTypes(){
    $("#admission_types").html('');
    var university_id = $('#university_id').val();
    var min_duration = $('#min_duration').val();
    $.ajax({
      url:'/app/sub-courses/admission-types?university_id='+university_id+'&min_duration='+min_duration,
      type: 'GET',
      success: function(data) {
        $("#admission_types").html(data);
      }
    })
  }

  $(function() {
    $('#form-add-sub-course').validate({
      rules: {
        name: {
          required: true
        },
        short_name: {
          required: true
        },
        department: {
          required: true
        },
        university_id: {
          required: true
        },
        course: {
          required: true
        },
        scheme: {
          required: true
        },
        mode: {
          required: true
        },
        'eligibilities[]': {
          required: true
        }
      },
      highlight: function(element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function(element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  $("#form-add-sub-course").on("submit", function(e) {
    if ($('#form-add-sub-course').valid()) {
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      $.ajax({
        url: this.action,
        type: 'post',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            notification('success', data.message);
            $('#sub-courses-table').DataTable().ajax.reload(null, false);
          } else {
            $(':input[type="submit"]').prop('disabled', false);
            notification('danger', data.message);
          }
        }
      });
      e.preventDefault();
    }
  });
</script>
