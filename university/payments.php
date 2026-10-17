<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .tooltip-inner {
    white-space: pre-wrap;
    max-width: 100% !important;
    text-align: left !important;
  }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php');
unset($_SESSION['current_session']);
unset($_SESSION['filterByDepartment']);
unset($_SESSION['filterBySubCourses']);
unset($_SESSION['filterByDate']);
unset($_SESSION['filterByStatus']);
unset($_SESSION['filterByUser']);
?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
              <div>
                <?php if ($_SESSION['Role'] == 'Administrator' || $_SESSION['Role'] == 'University Head') { ?>
                  <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload OA, Enrollment AND Roll No." onclick="uploadOAEnrollRoll()"> <i class="uil uil-upload"></i></button>
                <?php } ?>
                <?php
                    if($_SESSION['Designation']!='University'){ ?>
                        <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download" onclick="exportData()"> <i class="uil uil-down-arrow"></i></button>
                        <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Student" onclick="window.open('/admissions/application-form');"> <i class="uil uil-plus-circle"></i></button>
                  <?php  }
                ?>
                
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
        <?php if (isset($_SESSION['university_id'])) { ?>
          <?php
            if($_SESSION['Designation']!="University")
            { ?>
                <div class="card card-transparent">
            <div class="card-header">
              <div class="d-flex justify-content-start">
                <div class="col-md-2">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="sessions" onchange="changeSession(this.value)">
                      <option value="All">All</option>
                      <?php
                      $role_query = "";
                      if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') {
                        $role_query = str_replace('{{ table }}', 'Students', $_SESSION['RoleQuery']);
                        $role_query = str_replace('{{ column }}', 'Added_For', $role_query);
                      }
                      $sessions = $conn->query("SELECT Admission_Sessions.ID,Admission_Sessions.Name,Admission_Sessions.Current_Status FROM Admission_Sessions LEFT JOIN Students ON Admission_Sessions.ID = Students.Admission_Session_ID WHERE Admission_Sessions.University_ID = '" . $_SESSION['university_id'] . "' $role_query GROUP BY Name ORDER BY Admission_Sessions.ID ASC");
                      while ($session = mysqli_fetch_assoc($sessions)) { ?>
                        <option value="<?= $session['Name'] ?>" <?php print $session['Current_Status'] == 1 ? 'selected' : '' ?>><?= $session['Name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <!-- add filter -->
                <div class="col-md-2 ">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="departments" onchange="addFilter(this.value, 'departments');">
                      <option value="">Choose Types</option>
                      <?php $departments = $conn->query("SELECT ID, Name FROM Course_Types WHERE University_ID = " . $_SESSION['university_id']);
                      while ($department = $departments->fetch_assoc()) {
                        echo '<option value="' . $department['ID'] . '">' . $department['Name'] . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2 ">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_courses" onchange="addFilter(this.value, 'sub_courses')" data-placeholder="Choose Sub-Course">
                      <option value="">Choose Sub-Course</option>
                      <?php $programs = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as Name FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " $role_query GROUP BY Students.Sub_Course_ID");
                      while ($program = $programs->fetch_assoc()) {
                        echo '<option value="' . $program['ID'] . '">' . $program['Name'] . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4 m-b-10">
                  <div class="input-daterange input-group" id="datepicker-range">
                    <input type="text" class="input-sm form-control" placeholder="Select Date" id="startDateFilter" name="start" />
                    <div class="input-group-addon">to</div>
                    <input type="text" class="input-sm form-control" placeholder="Select Date" id="endDateFilter" onchange="addDateFilter()" name="end" />
                  </div>
                </div>
                <div class="col-md-2 m-b-10">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="application_status" onchange="addFilter(this.value, 'application_status')" data-placeholder="Choose App. Status">
                      <option value="">Application Status</option>
                      <option value="1">Document Verified</option>
                      <option value="2">Payment Verified</option>
                      <option value="3">Both Verified</option>
                    </select>
                  </div>
                </div>
                </div>
              <div class="d-flex justify-content-start">
                <div class="col-md-2 m-b-10">
                  <div class="form-group">
                    <select class="full-width" style="border: transparent;" data-init-plugin="select2" name="center"
                      id="center" onchange="addFilter(this.value, 'users')">
                      <option value="">Select</option>
                    </select>
                  </div>
                </div>
                <?php  if ($_SESSION['CanCreateSubCenter']==1) { ?>
                <div class="col-md-2 m-b-10">
                  <div class="form-group">
                    <select class="full-width" style="width:40px" data-init-plugin="select2" id="sub_center" onchange="addSubCenterFilter(this.value, 'users')" data-placeholder="Choose Sub Center">

                    </select>
                  </div>
                </div>
                <?php } ?>
              
              </div>
             <!-- end filter -->
              </div>
              <div class="clearfix"></div>
            </div>
            <?php }
          ?>
            <div class="card-body">
              <div class="card card-transparent p-3">
                    <div class="row d-flex justify-content-end">
                      <div class="col-md-2">
                        <input type="text" id="application-search-table" class="form-control pull-right" placeholder="Search">
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover nowrap" id="application-table">
                        <thead>
                          <tr>
                            <th data-orderable="false"></th>
                             <th>Student Name</th>
                             <th>Enrollment No.</th>
                             <th>Session</th>
                             <th>Center</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
              </div>
            </div>
          </div>
        <?php } ?>

        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->

    <div class="modal fade slide-up" id="reportmodal" style="z-index:9999" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-hidden="false">
      <div class="modal-dialog modal-md">
        <div class="modal-content-wrapper">
          <div class="modal-content" id="report-modal-content">
          </div>
        </div>
      </div>
    </div>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
    <script src="/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script>
      $('#datepicker-range').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        endDate: '0d'
      });
    </script>

    <?php if ($_SESSION['Role'] == 'Administrator' && !isset($_SESSION['university_id'])) { ?>
      <script type="text/javascript">
        changeUniversity();
      </script>
    <?php } ?>

    <script type="text/javascript">
      $(function() {
        var role = '<?php echo $_SESSION['Role']; ?>';
        var showInhouse = role != 'Center' && role != 'Sub-Center' ? true : false;
        var is_accountant = role == 'Accountant' ? true : false;
        var is_operations = ['Operations','Administrator','University Head'].includes(role) ? true : false;
        var hasStudentLogin = '<?php echo isset($_SESSION['has_lms']) && $_SESSION['has_lms'] == 1 ? true : false; ?>';
        var showStatus = false;
        var applicationTable = $('#application-table');
        var designation = "<?php echo $_SESSION['Designation']; ?>";
        var applicationSettings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/applications/application-server',
            'type': 'POST',
            complete: function(xhr, responseText) {
              $('#application_count').html(xhr.responseJSON.iTotalDisplayRecords);
            }
          },
          'columns': [{
              data: "ID",
              "render": function(data, type, row) {
                var edit = showInhouse || row.Step < 4 ? '<a href="/university/payment/create?id=' + data + '"><i class="uil uil-plus-circle mr-1" title="Edit Application Form"></i></a>' : '';
                
                return  edit ;
              }
            },
              { data: "First_Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
           
            {
              data: "Enrollment_No",
              "render": function(data, type, row) {
                var edit = showInhouse && row.Document_Verified != 1 ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
           
            {
              data: "Adm_Session"
            },
           
           
            
            
            {
              data: "Center_Name",
              visible: designation=="University"?false:true
            },
           
          ],
          "sDom": "l<t><'row'<p i>>",
          "destroy": true,
          "scrollCollapse": true,
          "oLanguage": {
            "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
          },
          drawCallback: function(settings, json) {
            $('[data-toggle="tooltip"]').tooltip();
          },
          "aaSorting": []
        };
        applicationTable.dataTable(applicationSettings);

        // search box for table
        $('#application-search-table').keyup(function() {
          applicationTable.fnFilter($(this).val());
        });
      })
    </script>

    <script type="text/javascript">
      function changeSession(value) {
        $('input[type=search]').val('');
        updateSession();
      }

      function updateSession() {
        var session_id = $('#sessions').val();
        $.ajax({
          url: '/app/applications/change-session',
          data: {
            session_id: session_id
          },
          type: 'POST',
          success: function(data) {
            $('.table').DataTable().ajax.reload(null, false);
          }
        })
      }
    </script>

    <script type="text/javascript">
      function addEnrollment(id) {
        $.ajax({
          url: '/app/applications/enrollment/create?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
      
    function addStudentLot(id) {
        $.ajax({
          url: '/app/applications/student-lot/create?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }

      function addOANumber(id) {
        $.ajax({
          url: '/app/applications/oa-number/create?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
    </script>

    <script type="text/javascript">
      function exportData() {
        var search = $('#application-search-table').val();
        var url = search.length > 0 ? "?search=" + search : "";
        window.open('/app/applications/export' + url);
      }

      function exportDocuments(id) {
        $.ajax({
          url: '/app/applications/document?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }

      function exportZip(id) {
        window.open('/app/applications/zip?id=' + id);
      }

      function exportPdf(id) {
        window.open('/app/applications/pdf?id=' + id);
      }
    </script>

    <script type="text/javascript">
      function uploadOAEnrollRoll() {
        $.ajax({
          url: '/app/applications/uploads/create_oa_enroll_roll',
          type: 'GET',
          success: function(data) {
            $('#md-modal-content').html(data);
            $('#mdmodal').modal('show');
          }
        })
      }
    </script>

    <script type="text/javascript">
      function printForm(id) {
        window.open('/forms/<?= $_SESSION['university_id'] ?>/index?student_id=' + id);
      }
    </script>

    <script type="text/javascript">
      function processByCenter(id) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Process'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "/app/applications/process-by-center",
              type: 'POST',
              dataType: 'json',
              data: {
                id: id
              },
              success: function(data) {
                if (data.status == 200) {
                  notification('success', data.message);
                  $('.table').DataTable().ajax.reload(null, false);
                } else {
                  notification('danger', data.message);
                  $('.table').DataTable().ajax.reload(null, false);
                }
              }
            });
          } else {
            $('.table').DataTable().ajax.reload(null, false);
          }
        })
      }

      function processedToUniversity(id) {
        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Process.'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "/app/applications/processed-to-university",
              type: 'POST',
              dataType: 'json',
              data: {
                id: id
              },
              success: function(data) {
                if (data.status == 200) {
                  notification('success', data.message);
                  $('.table').DataTable().ajax.reload(null, false);
                } else {
                  notification('danger', data.message);
                  $('.table').DataTable().ajax.reload(null, false);
                }
              }
            });
          } else {
            $('.table').DataTable().ajax.reload(null, false);
          }
        })
      }

      function verifyPayment(id) {
        $.ajax({
          url: '/app/applications/review-payment?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }

      function verifyDocument(id) {
        $.ajax({
          url: '/app/applications/review-documents?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#full-modal-content').html(data);
            $('#fullmodal').modal('show');
          }
        })
      }

      function reportPendency(id) {
        $.ajax({
          url: '/app/pendencies/create?id=' + id,
          type: 'GET',
          success: function(data) {
            $('#report-modal-content').html(data);
            $('#reportmodal').modal('show');
          }
        })
      }

      function uploadPendency(id) {
        $(".modal").modal('hide');
        $.ajax({
          url: '/app/pendencies/edit?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#lg-modal-content").html(data);
            $("#lgmodal").modal('show');
          }
        })
      }
    </script>
     <script type="text/javascript">
      $(document).ready(function(){
        $("#departments").select2({
          placeholder: "Choose Department",
        })
        $("#sub_courses").select2({
          placeholder: "Choose Sub-Courses",
        })
        getCenter('<?php $_SESSION['university_id'] ?>');
    })

    function getCenter(university_id) {
      $.ajax({
        url: '/app/application-form/center-form?university_id=' + university_id,
        type: 'GET',
        success: function (data) {
          $('#center').html(data);
          $('#center').val(<?php echo !empty($id) ? $student['Added_For'] : (isset($_GET['lead_id']) ? $lead['User_ID'] : (isset($_SESSION['lead_id']) ? $lead['Added_For'] : '')) ?>);
        }
      })
    }
        function addFilter(id, by) {
        $.ajax({
          url: '/app/applications/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
              $("#sub_center").html(data.subCenterName);
              if(by=="users"){
              var subcourse_id = $("#sub_center").val();
              addSubCenterFilter(subcourse_id, by);
            }


              if('<?= $_SESSION['Role'] ?>'=='Administrator'){
              //   $(".sub_center").html(data.subCenterName);
                
              }
            }
          }
        })
      }

      function addSubCenterFilter(id, by) {
        $.ajax({
          url: '/app/applications/filter',
          type: 'POST',
          data: {
            id,
            by
          },
          dataType: 'json',
          success: function(data) {

            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
              // $  ("#sub_center").html(data.subCenterName);
            }

          }
        })
      }
      
      function addDateFilter() {
        var startDate = $("#startDateFilter").val();
        var endDate = $("#endDateFilter").val();
        if (startDate.length == 0 || endDate == 0) {
          return
        }
        var id = 0;
        var by = 'date';
        $.ajax({
          url: '/app/applications/filter',
          type: 'POST',
          data: {
            id,
            by,
            startDate,
            endDate
          },
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              $('.table').DataTable().ajax.reload(null, false);
            }
          }
        })
      }

    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>