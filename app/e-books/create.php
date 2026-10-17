<?php
require '../../includes/db-config.php';
session_start();
//if($_SESSION['university_id'] == 48){}
?>

<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Add <span class="semi-bold">E-books</span></h5>
</div>

<form role="form" id="form-add-e-book" action="/app/e-books/store" method="POST" enctype="multipart/form-data">
  <div class="modal-body">
    <!-- University & Course -->
    <div class="row">

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Course</label>
          <select class="full-width select2" style="border: transparent;" id="course_id" name="course_id"
            onchange="getSubCourse(this.value);">
            <option value="">Select Course</option>
            <?php
            $programs = $conn->query("SELECT ID,Name,Short_Name FROM Courses WHERE Status=1 ORDER BY Name ASC");
            while ($program = $programs->fetch_assoc()) { ?>
              <option value="<?= $program['ID'] ?>">
                <?= $program['Name'] . ' (' . $program['Short_Name'] . ')' ?>
              </option>
            <?php } ?>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Specialization/Course</label>
          <select class="full-width select2" style="border: transparent;" id="sub_course_id" name="sub_course_id"
            onchange="getDuration(this.value)">
            <option value="">Select</option>

          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Duration </label>
          <select class="full-width" style="border: transparent;" name="duration" id="duration"
            data-placeholder="Choose Duration" onchange="getSubjects(this.value)">
            <option value="">Select Duration</option>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>Subject</label>
          <select class="full-width" style="border: transparent;" id="subject_id" name="subject_id">
            <option value="">Select Subject</option>
          </select>
        </div>
      </div>


      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>E-book file *</label>
          <input type="file" name="file" class="form-control" accept="image/png, image/jpg, image/jpeg, image/svg">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group form-group-default required">
          <label>E-book Name</label>
          <input type="text" name="ebook_name" class="form-control" placeholder="Enter E-book Name" required>
        </div>
      </div>
    </div>


    <div class="modal-footer clearfix justify-content-center">
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
  $(function () {
    $("#eligibilities").select2();
    $("#course_category").select2();
  })

  function getSubCourse(course_id) {
  
    $.ajax({
      url: '/app/e-books/sub_course',
      type: 'POST',
      dataType: 'text',
      data: {
        'course_id': course_id
      },
      success: function (result) {
        $('#sub_course_id').html(result);
        var sub_course = $("#sub_course_id").val();
        getDuration(sub_course);

      }
    })
  }

  function getSubjects(duration) {
    var sub_course_id = $("#sub_course_id").val();
    $.ajax({
      url: '/app/e-books/subjects',
      type: 'POST',
      dataType: 'text',
      data: {
        'duration':duration,
        'sub_course_id': sub_course_id,
      },
      success: function (result) {
        $('#subject_id').html(result);
      }
    })
  }

  function getDuration(id) {
    $.ajax({
      url: '/app/subjects/get-duration',
      data: { id: id },
      type: 'POST',
      success: function (data) {  
        $("#duration").html(data);
        // addFilter(id);
      }
    })
  }


  $(function () {
    $('#form-add-e-book').validate({
      rules: {
        course_id: {
          required: true
        },
        subject_id: {
          required: true
        },
        sub_course_id: {
          required: true
        },
        duration: {
          required: true
        },
        file: {
          required: true
        },
      },
      highlight: function (element) {
        //$(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function (element) {
        //$(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  $("#form-add-e-book").on("submit", function (e) {
    if ($('#form-add-e-book').valid()) {
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
      e.preventDefault();
      $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        success: function (data) {
          if (data.status == 200) {
            notification('success', data.message);
            $('.modal').modal('hide');
            $('#e_books-table').DataTable().ajax.reload(null, false);
          } else {
            notification('danger', data.message);
          }
        },
        error: function (data) {
          notification('danger', 'Server is not responding. Please try again later');
        }
      });
    } else {
      //notification('danger', 'Invalid form information.');
    }
  });
</script>