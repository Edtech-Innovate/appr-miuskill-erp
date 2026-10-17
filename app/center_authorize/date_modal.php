<?php
$field = $_GET['field'];
$value = $_GET['value'];
$id    = $_GET['id'];
$type = ($field == 'center_doc') ? 'file' : 'date';
$heading = ($field == 'center_doc') ? 'Center Document' : $field;

?>
<div class="modal-header">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="pg-icon">close</i>
    </button>
    <h5>Add <?= ucfirst(str_replace("_", " ", $heading)) ?></h5>
</div>
<form id="dateForm" enctype="multipart/form-data">
    <div class="modal-body">
        <input type="hidden" name="field" value="<?= $field ?>">
        <input type="hidden" name="id" value="<?= $id ?>">

        <?php if ($type == 'file'): ?>
            <!-- Multiple files (images + pdfs) -->
            <input type="file" name="files[]" class="form-control" multiple accept="image/*,.pdf" required>
        <?php else: ?>
            <!-- Date input -->
            <input type="date" name="date" value="<?= $value ?>" class="form-control edit_date" min="<?= date('Y-m-d'); ?>" required>
        <?php endif; ?>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

<script>
     document.addEventListener("DOMContentLoaded", function() {
        const today = new Date().toISOString().split("T")[0];
        document.getElementsByClassName("edit_date").setAttribute("min", today);
    });
    $("#dateForm").submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "/app/center_authorize/receiving_dispatch_date.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.status == 200) {
                    notification('success', res.message);
                    $('#mdmodal').modal('hide');
                    $('#autorize_center-table').DataTable().ajax.reload(null, false);
                } else {
                    notification('danger', res.message);
                }
            },
            error: function() {
                notification('danger', 'Something went wrong while uploading.');
            }
        });
    });
</script>
