<?php
$subCourseAdmissionType = $conn->query("SELECT JSON_KEYS(Admission_Type) as Admission_Type_IDs FROM Sub_Courses WHERE ID = " . $_SESSION['Sub_Course_ID']);
$subCourseAdmissionType = $subCourseAdmissionType->fetch_assoc();
$subCourseAdmissionType = json_decode($subCourseAdmissionType['Admission_Type_IDs'], true);
if (count($subCourseAdmissionType) == 1) {
  $_SESSION['Admission_Type_ID'] = $subCourseAdmissionType[0];
}

$studentDetails = array();
if (!empty($_SESSION['Student_Table_ID'])) {
  $studentDetails = $conn->query("SELECT * FROM Students WHERE ID = " . $_SESSION['Student_Table_ID']);
  if ($studentDetails->num_rows > 0) {
    $studentDetails = $studentDetails->fetch_assoc();
  }
}

?>

<div id="rootwizard" class="m-t-50">
  <!-- Nav tabs -->
  <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" role="tablist" data-init-reponsive-tabs="dropdownfx">
    <li class="nav-item">
      <a class="active d-flex align-items-center" data-toggle="tab" href="#tab1" data-target="#tab1" role="tab"><i class="uil uil-user-circle fs-14 tab-icon"></i> <span>Basic Details</span></a>
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
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab5" data-target="#tab5" role="tab"><i class="uil uil-file-check fs-14 tab-icon"></i> <span>Application Form</span></a>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane padding-20 sm-no-padding active slide-left" id="tab1">
      <div class="row row-same-height">
        <div class="col-md-4 b-r b-dashed b-grey sm-b-b">
          <div class="padding-10 sm-padding-5 sm-m-t-15 m-t-50">
            <div class="d-flex justify-content-center">
              <lottie-player src="https://assets6.lottiefiles.com/packages/lf20_qfkr9cgr.json" background="transparent" speed="1" style="width: 200px; height: 200px;" loop autoplay></lottie-player>
            </div>
            <h2>Fill up the Basic Details for admission</h2>
            <ol>
              <li>Option with (*) star mark are mandatory.</li>
              <li>Please keep the required documents within 500KB.</li>
              <li>Marksheet, Certificate, and Aadhaar Card are the only documents that can be uploaded in multiple files</li>
            </ol>
          </div>
        </div>
        <div class="col-md-8">
          <div class="padding-10 sm-padding-5">
            <form id="step_1" role="form" autocomplete="off" action="/app/application-form/student/update/step-1" enctype="multipart/form-data">
              <h5>Applying For</h5>
              <div class="row clearfix">
                <!-- Admission Session -->
                <div class="col-md-6">
                  <div class="form-group form-group-default required">
                    <label>Admission Session</label>
                    <select class="full-width" style="border: transparent;" name="admission_session" id="admission_session" onchange="getAdmissionType(this.value)">
                      <option value="">Select</option>
                    </select>
                  </div>
                </div>

                <!-- Admission Type -->
                <div class="col-md-6">
                  <div class="form-group form-group-default required">
                    <label>Admission Type</label>
                    <select class="full-width" style="border: transparent;" name="admission_type" id="admission_type" onchange="getCourse()">
                      <option value="">Select</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <!-- Center -->
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Course</label>
                    <select class="full-width" style="border: transparent;" name="course" id="course" onchange="getSubCourse()">
                      <option value="">Select</option>
                    </select>
                  </div>
                </div>

                <!-- Admission Session -->
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Sub Course</label>
                    <select class="full-width" style="border: transparent;" name="sub_course" id="sub_course" onchange="getDuration(); getEligibility();">
                      <option value="">Select</option>
                    </select>
                  </div>
                </div>

                <!-- Admission Type -->
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label id="mode">Mode</label>
                    <select class="full-width" style="border: transparent;" name="duration" id="duration">
                      <option value="">Select</option>
                    </select>
                  </div>
                </div>
              </div>

              <h5>Basic Details</h5>
              <div class="row clearfix">
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Full Name</label>
                    <?php $student_name = !empty($studentDetails) ? array_filter(array($studentDetails['First_Name'], $studentDetails['Middle_Name'], $studentDetails['Last_Name'])) : (!empty($lead_id) ? [$lead['Name']] : []) ?>
                    <input type="text" name="full_name" class="form-control" placeholder="ex: Jhon Doe" value="<?= implode(" ", $student_name) ?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Father Name</label>
                    <input type="text" name="father_name" class="form-control" value="<?php print !empty($studentDetails) ? $studentDetails['Father_Name'] : "" ?>" placeholder="">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Mother Name</label>
                    <input type="text" name="mother_name" value="<?php print !empty($studentDetails) ? $studentDetails['Mother_Name'] : "" ?>" class="form-control" placeholder="">
                  </div>
                </div>
              </div>

              <div class="row clearfix">

                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>DOB</label>
                    <input type="tel" name="dob" class="form-control" value="<?php print !empty($studentDetails) ? date('d-m-Y', strtotime($studentDetails['DOB'])) : "" ?>" placeholder="dd-mm-yyyy" id="dob">
                  </div>
                </div>

                <!-- Gender -->
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Gender</label>
                    <select class="full-width" style="border: transparent;" name="gender">
                      <option value="">Select</option>
                      <option value="Male" <?php print !empty($studentDetails) ? ($studentDetails['Gender'] == 'Male' ? 'selected' : '') : '' ?>>Male</option>
                      <option value="Female" <?php print !empty($studentDetails) ? ($studentDetails['Gender'] == 'Female' ? 'selected' : '') : '' ?>>Female</option>
                      <option value="Other" <?php print !empty($studentDetails) ? ($studentDetails['Gender'] == 'Other' ? 'selected' : '') : '' ?>>Other</option>
                    </select>
                  </div>
                </div>

                <!-- Category -->
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Category</label>
                    <select class="full-width" style="border: transparent;" name="category">
                      <option value="">Select</option>
                      <option value="General" <?php print !empty($studentDetails) ? ($studentDetails['Category'] == 'General' ? 'selected' : '') : '' ?>>General</option>
                      <option value="OBC" <?php print !empty($studentDetails) ? ($studentDetails['Category'] == 'OBC' ? 'selected' : '') : '' ?>>OBC</option>
                      <option value="SC" <?php print !empty($studentDetails) ? ($studentDetails['Category'] == 'SC' ? 'selected' : '') : '' ?>>SC</option>
                      <option value="ST" <?php print !empty($studentDetails) ? ($studentDetails['Category'] == 'ST' ? 'selected' : '') : '' ?>>ST</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row clearfix">

                <!-- Employment Status -->
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Employment Status</label>
                    <select class="full-width" style="border: transparent;" name="employment_status">
                      <option value="">Select</option>
                      <option value="Employed" <?php print !empty($studentDetails) ? ($studentDetails['Employement_Status'] == 'Employed' ? 'selected' : '') : '' ?>>Employed</option>
                      <option value="Unemployed" <?php print !empty($studentDetails) ? ($studentDetails['Employement_Status'] == 'Unemployed' ? 'selected' : '') : '' ?>>Unemployed</option>
                    </select>
                  </div>
                </div>

                <!-- Marital Status -->
                <div class="col-md-4">
                  <div class="form-group form-group-default">
                    <label>Marital Status</label>
                    <select class="full-width" style="border: transparent;" name="marital_status">
                      <option value="">Select</option>
                      <option value="Married" <?php print !empty($studentDetails) ? ($studentDetails['Marital_Status'] == 'Married' ? 'selected' : '') : '' ?>>Married</option>
                      <option value="Unmarried" <?php print !empty($studentDetails) ? ($studentDetails['Marital_Status'] == 'Unmarried' ? 'selected' : '') : '' ?>>Unmarried</option>
                    </select>
                  </div>
                </div>

                <!-- Religion -->
                <div class="col-md-4">
                  <div class="form-group form-group-default">
                    <label>Religion</label>
                    <select class="full-width" style="border: transparent;" name="religion">
                      <option value="">Select</option>
                      <option value="Hindu" <?php print !empty($studentDetails) ? ($studentDetails['Religion'] == 'Hindu' ? 'selected' : '') : '' ?>>Hindu</option>
                      <option value="Muslim" <?php print !empty($studentDetails) ? ($studentDetails['Religion'] == 'Muslim' ? 'selected' : '') : '' ?>>Muslim</option>
                      <option value="Sikh" <?php print !empty($studentDetails) ? ($studentDetails['Religion'] == 'Sikh' ? 'selected' : '') : '' ?>>Sikh</option>
                      <option value="Christian" <?php print !empty($studentDetails) ? ($studentDetails['Religion'] == 'Christian' ? 'selected' : '') : '' ?>>Christian</option>
                      <option value="Jain" <?php print !empty($studentDetails) ? ($studentDetails['Religion'] == 'Jain' ? 'selected' : '') : '' ?>>Jain</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Aadhaar Number</label>
                    <input type="tel" maxlength="14" minlength="14" name="aadhar" value="<?php print !empty($studentDetails) ? $studentDetails['Aadhar_Number'] : '' ?>" class="form-control" id="aadhar">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Nationality</label>
                    <select class="full-width" style="border: transparent;" name="nationality">
                      <option value="">Select</option>
                      <option value="Indian" <?php print !empty($studentDetails) ? ($studentDetails['Nationality'] == 'Indian' ? 'selected' : '') : '' ?>>Indian</option>
                      <option value="NRI" <?php print !empty($studentDetails) ? ($studentDetails['Nationality'] == 'NRI' ? 'selected' : '') : '' ?>>NRI</option>
                    </select>
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
    </div>
  </div>
</div>