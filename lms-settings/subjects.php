<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
unset($_SESSION['filterByUniversity']);
unset($_SESSION['subCourseFilter']);
unset($_SESSION['durationFilter']);
unset($_SESSION['usersFilter']);
unset($_SESSION['courseFilter']);


?>
<style>
  thead tr th {
    font-weight: 700 !important;
  }
</style>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i):
                  $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
              <div>
                  <button <?= $hide ?> class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Subject" onclick="add('subject','lg')"> <i class="uil uil-plus-circle"></i></button>
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload"
                  onclick="add('subjects', 'lg')"> <i class="uil uil-export"></i></button>
              </div>
            </ol>
            <!-- END BREADCRUMB -->

          </div>

        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="card card-transparent">
          <div class="card-header">
            <div class="row">
              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <?php $get_course = $conn->query("SELECT ID, Name FROM Courses WHERE Status = 1 AND University_ID = " . $_SESSION['university_id'] . " ORDER BY Name ASC"); ?>
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="course"
                    onchange="addFilter(this.value, 'course')" data-placeholder="Choose Courses">
                    <option value="">Select Courses</option>
                    <?php while ($row = $get_course->fetch_assoc()) { ?>
                      <option value="<?php echo $row['ID']; ?>"><?php echo ucwords(strtolower($row['Name'])); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_course"
                    onchange="addFilter(this.value, 'sub_course')" data-placeholder="Choose Sub-Courses">
                  </select>
                </div>
              </div>
              <div class="col-md-3 m-b-10">
                <div class="form-group">
                  <select class="full-width" style="width:40px" data-init-plugin="select2" id="duration"
                    onchange="addFilter(this.value, 'duration')" data-placeholder="Choose Duration">
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <input type="text" id="users-search-table" class="form-control pull-right" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="users-table">
                <thead>
                  <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Course </th>
                    <th>Sub-Course Name</th>
                    <th>Duration</th>
                    <th>Min/Max Marks</th>
                    <th>Paper Type</th>
                    <th>Credit</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>

        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script type="text/javascript">
      $(function () {

        var table = $('#users-table');
        var role = '<?= $_SESSION['Role'] ?>';
        var settings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/subjects/server'
          },
          'columns': [
            {
              data: "Code",
            },
            {
              data: "subject_name",
            },
            {
              data: "course_name",
            },
            {
              data: "sub_course_name"
            },
            {
              data: "Semester"
            },
            {
              data: "Marks",
            },
            {
              data: "Paper_Type"
            },
            {
              data: "Credit"
            },
            {
              data: "ID",
              "render": function (data, type, row) {
                let downloasdSylBtn ="";
                if(row.files!=null){ 
                   downloasdSylBtn = '<a href="..'+row.files+'"><i class="uil uil-down-arrow icon-xs cursor-pointer" title="Download" ></i></a>';
                }
                var uni_id = '<?= $_SESSION['university_id'] ?>';
                var deleteBtn = ['Administrator', 'University Head','Academics Internal'].includes(role) ? '<i class="uil uil-trash icon-xs cursor-pointer" title="Delete" onclick="destroy(&#39;subjects&#39;, &#39;' + data+ '&#39)"></i>' : '';
                var uploadSylBtn = ['Administrator', 'University Head','Academics Internal'].includes(role) ? '<i class="uil uil-upload icon-xs cursor-pointer" title="Upload" onclick="upload(&#39;subjects&#39;, &#39;' + data + '&#39, &#39;' + row.Code + '&#39, &#39;' + row.subject_name + '&#39)"></i>' : '';

                return '<div class="button-list">\
                '+uploadSylBtn+'\
                '+downloasdSylBtn+'\
                <i class="uil uil-edit icon-xs cursor-pointer" title="Edit" onclick="edit(&#39;subjects&#39;, &#39;' + data + '&#39, &#39;lg&#39;)"></i>\
                ' + deleteBtn + '\
              </div>'
              }
            },
          ],
          "sDom": "<t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
            "sLengthMenu": "_MENU_ ",
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          "aaSorting": [],
          "iDisplayLength": 25,
          "drawCallback": function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
          },
        };

        table.dataTable(settings);
        // search box for table
        $('#users-search-table').keyup(function () {
          table.fnFilter($(this).val());
        });

      })
    </script>


    <script>

      function upload(url,id,code,subject_name) {
        var modal= 'md';
        $.ajax({
          url: '/app/' + url + '/upload',
          type: 'POST',
          data:{id:id,modal: modal,code:code, subject_name: subject_name},
          success: function (data) {
            $('#' + modal + '-modal-content').html(data);
            $('#' + modal + 'modal').modal('show');
          }
        })
      }
      $(document).ready(function () {
        getDuration();
        $("#center").select2({
          placeholder: 'Choose Center',
        })
        $("#sub_course").select2({
          placeholder: 'Choose Sub Course',
        })
      })
      function addFilter(id, by) {
        $.ajax({
          url: '/app/subjects/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function (data) {
            const universityId = <?php echo json_encode($_SESSION['university_id']); ?>;
            if (by == "sub_course") {
              // getDuration(id);
            } else if (by == "course") {
              getSubCourse(id);
            }
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }
      function getSubCourse($id) {
        $.ajax({
          url: '/app/e-books/sub_course',
          data: { course_id: $id },
          type: 'POST',
          success: function (data) {
            $("#sub_course").html(data);
            var sub_course = $("#sub_course").val();
            addFilter(sub_course, 'sub_course');
            getDuration(sub_course);

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
    </script>


    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>