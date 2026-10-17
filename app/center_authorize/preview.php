<?php
require '../../includes/db-config.php';
session_start(); 

$id = intval($_GET['id']);
$query = $conn->query("SELECT * FROM center_authorize WHERE id = $id");
$center = $query->fetch_assoc();
?>
<div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="pg-icon">close</i>
    </button>
    <h5>Center Authorization Certificate Preview</h5>
</div>

<form id="centerForm" role="form" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
        <div class="card shadow-none border-none rounded">          
            <?php if ($center): ?>
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Center Name</th>
                        <td><?= htmlspecialchars($center['center_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td>
                            <?= ($center['type_id'] == 47 ? "Bvoc" : ($center['type_id'] == 48 ? "Skill" : "Other")) ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Date of Issue</th>
                        <td><?= htmlspecialchars($center['date_of_issue']) ?></td>
                    </tr>
                     <tr>
                        <th>Programs</th>
                        <td><?= htmlspecialchars($center['programs']) ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?= htmlspecialchars($center['address']) ?></td>
                    </tr>
                </table>

                <div class="mt-4 d-flex justify-content-end">
                    <a href="/app/center_authorize/parnter_pdf?id=<?= $center['id'] ?>" target="_blank" class="btn btn-danger mx-2">
                        <i class="uil uil-file-download"></i> Download PDF
                    </a>
                    <a href="/app/center_authorize" class="btn btn-secondary">
                        <i class="uil uil-arrow-left"></i> Back
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">Center not found!</div>
            <?php endif; ?>
        </div>  
    </div>
</form>