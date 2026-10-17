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
              <div class="text-end">
                <span class="text-muted bold cursor-pointer" onclick="add('notifications','lg')"> Add</sapn>
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
        <div class="row">
          <div class="col-md-6">
            <div class="form-group form-group-default required">
              <label>Select Notification Heading</label>
              <select class="full-width" style="border: transparent;" id="heading" onchange="getTable()">
                <option value="">Choose</option>
                <option value="Fee">Fee</option>
                <option value="Admisssion">Admission</option>
                <option value="Exam">Exam</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group form-group-default required">
              <label>Notification by user</label>
              <select class="full-width" style="border: transparent;" id="send_to" onchange="getTable()">
                <option value="">Choose</option>
                <option value="student">Student</option>
                <option value="center">Center</option>
                <option value="all">All</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row" id="notifications"></div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
     $(document).ready(function(){
      getTable();
     });

      function getSemester(id) {
        $.ajax({
          url: '/app/subjects/semester?id=' + id,
          type: 'GET',
          success: function(data) {
            $("#semester").html(data);
          }
        })
      }
    </script>

    <script type="text/javascript">
      function getTable() {
        var heading = $('#heading').val();
        var send_to = $('#send_to').val();
          $.ajax({
            url: '/app/notifications/server?heading=' + heading + '&send_to=' + send_to,
            type: 'GET',
            success: function(data) {
              $('#notifications').html(data);
            }
          })
      }

      function removeTable() {
        $('#notifications').html('');
      }
    </script>

    <script type="text/javascript">
      function uploadFile(table, column, id) {
        $.ajax({
          url: '/app/upload/create?id=' + id + '&column=' + column + '&table=' + table,
          type: 'GET',
          success: function(data) {
            $("#md-modal-content").html(data);
            $("#mdmodal").modal('show');
          }
        })
      }
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
