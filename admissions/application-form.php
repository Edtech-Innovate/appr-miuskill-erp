<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<style type="text/css">
    input {
        text-transform: uppercase;
    }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <!-- START PAGE CONTENT WRAPPER -->
    <div class="page-content-wrapper ">
        <!-- START PAGE CONTENT -->
        <div class="content ">
            <!-- START JUMBOTRON -->
            <div class="jumbotron" data-pages="parallax">
                <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <!-- START BREADCRUMB -->
                        <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                            <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                            for ($i = 1; $i <= count($breadcrumbs); $i++) {
                                if (count($breadcrumbs) == $i) : $active = "active";
                                    $crumb = explode("?", $breadcrumbs[$i]);
                                    echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                                endif;
                            }
                            ?>
                        </ol>
                        <!-- END BREADCRUMB -->
                    </div>
                </div>
            </div>
            <?php
            $is_get = 0;
            $id = 0;
            $address = [];
            if (isset($_GET['id']) && $_SESSION['Role'] != 'Student') {
                $id = mysqli_real_escape_string($conn, $_GET['id']);
                $id = base64_decode($id);
                $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
                $student = $conn->query("SELECT * FROM Students WHERE ID = $id");
                if ($student->num_rows > 0) {
                    $is_get = 1;
                } else {
                    header("Location: /admissions/applications");
                }
                $student = mysqli_fetch_assoc($student);

                if (!empty($student['Unique_ID']) && $_SESSION['crm']) {
                    $check_in_leads = $conn->query("SELECT Leads.Email, Leads.Mobile FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Lead_Status.Unique_ID = '" . $student['Unique_ID'] . "'");
                    if ($check_in_leads->num_rows > 0) {
                        $lead = $check_in_leads->fetch_assoc();
                        $student['Email'] = $lead['Email'];
                        $student['Contact'] = $lead['Mobile'];
                    }
                }

                echo '<script>localStorage.setItem("inserted_id",' . $id . ');</script>';
                $address = !empty($student['Address']) ? json_decode($student['Address'], true) : [];
            }

            if (isset($_GET['lead_id']) && $_SESSION['Role'] != 'Student') {
                $lead_id = mysqli_real_escape_string($conn, $_GET['lead_id']);
                $lead_id = base64_decode($lead_id);
                $lead_id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $lead_id));
                $lead = $conn->query("SELECT Lead_Status.Admission, Lead_Status.University_ID, Lead_Status.User_ID, Lead_Status.Course_ID,Lead_Status.Sub_Course_ID,Leads.Name,Leads.Email,Leads.Alternate_Email,Leads.Mobile,Leads.Alternate_Mobile,Leads.Address,Cities.`Name` AS City,States.`Name` AS State,Countries.`Name` AS Country,Universities.Name AS University,Courses.Name AS Category,Sub_Courses.Name AS Sub_Category,Stages.Name AS Stage,Reasons.Name AS Reason,Sources.Name AS Source,Sub_Sources.Name AS Sub_Source,Users.Name AS Lead_Owner,Users.Code,Leads.Created_At AS Created_On,Lead_Status.Created_At,Lead_Status.Updated_At FROM Leads LEFT JOIN Lead_Status ON Leads.ID=Lead_Status.Lead_ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Stages ON Lead_Status.Stage_ID=Stages.ID LEFT JOIN Reasons ON Lead_Status.Reason_ID=Reasons.ID LEFT JOIN Sources ON Leads.Source_ID=Sources.ID LEFT JOIN Sub_Sources ON Leads.Sub_Source_ID=Sub_Sources.ID LEFT JOIN Users ON Lead_Status.User_ID=Users.ID LEFT JOIN Cities ON Leads.City_ID=Cities.ID LEFT JOIN States ON Leads.State_ID=States.ID LEFT JOIN Countries ON Leads.Country_ID=Countries.ID WHERE Lead_Status.ID= $lead_id");
                if ($lead->num_rows > 0) {
                    $is_get = 1;
                } else {
                    echo '<script type="text/javascript">window.location.href="/leads/lists"</script>';
                }
                $lead = $lead->fetch_assoc();
            }

            if (isset($_SESSION['lead_id']) && $_SESSION['Role'] == 'Student') {
                $lead_id = mysqli_real_escape_string($conn, $_SESSION['lead_id']);
                $lead_id = base64_decode($lead_id);
                $lead_id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $lead_id));
                $details = $conn->query("SELECT Lead_Status.ID, Leads.`Name`, Leads.Email, Leads.Alternate_Email, Leads.Mobile, Leads.Alternate_Mobile, Lead_Status.Unique_ID, Lead_Status.University_ID, Lead_Status.Course_ID, Lead_Status.Sub_Course_ID, Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.Father_Name, Students.Mother_Name, Students.Aadhar_Number, Students.Admission_Session_ID, Students.Admission_Type_ID, Students.Duration, Students.Added_For, Students.Added_For as `User_ID`, Students.DOB, Students.Gender, Students.Category, Students.Employement_Status, Students.Marital_Status, Students.Religion, Students.Nationality, Students.Address, Students.ID as Student_Table_ID, Students.Step FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID LEFT JOIN Students ON Lead_Status.Unique_ID = Students.Unique_ID WHERE Lead_Status.ID = $lead_id");
                if ($details->num_rows > 0) {
                    $is_get = 1;
                } else {
                    header("Location: /logout");
                }

                $details = $details->fetch_assoc();

                if (!empty($details['Added_For']) && !empty($details['Step']) && $details['Step'] == 1) {
                    $student = $details;
                    $student['Contact'] = $details['Mobile'];
                    $student['Alternate_Contact'] = $details['Alternate_Mobile'];
                    $id = $student['Student_Table_ID'];
                    echo '<script>localStorage.setItem("inserted_id",' . $id . ');</script>';
                    $address = !empty($student['Address']) ? json_decode($student['Address'], true) : [];
                } elseif (!empty($details['Added_For']) && !empty($details['Step']) && $details['Step'] >= 2) {
                    $id = intval($details['Student_Table_ID']);
                    $student = $conn->query("SELECT * FROM Students WHERE ID = $id");
                    if ($student->num_rows > 0) {
                        $is_get = 1;
                    } else {
                        header("Location: /admissions/applications");
                    }
                    $student = mysqli_fetch_assoc($student);

                    if (!empty($student['Unique_ID']) && $_SESSION['crm']) {
                        $check_in_leads = $conn->query("SELECT Leads.Email, Leads.Mobile FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Lead_Status.Unique_ID = '" . $student['Unique_ID'] . "'");
                        if ($check_in_leads->num_rows > 0) {
                            $lead = $check_in_leads->fetch_assoc();
                            $student['Email'] = $lead['Email'];
                            $student['Contact'] = $lead['Mobile'];
                        }
                    }

                    echo '<script>localStorage.setItem("inserted_id",' . $id . ');</script>';
                    $address = !empty($student['Address']) ? json_decode($student['Address'], true) : [];
                } else {
                    $lead = $details;
                }
            }
            ?>
            <!-- END JUMBOTRON -->
            <!-- START CONTAINER FLUID -->
            <div class=" container-fluid  ">
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
                    <!-- Tab panes -->
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
                                        <form id="step_1" role="form" autocomplete="off" action="/app/application-form/step-1" enctype="multipart/form-data">
                                            <h5>Applying For</h5>
                                            <div class="row clearfix">
                                                <!-- Center -->
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Center</label>
                                                        <select class="full-width" style="border: transparent;" data-init-plugin="select2" name="center" id="center" onchange="getCourse()">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Admission Session -->
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Session</label>
                                                        <select class="full-width" style="border: transparent;" name="admission_session" data-init-plugin="select2" id="admission_session" onchange="getAdmissionType(this.value)">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Admission Type -->
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Admission Type</label>
                                                        <select class="full-width" style="border: transparent;" name="admission_type" data-init-plugin="select2" id="admission_type" onchange="getCourse();setMarksheet(this.value)">
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
                                                        <select class="full-width" style="border: transparent;" data-init-plugin="select2" name="course" id="course" onchange="getSubCourse()">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Admission Session -->
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Sub Course</label>
                                                        <select class="full-width" style="border: transparent;" data-init-plugin="select2" name="sub_course" id="sub_course" onchange="getDuration(); getEligibility();">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Admission Type -->
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label id="mode">Mode</label>
                                                        <select class="full-width" style="border: transparent;" data-init-plugin="select2" name="duration" id="duration">
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
                                                        <?php $student_name = !empty($id) ? array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) : (!empty($lead_id) ? [$lead['Name']] : []) ?>
                                                        <input type="text" name="full_name" class="form-control" placeholder="ex: Jhon Doe" value="<?= implode(" ", $student_name) ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Father Name</label>
                                                        <input type="text" name="father_name" class="form-control" value="<?php print !empty($id) ? $student['Father_Name'] : "" ?>" placeholder="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Mother Name</label>
                                                        <input type="text" name="mother_name" value="<?php print !empty($id) ? $student['Mother_Name'] : "" ?>" class="form-control" placeholder="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row clearfix">

                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>DOB</label>
                                                        <input type="tel" name="dob" class="form-control" value="<?php print !empty($id) ? date('d-m-Y', strtotime($student['DOB'])) : "" ?>" placeholder="dd-mm-yyyy" id="dob">
                                                    </div>
                                                </div>

                                                <!-- Gender -->
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Gender</label>
                                                        <select class="full-width" style="border: transparent;" name="gender">
                                                            <option value="">Select</option>
                                                            <option value="Male" <?php print !empty($id) ? ($student['Gender'] == 'Male' ? 'selected' : '') : '' ?>>Male</option>
                                                            <option value="Female" <?php print !empty($id) ? ($student['Gender'] == 'Female' ? 'selected' : '') : '' ?>>Female</option>
                                                            <option value="Other" <?php print !empty($id) ? ($student['Gender'] == 'Other' ? 'selected' : '') : '' ?>>Other</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Category -->
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Category</label>
                                                        <select class="full-width" style="border: transparent;" name="category">
                                                            <option value="">Select</option>
                                                            <option value="General" <?php print !empty($id) ? ($student['Category'] == 'General' ? 'selected' : '') : '' ?>>General</option>
                                                            <option value="OBC" <?php print !empty($id) ? ($student['Category'] == 'OBC' ? 'selected' : '') : '' ?>>OBC</option>
                                                            <option value="SC" <?php print !empty($id) ? ($student['Category'] == 'SC' ? 'selected' : '') : '' ?>>SC</option>
                                                            <option value="ST" <?php print !empty($id) ? ($student['Category'] == 'ST' ? 'selected' : '') : '' ?>>ST</option>
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
                                                            <option value="Employed" <?php print !empty($id) ? ($student['Employement_Status'] == 'Employed' ? 'selected' : '') : '' ?>>Employed</option>
                                                            <option value="Unemployed" <?php print !empty($id) ? ($student['Employement_Status'] == 'Unemployed' ? 'selected' : '') : '' ?>>Unemployed</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Marital Status -->
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default">
                                                        <label>Marital Status</label>
                                                        <select class="full-width" style="border: transparent;" name="marital_status">
                                                            <option value="">Select</option>
                                                            <option value="Married" <?php print !empty($id) ? ($student['Marital_Status'] == 'Married' ? 'selected' : '') : '' ?>>Married</option>
                                                            <option value="Unmarried" <?php print !empty($id) ? ($student['Marital_Status'] == 'Unmarried' ? 'selected' : '') : '' ?>>Unmarried</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Religion -->
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default">
                                                        <label>Religion</label>
                                                        <select class="full-width" style="border: transparent;" name="religion">
                                                            <option value="">Select</option>
                                                            <option value="Hindu" <?php print !empty($id) ? ($student['Religion'] == 'Hindu' ? 'selected' : '') : '' ?>>Hindu</option>
                                                            <option value="Muslim" <?php print !empty($id) ? ($student['Religion'] == 'Muslim' ? 'selected' : '') : '' ?>>Muslim</option>
                                                            <option value="Sikh" <?php print !empty($id) ? ($student['Religion'] == 'Sikh' ? 'selected' : '') : '' ?>>Sikh</option>
                                                            <option value="Christian" <?php print !empty($id) ? ($student['Religion'] == 'Christian' ? 'selected' : '') : '' ?>>Christian</option>
                                                            <option value="Jain" <?php print !empty($id) ? ($student['Religion'] == 'Jain' ? 'selected' : '') : '' ?>>Jain</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row clearfix">

                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Aadhaar Number/ Passport Number</label>
                                                        <input type="text" name="aadhar" value="<?php print !empty($id) ? $student['Aadhar_Number'] : '' ?>" class="form-control" id="aadhar" required>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Nationality</label>
                                                        <select class="full-width" style="border: transparent;" name="nationality">
                                                            <option value="">Select</option>
                                                            <option value="Indian" <?php print !empty($id) ? ($student['Nationality'] == 'Indian' ? 'selected' : '') : '' ?>>Indian</option>
                                                            <option value="NRI" <?php print !empty($id) ? ($student['Nationality'] == 'NRI' ? 'selected' : '') : '' ?>>NRI</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane slide-left padding-20 sm-no-padding" id="tab2">
                            <form id="step_2" role="form" autocomplete="off" action="/app/application-form/step-2">
                                <div class="row row-same-height">
                                    <div class="col-md-6 b-r b-dashed b-grey sm-b-b">
                                        <div class="padding-10 sm-padding-5">
                                            <h5>Social</h5>
                                            <div class="row clearfix">
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label>Email</label>
                                                        <input type="email" name="email" <?php echo $_SESSION['crm'] ? 'disabled' : '' ?> class="form-control" value="<?php print !empty($id) ? $student['Email'] : (!empty($lead_id) ? $lead['Email'] : '') ?>" placeholder="ex: jhon@example.com">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default">
                                                        <label>Alternate Email</label>
                                                        <input type="email" name="alternate_email" value="<?php print !empty($id) ? $student['Alternate_Email'] : (!empty($lead_id) ? $lead['Alternate_Email'] : '') ?>" class="form-control" placeholder="ex: jhondoe@example.com">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row clearfix">
                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default required">
                                                        <label>Mobile</label>
                                                        <input type="tel" name="contact" <?php echo $_SESSION['crm'] ? 'disabled' : '' ?> onkeypress="return isNumberKey(event);" value="<?php print !empty($id) && !empty($student['Contact']) ? $student['Contact'] : (!empty($lead_id) ? $lead['Mobile'] : '') ?>" class="form-control" placeholder="ex: 9977886655">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group form-group-default">
                                                        <label>Alternate Mobile</label>
                                                        <input type="tel" name="alternate_contact" class="form-control" value="<?php print !empty($id) ? $student['Alternate_Contact'] : (!empty($lead_id) ? $lead['Alternate_Mobile'] : '') ?>" placeholder="ex: 9988776654">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="padding-10 sm-padding-5">
                                            <h5>Address</h5>
                                            <div class="row clearfix">
                                                <div class="col-md-8">
                                                    <div class="form-group form-group-default required">
                                                        <label>Address</label>
                                                        <input type="text" name="address" class="form-control" value="<?php print !empty($id) ? (!empty($address) ? $address['present_address'] : '') : '' ?>" placeholder="ex: 23 Street, California">
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>Zipcode</label>
                                                        <input type="tel" name="pincode" maxlength="6" class="form-control" placeholder="ex: 123456" value="<?php print !empty($address) ? (array_key_exists('present_pincode', $address) ? $address['present_pincode'] : '') : '' ?>" onkeypress="return isNumberKey(event)">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>City</label>
                                                        <input type="text" class="full-width" style="border: transparent;" name="city" id="city" placeholder="City" value="<?php print !empty($address) ? (array_key_exists('present_city', $address) ? $address['present_city'] : '') : '' ?>">

                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>District</label>
                                                        <input type="text" class="full-width" style="border: transparent;" name="district" id="district" placeholder="District" value="<?php print !empty($address) ? (array_key_exists('present_district', $address) ? $address['present_district'] : '') : '' ?>">

                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-group-default required">
                                                        <label>State</label>
                                                        <input type="text" name="state" class="form-control" placeholder="ex: California" id="state" value="<?php print !empty($address) ? (array_key_exists('present_state', $address) ? $address['present_state'] : '') : '' ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane slide-left padding-20 sm-no-padding" id="tab3">
                            <form id="step_3" role="form" autocomplete="off" action="/app/application-form/step-3" method="POST" enctype="multipart/form-data">
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
                                                        <input type="text" name="high_obtained" id="high_obtained" value="<?php print !empty($high_school) ? $high_school['Marks_Obtained'] : '' ?>" class="form-control" onblur="checkHighMarks();" placeholder="ex: 400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default">
                                                        <label>Max Marks</label>
                                                        <input type="text" name="high_max" id="high_max" value="<?php print !empty($high_school) ? $high_school['Max_Marks'] : '' ?>" class="form-control" onblur="checkHighMarks();" placeholder="ex: 600">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default">
                                                        <label>Grade/Percentage</label>
                                                        <input type="text" name="high_total" id="high_total" value="<?php print !empty($high_school) ? $high_school['Total_Marks'] : '' ?>" class="form-control" placeholder="ex: 66%">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default high_school required">
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
                                                        <input type="text" name="inter_obtained" id="inter_obtained" class="form-control" onblur="checkInterMarks();" value="<?php print !empty($intermediate) ? (array_key_exists('Marks_Obtained', $intermediate) ? $intermediate['Marks_Obtained'] : '') : '' ?>" placeholder="ex: 400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default">
                                                        <label>Max Marks</label>
                                                        <input type="text" name="inter_max" id="inter_max" class="form-control" value="<?php print !empty($intermediate) ? (array_key_exists('Max_Marks', $intermediate) ? $intermediate['Max_Marks'] : '') : '' ?>" onblur="checkInterMarks();" placeholder="ex: 600">
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
                                                    <div class="form-group form-group-default intermediate required">
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
                                                        <input type="text" name="ug_obtained" id="ug_obtained" class="form-control" value="<?php print !empty($ug) ? (array_key_exists('Marks_Obtained', $ug) ? $ug['Marks_Obtained'] : '') : '' ?>" onblur="checkUGMarks()" placeholder="ex: 400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default">
                                                        <label>Max Marks</label>
                                                        <input type="text" name="ug_max" id="ug_max" class="form-control" value="<?php print !empty($ug) ? (array_key_exists('Max_Marks', $ug) ? $ug['Max_Marks'] : '') : '' ?>" onblur="checkUGMarks()" placeholder="ex: 600">
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
                                                        <input type="text" name="pg_obtained" id="pg_obtained" value="<?php print !empty($pg) ? (array_key_exists('Marks_Obtained', $pg) ? $pg['Marks_Obtained'] : '') : '' ?>" class="form-control" placeholder="ex: 400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default">
                                                        <label>Max Marks</label>
                                                        <input type="text" name="pg_max" id="pg_max" value="<?php print !empty($pg) ? (array_key_exists('Max_Marks', $pg) ? $pg['Max_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 600">
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
                                                        <input type="text" name="other_obtained" id="other_obtained" value="<?php print !empty($other) ? (array_key_exists('Marks_Obtained', $other) ? $other['Marks_Obtained'] : '') : '' ?>" class="form-control" placeholder="ex: 400">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row clearfix">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default">
                                                        <label>Max Marks</label>
                                                        <input type="text" name="other_max" id="other_max" value="<?php print !empty($other) ? (array_key_exists('Max_Marks', $other) ? $other['Max_Marks'] : '') : '' ?>" class="form-control" placeholder="ex: 600">
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
                            </form>
                        </div>
                        <div class="tab-pane slide-left padding-20 sm-no-padding" id="tab4">
                            <form id="step_4" role="form" action="/app/application-form/step-4" enctype="multipart/form-data" autocomplete="off">
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
                                        $aadhaars_front = array();
                                        $aadhaar_front = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Aadhar_Front'");
                                        if ($aadhaar_front->num_rows > 0) {
                                            $aadhaar_front = mysqli_fetch_array($aadhaar_front);
                                            $aadhaars_front = explode("|", $aadhaar_front['Location']);
                                        }
                                    }
                                    ?>
                                    <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
                                        <div class="padding-10 sm-padding-5">
                                            <h6>Aadhar/Passport Front</h6>
                                            <div class="row clearfix">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default required">
                                                        <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('aadhar_front');" id="aadhar_front" name="aadhar_front[]" multiple="multiple" class="form-control mt-1">
                                                        <?php if (!empty($id) && !empty($aadhaars_front)) {
                                                            foreach ($aadhaars_front as $aadhar_front) { ?>
                                                                <img src="<?php print !empty($id) ? $aadhar_front : '' ?>" height="80" />
                                                        <?php }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if (!empty($id)) {
                                        $aadhaars_back = array();
                                        $aadhaar_back = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Aadhar_Front'");
                                        if ($aadhaar_back->num_rows > 0) {
                                            $aadhaar_back = mysqli_fetch_array($aadhaar_back);
                                            $aadhaars_back = explode("|", $aadhaar_back['Location']);
                                        }
                                    }
                                    ?>
                                    <div class="col-md-3 b-r b-dashed b-grey sm-b-b">
                                        <div class="padding-10 sm-padding-5">
                                            <h6>Aadhar/Passport Back</h6>
                                            <div class="row clearfix">
                                                <div class="col-md-12">
                                                    <div class="form-group form-group-default required">
                                                        <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('aadhar_back');" id="aadhar_back" name="aadhar_back[]" multiple="multiple" class="form-control mt-1">
                                                        <?php if (!empty($id) && !empty($aadhaars_back)) {
                                                            foreach ($aadhaars_back as $aadhar_back) { ?>
                                                                <img src="<?php print !empty($id) ? $aadhar_back : '' ?>" height="80" />
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
                                            <!--<h6>Parent's Signature</h6>-->
                                            <h6>Job Offer Letter </h6>
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
                                    <?php
                                    if ($_SESSION['university_id'] == 20) {
                                        // print_r($_SESSION['university_id']);die;
                                        if (!empty($id)) {
                                            $marksheets_1 = array();
                                            $marksheet_1 = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'marksheet_semester_1'");
                                            if ($marksheet_1->num_rows > 0) {
                                                $marksheet_1 = mysqli_fetch_array($marksheet_1);
                                                $marksheets_1 = explode("|", $marksheet_1['Location']);
                                            }
                                        }

                                    ?>
                                        <div class="col-md-3 b-r b-dashed b-grey sm-b-b semester_1">
                                            <div class="padding-10 sm-padding-5">
                                                <h6>Marksheet (Semester 1)</h6>
                                                <div class="row clearfix">
                                                    <div class="col-md-12">
                                                        <div class="form-group form-group-default required">
                                                            <label></label>
                                                            <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('marksheet_1');" id="marksheet_1" name="marksheet_1[]" multiple="multiple" class="form-control mt-1">
                                                            <?php if (!empty($id) && !empty($marksheets_1)) {
                                                                foreach ($marksheets_1 as $marksheet_1) { ?>
                                                                    <img src="<?php print !empty($id) ? $marksheet_1 : '' ?>" height="80" />
                                                            <?php }
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($id)) {
                                            $marksheets_2 = array();
                                            $marksheet_2 = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'marksheet_semester_2'");
                                            if ($marksheet_2->num_rows > 0) {
                                                $marksheet_2 = mysqli_fetch_array($marksheet_2);
                                                $marksheets_2 = explode("|", $marksheet_2['Location']);
                                            }
                                        }

                                        ?>
                                        <div class="col-md-3 b-r b-dashed b-grey sm-b-b semester_2">
                                            <div class="padding-10 sm-padding-5">
                                                <h6>Marksheet (Semester 2)</h6>
                                                <div class="row clearfix">
                                                    <div class="col-md-12">
                                                        <div class="form-group form-group-default required">
                                                            <label></label>
                                                            <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('marksheet_2');" id="marksheet_2" name="marksheet_2[]" multiple="multiple" class="form-control mt-1">
                                                            <?php if (!empty($id) && !empty($marksheets_2)) {
                                                                foreach ($marksheets_2 as $marksheet_2) { ?>
                                                                    <img src="<?php print !empty($id) ? $marksheet_2 : '' ?>" height="80" />
                                                            <?php }
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($id)) {
                                            $marksheets_3 = array();
                                            $marksheet_3 = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'marksheet_semester_3'");
                                            if ($marksheet_3->num_rows > 0) {
                                                $marksheet_3 = mysqli_fetch_array($marksheet_3);
                                                $marksheets_3 = explode("|", $marksheet_3['Location']);
                                            }
                                        }

                                        ?>
                                        <div class="col-md-3 b-r b-dashed b-grey sm-b-b semester_3">
                                            <div class="padding-10 sm-padding-5">
                                                <h6>Marksheet (Semester 3)</h6>
                                                <div class="row clearfix">
                                                    <div class="col-md-12">
                                                        <div class="form-group form-group-default required">
                                                            <label></label>
                                                            <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('marksheet_3');" id="marksheet_3" name="marksheet_3[]" multiple="multiple" class="form-control mt-1">
                                                            <?php if (!empty($id) && !empty($marksheets_3)) {
                                                                foreach ($marksheets_3 as $marksheet_3) { ?>
                                                                    <img src="<?php print !empty($id) ? $marksheet_3 : '' ?>" height="80" />
                                                            <?php }
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if (!empty($id)) {
                                            $marksheets_4 = array();
                                            $marksheet_4 = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'marksheet_semester_4'");
                                            if ($marksheet_4->num_rows > 0) {
                                                $marksheet_4 = mysqli_fetch_array($marksheet_4);
                                                $marksheets_4 = explode("|", $marksheet_4['Location']);
                                            }
                                        }

                                        ?>
                                        <div class="col-md-3 b-r b-dashed b-grey sm-b-b semester_4">
                                            <div class="padding-10 sm-padding-5">
                                                <h6>Marksheet (Semester 4)</h6>
                                                <div class="row clearfix">
                                                    <div class="col-md-12">
                                                        <div class="form-group form-group-default required">
                                                            <label></label>
                                                            <input type="file" accept="image/png, image/jpeg, image/jpg" onchange="fileValidation('marksheet_4');" id="marksheet_4" name="marksheet_4[]" multiple="multiple" class="form-control mt-1">
                                                            <?php if (!empty($id) && !empty($marksheets_4)) {
                                                                foreach ($marksheets_4 as $marksheet_4) { ?>
                                                                    <img src="<?php print !empty($id) ? $marksheet_4 : '' ?>" height="80" />
                                                            <?php }
                                                            } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php

                                    }
                                    ?>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane slide-left padding-20 sm-no-padding" id="tab5">
                            <h1>Thank you for providing the requested information.<h1>
                                    <h3>Please use the link below to print the pre-filled application form.</h3>
                                    <!--<button class="btn btn-primary btn-lg m-b-10" onclick="printForm()"><i class="uil uil-print"></i> &nbsp;Print </button>-->
                        </div>
                        <div class="padding-20 sm-padding-5 sm-m-b-20 sm-m-t-20 bg-white clearfix">
                            <ul class="pager wizard no-style">
                                <li class="next">
                                    <button aria-label="" class="btn btn-primary btn-cons btn-animated from-left pull-right" type="button">
                                        <span>Next</span>
                                        <span class="hidden-block">
                                            <i class="uil uil-angle-right"></i>
                                        </span>
                                    </button>
                                </li>
                                <li class="next finish hidden">
                                    <button aria-label="" class="btn btn-primary btn-cons btn-animated from-left pull-right" type="button">
                                        <span>Submitted</span>
                                        <span class="hidden-block">
                                            <i class="uil uil-check"></i>
                                        </span>
                                    </button>
                                </li>
                                <li class="previous first hidden">
                                    <button aria-label="" class="btn btn-default btn-cons btn-animated from-left pull-right" type="button">
                                        <span>First</span>
                                        <span class="hidden-block">
                                            <i class="uil uil-angle-left"></i>
                                        </span>
                                    </button>
                                </li>
                                <li class="previous" id="previous-button">
                                    <button aria-label="" class="btn btn-default btn-cons btn-animated from-left pull-right" type="button">
                                        <span>Previous</span>
                                        <span class="hidden-block">
                                            <i class="uil uil-angle-left"></i>
                                        </span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

        <?php if (isset($_GET['lead_id']) && $lead['Admission'] == 1 && $_SESSION['Role'] != 'Student') { ?>
            <script>
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Application saved successfully! To Proceed again go to Applications.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    timer: 5000
                }).then((result) => {
                    window.location.href = "/leads/lists"
                })
            </script>
        <?php } ?>

        <?php if ($_SESSION['crm'] > 0 && !$is_get && $_SESSION['Role'] != 'Student') { ?>
            <script>
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'To apply New Application, Please add lead first!',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    timer: 3000
                }).then((result) => {
                    window.location.href = "/leads/generate"
                })
            </script>
        <?php } ?>

        <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
        <script src="/assets/plugins/bootstrap-form-wizard/js/jquery.bootstrap.wizard.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>

        <?php if (empty($id)) { ?>
            <script>
                $(function() {
                    if (localStorage.getItem('inserted_id') !== null) {
                        localStorage.removeItem('inserted_id');
                        Swal.fire(
                            'Previous Application is saved!',
                            'Please go to Applications > Edit if you want to proceed further!',
                            'success'
                        );
                    }
                });
            </script>
        <?php } ?>

        <script>
            $(function() {
                localStorage.removeItem('print_id');
                $("#dob").mask("99-99-9999")
                $('#dob').datepicker({
                    format: 'dd-mm-yyyy',
                    autoclose: true,
                    endDate: '-15y'
                });
            });
        </script>

        <script>
            function checkHighMarks() {
                var obtained = parseInt($('#high_obtained').val());
                var max = parseInt($("#high_max").val());
                var alerted = localStorage.getItem('alertedHigh') || '';
                if (obtained > max) {
                    if (alerted != 'yes') {
                        alert("Obtained marks can not be higher than Maximum marks");
                        $(':input[type="submit"]').prop('disabled', true);
                        localStorage.setItem('alertedHigh', 'yes');
                    }
                } else {
                    localStorage.setItem('alertedHigh', 'no');
                    $(':input[type="submit"]').prop('disabled', false);
                    if ($('#high_obtained').val().length > 0) {
                        var percentage = (obtained / max) * 100;
                        $('#high_total').val(percentage.toFixed(2));
                        $("#high_total").prop("readonly", true);
                    } else if ($('#high_obtained').val().length == 0) {
                        $("#high_total").prop("readonly", false);
                        $('#high_total').val('');
                    }
                }
            }

            function checkInterMarks() {
                var obtained = parseInt($('#inter_obtained').val());
                var max = parseInt($("#inter_max").val());
                var alerted = localStorage.getItem('alertedInter') || '';
                if (obtained > max) {
                    if (alerted != 'yes') {
                        alert("Obtained marks can not be higher than Maximum marks");
                        $(':input[type="submit"]').prop('disabled', true);
                        localStorage.setItem('alertedInter', 'yes');
                    }
                } else {
                    localStorage.setItem('alertedInter', 'no');
                    $(':input[type="submit"]').prop('disabled', false);
                    if ($('#inter_obtained').val().length > 0) {
                        var percentage = (obtained / max) * 100;
                        $('#inter_total').val(percentage.toFixed(2));
                        $("#inter_total").prop("readonly", true);
                    } else if ($('#inter_obtained').val().length == 0) {
                        $("#inter_total").prop("readonly", false);
                        $('#inter_total').val('');
                    }
                }
            }

            function checkUGMarks() {
                var obtained = parseInt($('#ug_obtained').val());
                var max = parseInt($("#ug_max").val());
                var alerted = localStorage.getItem('alertedUG') || '';
                if (obtained > max) {
                    if (alerted != 'yes') {
                        alert("Obtained marks can not be higher than Maximum marks");
                        $(':input[type="submit"]').prop('disabled', true);
                        localStorage.setItem('alertedUG', 'yes');
                    }
                } else {
                    localStorage.setItem('alertedUG', 'no');
                    $(':input[type="submit"]').prop('disabled', false);
                    if ($('#ug_obtained').val().length > 0) {
                        var percentage = (obtained / max) * 100;
                        $('#ug_total').val(percentage.toFixed(2));
                        $("#ug_total").prop("readonly", true);
                    } else if ($('#ug_obtained').val().length == 0) {
                        $("#ug_total").prop("readonly", false);
                        $('#ug_total').val('');
                    }
                }
            }

            function checkPGMarks() {
                var obtained = parseInt($('#pg_obtained').val());
                var max = parseInt($("#pg_max").val());
                var alerted = localStorage.getItem('alertedPG') || '';
                if (obtained > max) {
                    if (alerted != 'yes') {
                        alert("Obtained marks can not be higher than Maximum marks");
                        $(':input[type="submit"]').prop('disabled', true);
                        localStorage.setItem('alertedPG', 'yes');
                    }
                } else {
                    localStorage.setItem('alertedPG', 'no');
                    $(':input[type="submit"]').prop('disabled', false);
                    if ($('#pg_obtained').val().length > 0) {
                        var percentage = (obtained / max) * 100;
                        $('#pg_total').val(percentage.toFixed(2));
                        $("#pg_total").prop("readonly", true);
                    } else if ($('#pg_obtained').val().length == 0) {
                        $("#pg_total").prop("readonly", false);
                        $('#pg_total').val('');
                    }
                }
            }

            function checkOtherMarks() {
                var obtained = parseInt($('#other_obtained').val());
                var max = parseInt($("#other_max").val());
                var alerted = localStorage.getItem('alertedOther') || '';
                if (obtained > max) {
                    if (alerted != 'yes') {
                        alert("Obtained marks can not be higher than Maximum marks");
                        $(':input[type="submit"]').prop('disabled', true);
                        localStorage.setItem('alertedOther', 'yes');
                    }
                } else {
                    localStorage.setItem('alertedOther', 'no');
                    $(':input[type="submit"]').prop('disabled', false);
                    if ($('#other_obtained').val().length > 0) {
                        var percentage = (obtained / max) * 100;
                        $('#other_total').val(percentage.toFixed(2));
                        $("#other_total").prop("readonly", true);
                    } else if ($('#other_obtained').val().length == 0) {
                        $("#other_total").prop("readonly", false);
                        $('#other_total').val('');
                    }
                }
            }

            function getRegion(pincode) {
                if (pincode.length == 6) {
                    $.ajax({
                        url: '/app/regions/cities?pincode=' + pincode,
                        type: 'GET',
                        success: function(data) {
                            //   $('#city').html(data);
                            <?php if (!empty($id) && !empty($address)) { ?>
                                // $('#city').val('<?php echo !empty($id) && !empty($address) ? (array_key_exists('present_city', $address) ? $address['present_city'] : '') : '' ?>');
                            <?php } ?>
                        }
                    });

                    $.ajax({
                        url: '/app/regions/districts?pincode=' + pincode,
                        type: 'GET',
                        success: function(data) {
                            //   $('#district').html(data);
                            <?php if (!empty($id) && !empty($address)) { ?>
                                // $('#district').val('<?php echo !empty($id) && !empty($address) ? (array_key_exists('present_district', $address) ? $address['present_district'] : '') : '' ?>');
                            <?php } ?>
                        }
                    });

                    $.ajax({
                        url: '/app/regions/state?pincode=' + pincode,
                        type: 'GET',
                        success: function(data) {
                            //   $('#state').val(data);
                        }
                    })
                }
            }

            <?php if (!empty($id)) { ?>
                // getRegion('<?php echo !empty($id) && !empty($address) ? (array_key_exists('present_pincode', $address) ? $address['present_pincode'] : '') : '' ?>');
            <?php } ?>
        </script>

        <!-- Application Form Functions -->
        <script type="text/javascript">
            function getCenter(university_id) {
                $.ajax({
                    url: '/app/application-form/center?university_id=' + university_id,
                    type: 'GET',
                    success: function(data) {
                        $('#center').html(data);
                        $('#center').val(<?php echo !empty($id) ? $student['Added_For'] : (isset($_GET['lead_id']) ? $lead['User_ID'] : (isset($_SESSION['lead_id']) ? $lead['Added_For'] : '')) ?>);
                    }
                })
            }

            function getAdmissionSession(university_id) {
                $.ajax({
                    url: '/app/application-form/admission-session?university_id=' + university_id + '&form=<?php print !empty($id) ? 1 : "" ?>',
                    type: 'GET',
                    success: function(data) {
                        $('#admission_session').html(data);
                        $('#admission_session').val(<?php print !empty($id) ? $student['Admission_Session_ID'] : '' ?>);
                        getAdmissionType($('#admission_session').val());

                    }
                })
            }

            function getAdmissionType(session_id) {
                const university_id = '<?= $_SESSION['university_id'] ?>';
                $.ajax({
                    url: '/app/application-form/admission-type?university_id=' + university_id + '&session_id=' + session_id,
                    type: 'GET',
                    success: function(data) {
                        $('#admission_type').html(data);
                        $('#admission_type').val(<?php print !empty($id) ? $student['Admission_Type_ID'] : '' ?>);
                        getCourse();
                        setMarksheet(<?= $student['Admission_Type_ID']; ?>)
                    }
                })
            }

            function getCourse() {
                var center = $('#center').val();
                const university_id = '<?= $_SESSION['university_id'] ?>';
                const session_id = $('#admission_session').val();
                const admission_type_id = $('#admission_type').val();
                $.ajax({
                    url: '/app/application-form/course?center=' + center + '&session_id=' + session_id + '&admission_type_id=' + admission_type_id + '&university_id=' + university_id + '&form=<?php print !empty($id) || !empty($lead_id) ? 1 : "" ?>',
                    type: 'GET',
                    success: function(data) {
                        $('#course').html(data);
                        $('#course').val(<?php print !empty($id) ? $student['Course_ID'] : (isset($_GET['lead_id']) || isset($_SESSION['lead_id']) ? $lead['Course_ID'] : '') ?>);
                        getSubCourse();
                    }
                })
            }

            function highDetailsRequired() {
                // $('.high_school').addClass('required');
                $('#high_subject').validate();
                $('#high_subject').rules('add', {
                    //   required: true
                });
                $('#high_year').validate();
                $('#high_year').rules('add', {
                    //   required: true
                });
                $('#high_board').validate();
                $('#high_board').rules('add', {
                    //   required: true
                });
                $('#high_total').validate();
                $('#high_total').rules('add', {
                    //   required: true
                });
                <?php if (empty($id)) { ?>
                    $('#high_marksheet').validate();
                    $('#high_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>

                <?php if (!empty($id) && empty($high_marksheet)) { ?>
                    $('#high_marksheet').validate();
                    $('#high_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>
            }

            function highDetailsNotRequired() {
                $('.high_school').removeClass('required');
                $('#high_subject').rules('remove', 'required');
                $('#high_year').rules('remove', 'required');
                $('#high_board').rules('remove', 'required');
                $('#high_total').rules('remove', 'required');
                $('#high_marksheet').rules('remove', 'required');
            }

            function interDetailsRequired() {
                // $('.intermediate').addClass('required');
                $('#inter_subject').validate();
                $('#inter_subject').rules('add', {
                    //   required: true
                });
                $('#inter_year').validate();
                $('#inter_year').rules('add', {
                    //   required: true
                });
                $('#inter_board').validate();
                $('#inter_board').rules('add', {
                    //   required: true
                });
                $('#inter_total').validate();
                $('#inter_total').rules('add', {
                    //   required: true
                });
                <?php if (empty($id)) { ?>
                    $('#inter_marksheet').validate();
                    $('#inter_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>

                <?php if (!empty($id) && empty($inter_marksheet)) { ?>
                    $('#inter_marksheet').validate();
                    $('#inter_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>
            }

            function interDetailsNotRequired() {
                $('.intermediate').removeClass('required');
                $('#inter_subject').rules('remove', 'required');
                $('#inter_year').rules('remove', 'required');
                $('#inter_board').rules('remove', 'required');
                $('#inter_total').rules('remove', 'required');
                $('#inter_marksheet').rules('remove', 'required');
            }

            function ugDetailsRequired() {
                $('.ug-program').addClass('required');
                $('#ug_subject').validate();
                $('#ug_subject').rules('add', {
                    required: true
                });
                $('#ug_year').validate();
                $('#ug_year').rules('add', {
                    required: true
                });
                $('#ug_board').validate();
                $('#ug_board').rules('add', {
                    required: true
                });
                $('#ug_total').validate();
                $('#ug_total').rules('add', {
                    required: true
                });
                <?php if (empty($id)) { ?>
                    $('#ug_marksheet').validate();
                    $('#ug_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>

                <?php if (!empty($id) && empty($ug_marksheet)) { ?>
                    $('#ug_marksheet').validate();
                    $('#ug_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>
            }

            function ugDetailsNotRequired() {
                $('.ug-program').removeClass('required');
                $('#ug_subject').rules('remove', 'required');
                $('#ug_year').rules('remove', 'required');
                $('#ug_board').rules('remove', 'required');
                $('#ug_total').rules('remove', 'required');
                $('#ug_marksheet').rules('remove', 'required');
            }

            function pgDetailsRequired() {
                $('.pg-program').addClass('required');
                $('#pg_subject').validate();
                $('#pg_subject').rules('add', {
                    required: true
                });
                $('#pg_year').validate();
                $('#pg_year').rules('add', {
                    required: true
                });
                $('#pg_board').validate();
                $('#pg_board').rules('add', {
                    required: true
                });
                $('#pg_total').validate();
                $('#pg_total').rules('add', {
                    required: true
                });
                <?php if (empty($id)) { ?>
                    $('#pg_marksheet').validate();
                    $('#pg_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>

                <?php if (!empty($id) && empty($pg_marksheet)) { ?>
                    $('#pg_marksheet').validate();
                    $('#pg_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>
            }

            function pgDetailsNotRequired() {
                $('.pg-program').removeClass('required');
                $('#pg_subject').rules('remove', 'required');
                $('#pg_year').rules('remove', 'required');
                $('#pg_board').rules('remove', 'required');
                $('#pg_total').rules('remove', 'required');
                $('#pg_marksheet').rules('remove', 'required');
            }

            function otherDetailsRequired() {
                $('.other-program').addClass('required');
                $('#other_subject').validate();
                $('#other_subject').rules('add', {
                    required: true
                });
                $('#other_year').validate();
                $('#other_year').rules('add', {
                    required: true
                });
                $('#other_board').validate();
                $('#other_board').rules('add', {
                    required: true
                });
                $('#other_total').validate();
                $('#other_total').rules('add', {
                    required: true
                });
                <?php if (empty($id)) { ?>
                    $('#other_marksheet').validate();
                    $('#other_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>

                <?php if (!empty($id) && empty($other_marksheet)) { ?>
                    $('#other_marksheet').validate();
                    $('#other_marksheet').rules('add', {
                        required: true
                    });
                <?php } ?>
            }

            function otherDetailsNotRequired() {
                $('.other-program').removeClass('required');
                $('#other_subject').rules('remove', 'required');
                $('#other_year').rules('remove', 'required');
                $('#other_board').rules('remove', 'required');
                $('#other_total').rules('remove', 'required');
                $('#other_marksheet').rules('remove', 'required');
            }

            function getSubCourse() {
                var center = $('#center').val();
                const university_id = '<?= $_SESSION['university_id'] ?>';
                const session_id = $('#admission_session').val();
                const admission_type_id = $('#admission_type').val();
                const course_id = $('#course').val();
                $.ajax({
                    url: '/app/application-form/sub-course?center=' + center + '&session_id=' + session_id + '&admission_type_id=' + admission_type_id + '&university_id=' + university_id + '&course_id=' + course_id,
                    type: 'GET',
                    success: function(data) {
                        $('#sub_course').html(data);
                        $('#sub_course').val(<?php print !empty($id) ? $student['Sub_Course_ID'] : (isset($_GET['lead_id']) || isset($_SESSION['lead_id']) ? $lead['Sub_Course_ID'] : '') ?>);
                        getMode();
                    }
                })
            }

            function getMode() {
                const sub_course_id = $('#sub_course').val();
                $.ajax({
                    url: '/app/application-form/mode?sub_course_id=' + sub_course_id,
                    type: 'GET',
                    success: function(data) {
                        $('#mode').html(data);
                        getDuration();
                        getEligibility();
                    }
                })
            }

            function getDuration() {
                const admission_type_id = $('#admission_type').val();
                const sub_course_id = $('#sub_course').val();
                $.ajax({
                    url: '/app/application-form/duration?admission_type_id=' + admission_type_id + '&sub_course_id=' + sub_course_id,
                    type: 'GET',
                    success: function(data) {
                        $('#duration').html(data);
                        $('#duration').val(<?php print !empty($id) ? $student['Duration'] : '' ?>)
                    }
                })
            }

            function getEligibility() {
                const sub_course_id = $('#sub_course').val();
                const admission_type_id = $("#admission_type").val();
                $.ajax({
                    url: '/app/application-form/course-eligibility?id=' + sub_course_id + '&admission_type=' + admission_type_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.status) {
                            var col_size = data.count == 1 ? 10 : data.count == 2 ? 5 : data.count == 3 ? 3 : data.count == 4 ? 2 : 2

                            if (data.required.includes('High School')) {
                                highDetailsRequired();
                                $("#high_school_column").css('display', 'block');
                                $("#high_school_column").addClass('col-md-' + col_size);
                            } else if (data.optional.includes('High School')) {
                                highDetailsNotRequired();
                                $("#high_school_column").css('display', 'block');
                                $("#high_school_column").addClass('col-md-' + col_size);
                            } else {
                                highDetailsNotRequired();
                                $("#high_school_column").css('display', 'none');
                            }

                            if (data.required.includes('Intermediate')) {
                                interDetailsRequired();
                                $("#intermediate_column").css('display', 'block');
                                $("#intermediate_column").addClass('col-md-' + col_size);
                            } else if (data.optional.includes('Intermediate')) {
                                interDetailsNotRequired();
                                $("#intermediate_column").css('display', 'block');
                                $("#intermediate_column").addClass('col-md-' + col_size);
                            } else {
                                interDetailsNotRequired();
                                $("#intermediate_column").css('display', 'none');
                            }

                            if (data.required.includes('UG')) {
                                ugDetailsRequired();
                                $("#ug_column").css('display', 'block');
                                $("#ug_column").addClass('col-md-' + col_size);
                            } else if (data.optional.includes('UG')) {
                                ugDetailsNotRequired();
                                $("#ug_column").css('display', 'block');
                                $("#ug_column").addClass('col-md-' + col_size);
                            } else {
                                ugDetailsNotRequired();
                                $("#ug_column").css('display', 'none');
                            }

                            if (data.required.includes('PG')) {
                                pgDetailsRequired();
                                $("#pg_column").css('display', 'block');
                                $("#pg_column").addClass('col-md-' + col_size);
                            } else if (data.optional.includes('PG')) {
                                pgDetailsNotRequired();
                                $("#pg_column").css('display', 'block');
                                $("#pg_column").addClass('col-md-' + col_size);
                            } else {
                                pgDetailsNotRequired();
                                $("#pg_column").css('display', 'none');
                            }

                            if (data.required.includes('Other')) {
                                otherDetailsRequired();
                                $("#other_column").css('display', 'block');
                                $("#other_column").addClass('col-md-' + col_size);
                            } else if (data.optional.includes('Other')) {
                                otherDetailsNotRequired();
                                $("#other_column").css('display', 'block');
                                $("#other_column").addClass('col-md-' + col_size);
                            } else {
                                otherDetailsNotRequired();
                                $("#other_column").css('display', 'none');
                            }
                        } else {
                            notification('danger', 'Eligibility is not configured for this course!');
                        }
                    }
                })
            }

            getCenter('<?= $_SESSION['university_id'] ?>');
            getAdmissionSession('<?= $_SESSION['university_id'] ?>');

            function fileValidation(id) {
                var fi = document.getElementById(id);
                if (fi.files.length > 0) {
                    for (var i = 0; i <= fi.files.length - 1; i++) {
                        var fsize = fi.files.item(i).size;
                        var file = Math.round((fsize / 1024));
                        // The size of the file.
                        if (file >= 11000) {
                            $('#' + id).val('');
                            alert("File too Big, each file should be less than or equal to 10MB");
                        }
                    }
                }
            }
        </script>

        <script>
            // Aadhar Validator
            // multiplication table d
            var d = [
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
                [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
                [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
                [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
                [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
                [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
                [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
                [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
                [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
            ];
            // permutation table p
            var p = [
                [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
                [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
                [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
                [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
                [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
                [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
                [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
            ];
            // inverse table inv
            var inv = [0, 4, 3, 2, 1, 5, 6, 7, 8, 9];
            // converts string or number to an array and inverts it
            function invArray(array) {

                if (Object.prototype.toString.call(array) == "[object Number]") {
                    array = String(array);
                }

                if (Object.prototype.toString.call(array) == "[object String]") {
                    array = array.split("").map(Number);
                }

                return array.reverse();

            }
            // generates checksum
            function generate(array) {

                var c = 0;
                var invertedArray = invArray(array);

                for (var i = 0; i < invertedArray.length; i++) {
                    c = d[c][p[((i + 1) % 8)][invertedArray[i]]];
                }

                return inv[c];
            }
            // validates checksum
            function validate(array) {
                var c = 0;
                var invertedArray = invArray(array);

                for (var i = 0; i < invertedArray.length; i++) {
                    c = d[c][p[(i % 8)][invertedArray[i]]];
                }
                return (c === 0);
            }
        </script>

        <script type="text/javascript">
            $(document).ready(function() {



                $('#step_1').validate({
                    rules: {
                        center: {
                            required: true
                        },
                        admission_session: {
                            required: true
                        },
                        admission_type: {
                            required: true
                        },
                        course: {
                            required: true
                        },
                        sub_course: {
                            required: true
                        },
                        duration: {
                            required: true
                        },
                        full_name: {
                            required: true
                        },
                        first_name: {
                            required: true
                        },
                        last_name: {
                            required: true
                        },
                        father_name: {
                            required: true
                        },
                        mother_name: {
                            required: true
                        },
                        dob: {
                            required: true
                        },
                        gender: {
                            required: true
                        },
                        category: {
                            required: true
                        },
                        employment_status: {
                            required: true
                        },
                        nationality: {
                            required: true
                        },
                        aadhar: {
                            required: true
                        },
                    },
                    highlight: function(element) {
                        $(element).addClass('error');
                        $(element).closest('.form-control').addClass('has-error');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('error');
                        $(element).closest('.form-control').removeClass('has-error');
                    }
                });

                $('#step_2').validate({
                    rules: {
                        email: {
                            required: true
                        },
                        contact: {
                            required: true
                        },
                        address: {
                            required: true
                        },
                        pincode: {
                            required: true
                        },
                        city: {
                            required: true
                        },
                        district: {
                            required: true
                        },
                        state: {
                            required: true
                        },
                    },
                    highlight: function(element) {
                        $(element).addClass('error');
                        $(element).closest('.form-control').addClass('has-error');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('error');
                        $(element).closest('.form-control').removeClass('has-error');
                    }
                });

                $('#step_3').validate();

                $('#step_4').validate({
                    rules: {
                        <?php print (!empty($id) && empty($photo)) ? "photo: {required:true}," : "" ?>
                        <?php print empty($id) ? "photo: {required:true}," : "" ?>
                        <?php print (!empty($id) && empty($aadhaars)) ? "'aadhar[]': {}," : "" ?>
                        <?php print empty($id) ? "'aadhar[]': {}," : "" ?>
                        <?php print (!empty($id) && empty($students_signature)) ? "student_signature: {required:true}," : "" ?>
                        <?php print empty($id) ? "student_signature: {required:true}," : "" ?>


                    },
                    highlight: function(element) {
                        $(element).addClass('error');
                        $(element).closest('.form-control').addClass('has-error');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('error');
                        $(element).closest('.form-control').removeClass('has-error');
                    }
                });


                $('#rootwizard').bootstrapWizard({
                    onTabShow: function(tab, navigation, index) {
                        var $total = navigation.find('li').length;
                        var $current = index + 1;

                        // If it's the last tab then hide the last button and show the finish instead
                        if ($current >= $total) {
                            $('#rootwizard').find('.pager .next').hide();
                            $('#rootwizard').find('.pager .finish').show().removeClass('disabled hidden');
                        } else {
                            $('#rootwizard').find('.pager .next').show();
                            $('#rootwizard').find('.pager .finish').hide();
                        }

                        var li = navigation.find('li a.active').parent();

                        var btnNext = $('#rootwizard').find('.pager .next').find('button');
                        var btnPrev = $('#rootwizard').find('.pager .previous').find('button');

                        if ($current < $total) {
                            var nextIcon = li.next().find('.uil');
                            var nextIconClass = nextIcon.text();

                            btnNext.find('.uil').html(nextIconClass)

                            var prevIcon = li.prev().find('.uil');
                            var prevIconClass = prevIcon.html()
                            btnPrev.addClass('btn-animated');
                            btnPrev.find('.hidden-block').show();
                            btnPrev.find('.uil').html(prevIconClass);
                        }

                        if ($current == 1) {
                            btnPrev.find('.hidden-block').hide();
                            btnPrev.removeClass('btn-animated');
                        }
                    },
                    onTabClick: function(activeTab, navigation, currentIndex, nextIndex) {
                        console.log(nextIndex, currentIndex);
                        if (nextIndex <= currentIndex) {
                            return;
                        }
                        if (nextIndex > currentIndex + 1) {
                            return false;
                        }
                        return submitForm(nextIndex);
                    },
                    onNext: function(tab, navigation, index) {
                        return submitForm(index);
                    },
                    onPrevious: function(tab, navigation, index) {
                        console.log("previous");
                    },
                    onInit: function() {
                        $('#rootwizard ul').removeClass('nav-pills');
                    }
                });

                $('.remove-item').click(function() {
                    $(this).parents('tr').fadeOut(function() {
                        $(this).remove();
                    });
                });
            });

            function submitForm(index) {
                var $valid = $("#step_" + index).valid();
                if (!$valid) {
                    return false;
                } else {
                    $('#step_' + index).submit();
                }
            }

            function getPaymentType() {
                var admission_type = $('#admission_type').val();
                var duration = $("#duration").val();
                $.ajax({
                    url: '/app/application-form/payment-type',
                    type: 'POST',
                    data: {
                        admission_type,
                        duration
                    },
                    success: function(data) {
                        $("#payment_type").html(data);
                    }
                })
            }

            function getFeeTable() {
                var user = $("#center").val();
                var admission_session = $('#admission_session').val();
                var sub_course = $("#sub_course").val();
                var duration = $("#duration").val();
                var payment_type = $("#payment_type").val();
                $.ajax({
                    url: '/app/application-form/fee-table',
                    type: 'POST',
                    data: {
                        admission_session,
                        user,
                        sub_course,
                        duration,
                        payment_type
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.status) {
                            $("#fee_table").html(data.table);
                            $("#amount").val(data.amount);
                            s
                        }
                    }
                })
            }

            $('#step_1').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                formData.append('inserted_id', localStorage.getItem('inserted_id'));
                formData.append('lead_id', '<?php echo isset($_GET['lead_id']) || isset($_SESSION['lead_id']) ? $lead_id : 0 ?>');
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 200) {
                            notification('success', data.message);
                            localStorage.setItem('inserted_id', data.id);
                            getPaymentType();
                        } else {
                            notification('danger', data.message);
                            $('#previous-button').click();
                        }
                    },
                    error: function(data) {
                        notification('danger', 'Server is not responding. Please try again later');
                        $('#previous-button').click();
                    }
                });
            });

            $('#step_2').submit(function(e) {
                var formData = new FormData(this);
                formData.append('inserted_id', localStorage.getItem('inserted_id'));
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 200) {
                            notification('success', data.message);
                        } else {
                            notification('danger', data.message);
                            $('#previous-button').click();
                        }
                    },
                    error: function(data) {
                        notification('danger', 'Server is not responding. Please try again later');
                        $('#previous-button').click();
                        console.log(data);
                    }
                });
            });

            $('#step_3').submit(function(e) {
                var formData = new FormData(this);
                formData.append('inserted_id', localStorage.getItem('inserted_id'));
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 200) {
                            notification('success', data.message);
                        } else {
                            notification('danger', data.message);
                            $('#previous-button').click();
                        }
                    },
                    error: function(data) {
                        notification('danger', 'Server is not responding. Please try again later');
                        $('#previous-button').click();
                        console.log(data);
                    }
                });
            });

            $('#step_4').submit(function(e) {
                var formData = new FormData(this);
                formData.append('inserted_id', localStorage.getItem('inserted_id'));
                e.preventDefault();
                $.ajax({
                    url: $(this).attr('action'),
                    type: "POST",
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 200) {
                            localStorage.removeItem('inserted_id');
                            localStorage.setItem('print_id', data.print_id);
                            notification('success', data.message);
                        } else {
                            notification('danger', data.message);
                        }
                    },
                    error: function(data) {
                        notification('danger', 'Server is not responding. Please try again later');
                        console.log(data);
                    }
                });
            });

            function printForm() {
                window.open('/forms/<?= $_SESSION['university_id'] ?>/?student_id=' + localStorage.getItem('print_id'));
            }

            function setMarksheet(id) {
                if (id == '53') {
                    $(".semester_1").show();
                    $(".semester_2").show();
                    $(".semester_3").show();
                    $(".semester_4").show();
                    $('#marksheet_1').validate();
                    $('#marksheet_1').rules('add', {

                        required: true
                    });
                    $('#marksheet_2').validate();
                    $('#marksheet_2').rules('add', {
                        required: true
                    });
                    $('#marksheet_3').validate();
                    $('#marksheet_3').rules('add', {
                        required: true
                    });
                    $('#marksheet_4').validate();
                    $('#marksheet_4').rules('add', {
                        required: true
                    });
                } else {
                    $(".semester_1").hide();
                    $(".semester_2").hide();
                    $('#marksheet_1').rules('remove', 'required');
                    $('#marksheet_2').rules('remove', 'required');
                }

                // var stid = '';
                // if (stid !== undefined || stid != "" || stid > 0) {
                //     $('#marksheet_1').rules('remove', 'required');
                //     $('#marksheet_2').rules('remove', 'required');
                //     $('#marksheet_3').rules('remove', 'required');
                //     $('#marksheet_4').rules('remove', 'required');
                // }
            }
          
          document.addEventListener("DOMContentLoaded", function () {
    // 1. Reset all 4 step forms to clear cached values
    for (let i = 1; i <= 4; i++) {
        const form = document.getElementById(`step_${i}`);
        if (form) {
            form.reset();
        }
    }

    // 2. Target text inputs across all forms starting with "step_"
    const formInputs = document.querySelectorAll('form[id^="step_"] input[type="text"]');

    formInputs.forEach(input => {
        // Force readonly on load to block browser autofill
        input.setAttribute('readonly', 'readonly');

        // Remove readonly on user interaction
        input.addEventListener('focus', function () {
            this.removeAttribute('readonly');
        });

        input.addEventListener('touchstart', function () {
            this.removeAttribute('readonly');
        }, { once: true });
    });
});
        </script>

        <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>