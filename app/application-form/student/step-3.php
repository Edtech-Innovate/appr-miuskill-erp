<?php $id = $_SESSION['Student_Table_ID'] ?>
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
      <a class="active d-flex align-items-center" data-toggle="tab" href="#tab3" data-target="#tab3" role="tab"><i class="uil uil-graduation-hat fs-14 tab-icon"></i> <span>Academics</span></a>
    </li>
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab4" data-target="#tab4" role="tab"><i class="uil uil-document fs-14 tab-icon"></i> <span>Documents</span></a>
    </li>
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab5" data-target="#tab5" role="tab"><i class="uil uil-file-check fs-14 tab-icon"></i> <span>Application Form</span></a>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane slide-left padding-20 active sm-no-padding" id="tab3">
      <form id="step_3" role="form" autocomplete="off" action="/app/application-form/student/update/step-3" method="POST" enctype="multipart/form-data">
        <div class="row row-same-height">
          <div class="col-md-2 b-r b-dashed b-grey sm-b-b">
            <div class="d-flex justify-content-center">
              <lottie-player src="https://assets5.lottiefiles.com/packages/lf20_oojuetow.json" background="transparent" speed="1" style="width: 200px; height: 200px;" hover loop autoplay></lottie-player>
            </div>
          </div>
          <?php
          $high_school = [];
          if (!empty($id)) {
            $high_school = $conn->query("SELECT Student_Academics.*, Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'High School' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'High School' GROUP BY Student_ID");
            if ($high_school->num_rows > 0) {
              $high_school = mysqli_fetch_assoc($high_school);
              $high_marksheet = !empty($high_school['Location']) ? explode('|', $high_school['Location']) : [];
            } else {
              $high_school = [];
            }
          }
          ?>
          <!-- High School -->
          <div class=" b-r b-dashed b-grey sm-b-b" id="high_school_column" style="display:none">
            <div class="padding-10 sm-padding-5">
              <h5>High School</h5>
              <div class="row clearfix">
                <div class="row col-md-12">
                  <div class="form-group form-group-default high_school">
                    <label>Subjects</label>
                    <input type="text" name="high_subject" id="high_subject" class="form-control" value="<?php print !empty($high_school) ? (array_key_exists('Subject', $high_school) ? $high_school['Subject'] : '') : 'All Subjects' ?>" placeholder="ex: All">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default high_school">
                    <label>Year</label>
                    <select class="full-width" style="border: transparent;" name="high_year" id="high_year">
                      <option value="">Select</option>
                      <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                        <option value="<?= $i ?>" <?php print !empty($high_school) ? ($high_school['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default high_school">
                    <label>Board/University</label>
                    <input type="text" name="high_board" id="high_board" value="<?php print !empty($high_school) ? $high_school['Board/Institute'] : '' ?>" class="form-control" placeholder="ex: CBSE">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Marks Obtained</label>
                    <input type="number" min="0" name="high_obtained" id="high_obtained" value="<?php print !empty($high_school) ? $high_school['Marks_Obtained'] : '' ?>" class="form-control" onblur="checkHighMarks();" placeholder="ex: 400">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Max Marks</label>
                    <input type="number" min="0" name="high_max" id="high_max" value="<?php print !empty($high_school) ? $high_school['Max_Marks'] : '' ?>" class="form-control" onblur="checkHighMarks();" placeholder="ex: 600">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default required">
                    <label>Grade/Percentage</label>
                    <input type="text" name="high_total" id="high_total" value="<?php print !empty($high_school) ? $high_school['Total_Marks'] : '' ?>" class="form-control" placeholder="ex: 66%">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default high_school">
                    <label>Marksheet</label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('high_marksheet');" id="high_marksheet" name="high_marksheet[]" multiple="multiple" class="form-control mt-1">
                    <dt><?php print !empty($high_marksheet) ?  count($high_marksheet) . " Marksheet(s) Uploaded" : ''; ?></dt>
                    <?php if (!empty($high_marksheet)) {
                      foreach ($high_marksheet as $hm) { ?>
                        <img src="<?= $hm ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $hm ?>')" width="40" height="40" />
                    <?php }
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php
          $intermediate = [];
          if (!empty($id)) {
            $intermediate = $conn->query("SELECT Student_Academics.*, Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Intermediate' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'Intermediate'");
            if ($intermediate->num_rows > 0) {
              $intermediate = mysqli_fetch_assoc($intermediate);
              $inter_marksheet = !empty($intermediate['Location']) ? explode('|', $intermediate['Location']) : [];
            } else {
              $intermediate = [];
            }
          }
          ?>
          <!-- Intermediate -->
          <div class=" b-r b-dashed b-grey sm-b-b" id="intermediate_column" style="display:none">
            <div class="padding-10 sm-padding-5">
              <h5>Intermediate</h5>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default intermediate">
                    <label>Subjects</label>
                    <input type="text" name="inter_subject" class="form-control" value="<?php print !empty($intermediate) ? (array_key_exists('Subject', $intermediate) ? $intermediate['Subject'] : '') : '' ?>" id="inter_subject" placeholder="ex: PCM">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default intermediate">
                    <label>Year</label>
                    <select class="full-width" style="border: transparent;" name="inter_year" id="inter_year">
                      <option value="">Select</option>
                      <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                        <option value="<?= $i ?>" <?php print !empty($intermediate) ? ($intermediate['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default intermediate">
                    <label>Board/University</label>
                    <input type="text" name="inter_board" id="inter_board" value="<?php print !empty($intermediate) ? (array_key_exists('Board/Institute', $intermediate) ? $intermediate['Board/Institute'] : '') : '' ?>" class="form-control" placeholder="ex: CBSE">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Marks Obtained</label>
                    <input type="number" min="0" name="inter_obtained" id="inter_obtained" class="form-control" onblur="checkInterMarks();" value="<?php print !empty($intermediate) ? (array_key_exists('Marks_Obtained', $intermediate) ? $intermediate['Marks_Obtained'] : '') : '' ?>" placeholder="ex: 400">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Max Marks</label>
                    <input type="number" min="0" name="inter_max" id="inter_max" class="form-control" value="<?php print !empty($intermediate) ? (array_key_exists('Max_Marks', $intermediate) ? $intermediate['Max_Marks'] : '') : '' ?>" onblur="checkInterMarks();" placeholder="ex: 600">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default intermediate">
                    <label>Grade/Percentage</label>
                    <input type="text" name="inter_total" id="inter_total" value="<?php print !empty($intermediate) ? (array_key_exists('Total_Marks', $intermediate) ? $intermediate['Total_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 66%">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default intermediate">
                    <label>Marksheet</label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('inter_marksheet');" id="inter_marksheet" name="inter_marksheet[]" multiple="multiple" class="form-control mt-1">
                    <dt><?php print !empty($inter_marksheet) ? count($inter_marksheet) . " Marksheet Uploaded" : '' ?></dt>
                    <?php if (!empty($inter_marksheet)) {
                      foreach ($inter_marksheet as $im) { ?>
                        <img src="<?= $im ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $im ?>')" width="40" height="40" />
                    <?php }
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php
          $ug = [];
          if (!empty($id)) {
            $ug = $conn->query("SELECT Student_Academics.*, Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'UG' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'UG'");
            if ($ug->num_rows > 0) {
              $ug = mysqli_fetch_assoc($ug);
              $ug_marksheet = !empty($ug['Location']) ? explode('|', $ug['Location']) : [];
            } else {
              $ug = [];
            }
          }
          ?>

          <!-- UG -->
          <div class=" b-r b-dashed b-grey sm-b-b" id="ug_column" style="display:none">
            <div class="padding-10 sm-padding-5">
              <h5>Under Graduate</h5>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default ug-program ">
                    <label>Subjects</label>
                    <input type="text" name="ug_subject" id="ug_subject" class="form-control" value="<?php print !empty($ug) ? (array_key_exists('Subject', $ug) ? $ug['Subject'] : '') : '' ?>" placeholder="ex: BBA">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default ug-program ">
                    <label>Year</label>
                    <select class="full-width" style="border: transparent;" name="ug_year" id="ug_year">
                      <option value="">Select</option>
                      <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                        <option value="<?= $i ?>" <?php print !empty($ug) ? ($ug['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default ug-program ">
                    <label>Board/University</label>
                    <input type="text" name="ug_board" id="ug_board" value="<?php print !empty($ug) ? (array_key_exists('Board/Institute', $ug) ? $ug['Board/Institute'] : '') : '' ?>" class="form-control" placeholder="ex: DU">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Marks Obtained</label>
                    <input type="number" min="0" name="ug_obtained" id="ug_obtained" class="form-control" value="<?php print !empty($ug) ? (array_key_exists('Marks_Obtained', $ug) ? $ug['Marks_Obtained'] : '') : '' ?>" onblur="checkUGMarks()" placeholder="ex: 400">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Max Marks</label>
                    <input type="number" min="0" name="ug_max" id="ug_max" class="form-control" value="<?php print !empty($ug) ? (array_key_exists('Max_Marks', $ug) ? $ug['Max_Marks'] : '') : '' ?>" onblur="checkUGMarks()" placeholder="ex: 600">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default ug-program ">
                    <label>Grade/Percentage</label>
                    <input type="text" name="ug_total" value="<?php print !empty($ug) ? (array_key_exists('Total_Marks', $ug) ? $ug['Total_Marks'] : '') : '' ?>" id="ug_total" class="form-control" placeholder="ex: 66%">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default ug-program ">
                    <label>Marksheet</label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('ug_marksheet');" id="ug_marksheet" name="ug_marksheet[]" multiple="multiple" class="form-control mt-1">
                    <dt><?php print !empty($ug_marksheet) ? count($ug_marksheet) . " Marksheet Uploaded" : '' ?></dt>
                    <?php if (!empty($ug_marksheet)) {
                      foreach ($ug_marksheet as $um) { ?>
                        <img src="<?= $um ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $um ?>')" width="40" height="40" />
                    <?php }
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php
          $pg = [];
          if (!empty($id)) {
            $pg = $conn->query("SELECT Student_Academics.*, Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'PG' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'PG'");
            if ($pg->num_rows > 0) {
              $pg = mysqli_fetch_assoc($pg);
              $pg_marksheet = !empty($pg['Location']) ? explode('|', $pg['Location']) : [];
            } else {
              $pg = [];
            }
          }
          ?>
          <!-- PG -->
          <div class=" b-r b-dashed b-grey sm-b-b" id="pg_column" style="display:none">
            <div class="padding-10 sm-padding-5">
              <h5>Post Graduate</h5>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default pg-program ">
                    <label>Subjects</label>
                    <input type="text" name="pg_subject" id="pg_subject" value="<?php print !empty($pg) ? (array_key_exists('Subject', $pg) ? $pg['Subject'] : '') : '' ?>" class="form-control" placeholder="ex: MBA">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default pg-program ">
                    <label>Year</label>
                    <select class="full-width" style="border: transparent;" name="pg_year" id="pg_year">
                      <option value="">Select</option>
                      <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                        <option value="<?= $i ?>" <?php print !empty($pg) ? ($pg['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default pg-program ">
                    <label>Board/University</label>
                    <input type="text" name="pg_board" value="<?php print !empty($pg) ? (array_key_exists('Board/Institute', $pg) ? $pg['Board/Institute'] : '') : '' ?>" id="pg_board" class="form-control" placeholder="ex: DU">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Marks Obtained</label>
                    <input type="number" min="0" name="pg_obtained" id="pg_obtained" value="<?php print !empty($pg) ? (array_key_exists('Marks_Obtained', $pg) ? $pg['Marks_Obtained'] : '') : '' ?>" class="form-control" onblur="checkPGMarks()" placeholder="ex: 400">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Max Marks</label>
                    <input type="number" min="0" name="pg_max" id="pg_max" value="<?php print !empty($pg) ? (array_key_exists('Max_Marks', $pg) ? $pg['Max_Marks'] : '') : '' ?>" class="form-control" onblur="checkPGMarks()" placeholder="ex: 600">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default pg-program ">
                    <label>Grade/Percentage</label>
                    <input type="text" name="pg_total" id="pg_total" value="<?php print !empty($pg) ? (array_key_exists('Total_Marks', $pg) ? $pg['Total_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 66%">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default pg-program ">
                    <label>Marksheet</label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('pg_marksheet');" name="pg_marksheet[]" id="pg_marksheet" multiple="multiple" class="form-control mt-1">
                    <dt><?php print !empty($pg_marksheet) ? count($pg_marksheet) . " Marksheet Uploaded" : '' ?></dt>
                    <?php if (!empty($pg_marksheet)) {
                      foreach ($pg_marksheet as $pm) { ?>
                        <img src="<?= $pm ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $pm ?>')" width="40" height="40" />
                    <?php }
                    } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php
          $other = [];
          if (!empty($id)) {
            $other = $conn->query("SELECT Student_Academics.*, Location FROM Student_Academics LEFT JOIN Student_Documents ON Student_Academics.Student_ID = Student_Documents.Student_ID AND Student_Documents.`Type` = 'Other' WHERE Student_Academics.Student_ID = $id AND Student_Academics.Type = 'Other' GROUP BY Student_ID");
            if ($other->num_rows > 0) {
              $other = mysqli_fetch_assoc($other);
              $other_marksheet = !empty($other['Location']) ? explode('|', $other['Location']) : [];
            } else {
              $other = [];
            }
          }
          ?>
          <!-- Other -->
          <div class=" " id="other_column" style="display:none">
            <div class="padding-10 sm-padding-5">
              <h5>Other</h5>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default other-program ">
                    <label>Subjects</label>
                    <input type="text" name="other_subject" id="other_subject" class="form-control" value="<?php print !empty($other) ? (array_key_exists('Subject', $other) ? $other['Subject'] : '') : '' ?>" placeholder="ex: Diploma">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default other-program ">
                    <label>Year</label>
                    <select class="full-width" style="border: transparent;" name="other_year" id="other_year">
                      <option value="">Select</option>
                      <?php for ($i = date('Y'); $i >= 1947; $i--) { ?>
                        <option value="<?= $i ?>" <?php print !empty($other) ? ($other['Year'] == $i ? 'selected' : '') : '' ?>><?= $i ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default other-program ">
                    <label>Board/University</label>
                    <input type="text" name="other_board" id="other_board" value="<?php print !empty($other) ? (array_key_exists('Board/Institute', $other) ? $other['Board/Institute'] : '') : '' ?>" class="form-control" placeholder="ex: DU">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Marks Obtained</label>
                    <input type="number" min="0" name="other_obtained" id="other_obtained" value="<?php print !empty($other) ? (array_key_exists('Marks_Obtained', $other) ? $other['Marks_Obtained'] : '') : '' ?>" class="form-control" onblur="checkOtherMarks()" placeholder="ex: 400">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default">
                    <label>Max Marks</label>
                    <input type="number" min="0" name="other_max" id="other_max" value="<?php print !empty($other) ? (array_key_exists('Max_Marks', $other) ? $other['Max_Marks'] : '') : '' ?>" class="form-control" onblur="checkOtherMarks()" placeholder="ex: 600">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default other-program ">
                    <label>Grade/Percentage</label>
                    <input type="text" name="other_total" id="other_total" value="<?php print !empty($other) ? (array_key_exists('Total_Marks', $other) ? $other['Total_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 66%">
                  </div>
                </div>
              </div>
              <div class="row clearfix">
                <div class="col-md-12">
                  <div class="form-group form-group-default other-program ">
                    <label>Marksheet</label>
                    <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('other_marksheet');" id="other_marksheet" name="other_marksheet[]" multiple="multiple" class="form-control mt-1">
                    <dt><?php print !empty($other_marksheet) ? count($other_marksheet) . " Marksheet Uploaded" : '' ?></dt>
                    <?php if (!empty($other_marksheet)) {
                      foreach ($other_marksheet as $om) { ?>
                        <img src="<?= $om ?>" class="cursor-pointer mr-2" onclick="window.open('<?= $om ?>')" width="40" height="40" />
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