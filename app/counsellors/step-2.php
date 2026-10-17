<?php
if (isset($_POST['id']) && isset($_POST['university_id']) && isset($_POST['name'])) {
  require '../../includes/db-config.php';
  session_start();
  $id = intval($_POST['id']);
  $university_id = intval($_POST['university_id']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $counsellor_id = '';
  $sub_counsellor_id = '';
  $durations = '';
  $alloted_counsellor_id = '';
  $alloted_sub_counsellor_id = '';

  $university_is_vocational = 0;
  $is_vocational = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Sharing = 0");
  if ($is_vocational->num_rows > 0) {
    $university_is_vocational = 1;
  }

  $manager = 0;
  $reportingManager = $conn->query("SELECT Reporting FROM University_User WHERE User_ID = $id AND University_ID = $university_id");
  if($reportingManager->num_rows > 0) {
    $manager = $reportingManager->fetch_assoc();
    $manager = $manager['Reporting'];
  }

?>
  <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <p>
      <?php if ($_SESSION['Role'] == 'Administrator') { ?>
        <!-- Back Button -->
        <span class="pull-left link text-color cursor-pointer" onclick="allot(<?= $id ?>, 'full')"><i class="uil uil-arrow-left"></i> Back</span>
      <?php } ?>
    </p>

    <h5>Allot <span class="semi-bold"><?= $name ?></span></h5>

    <!-- Delete Button -->
    <?php if (!empty($alloted_counsellor_id)) { ?>
      <p class="close link text-danger small cursor-pointer" style="margin-top:40px;" onclick="removeAllotment()"><i class="uil uil-trash"></i> Remove</p>
    <?php } ?>

  </div>
  <form role="form" id="form-university-allotment" action="/app/university-allotment/allot<?php print $university_is_vocational == 1 ? '-vocational' : '' ?>" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-12">
          <div class="form-group form-group-default required">
            <label>Vertical Head</label>
            <select class="full-width" style="border: transparent;" name="reporting[1]" id="reporting">
              <option value="">Select</option>
              <?php
                $universityHeads = $conn->query("SELECT Users.ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'University Head' AND University_User.University_ID = " . $university_id);
                while ($universityHead = $universityHeads->fetch_assoc()) { ?>
                  <option value="<?= $universityHead['ID'] ?>" <?php echo $universityHead['ID'] == $manager ? 'selected' : '' ?>><?= $universityHead['Name'] ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>

      <div id="fee">

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
  <script type="text/javascript" src="/assets/plugins/select2/js/select2.full.min.js"></script>
  <script type="text/javascript" src="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js"></script>
  <script>
    function getFeeSructures() {
      const university_id = '<?= $university_id ?>';
      const id = '<?= $id ?>';
      $.ajax({
        url: '/app/university-allotment/<?php print $university_is_vocational == 1 ? 'vocational-departments' : 'sharing' ?>?university_id=' + university_id + '&id=' + id,
        type: 'GET',
        success: function(data) {
          $('#fee').html(data);
        }
      });
    }

    getFeeSructures();

    function removeAllotment() {
      const id = '<?= $id ?>';
      const university_id = '<?= $university_id ?>';
      $.ajax({
        url: '/app/university-allotment/remove-allotment',
        type: 'POST',
        data: {
          id: id,
          university_id: university_id
        },
        dataType: 'json',
        success: function(data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            notification('success', data.message);
          } else {
            notification('danger', data.message);
          }
        }
      })
    }

    $(function() {
      $('#form-university-allotment').validate({
        rules: {
          'reporting[1]': {
            required: true
          },
          'fee[]': {
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

    $("#form-university-allotment").on("submit", function(e) {
      if ($('#form-university-allotment').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?= $id ?>');
        formData.append('university_id', '<?= $university_id ?>');
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
<?php } ?>
