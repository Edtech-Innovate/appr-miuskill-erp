<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<style>
  .profile_img {
    width: 150px;
    height: 150px;
    object-fit: fill;
    margin: 10px auto;
    border: 5px solid #ccc;
    border-radius: 50%;
  }
  
  /* Apply to all modal select2 containers EXCEPT the one with .no-modal-zindex */
.modal-open .select2-container:not(.no-modal-zindex) {
    z-index: 0 !importtnt;
}
</style>
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
        <div class="row d-flex justify-content-center">
          <div class="col-md-8">
            <div class="card">
              <div class="card-body">
                <div class="form-group form-group-default required">
                  <label>Students</label>
                  <select class="full-width" style="border: transparent;" data-init-plugin="select2" id="student" onchange="getLedger(this.value)">
                    <option value="">Select</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row m-t-20">
          <div class="col-md-12" id="ledger">

          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

   <script type="text/javascript">
  function getLedger(id) {
    $.ajax({
      url: '/app/students/ledger?id=' + id,
      type: 'GET',
      success: function(data) {
        $("#ledger").html(data);
      }
    });
  }

  // Initialize your student list dropdown
  getStudentList('student');

  // Listen for changes on the student dropdown selection
  $(document).on('change', '#student', function() {
    // Get the data-id attribute ($std_row_ID) from the selected <option>
    var std_row_ID = $(this).find(':selected').data('id');
    console.log(std_row_ID);
    
    // Check if a valid ID exists (avoids running on the placeholder "Select" option)
    if (std_row_ID) {
      getLedger(std_row_ID);
    } else {
      $("#ledger").html(''); // Clear the ledger if they select the empty option
    }
  });
</script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
