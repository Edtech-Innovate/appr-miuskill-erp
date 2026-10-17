<!-- Modal -->
<?php

require '../../includes/db-config.php';
$label = '';
if (isset($_POST['id']) && isset($_POST['code']) && isset($_POST['subject_name'])) {
    $id = intval($_POST['id']);
    $code = $_POST['code'];
    $subject_name = $_POST['subject_name'];
    $label = " of " . $subject_name . "(" . $code . ")";
    $getfile = $conn->query("SELECT Syllabus as files FROM Syllabi WHERE ID = " . $id ."");
    $getfile = $getfile->fetch_assoc();

}
?>
<div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
            class="pg-icon">close</i>
    </button>
    <h5>Upload Syllabus <?= $label ?></h5>

</div>
<form role="form" id="form-upload" action="/app/subjects/upload-syllabus" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="row">
            <div class="col-md-10">
                <input name="file" type="file"
                    accept="image/*, .pdf, .csv, .doc, .docx, application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
            </div>
            <?php if(isset($getfile['files']) && isset($id)){ ?>
                <div class="col-md-2">
                    <input type="hidden" name="update_file" value="<?= $getfile['files'] ?>">
                <a href="..<?= $getfile['files']?>" download> <i class="uil uil-down-arrow " title="Download Syllabus" ></i></a>
            </div>
            <?php } ?>


        </div>
    </div>
    <div class="modal-footer clearfix text-end">
        <div class="col-md-4 m-t-10 sm-m-t-10">
            <button aria-label="" type="submit" id="submit-button"
                class="btn btn-primary btn-cons btn-animated from-left">
                <span>Upload</span>
                <span class="hidden-block">
                    <i class="uil uil-upload"></i>
                </span>
            </button>
        </div>
    </div>
</form>

<script>

    $("#form-upload").on("submit", function (e) {
        if ($('#form-upload').valid()) {
            $(':input[type="submit"]').prop('disabled', true);
            var formData = new FormData(this);
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
                        $('#users-table').DataTable().ajax.reload(null, false);
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