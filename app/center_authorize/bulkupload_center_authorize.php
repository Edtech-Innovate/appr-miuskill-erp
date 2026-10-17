<?php
require '../../includes/db-config.php';
session_start();
?>

<!-- Modal -->
<div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="pg-icon">close</i>
    </button>
    <h5>Upload Center Authorization Certificate</h5>
</div>

<form id="uploadForm" enctype="multipart/form-data" method="POST" action="/app/center_authorize/store.php">
    <div class="modal-body">
        <div class="form-group">
            <div class="text-end">
                <a href="/app/center_authorize/samplefile.csv" download="authorize_center_sample.csv">Download Sample CSV</a>
            </div>
            <label for="file">Upload Excel File</label>
            <input type="file" class="form-control" name="file" id="file" accept=".xls,.xlsx" required>
        </div>
    </div>
    <div class="modal-footer clearfix justify-content-end">
        <button type="submit" class="btn btn-success">Upload</button>
    </div>
</form>

