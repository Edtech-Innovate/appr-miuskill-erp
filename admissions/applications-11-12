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
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download" onclick="exportData()"> <i class="uil uil-down-arrow"></i></button>
                <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Student" onclick="window.open('/admissions/application-form');"> <i class="uil uil-plus-circle"></i></button>
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
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="card-body">
              <div class="card card-transparent">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-linetriangle" data-init-reponsive-tabs="dropdownfx">
                  <li class="nav-item">
                    <a class="active" data-toggle="tab" data-target="#applications" href="#"><span>All Applications - <span id="application_count">0</span></span></a>
                  </li>
                  <li class="nav-item">
                    <a data-toggle="tab" data-target="#not_processed" href="#"><span>New - <span id="not_processed_count">0</span></span></a>
                  </li>
                  <li class="nav-item">
                    <a data-toggle="tab" data-target="#ready_for_verification" href="#"><span>Fee Received - <span id="ready_for_verification_count">0</span></span></a>
                  </li>
                  <li class="nav-item">
                    <a data-toggle="tab" data-target="#verified" href="#"><span>Ready for Enrollment - <span id="verified_count">0</span></span></a>
                  </li>
                  <li class="nav-item">
                    <a data-toggle="tab" data-target="#enrolled" href="#"><span>Enrolled - <span id="enrolled_count">0</span></span></a>
                  </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                  <div class="tab-pane active" id="applications">
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
                            <th>Photo</th>
                            <th>Student ID</th>
                            <th>Status</th>
                            <th>Fee Received on</th>
                            <th>Document Verification</th>
                            <th>Payment Verification</th>
                            <th>Processed to University</th>
                            <th>Enrollment No.</th>
                            <th><?php print $_SESSION['university_id'] == '16' ? 'Form No.' : ($_SESSION['university_id'] == 14 ? 'SID Number' : 'Form No') ?></th>
                            <th>Adm Session</th>
                            <th>Adm Type</th>
                            <th>Pendency</th>
                            <th>Student Name</th>
                            <th>Father Name</th>
                            <th>Program</th>
                            <th>
                              <?php $alloted_modes = [];
                              if (isset($_SESSION['university_id'])) {
                                $modes = $conn->query("SELECT Name FROM Modes WHERE University_ID = " . $_SESSION['university_id']);
                                while ($mode = $modes->fetch_assoc()) {
                                  $alloted_modes[] = $mode['Name'];
                                }
                                echo implode('/', $alloted_modes);
                              } else {
                                echo 'Modes';
                              }
                              ?></th>
                            <th>Login</th>
                            <th>ID Card</th>
                            <th>Admit Card</th>
                            <th>Exam</th>
                            <th>DOB</th>
                            <th>Code</th>
                            <th>Center</th>
                            <th>RM</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="not_processed">
                    <div class="row d-flex justify-content-end">
                      <div class="col-md-2">
                        <input type="text" id="not-processed-search-table" class="form-control pull-right" placeholder="Search">
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover nowrap" id="not-processed-table">
                        <thead>
                          <tr>
                            <th data-orderable="false"></th>
                            <th>Photo</th>
                            <th>Student ID</th>
                            <th>Status</th>
                            <th>Fee Received on</th>
                            <th>Adm Session</th>
                            <th>Adm Type</th>
                            <th>Pendency</th>
                            <th>Student Name</th>
                            <th>Father Name</th>
                            <th>Program</th>
                            <th>
                              <?php $alloted_modes = [];
                              if (isset($_SESSION['university_id'])) {
                                $modes = $conn->query("SELECT Name FROM Modes WHERE University_ID = " . $_SESSION['university_id']);
                                while ($mode = $modes->fetch_assoc()) {
                                  $alloted_modes[] = $mode['Name'];
                                }
                                echo implode('/', $alloted_modes);
                              } else {
                                echo 'Modes';
                              }
                              ?></th>
                            <th>Login</th>
                            <th>ID Card</th>
                            <th>Admit Card</th>
                            <th>Exam</th>
                            <th>DOB</th>
                            <th>Code</th>
                            <th>Center</th>
                            <th>RM</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="ready_for_verification">
                    <div class="row d-flex justify-content-end">
                      <div class="col-md-2">
                        <input type="text" id="ready-for-verification-search-table" class="form-control pull-right" placeholder="Search">
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover nowrap" id="ready-for-verification-table">
                        <thead>
                          <tr>
                            <th data-orderable="false"></th>
                            <th>Photo</th>
                            <th>Student ID</th>
                            <th>Status</th>
                            <th>Fee Received on</th>
                            <th>Document Verification</th>
                            <th>Payment Verification</th>
                            <th>Enrollment No.</th>
                            <th><?php print $_SESSION['university_id'] == '16' ? 'Form No.' : ($_SESSION['university_id'] == 14 ? 'SID Number' : 'Form No') ?></th>
                            <th>Adm Session</th>
                            <th>Adm Type</th>
                            <th>Pendency</th>
                            <th>Student Name</th>
                            <th>Father Name</th>
                            <th>Program</th>
                            <th>
                              <?php $alloted_modes = [];
                              if (isset($_SESSION['university_id'])) {
                                $modes = $conn->query("SELECT Name FROM Modes WHERE University_ID = " . $_SESSION['university_id']);
                                while ($mode = $modes->fetch_assoc()) {
                                  $alloted_modes[] = $mode['Name'];
                                }
                                echo implode('/', $alloted_modes);
                              } else {
                                echo 'Modes';
                              }
                              ?></th>
                            <th>Login</th>
                            <th>ID Card</th>
                            <th>Admit Card</th>
                            <th>Exam</th>
                            <th>DOB</th>
                            <th>Code</th>
                            <th>Center</th>
                            <th>RM</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="verified">
                    <div class="row d-flex justify-content-end">
                      <div class="col-md-2">
                        <input type="text" id="verified-search-table" class="form-control pull-right" placeholder="Search">
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover nowrap" id="verified-table">
                        <thead>
                          <tr>
                            <th data-orderable="false"></th>
                            <th>Photo</th>
                            <th>Student ID</th>
                            <th>Status</th>
                            <th>Fee Received on</th>
                            <th>Document Verification</th>
                            <th>Payment Verification</th>
                            <th>Processed to University</th>
                            <th>Enrollment No.</th>
                            <th><?php print $_SESSION['university_id'] == '16' ? 'Form No.' : ($_SESSION['university_id'] == 14 ? 'SID Number' : 'Form No') ?></th>
                            <th>Adm Session</th>
                            <th>Adm Type</th>
                            <th>Pendency</th>
                            <th>Student Name</th>
                            <th>Father Name</th>
                            <th>Program</th>
                            <th>
                              <?php $alloted_modes = [];
                              if (isset($_SESSION['university_id'])) {
                                $modes = $conn->query("SELECT Name FROM Modes WHERE University_ID = " . $_SESSION['university_id']);
                                while ($mode = $modes->fetch_assoc()) {
                                  $alloted_modes[] = $mode['Name'];
                                }
                                echo implode('/', $alloted_modes);
                              } else {
                                echo 'Modes';
                              }
                              ?></th>
                            <th>Login</th>
                            <th>ID Card</th>
                            <th>Admit Card</th>
                            <th>Exam</th>
                            <th>DOB</th>
                            <th>Code</th>
                            <th>Center</th>
                            <th>RM</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="enrolled">
                    <div class="row d-flex justify-content-end">
                      <div class="col-md-2">
                        <input type="text" id="enrolled-search-table" class="form-control pull-right" placeholder="Search">
                      </div>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover nowrap" id="enrolled-table">
                        <thead>
                          <tr>
                            <th data-orderable="false"></th>
                            <th>Photo</th>
                            <th>Student ID</th>
                            <th>Status</th>
                            <th>Fee Received on</th>
                            <th>Document Verification</th>
                            <th>Payment Verification</th>
                            <th>Processed to University</th>
                            <th>Enrollment No.</th>
                            <th><?php print $_SESSION['university_id'] == '16' ? 'Form No.' : ($_SESSION['university_id'] == 14 ? 'SID Number' : 'Form No') ?></th>
                            <th>Adm Session</th>
                            <th>Adm Type</th>
                            <th>Pendency</th>
                            <th>Student Name</th>
                            <th>Father Name</th>
                            <th>Program</th>
                            <th>
                              <?php $alloted_modes = [];
                              if (isset($_SESSION['university_id'])) {
                                $modes = $conn->query("SELECT Name FROM Modes WHERE University_ID = " . $_SESSION['university_id']);
                                while ($mode = $modes->fetch_assoc()) {
                                  $alloted_modes[] = $mode['Name'];
                                }
                                echo implode('/', $alloted_modes);
                              } else {
                                echo 'Modes';
                              }
                              ?></th>
                            <th>Login</th>
                            <th>ID Card</th>
                            <th>Admit Card</th>
                            <th>Exam</th>
                            <th>DOB</th>
                            <th>Code</th>
                            <th>Center</th>
                            <th>RM</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
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
        var notProcessedTable = $('#not-processed-table');
        var readyForVerificationTable = $('#ready-for-verification-table');
        var verifiedTable = $('#verified-table');
        var processedToUniversityTable = $('#proccessed-to-university-table');
        var enrolledTable = $('#enrolled-table');

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
                var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="uil uil-edit mr-1" title="Edit Application Form"></i></a>' : '';
                var deleted = showInhouse ? '<i class="uil uil-trash mr-1 cursor-pointer" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
                var print = row.Step == 4 ? '<i class="uil uil-print mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
                var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
                var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
                var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
                var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
                var info = row.Step == 4 ? '<i class="uil uil-info-circle cursor-pointer" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
                return print + edit + deleted + info;
              }
            },
            {
              data: "Photo",
              "render": function(data, type, row) {
                return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32">\
            </span>';
              }
            },
            {
              data: "Unique_ID",
              "render": function(data, type, row) {
                return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>';
              }
            },
            {
              data: "Step",
              "render": function(data, type, row) {
                var label_class = data < 4 ? 'label-important' : 'label-success';
                var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
                return '<sapn class="label ' + label_class + '">' + status + '</sapn>';
              }
            },
            {
              data: "Process_By_Center",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4) {
                  return '<div class="text-center"><span class="label label-primary">Not Received</span></div>';
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">' + data + '</span></div>' : '';
                  return show;
                }
              }
            },
            {
              data: "Document_Verified",
              "render": function(data, type, row) {
                if (row.Pendency_Status == 2) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer"><strong>In Review</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Re-Review</strong></span></div>'
                  }
                } else if (row.Pendency != 0) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer" onclick="uploadPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="reportPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Pendency</strong></span></div>'
                  }
                } else {
                  if (data == 1) {
                    var show = is_operations && row.Process_By_Center != 1 ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center text-danger"><strong>Pending</strong></div>' : '';
                    return show;
                  } else {
                    var show = row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center"><span class="label label-success">Verified at ' + data + '</span></div>' : '';
                    return show;
                  }
                }
              }
            },
            {
              data: "Payment_Received",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                  var show = is_accountant ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyPayment(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : '<center><span class="label label-primary">Pending</span></center>';
                  return show;
                } else if (row.Process_By_Center != 1) {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">Verified on ' + data + '</span></div>' : '';
                  return show;
                } else {
                  return '';
                }
              },
              visible: false
            },
            {
              data: "Processed_To_University",
              "render": function(data, type, row) {
                if (data == 1) {
                  var show = showInhouse && row.Document_Verified != 1 && row.Payment_Received != 1 ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="processed-to-university-' + row.ID + '" onclick="processedToUniversity(&#39;' + row.ID + '&#39;)">\
                <label for="processed-to-university-' + row.ID + '">Mark as Processed</label>\
              </div>' : "";
                  return show;
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-success">Processed on ' + data + '</span></div>' : '';
                  return show;
                }
              },
              visible: false
            },
            {
              data: "Enrollment_No",
              "render": function(data, type, row) {
                var edit = showInhouse && row.Document_Verified != 1 ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "OA_Number",
              "render": function(data, type, row) {
                var edit = showInhouse ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Form No" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "Adm_Session"
            },
            {
              data: "Adm_Type"
            },
            {
              data: "Adm_Type",
              "render": function(data, type, row) {
                return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
              },
              visible: false,
            },
            {
              data: "First_Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Father_Name"
            },
            {
              data: "Short_Name"
            },
            {
              data: "Duration"
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;);" type="checkbox" ' + checked + ' id="student-status-switch-' + row.ID + '">\
                <label for="student-status-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin || showStatus
            },
            {
              data: "ID_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'ID_Card\');" type="checkbox" ' + checked + ' id="student-id-card-switch-' + row.ID + '">\
                <label for="student-id-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Admit_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Admit_Card\');" type="checkbox" ' + checked + ' id="student-admit-card-switch-' + row.ID + '">\
                <label for="student-admit-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Exam",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Exam\');" type="checkbox" ' + checked + ' id="student-exam-switch-' + row.ID + '">\
                <label for="student-exam-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "DOB"
            },
            {
              data: "Center_Code"
            },
            {
              data: "Center_Name"
            },
            {
              data: "RM",
              visible: ['Center','Sub-Center'].includes(role) ? false : true
            }
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

        var notProcessedSettings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/applications/not-processed-server',
            'type': 'POST',
            complete: function(xhr, responseText) {
              $('#not_processed_count').html(xhr.responseJSON.iTotalDisplayRecords);
            }
          },
          'columns': [{
              data: "ID",
              "render": function(data, type, row) {
                var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="uil uil-edit mr-1" title="Edit Application Form"></i></a>' : '';
                var deleted = showInhouse ? '<i class="uil uil-trash mr-1 cursor-pointer" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
                var print = row.Step == 4 ? '<i class="uil uil-print mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
                var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
                var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
                var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
                var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
                var info = row.Step == 4 ? '<i class="uil uil-info-circle cursor-pointer" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
                return print + edit + deleted + info;
              }
            },
            {
              data: "Photo",
              "render": function(data, type, row) {
                return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32">\
            </span>';
              }
            },
            {
              data: "Unique_ID",
              "render": function(data, type, row) {
                return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>';
              }
            },
            {
              data: "Step",
              "render": function(data, type, row) {
                var label_class = data < 4 ? 'label-important' : 'label-success';
                var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
                return '<sapn class="label ' + label_class + '">' + status + '</sapn>';
              }
            },
            {
              data: "Process_By_Center",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4) {
                  return '<div class="text-center"><span class="label label-primary">Not Received</span></div>';
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">' + data + '</span></div>' : '';
                  return show;
                }
              }
            },
            {
              data: "Adm_Session"
            },
            {
              data: "Adm_Type"
            },
            {
              data: "Adm_Type",
              "render": function(data, type, row) {
                return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
              },
              visible: false,
            },
            {
              data: "First_Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Father_Name"
            },
            {
              data: "Short_Name"
            },
            {
              data: "Duration"
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;);" type="checkbox" ' + checked + ' id="student-status-switch-' + row.ID + '">\
                <label for="student-status-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin || showStatus
            },
            {
              data: "ID_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'ID_Card\');" type="checkbox" ' + checked + ' id="student-id-card-switch-' + row.ID + '">\
                <label for="student-id-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Admit_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Admit_Card\');" type="checkbox" ' + checked + ' id="student-admit-card-switch-' + row.ID + '">\
                <label for="student-admit-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Exam",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Exam\');" type="checkbox" ' + checked + ' id="student-exam-switch-' + row.ID + '">\
                <label for="student-exam-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "DOB"
            },
            {
              data: "Center_Code"
            },
            {
              data: "Center_Name"
            },
            {
              data: "RM",
              visible: ['Center','Sub-Center'].includes(role) ? false : true
            }
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

        var readyForVerificationSettings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/applications/ready-for-verification-server',
            'type': 'POST',
            complete: function(xhr, responseText) {
              $('#ready_for_verification_count').html(xhr.responseJSON.iTotalDisplayRecords);
            }
          },
          'columns': [{
              data: "ID",
              "render": function(data, type, row) {
                var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="uil uil-edit mr-1" title="Edit Application Form"></i></a>' : '';
                var deleted = showInhouse ? '<i class="uil uil-trash mr-1 cursor-pointer" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
                var print = row.Step == 4 ? '<i class="uil uil-print mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
                var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
                var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
                var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
                var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
                var info = row.Step == 4 ? '<i class="uil uil-info-circle cursor-pointer" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
                return print + edit + deleted + info;
              }
            },
            {
              data: "Photo",
              "render": function(data, type, row) {
                return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32">\
            </span>';
              }
            },
            {
              data: "Unique_ID",
              "render": function(data, type, row) {
                return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>';
              }
            },
            {
              data: "Step",
              "render": function(data, type, row) {
                var label_class = data < 4 ? 'label-important' : 'label-success';
                var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
                return '<sapn class="label ' + label_class + '">' + status + '</sapn>';
              }
            },
            {
              data: "Process_By_Center",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4) {
                  return '<div class="text-center"><span class="label label-primary">Not Received</span></div>';
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">' + data + '</span></div>' : '';
                  return show;
                }
              }
            },
            {
              data: "Document_Verified",
              "render": function(data, type, row) {
                if (row.Pendency_Status == 2) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer"><strong>In Review</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Re-Review</strong></span></div>'
                  }
                } else if (row.Pendency != 0) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer" onclick="uploadPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="reportPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Pendency</strong></span></div>'
                  }
                } else {
                  if (data == 1) {
                    var show = is_operations && row.Process_By_Center != 1 ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center text-danger"><strong>Pending</strong></div>' : '';
                    return show;
                  } else {
                    var show = row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center"><span class="label label-success">Verified at ' + data + '</span></div>' : '';
                    return show;
                  }
                }
              }
            },
            {
              data: "Payment_Received",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                  var show = is_accountant ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyPayment(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : '<center><span class="label label-primary">Pending</span></center>';
                  return show;
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">Verified on ' + data + '</span></div>' : '';
                  return show;
                }
              },
              visible: false
            },
            {
              data: "Enrollment_No",
              "render": function(data, type, row) {
                var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "OA_Number",
              "render": function(data, type, row) {
                var edit = showInhouse ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Form No" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "Adm_Session"
            },
            {
              data: "Adm_Type"
            },
            {
              data: "Adm_Type",
              "render": function(data, type, row) {
                return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
              },
              visible: false,
            },
            {
              data: "First_Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Father_Name"
            },
            {
              data: "Short_Name"
            },
            {
              data: "Duration"
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;);" type="checkbox" ' + checked + ' id="student-status-switch-' + row.ID + '">\
                <label for="student-status-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin || showStatus
            },
            {
              data: "ID_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'ID_Card\');" type="checkbox" ' + checked + ' id="student-id-card-switch-' + row.ID + '">\
                <label for="student-id-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Admit_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Admit_Card\');" type="checkbox" ' + checked + ' id="student-admit-card-switch-' + row.ID + '">\
                <label for="student-admit-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Exam",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Exam\');" type="checkbox" ' + checked + ' id="student-exam-switch-' + row.ID + '">\
                <label for="student-exam-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "DOB"
            },
            {
              data: "Center_Code"
            },
            {
              data: "Center_Name"
            },
            {
              data: "RM",
              visible: ['Center','Sub-Center'].includes(role) ? false : true
            }
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

        var verifiedSettings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/applications/verified-server',
            'type': 'POST',
            complete: function(xhr, responseText) {
              $('#verified_count').html(xhr.responseJSON.iTotalDisplayRecords);
            }
          },
          'columns': [{
              data: "ID",
              "render": function(data, type, row) {
                var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="uil uil-edit mr-1" title="Edit Application Form"></i></a>' : '';
                var deleted = showInhouse ? '<i class="uil uil-trash mr-1 cursor-pointer" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
                var print = row.Step == 4 ? '<i class="uil uil-print mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
                var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
                var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
                var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
                var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
                var info = row.Step == 4 ? '<i class="uil uil-info-circle cursor-pointer" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
                return print + edit + deleted + info;
              }
            },
            {
              data: "Photo",
              "render": function(data, type, row) {
                return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32">\
            </span>';
              }
            },
            {
              data: "Unique_ID",
              "render": function(data, type, row) {
                return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>';
              }
            },
            {
              data: "Step",
              "render": function(data, type, row) {
                var label_class = data < 4 ? 'label-important' : 'label-success';
                var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
                return '<sapn class="label ' + label_class + '">' + status + '</sapn>';
              }
            },
            {
              data: "Process_By_Center",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4) {
                  return '<div class="text-center"><span class="label label-primary">Not Received</span></div>';
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">' + data + '</span></div>' : '';
                  return show;
                }
              }
            },
            {
              data: "Document_Verified",
              "render": function(data, type, row) {
                if (row.Pendency_Status == 2) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer"><strong>In Review</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Re-Review</strong></span></div>'
                  }
                } else if (row.Pendency != 0) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer" onclick="uploadPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="reportPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Pendency</strong></span></div>'
                  }
                } else {
                  if (data == 1) {
                    var show = is_operations && row.Process_By_Center != 1 ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center text-danger"><strong>Pending</strong></div>' : '';
                    return show;
                  } else {
                    var show = row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center"><span class="label label-success">Verified at ' + data + '</span></div>' : '';
                    return show;
                  }
                }
              }
            },
            {
              data: "Payment_Received",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                  var show = is_accountant ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyPayment(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : '<center><span class="label label-primary">Pending</span></center>';
                  return show;
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">Verified on ' + data + '</span></div>' : '';
                  return show;
                }
              },
              visible: false
            },
            {
              data: "Processed_To_University",
              "render": function(data, type, row) {
                if (data == 1) {
                  var show = showInhouse && row.Document_Verified != 1 && row.Payment_Received != 1 ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="processed-to-university-' + row.ID + '" onclick="processedToUniversity(&#39;' + row.ID + '&#39;)">\
                <label for="processed-to-university-' + row.ID + '">Mark as Processed</label>\
              </div>' : "";
                  return show;
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-success">Processed on ' + data + '</span></div>' : '';
                  return show;
                }
              },
              visible: false
            },
            {
              data: "Enrollment_No",
              "render": function(data, type, row) {
                var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "OA_Number",
              "render": function(data, type, row) {
                var edit = showInhouse ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Form No" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "Adm_Session"
            },
            {
              data: "Adm_Type"
            },
            {
              data: "Adm_Type",
              "render": function(data, type, row) {
                return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
              },
              visible: false,
            },
            {
              data: "First_Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Father_Name"
            },
            {
              data: "Short_Name"
            },
            {
              data: "Duration"
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;);" type="checkbox" ' + checked + ' id="student-status-switch-' + row.ID + '">\
                <label for="student-status-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin || showStatus
            },
            {
              data: "ID_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'ID_Card\');" type="checkbox" ' + checked + ' id="student-id-card-switch-' + row.ID + '">\
                <label for="student-id-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Admit_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Admit_Card\');" type="checkbox" ' + checked + ' id="student-admit-card-switch-' + row.ID + '">\
                <label for="student-admit-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Exam",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Exam\');" type="checkbox" ' + checked + ' id="student-exam-switch-' + row.ID + '">\
                <label for="student-exam-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "DOB"
            },
            {
              data: "Center_Code"
            },
            {
              data: "Center_Name"
            },
            {
              data: "RM",
              visible: ['Center','Sub-Center'].includes(role) ? false : true
            }
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

        var processedToUniversitySettings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/applications/processed-to-university-server',
            'type': 'POST',
            complete: function(xhr, responseText) {
              $('#processed_to_university_count').html(xhr.responseJSON.iTotalDisplayRecords);
            }
          },
          'columns': [{
              data: "ID",
              "render": function(data, type, row) {
                var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="uil uil-edit mr-1" title="Edit Application Form"></i></a>' : '';
                var deleted = showInhouse ? '<i class="uil uil-trash mr-1 cursor-pointer" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
                var print = row.Step == 4 ? '<i class="uil uil-print mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
                var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
                var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
                var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
                var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
                var info = row.Step == 4 ? '<i class="uil uil-info-circle cursor-pointer" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
                return print + edit + deleted + info;
              }
            },
            {
              data: "Photo",
              "render": function(data, type, row) {
                return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32">\
            </span>';
              }
            },
            {
              data: "Unique_ID",
              "render": function(data, type, row) {
                return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>';
              }
            },
            {
              data: "Step",
              "render": function(data, type, row) {
                var label_class = data < 4 ? 'label-important' : 'label-success';
                var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
                return '<sapn class="label ' + label_class + '">' + status + '</sapn>';
              }
            },
            {
              data: "Process_By_Center",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4) {
                  return '<div class="text-center"><span class="label label-primary">Not Received</span></div>';
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">' + data + '</span></div>' : '';
                  return show;
                }
              }
            },
            {
              data: "Document_Verified",
              "render": function(data, type, row) {
                if (row.Pendency_Status == 2) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer"><strong>In Review</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Re-Review</strong></span></div>'
                  }
                } else if (row.Pendency != 0) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer" onclick="uploadPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="reportPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Pendency</strong></span></div>'
                  }
                } else {
                  if (data == 1) {
                    var show = is_operations && row.Process_By_Center != 1 ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center text-danger"><strong>Pending</strong></div>' : '';
                    return show;
                  } else {
                    var show = row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center"><span class="label label-success">Verified at ' + data + '</span></div>' : '';
                    return show;
                  }
                }
              }
            },
            {
              data: "Payment_Received",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                  var show = is_accountant ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyPayment(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : '<center><span class="label label-primary">Pending</span></center>';
                  return show;
                } else if (row.Process_By_Center != 1) {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">Verified on ' + data + '</span></div>' : '';
                  return show;
                } else {
                  return '';
                }
              },
              visible: false
            },
            {
              data: "Processed_To_University",
              "render": function(data, type, row) {
                if (data == 1) {
                  var show = showInhouse && row.Document_Verified != 1 && row.Payment_Received != 1 ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="processed-to-university-' + row.ID + '" onclick="processedToUniversity(&#39;' + row.ID + '&#39;)">\
                <label for="processed-to-university-' + row.ID + '">Mark as Processed</label>\
              </div>' : "";
                  return show;
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-success">Processed on ' + data + '</span></div>' : '';
                  return show;
                }
              },
              visible: false
            },
            {
              data: "Enrollment_No",
              "render": function(data, type, row) {
                var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "OA_Number",
              "render": function(data, type, row) {
                var edit = showInhouse ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Form No" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "Adm_Session"
            },
            {
              data: "Adm_Type"
            },
            {
              data: "Adm_Type",
              "render": function(data, type, row) {
                return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
              },
              visible: false,
            },
            {
              data: "First_Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Father_Name"
            },
            {
              data: "Short_Name"
            },
            {
              data: "Duration"
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;);" type="checkbox" ' + checked + ' id="student-status-switch-' + row.ID + '">\
                <label for="student-status-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin || showStatus
            },
            {
              data: "ID_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'ID_Card\');" type="checkbox" ' + checked + ' id="student-id-card-switch-' + row.ID + '">\
                <label for="student-id-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Admit_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Admit_Card\');" type="checkbox" ' + checked + ' id="student-admit-card-switch-' + row.ID + '">\
                <label for="student-admit-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Exam",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Exam\');" type="checkbox" ' + checked + ' id="student-exam-switch-' + row.ID + '">\
                <label for="student-exam-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "DOB"
            },
            {
              data: "Center_Code"
            },
            {
              data: "Center_Name"
            },
            {
              data: "RM",
              visible: ['Center','Sub-Center'].includes(role) ? false : true
            }
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

        var enrolledSettings = {
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'ajax': {
            'url': '/app/applications/enrolled-server',
            'type': 'POST',
            complete: function(xhr, responseText) {
              $('#enrolled_count').html(xhr.responseJSON.iTotalDisplayRecords);
            }
          },
          'columns': [{
              data: "ID",
              "render": function(data, type, row) {
                var edit = showInhouse || row.Step < 4 ? '<a href="/admissions/application-form?id=' + data + '"><i class="uil uil-edit mr-1" title="Edit Application Form"></i></a>' : '';
                var deleted = showInhouse ? '<i class="uil uil-trash mr-1 cursor-pointer" title="Delete Application Form" onclick="destroy(&#39;application-form&#39;, &#39;' + data + '&#39;)"></i>' : '';
                var print = row.Step == 4 ? '<i class="uil uil-print mr-1 cursor-pointer" title="Print Application Form" onclick="printForm(&#39;' + data + '&#39;)"></i>' : '';
                var proccessedByCenter = row.Process_By_Center == 1 ? "Not Proccessed" : row.Process_By_Center
                var documentVerified = row.Document_Verified == 1 ? "Not Verified" : row.Document_Verified
                var proccessedToUniversity = row.Processed_To_University == 1 ? "Not Proccessed" : row.Processed_To_University
                var paymentVerified = row.Payment_Received == 1 ? "Not Verified" : row.Payment_Received
                var info = row.Step == 4 ? '<i class="uil uil-info-circle cursor-pointer" data-html="true" data-toggle="tooltip" data-placement="top" title="Proccessed By Center: <strong>' + proccessedByCenter + '</strong>&#013;&#010;Document Verified: <strong>' + documentVerified + '</strong>&#013;&#010;Payment Verified: <strong>' + paymentVerified + '</strong>&#013;&#010;Proccessed to University: <strong>' + proccessedToUniversity + '</strong>"></i>' : '';
                return print + edit + deleted + info;
              }
            },
            {
              data: "Photo",
              "render": function(data, type, row) {
                return '<span class="thumbnail-wrapper d48 circular inline">\
              <img src="' + data + '" alt="" data-src="' + data + '"\
                data-src-retina="' + data + '" width="32" height="32">\
            </span>';
              }
            },
            {
              data: "Unique_ID",
              "render": function(data, type, row) {
                return '<span class="cursor-pointer" title="Click to export documents" onclick="exportDocuments(&#39;' + row.ID + '&#39;)"><strong>' + data + '</strong></span>';
              }
            },
            {
              data: "Step",
              "render": function(data, type, row) {
                var label_class = data < 4 ? 'label-important' : 'label-success';
                var status = data < 4 ? 'In Draft @ Step ' + data : 'Completed';
                return '<sapn class="label ' + label_class + '">' + status + '</sapn>';
              }
            },
            {
              data: "Process_By_Center",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4) {
                  return '<div class="text-center"><span class="label label-primary">Not Received</span></div>';
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">' + data + '</span></div>' : '';
                  return show;
                }
              }
            },
            {
              data: "Document_Verified",
              "render": function(data, type, row) {
                if (row.Pendency_Status == 2) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer"><strong>In Review</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Re-Review</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Re-Review</strong></span></div>'
                  }
                } else if (row.Pendency != 0) {
                  if (!showInhouse) {
                    return '<div class="text-center text-danger"><span class="cursor-pointer" onclick="uploadPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>'
                  } else {
                    return is_operations ? '<div class="text-center text-danger"><span class="cursor-pointer" onclick="reportPendency(&#39;' + row.ID + '&#39;)"><strong>Pendency</strong></span></div>' : '<div class="text-center text-danger"><span><strong>Pendency</strong></span></div>'
                  }
                } else {
                  if (data == 1) {
                    var show = is_operations && row.Process_By_Center != 1 ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyDocument(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center text-danger"><strong>Pending</strong></div>' : '';
                    return show;
                  } else {
                    var show = row.Step == 4 && row.Process_By_Center != 1 ? '<div class="text-center"><span class="label label-success">Verified at ' + data + '</span></div>' : '';
                    return show;
                  }
                }
              }
            },
            {
              data: "Payment_Received",
              "render": function(data, type, row) {
                if (data == 1 && row.Step == 4 && row.Process_By_Center != 1) {
                  var show = is_accountant ? '<div class="text-center"><span class="cursor-pointer" onclick="verifyPayment(&#39;' + row.ID + '&#39;)"><strong>Review</strong></span></div>' : '<center><span class="label label-primary">Pending</span></center>';
                  return show;
                } else if (row.Process_By_Center != 1) {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-primary">Verified on ' + data + '</span></div>' : '';
                  return show;
                } else {
                  return '';
                }
              },
              visible: false
            },
            {
              data: "Processed_To_University",
              "render": function(data, type, row) {
                if (data == 1) {
                  var show = showInhouse && row.Document_Verified != 1 && row.Payment_Received != 1 ? '<div class="form-check complete mt-2">\
                <input type="checkbox" id="processed-to-university-' + row.ID + '" onclick="processedToUniversity(&#39;' + row.ID + '&#39;)">\
                <label for="processed-to-university-' + row.ID + '">Mark as Processed</label>\
              </div>' : "";
                  return show;
                } else {
                  var show = row.Step == 4 ? '<div class="text-center"><span class="label label-success">Processed on ' + data + '</span></div>' : '';
                  return show;
                }
              },
              visible: false
            },
            {
              data: "Enrollment_No",
              "render": function(data, type, row) {
                var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "OA_Number",
              "render": function(data, type, row) {
                var edit = showInhouse ? '<i class="uil uil-edit ml-2 cursor-pointer" title="Add Form No" onclick="addOANumber(&#39;' + row.ID + '&#39;)">' : '';
                return data + edit;
              }
            },
            {
              data: "Adm_Session"
            },
            {
              data: "Adm_Type"
            },
            {
              data: "Adm_Type",
              "render": function(data, type, row) {
                return '<span onclick="reportPendnency(' + row.ID + ')"><strong>Report</strong><span>';
              },
              visible: false,
            },
            {
              data: "First_Name",
              "render": function(data, type, row) {
                return '<strong>' + data + '</strong>';
              }
            },
            {
              data: "Father_Name"
            },
            {
              data: "Short_Name"
            },
            {
              data: "Duration"
            },
            {
              data: "Status",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;);" type="checkbox" ' + checked + ' id="student-status-switch-' + row.ID + '">\
                <label for="student-status-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin || showStatus
            },
            {
              data: "ID_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'ID_Card\');" type="checkbox" ' + checked + ' id="student-id-card-switch-' + row.ID + '">\
                <label for="student-id-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Admit_Card",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Admit_Card\');" type="checkbox" ' + checked + ' id="student-admit-card-switch-' + row.ID + '">\
                <label for="student-admit-card-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "Exam",
              "render": function(data, type, row) {
                var active = data == 1 ? 'Active' : 'Inactive';
                if (row.Step == 4 && showInhouse && row.Enrollment_No.length > 0) {
                  var checked = data == 1 ? 'checked' : '';
                  return '<div class="form-check form-check-inline switch switch-lg success">\
                <input onclick="changeStatus(\'Students\', &#39;' + row.ID + '&#39;, \'Exam\');" type="checkbox" ' + checked + ' id="student-exam-switch-' + row.ID + '">\
                <label for="student-exam-switch-' + row.ID + '">' + active + '</label>\
              </div>';
                } else {
                  return active;
                }
              },
              visible: hasStudentLogin
            },
            {
              data: "DOB"
            },
            {
              data: "Center_Code"
            },
            {
              data: "Center_Name"
            },
            {
              data: "RM",
              visible: ['Center','Sub-Center'].includes(role) ? false : true
            }
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
        notProcessedTable.dataTable(notProcessedSettings);
        readyForVerificationTable.dataTable(readyForVerificationSettings);
        verifiedTable.dataTable(verifiedSettings);
        processedToUniversityTable.dataTable(processedToUniversitySettings);
        enrolledTable.dataTable(enrolledSettings);

        // search box for table
        $('#application-search-table').keyup(function() {
          applicationTable.fnFilter($(this).val());
        });

        $('#not-processed-search-table').keyup(function() {
          notProcessedTable.fnFilter($(this).val());
        });

        $('#ready-for-verification-search-table').keyup(function() {
          readyForVerificationTable.fnFilter($(this).val());
        });

        $('#document-verified-search-table').keyup(function() {
          documentVerifiedTable.fnFilter($(this).val());
        });

        $('#processed-to-university-search-table').keyup(function() {
          processedToUniversityTable.fnFilter($(this).val());
        });

        $('#enrolled-search-table').keyup(function() {
          enrolledTable.fnFilter($(this).val());
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
        window.open('/forms/<?= $_SESSION['university_id'] ?>/?student_id=' + id);
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
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>