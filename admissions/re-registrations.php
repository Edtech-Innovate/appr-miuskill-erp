<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
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
                <?php if (isset($_SESSION['active_rr_session_id'])) { ?>
                  <button class="btn btn-outline-primary btn-sm" onclick="resetRRSession()">Change Session</button>
                  <a href="/app/re-registrations/export" target="_blank" class="btn btn-outline-info btn-sm">Download</a>
                <?php } ?>
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
        <?php if (!isset($_SESSION['active_rr_session_id'])) { ?>
          <div class="row">
            <?php
            $examSessions = $conn->query("SELECT ID, Name FROM Exam_Sessions WHERE University_ID = " . $_SESSION['university_id'] . " AND (RR_Status = 1 OR RR_Last_Date IS NOT NULL)");
            while ($examSession = $examSessions->fetch_assoc()) { ?>
              <div class="col-md-3">
                <div class="card cursor-pointer" onclick="setRRSession(<?= $examSession['ID'] ?>, '<?= $examSession['Name'] ?>')">
                  <div class="card-body">
                    <p class="hint-text overline">EXAM SESSION</p>
                    <h3><?= $examSession['Name'] ?></h3>
                  </div>
                </div>
              </div>
            <?php }
            ?>
          </div>
        <?php } else { ?>
          <div class="row clearfix">
            <div class="col-md-12">
              <h5><?= $_SESSION['active_rr_session_name'] ?></h5>
            </div>
          </div>
          <div class="row clearfix">
            <div class="col-md-12">
              <div class="card card-transparent">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-linetriangle" data-init-reponsive-tabs="dropdownfx">
                  <li class="nav-item">
                    <a class="active" data-toggle="tab" data-target="#applicableList" href="#"><span>Applicable Students - <span id="applicableCount">0</span></span></a>
                  </li>
                  <li class="nav-item">
                    <a data-toggle="tab" data-target="#applied" href="#"><span>Applied - <span id="appliedCount">0</span></span></a>
                  </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                  <div class="tab-pane active" id="applicableList">
                    <div class="row m-t-20">
                      <div class="col-lg-12">
                        <div class="table-responsive">
                          <table class="table table-striped nowrap" id="applicableTable">
                            <thead>
                              <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>RR Sem</th>
                                <th>Enrollemnt No</th>
                                <th>Adm Session</th>
                                <th>Course</th>
                                <th>Owner</th>
                                <th data-orderable="false"></th>
                              </tr>
                            </thead>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="tab-pane" id="applied">
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="table-responsive">
                          <table class="table table-striped nowrap" id="appliedTable">
                            <thead>
                              <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>RR Sem</th>
                                <th>Enrollemnt No</th>
                                <th>Adm Session</th>
                                <th>Course</th>
                                <th>Owner</th>
                                <th data-orderable="false"></th>
                              </tr>
                            </thead>
                          </table>
                        </div>
                      </div>
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
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script>
      function setRRSession(id, name) {
        $.ajax({
          url: '/app/re-registrations/set-session',
          type: 'POST',
          data: {
            id,
            name
          },
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              window.location.reload();
            } else {
              notification('danger', data.message);
            }
          }
        })
      }

      function resetRRSession() {
        $.ajax({
          url: '/app/re-registrations/reset-session',
          type: 'POST',
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              window.location.reload();
            } else {
              notification('danger', data.message);
            }
          }
        })
      }
    </script>

    <script>
      var applicableTable = $('#applicableTable');
      var appliedTable = $('#appliedTable');
      var actionVisibility = <?= $_SESSION['show_action_in_active_rr'] == 0 ? 'false' : 'true' ?>;
      var applicableSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/re-registrations/applicable-server',
          'type': 'POST',
          complete: function(xhr, responseText) {
            $('#applicableCount').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
            data: "Unique_ID"
          },
          {
            data: "First_Name"
          },
          {
            data: "Duration"
          },
          {
            data: "Enrollment_No"
          },
          {
            data: "Admission_Session_ID"
          },
          {
            data: "Course_ID"
          },
          {
            data: "Added_For"
          },
          {
            data: "ID",
            "render": function(data, type, row) {
              return '<span class="cursor-pointer bold" onclick="applyRR(&#39;' + data + '&#39;)">Apply</span>';
            },
            visible: actionVisibility
          }
        ],
        "sDom": "lf<t><'row'<p i>>",
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

      var appliedSettings = {
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
          'url': '/app/re-registrations/applied-server',
          'type': 'POST',
          complete: function(xhr, responseText) {
            $('#appliedCount').html(xhr.responseJSON.iTotalDisplayRecords);
          }
        },
        'columns': [{
            data: "Unique_ID"
          },
          {
            data: "First_Name"
          },
          {
            data: "Duration"
          },
          {
            data: "Enrollment_No"
          },
          {
            data: "Admission_Session_ID"
          },
          {
            data: "Course_ID"
          },
          {
            data: "Added_For"
          },
          {
            data: "ID",
            "render": function(data, type, row) {
              return '<span class="cursor-pointer bold" onclick="cancelRR(&#39;' + data + '&#39;)">Cancel</span>';
            },
            visible: actionVisibility
          }
        ],
        "sDom": "lf<t><'row'<p i>>",
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

      applicableTable.dataTable(applicableSettings);
      appliedTable.dataTable(appliedSettings);
    </script>

    <?php if ($_SESSION['show_action_in_active_rr'] == 1) { ?>
      <script>
        function applyRR(id) {
          Swal.fire({
            title: 'Are you sure?',
            text: "You will be able to cancel before the last date!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Apply',
            cancelButtonText: 'Close'
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: '/app/re-registrations/apply',
                type: 'POST',
                data: {
                  id
                },
                dataType: 'json',
                success: function(data) {
                  if (data.status) {
                    notification('success', data.message);
                    $('.table').DataTable().ajax.reload(null, false);
                  } else {
                    notification('danger', data.message);
                  }
                }
              })
            } else {
              $('.table').DataTable().ajax.reload(null, false);
            }
          })
        }

        function cancelRR(id) {
          Swal.fire({
            title: 'Are you sure?',
            text: "You will be able to apply again before the last date!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'Close'
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: '/app/re-registrations/cancel',
                type: 'POST',
                data: {
                  id
                },
                dataType: 'json',
                success: function(data) {
                  if (data.status) {
                    notification('success', data.message);
                    $('.table').DataTable().ajax.reload(null, false);
                  } else {
                    notification('danger', data.message);
                  }
                }
              })
            } else {
              $('.table').DataTable().ajax.reload(null, false);
            }
          })
        }
      </script>
    <?php } ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>