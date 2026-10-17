<?php

if (isset($_FILES['file'])) {
   
  require '../../includes/db-config.php';
//  require '../../includes/helpers.php';
 //echo "karuna"; die;
  require('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');
  require('../../extras/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php');
  session_start();

//   ini_set('display_errors', 1);
//   ini_set('display_startup_errors', 1);
//   error_reporting(E_ALL);
  ini_set('max_execution_time', 1400);
  
  $export_data = array();
  $header = array('Course', 'Sub-Course', 'Enrollment Number', 'Subject Code', 'Obtained External Marks', 'Obtained Internal Marks', 'Semester', 'Exam Month', 'Exam Year', 'Paper Code', 'Remark');
  $export_data[] = $header;
  $mimes = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

  if (in_array($_FILES["file"]["type"], $mimes)) {
    
    $uploadFilePath = basename($_FILES['file']['name']);
    move_uploaded_file($_FILES['file']['tmp_name'], $uploadFilePath);
    $reader = new SpreadsheetReader($uploadFilePath);
    $totalSheet = count($reader->sheets());
    for ($i = 0; $i < $totalSheet; $i++) {
      $reader->ChangeSheet($i);

      foreach ($reader as $dd=> $row) {
       if($dd==0){
           continue;
       }

        $course = mysqli_real_escape_string($conn, trim($row[0]));
        $sub_course = mysqli_real_escape_string($conn, trim($row[1]));
        $enrollment = mysqli_real_escape_string($conn, $row[2]);
        $subject_code = mysqli_real_escape_string($conn, trim($row[3]));
        $obt_ext_marks = (isset($row[4]) && $row[4] != '') ? mysqli_real_escape_string($conn, trim($row[4])) : 0;
        $obt_int_marks = (isset($row[5]) && $row[5] != '') ? mysqli_real_escape_string($conn, trim($row[5])) : 0;
        $semester = mysqli_real_escape_string($conn, $row[6]);
        $exam_month = isset($row[7]) ? mysqli_real_escape_string($conn, $row[7]) : '';
        $exam_year = isset($row[8]) ? intval($row[8]) : '';
        $subject_name = mysqli_real_escape_string($conn, $row[9]);
        $created_at = date("Y-m-d:H:i:s");




        $course = $conn->query("SELECT ID,University_ID FROM Courses WHERE University_ID = " . $_SESSION['university_id'] . " AND (Name = '$course' OR Short_Name = '$course')");

        if ($course->num_rows == 0) {
        //   print_r("SELECT ID,University_ID FROM Courses WHERE University_ID = " . $_SESSION['university_id'] . " AND (Name = '$row[0]' OR Short_Name = '$row[0]')");die;
          $export_data[] = array_merge($row, ['Course not found!']);
          continue;
        }

        $course_ids = array();
        while ($course_id = $course->fetch_assoc()) {
          $course_ids[] = $course_id['ID'];
        }

        $sub_course = $conn->query("SELECT ID, Course_ID,Name FROM Sub_Courses WHERE University_ID = " . $_SESSION['university_id'] . " AND Name = '$sub_course' AND Course_ID IN (" . implode(',', $course_ids) . ")");
        if ($sub_course->num_rows == 0) {
          $export_data[] = array_merge($row, ['Sub-Course not found!']);
          continue;
        }

        $sub_course = $sub_course->fetch_assoc();
        $course_id = $sub_course['Course_ID'];
        $sub_course_id = $sub_course['ID'];
        $sub_course_name = $sub_course['Name'];
       
        if((empty($obt_int_marks) || $obt_int_marks=='' || $obt_int_marks==0)){
          $checkres = $conn->query("SELECT marksheets.obt_marks_int FROM marksheets LEFT JOIN Syllabi on subject_id = Syllabi.ID  WHERE enrollment_no ='$enrollment' AND University_ID = " . $_SESSION['university_id'] . " AND marksheets.code = '$subject_code' AND marksheets.semester =  '".$semester."'  AND marksheets.course_id = $course_id AND marksheets.sub_course_id = $sub_course_id");
          if($checkres->num_rows > 0){
           $obt_int_marks = $checkres->fetch_assoc()['obt_marks_int'];
          }
         }

         if((empty($obt_ext_marks) || $obt_ext_marks=='' || $obt_ext_marks==0) ){
          $checkres = $conn->query("SELECT marksheets.obt_marks_ext FROM marksheets LEFT JOIN Syllabi on subject_id = Syllabi.ID  WHERE enrollment_no ='$enrollment' AND University_ID = " . $_SESSION['university_id'] . " AND marksheets.code = '$subject_code' AND marksheets.semester = '".$semester."'  AND marksheets.course_id = $course_id AND marksheets.sub_course_id = $sub_course_id");
          if($checkres->num_rows > 0){
           $obt_ext_marks = $checkres->fetch_assoc()['obt_marks_ext'];
          }
         }
        
        $check_enrollment = $conn->query("SELECT ID FROM Students WHERE Enrollment_No = '$enrollment'");
        if ($check_enrollment->num_rows == 0) {
            $export_data[] = array_merge($row, ['Invalid Enrollment No.']);
            continue;
        }


        $check_student_sub_course = $conn->query("SELECT Users.Code,Users.ID, Role FROM Students LEFT JOIN Users on Added_For= Users.ID WHERE Enrollment_No = '$enrollment' AND Sub_Course_ID = '$sub_course_id' AND Course_ID = '$course_id'");
        if ($check_student_sub_course->num_rows == 0) {
          $export_data[] = array_merge($row, ['This Sub-Course is not assigned to this Student.']);
          continue;
        }

        $subjects = $conn->query("SELECT ID, Min_Marks,Max_Marks,University_ID FROM Syllabi WHERE University_ID = " . $_SESSION['university_id'] . " AND Code = '$subject_code' AND Semester = '" . $semester . "'  AND Course_ID = $course_id AND Sub_Course_ID = $sub_course_id");
        // if ($subjects->num_rows == 0) {
        //   $error_message = "Subject not found!";

        //   $export_data[] = array_merge($row, [$error_message]);
        //   continue;
        // }

        $subject_ids = array();
        $subject_arr = $subjects->fetch_assoc();
        $subject_ids = $subject_arr['ID'];

        if ($obt_ext_marks == 'AB' || $obt_int_marks == 'AB' || $obt_ext_marks == 0 || $obt_int_marks == 0) {
          $remarks = "Fail";
          $obt_ext_marks = ($obt_ext_marks == 'AB') ? 'AB' : $obt_ext_marks;
          $obt_int_marks = ($obt_int_marks == 'AB') ? 'AB' : $obt_int_marks;
        }
     
        
        if ($obt_ext_marks != 'AB' && $obt_int_marks !== 'AB') {
            $total = (int)$obt_ext_marks + (int)$obt_int_marks;
            $total = 0;
        }


          if ($subject_arr['University_ID'] != 41) {
          $min_marks = ($subject_arr['Min_Marks'] + $subject_arr['Max_Marks']) * 40 / 100;
          if ($total < $min_marks) {
            $remarks = "Fail";
          } else {
            $remarks = "Pass";
          }
        } else {
          $min_marks = isset($subject_arr['Min_Marks']) ? $subject_arr['Min_Marks'] : 0;

          if (($obt_ext_marks >= $min_marks || $obt_int_marks >= $min_marks) && ($obt_ext_marks != 'AB' || $obt_int_marks != 'AB')) {
            $remarks = "Pass";
          } else {
            $remarks = "Fail";
          }
        }


        

        $check = $conn->query("SELECT * FROM marksheets WHERE enrollment_no ='$enrollment' AND code='$subject_code' ");

        if ($check->num_rows > 0) {

          $update = $conn->query("UPDATE `marksheets` SET `obt_marks_ext` = '" . $obt_ext_marks . "',  `obt_marks_int` = '" . $obt_int_marks . "',   `obt_marks` = '" . $total . "',  `remarks` = '" . $remarks . "',  `exam_month` = '" . $exam_month . "', `exam_year` = " . $exam_year . ", `updated_at` = '" . $created_at . "'
              WHERE enrollment_no = '" . $enrollment . "' AND code = '" . $subject_code . "' ");
          if ($update) {
            $export_data[] = array_merge($row, ['Result Updated successfully!']);
          } else {
            $export_data[] = array_merge($row, ['Something went wrong!']);
          }
        } else {
          $add = $conn->query("INSERT INTO `marksheets`(`enrollment_no`, `code`, `obt_marks_ext`, `obt_marks_int`, `obt_marks`, `remarks`, `status`, `exam_month`,`exam_year`,`created_at`, `subject`,`course_id`,`sub_course_id`,`semester`) VALUES ('" . $enrollment . "', '" . $subject_code . "', '" . $obt_ext_marks . "','" . $obt_int_marks . "', '" . $total . "', '" . $remarks . "',1, '" . $exam_month . "', $exam_year,'$created_at','" . $subject_name . "','". $course_id ."','". $sub_course_id ."','".$semester."')");
          if ($add) {
            $export_data[] = array_merge($row, ['Result added successfully!']);
          } else {
            $export_data[] = array_merge($row, ['Something went wrong!']);
          }
        }
      }
    }

    unlink($uploadFilePath);
    $xlsx = SimpleXLSXGen::fromArray($export_data)->downloadAs('Result Status ' . date('h m s') . '.xlsx');
  }
}
