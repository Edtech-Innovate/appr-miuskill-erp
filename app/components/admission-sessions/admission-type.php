<?php
if (isset($_GET['id'])) {
  require '../../../includes/db-config.php';
  $id = intval($_GET['id']);

  $admissionSession = $conn->query("SELECT Admission_Type, University_ID FROM Admission_Sessions WHERE ID = $id");
  $admissionSession = $admissionSession->fetch_assoc();
  $university_id = $admissionSession['University_ID'];
  $assignedAdmissionType = $admissionSession['Admission_Type'];
?>

  <div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h6>Allot <span class="semi-bold">Admission Type</span></h6>
  </div>
  <form role="form" id="form-allot-admission-type" action="/app/components/admission-sessions/allot-admission-type" method="POST">
    <div class="modal-body">
      <div class="row">
        <?php
        $admissionTypes = $conn->query("SELECT ID, Name FROM Admission_Type WHERE University_ID = $university_id");
        while ($admissionType = $admissionTypes->fetch_assoc()) { ?>
          <div class="row">
            <div class="form-check complete">
              <input type="checkbox" id="allot-admission-type-<?= $admissionType['ID'] ?>" <?php print in_array($admissionType['ID'], $assignedAdmissionType) ? 'checked' : '' ?> name="allot[]" value="<?= $admissionType['ID'] ?>">
              <label for="allot-admission-type-<?= $admissionType['ID'] ?>" class="font-weight-bold">
                <?= $admissionType['Name'] ?>
              </label>
            </div>
          </div>
        <?php }
        ?>
      </div>
    </div>
  </form>

  <script>
    $("#form-allot-admission-type").on("submit", function(e) {
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
        success: function(data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            notification('success', data.message);
            $('#users-table').DataTable().ajax.reload(null, false);
          } else {
            $(':input[type="submit"]').prop('disabled', false);
            notification('danger', data.message);
          }
        }
      });
      e.preventDefault();
    });
  </script>
<?php }
