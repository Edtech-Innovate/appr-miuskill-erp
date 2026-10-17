<?php
require '../../includes/db-config.php';
session_start();

$id   = intval($_GET['id']);
$type = $_GET['type'];

$query  = $conn->query("SELECT center_doc, payment_proof FROM center_authorize WHERE id = $id");
$center = $query->fetch_assoc();

$files = [];
if (!empty($center['center_doc'])) {
    $files = json_decode($center['center_doc'], true); // stored as JSON
    if (!is_array($files)) {
        $files = [];
    }
}
?>

<div class="modal-header clearfix text-left">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="pg-icon">close</i>
    </button>
    <h5><?= ($type == 'payment_proof' ? 'Payment Image / Pdf' : 'Center Documents') ?></h5>
</div>

<div class="modal-body">
    <div class="row" style="overflow-y: auto;max-height:600px;">

        <?php if ($type == 'center_doc') { ?>
            <?php foreach ($files as $file):
                $ext      = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $filePath = "../../uploads/center_docs/" . $file;
                $fileUrl  = "/uploads/center_docs/" . $file;
            ?>
                <div class="col-md-4 text-center mb-3">
                    <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                        <img src="<?= $fileUrl ?>" class="img-fluid img-thumbnail" style="height:100px;width:120px;">
                    <?php elseif ($ext == 'pdf'): ?>
                        <img src="/assets/img/icons/pdf.png" style="height:100px;width:120px;">
                    <?php else: ?>
                        <i class="fa fa-file fa-5x text-secondary"></i>
                    <?php endif; ?>

                    <div class="mt-2">
                        <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-sm btn-info">View</a>
                        <a href="<?= $fileUrl ?>" download class="btn btn-sm btn-success">Download</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="col-12 text-center mt-3">
                <a href="/app/center_authorize/center_doc_download_zip.php?id=<?= $id ?>" class="btn btn-primary">
                    <i class="fa fa-download"></i> Download All (ZIP)
                </a>
            </div>

        <?php } elseif ($type == 'payment_proof') { ?>
            <?php if (!empty($center['payment_proof'])):
                $proof   = $center['payment_proof'];
                $ext     = strtolower(pathinfo($proof, PATHINFO_EXTENSION));
                $fileUrl = $proof;
            ?>
                <div class="col-md-12 text-center mb-3">
                    <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                        <img src="<?= $fileUrl ?>" class="img-fluid img-thumbnail" style="max-height:300px;">
                    <?php elseif ($ext == 'pdf'): ?>
                        <embed src="<?= $fileUrl ?>" type="application/pdf" width="100%" height="400px" />
                    <?php else: ?>
                        <i class="fa fa-file fa-5x text-secondary"></i>
                    <?php endif; ?>

                    <div class="mt-2">
                        <a href="<?= $fileUrl ?>" target="_blank" class="btn btn-sm btn-info">View</a>
                        <a href="<?= $fileUrl ?>" download class="btn btn-sm btn-success">Download</a>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-center text-danger">No payment proof uploaded.</p>
            <?php endif; ?>
        <?php } ?>

    </div>
</div>
