<div id="rootwizard" class="m-t-50">
  <!-- Nav tabs -->
  <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" role="tablist" data-init-reponsive-tabs="dropdownfx">
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab1" data-target="#tab1" role="tab"><i class="uil uil-user-circle fs-14 tab-icon"></i> <span>Basic Details</span></a>
    </li>
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab2" data-target="#tab2" role="tab"><i class="uil uil-location fs-14 tab-icon"></i> <span>Contact Details & Fee</span></a>
    </li>
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab3" data-target="#tab3" role="tab"><i class="uil uil-graduation-hat fs-14 tab-icon"></i> <span>Academics</span></a>
    </li>
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab4" data-target="#tab4" role="tab"><i class="uil uil-document fs-14 tab-icon"></i> <span>Documents</span></a>
    </li>
    <li class="nav-item">
      <a class="active d-flex align-items-center" data-toggle="tab" href="#tab5" data-target="#tab5" role="tab"><i class="uil uil-file-check fs-14 tab-icon"></i> <span>Application Form</span></a>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane slide-left active padding-20 sm-no-padding" id="tab5">
      <h1>Thank you for providing the requested information.</h1>
      <h3>Please use the link below to print the pre-filled application form.</h3>
      <?php $_SESSION['printFromId'] = base64_encode('W1Ebt1IhGN3ZOLplom9I' . $_SESSION['Student_Table_ID']); ?>
      <button class="btn btn-primary btn-lg m-b-10" onclick="printForm('<?= $_SESSION['printFromId'] ?>')"><i class="uil uil-print"></i> &nbsp;Print </button>
    </div>
  </div>
</div>