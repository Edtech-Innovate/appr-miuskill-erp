<?php
if (isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_GET['id']);
  $subject = $conn->query("SELECT * FROM Syllabi WHERE ID = $id");
  $subjectArr = mysqli_fetch_assoc($subject);
  $subjectArr['Paper_Type'] = isset($subjectArr['Paper_Type']) ? $subjectArr['Paper_Type'] : '';
  ?>
  <link href="../../assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="../../assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
  <!-- Modal -->
  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Edit <span class="semi-bold">Subject</span></h5>
  </div>
  <form role="form" id="form-edit-sub-course" action="/app/subject/update" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <!-- University & Course -->
      <div class="row">
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Courses</label>
            <select class="full-width" style="border: transparent;" id="course_id" name="course"
              onchange="getCourses(this.value)">
              <option value="">Choose</option>
              <?php
              $course_sql = $conn->query("SELECT ID, Name FROM Courses WHERE Status=1 AND University_ID = '".$_SESSION['university_id']."'");
              while ($courseArr = $course_sql->fetch_assoc()) { ?>
                <option value="<?= $courseArr['ID'] ?>" <?php if ($courseArr['ID'] == $subjectArr['Course_ID']) {
                    echo "selected";
                  } else {
                    echo "";
                  } ?>> <?= $courseArr['Name'] ?> </option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Sub-Courses</label>
            <select class="full-width" style="border: transparent;" id="sub_course_id" name="sub_course"
              onchange="getDuration(this.value)">
              <option value="">Choose</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required" aria-required="true">
            <label>Duration</label>
            <select class="full-width error" style="border: transparent;" id="duration_id" name="semester">
              <option value="">Choose</option>
            </select>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group form-group-default ">
            <label>Faculty</label>
            <select class="full-width" style="border: transparent;" id="faculty" name="faculty">
              <option value="">Choose Faculty</option>
              <?php $getData = $conn->query("SELECT ID, Name FROM Users WHERE Role='Faculty'");
              if ($getData->num_rows == 0) {
                echo '<option value ="" >No Faculty Found !</option>';
              } else {
                while ($row = $getData->fetch_assoc()) { ?>
                  <option value="<?= $row['ID'] ?>" <?php if ($row['ID'] == $subjectArr['Faculty_ID']) {
                      echo "selected";
                    } else {
                      echo "";
                    } ?>><?= $row['Name'] ?></option>
                <?php }
              } ?>
            </select>
          </div>
        </div>


        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Name</label>
            <input type="text" name="name" class="form-control" placeholder="ex: English"
              value="<?= $subjectArr['Name'] ?>" required>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group form-group-default required">
            <label>Code</label>
            <input type="text" value="<?= $subjectArr['Code'] ?>" name="code" class="form-control"
              placeholder="ex: ABC123" required>
          </div>
        </div>


        <div class="col-md-6">
          <div class="form-group form-group-default required" aria-required="true">
            <label>Paper Type</label>
            <select class="full-width error" style="border: transparent;" id="paper_type" name="paper_type"
              aria-required="true" aria-invalid="true">
              <option value="">Choose</option>
              <option value="Theory" <?php if (isset($subjectArr['Paper_Type']) && "Theory" == $subjectArr['Paper_Type']) {
                echo "selected";
              } ?>>Theory</option>
              <option value="Practical" <?php if (isset($subjectArr['Paper_Type']) && "Practical" == $subjectArr['Paper_Type']) {
                echo "selected";
              } ?>>Practical</option>
               <option value="Project" <?php if (isset($subjectArr['Paper_Type']) && "Project" == $subjectArr['Paper_Type']) {
                echo "selected";
              } ?>>Project</option>
              <option value="Seminar" <?php if (isset($subjectArr['Paper_Type']) && "Seminar" == $subjectArr['Paper_Type']) {
                echo "selected";
              } ?>>Seminar</option>
              <option value="Workshop" <?php if (isset($subjectArr['Paper_Type']) && "Workshop" == $subjectArr['Paper_Type']) {
                echo "selected";
              } ?>>Workshop</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required" aria-required="true">
            <label>Credit</label>
            <input type="number" step="1" min="0" name="credit" value="<?= $subjectArr['Credit'] ?>" class="form-control"
              placeholder="ex: 4" required="">
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group form-group-default required" aria-required="true">
            <label>Minimum Marks</label>
            <input type="number" step="1" min="0" name="min_marks" value="<?= $subjectArr['Min_Marks'] ?>"
              class="form-control" placeholder="ex: 35" required="" aria-required="true">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default required" aria-required="true">
            <label>Maximum Marks</label>
            <input type="number" step="1" min="0" name="max_marks" value="<?= $subjectArr['Max_Marks'] ?>"
              class="form-control" placeholder="ex: 100" required="" aria-required="true">
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group form-group-default">
            <label>Scheme</label>
            <select class="full-width" style="border: transparent;" id="Scheme_ID" name="Scheme_ID">
              <option value="">Choose Scheme</option>
              <?php $getData = $conn->query("SELECT ID, Name FROM Schemes");
              if ($getData->num_rows == 0) {
                echo '<option value ="" >No Faculty Found !</option>';
              } else {
                while ($row = $getData->fetch_assoc()) { ?>
                  <option value="<?= $row['ID'] ?>" <?php if (isset($subjectArr['Scheme_ID']) && $row['ID'] == $subjectArr['Scheme_ID']) {
                      echo "selected";
                    } ?>><?= $row['Name'] ?></option>
                <?php }
              } ?>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span>Update</span>
          <span class="hidden-block">
            <i class="pg-icon">tick</i>
          </span>
        </button>
      </div>
    </div>
  </form>
  <script type="text/javascript" src="../../assets/plugins/select2/js/select2.full.min.js"></script>
  <script>
    <?php if (isset($_GET['id']) && !empty($_GET['id'])) { ?>
      $(function () {
        getCourses(<?= $subjectArr['Course_ID'] ?>);
        getDuration(<?= $subjectArr['Sub_Course_ID'] ?>);
      })
    <?php } ?>
 $(function () {
    $("#eligibilities").select2();
    $("#course_id").select2({
      placeholder: 'Choose Course',
    })
    $("#sub_course_id").select2({
      placeholder: 'Choose Sub-Course',
    })
     $("#duration_id").select2({
      placeholder: 'Choose Duration',
    })
    $("#faculty").select2({
      placeholder: 'Choose Faculty',
    })

    $("#paper_type").select2({
      placeholder: 'Choose Paper Type',
    })
  })

    function getCourses(id) {
      $.ajax({
        url: '/app/regions/get-data?id=' + id + '&type=Course',
        type: 'GET',
        success: function (data) {
          $('#sub_course_id').html(data);
          $('#sub_course_id').val(<?= $subjectArr['Sub_Course_ID'] ?>);


        }
      });
    }

    function getDuration(sub_course_id) {
      var course_id = $("#course_id").val();
      $.ajax({
        url: '/app/regions/get-data?course_id=' + course_id + '&sub_course_id=' + sub_course_id + '&type=Semester',
        type: 'GET',
        success: function (data) {
          $('#duration_id').html(data);
          $('#duration_id').val(<?= $subjectArr['Semester'] ?>);


        }
      });
    }

    $(function () {
      $('#form-edit-sub-course').validate({
        rules: {
          name: {
            required: true
          },
          short_name: {
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
          lateral: {
            required: true
          },
          ct_transfer: {
            required: true
          },
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

    $("#form-edit-sub-course").on("submit", function (e) {
      if ($('#form-edit-sub-course').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?= $id ?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function (data) {
            if (data.status == 200) {
              $('.modal').modal('hide');
              notification('success', data.message);
              $('#subject-table').DataTable().ajax.reload(null, false);
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
<?php } ?>