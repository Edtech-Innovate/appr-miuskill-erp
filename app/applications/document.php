<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5>Export <span class="semi-bold"></span>Documents as</h5>
  </div>
  <div class="modal-body">
    <div class="row text-center">
      <div class="col-md-6 col-sm-6 col-xs-6">
        <i class="uil uil-file-download-alt cursor-pointer" style="font-size:30px" onclick="exportPdf('<?=$id?>')"></i>
      </div>
      <div class="col-sm-6 col-sm-6 col-xs-6 sm-p-t-30">
        <i class="uil uil-files-landscapes-alt cursor-pointer" style="font-size:30px" onclick="exportZip('<?=$id?>')"></i>
      </div>
    </div>
  </div>
<?php } ?>
