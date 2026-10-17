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
      <a class="active d-flex align-items-center" data-toggle="tab" href="#tab4" data-target="#tab4" role="tab"><i class="uil uil-document fs-14 tab-icon"></i> <span>Documents</span></a>
    </li>
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab5" data-target="#tab5" role="tab"><i class="uil uil-file-check fs-14 tab-icon"></i> <span>Application Form</span></a>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane slide-left padding-20 active sm-no-padding" id="tab4">
      <form id="step_4" role="form" action="/app/application-form/student/update/step-4" enctype="multipart/form-data">
        <?php
        if (!empty($id)) {
          $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
          $photo = mysqli_fetch_array($photo);
        }
        ?>
        <div class="row clearfix">
          <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
            <div class="padding-10 sm-padding-5">
              <h6>Photo</h6>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default required">
                    <label></label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('photo');" id="photo" name="photo" class="form-control mt-1">
                    <?php if (!empty($id) && !empty($photo)) { ?>
                      <img src="<?php print !empty($id) ? $photo['Location'] : '' ?>" height="100" />
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (!empty($id)) {
            $aadhaars = array();
            $aadhaar = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Aadhar'");
            if ($aadhaar->num_rows > 0) {
              $aadhaar = mysqli_fetch_array($aadhaar);
              $aadhaars = explode("|", $aadhaar['Location']);
            }
          }
          ?>
          <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
            <div class="padding-10 sm-padding-5">
              <h6>Aadhar</h6>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default required">
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('aadhar');" id="aadhar" name="aadhar[]" multiple="multiple" class="form-control mt-1">
                    <?php if (!empty($id) && !empty($aadhaars)) {
                      foreach ($aadhaars as $aadhar) { ?>
                        <img src="<?php print !empty($id) ? $aadhar : '' ?>" height="80" />
                    <?php }
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (!empty($id)) {
            $students_signature = "";
            $student_signature = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Student Signature'");
            if ($student_signature->num_rows > 0) {
              $student_signature = mysqli_fetch_array($student_signature);
              $students_signature = $student_signature['Location'];
            }
          }
          ?>
          <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
            <div class="padding-10 sm-padding-5">
              <h6>Student's Signature</h6>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default required">
                    <label></label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('student_signature');" id="student_signature" name="student_signature" class="form-control mt-1">
                    <?php if (!empty($id) && !empty($students_signature)) { ?>
                      <img src="<?php print !empty($id) ? $students_signature : '' ?>" height="100" />
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (!empty($id)) {
            $parents_signature = "";
            $parent_signature = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Parent Signature'");
            if ($parent_signature->num_rows > 0) {
              $parent_signature = mysqli_fetch_array($parent_signature);
              $parents_signature = $parent_signature['Location'];
            }
          }
          ?>
          <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
            <div class="padding-10 sm-padding-5">
              <h6>Parent's Signature</h6>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label></label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('parent_signature');" id="parent_signature" name="parent_signature" class="form-control mt-1">
                    <?php if (!empty($id) && !empty($parents_signature)) { ?>
                      <img src="<?php print !empty($id) ? $parents_signature : '' ?>" height="100" />
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php
        if (!empty($id)) {
          $migrations = array();
          $migration = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Migration'");
          if ($migration->num_rows > 0) {
            $migration = mysqli_fetch_array($migration);
            $migrations = explode("|", $migration['Location']);
          }
        }
        ?>
        <div class="row clearfix">
          <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
            <div class="padding-10 sm-padding-5">
              <h6>Migration</h6>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label></label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('migration');" id="migration" name="migration[]" multiple="multiple" class="form-control mt-1">
                    <?php if (!empty($id) && !empty($migrations)) {
                      foreach ($migrations as $migration) { ?>
                        <img src="<?php print !empty($id) ? $migration : '' ?>" height="80" />
                    <?php }
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (!empty($id)) {
            $affidavits = array();
            $affidavit = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Affidavit'");
            if ($affidavit->num_rows > 0) {
              $affidavit = mysqli_fetch_array($affidavit);
              $affidavits = explode("|", $affidavit['Location']);
            }
          }
          ?>
          <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
            <div class="padding-10 sm-padding-5">
              <h6>Affidavit</h6>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label></label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('affidavit');" id="affidavit" name="affidavit[]" multiple="multiple" class="form-control mt-1">
                    <?php if (!empty($id) && !empty($affidavits)) {
                      foreach ($affidavits as $affidavit) { ?>
                        <img src="<?php print !empty($id) ? $affidavit : '' ?>" height="80" />
                    <?php }
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php
          if (!empty($id)) {
            $other_certificates = array();
            $other_certificate = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Other Certificate'");
            if ($other_certificate->num_rows > 0) {
              $other_certificate = mysqli_fetch_array($other_certificate);
              $other_certificates = explode("|", $other_certificate['Location']);
            }
          }
          ?>
          <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
            <div class="padding-10 sm-padding-5">
              <h6>Other Certificates</h6>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label></label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('other_certificate');" id="other_certificate" name="other_certificate[]" multiple="multiple" class="form-control mt-1">
                    <?php if (!empty($id) && !empty($other_certificates)) {
                      foreach ($other_certificates as $other_certificate) { ?>
                        <img src="<?php print !empty($id) ? $other_certificate : '' ?>" height="80" />
                    <?php }
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer m-t-20">
          <button aria-label="" class="btn btn-primary btn-cons btn-animated from-left pull-right" type="submit">
            <span>Next</span>
            <span class="hidden-block">
              <i class="uil uil-angle-right"></i>
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>