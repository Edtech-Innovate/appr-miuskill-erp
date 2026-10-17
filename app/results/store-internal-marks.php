<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_POST['enroll'])) {
  require '../../includes/db-config.php';
  session_start();

  $enroll = mysqli_real_escape_string($conn, $_POST['enroll']);
  $semester = isset($_POST['duration']) ? $_POST['duration'] : "";

  if ($_SESSION['university_id'] == 41) {
    $marksArr = is_array($_POST['obt_ext_marks']) ? array_filter($_POST['obt_ext_marks']) : [];
  } else {
    $marksArr = is_array($_POST['obt_marks_int']) ? array_filter($_POST['obt_marks_int']) : [];
  }
  $max_marks_arr = is_array($_POST['max_marks']) ? array_filter($_POST['max_marks']) : [];
//   $ext_max_marks_arr = is_array($_POST['ext_max_marks']) ? array_filter($_POST['ext_max_marks']) : [];
//   $extPracMarksArr = is_array($_POST['obt_marks_ext']) ? array_filter($_POST['obt_marks_ext']) : [];
  $ext_max_marks_arr = isset($_POST['ext_max_marks']) && is_array($_POST['ext_max_marks'])
    ? array_filter($_POST['ext_max_marks'])
    : [];

$extPracMarksArr = isset($_POST['obt_marks_ext']) && is_array($_POST['obt_marks_ext'])
    ? array_filter($_POST['obt_marks_ext'])
    : [];
//   echo "<pre>";
//   print_r($_POST); die;


  $add = '';
  $update = '';

  if (!empty($marksArr)) {
    if ($_SESSION['university_id'] == 41) {
      foreach ($marksArr as $sub_id => $obt_ext_marks) {
        $obt_ext_marks = mysqli_real_escape_string($conn, $obt_ext_marks);
        // print_r($max_marks_arr);die;
        $max_marks = $max_marks_arr[$sub_id];
        if (($max_marks < $obt_ext_marks || $obt_ext_marks < 0) && strtolower($obt_ext_marks) != 'ab') {
          echo json_encode(['status' => 400, 'message' => 'Invalid marks!']);
          exit;
        }

        $check = $conn->query("SELECT * FROM marksheets WHERE enrollment_no='$enroll' AND subject_id = $sub_id ");
        if ($check->num_rows == 0) {
          $add = $conn->query("INSERT INTO marksheets (enrollment_no, subject_id, obt_marks_ext) VALUES ('$enroll', '$sub_id', '$obt_ext_marks')");
          if (!$add) {
            break;
          }
        } else {
          $update = $conn->query("UPDATE marksheets SET obt_marks_ext = '$obt_ext_marks' WHERE enrollment_no='$enroll' AND subject_id = $sub_id");
          if (!$update) {
            break;
          }
        }
      }
    } else {
      foreach ($marksArr as $sub_id => $obt_marks_int) {
        $obt_marks_int = mysqli_real_escape_string($conn, $obt_marks_int);

        $max_marks = $max_marks_arr[$sub_id];
        if (($max_marks < $obt_marks_int || $obt_marks_int < 0) && strtolower($obt_marks_int) != 'ab') {
          echo json_encode(['status' => 400, 'message' => 'Invalid marks!']);
          exit;
        }
        $obt_marks_ext ="";
        // echo('<pre>');print_r($extPracMarksArr[$sub_id]);die;
        if(!empty($ext_max_marks_arr[$sub_id]) && $sub_id){
          $ext_max_marks = $ext_max_marks_arr[$sub_id];
          $obt_marks_ext = $extPracMarksArr[$sub_id];
// echo('<pre>');print_r($obt_marks_ext);die;
          if (($ext_max_marks < $obt_marks_ext || $obt_marks_ext < 0) && strtolower($obt_marks_ext)!='ab') {
            echo json_encode(['status' => 400,'message' => 'Invalid Practical marks!']);
            exit;
          }
        }

        $check = $conn->query("SELECT * FROM marksheets WHERE enrollment_no='$enroll' AND subject_id = $sub_id ");
        // print_r($check->num_rows);die;
        if ($check->num_rows == 0) {
          $add = $conn->query("INSERT INTO marksheets (enrollment_no, subject_id, obt_marks_int, obt_marks_ext) VALUES ('$enroll', '$sub_id', '$obt_marks_int', '$obt_marks_ext')");
          if (!$add) {
            break;
          }
        } else {
           
            $paperType = $conn->query("select Paper_Type from Syllabi where ID=$sub_id");
            $paperTypeName = $paperType->fetch_assoc();
            if($paperTypeName['Paper_Type']=='Theory')
            {
                $update = $conn->query("UPDATE marksheets SET obt_marks_int = '$obt_marks_int' WHERE enrollment_no='$enroll' AND subject_id = $sub_id");
            }
            else
            {
                $update = $conn->query("UPDATE marksheets SET obt_marks_ext = '$obt_marks_ext', obt_marks_int = '$obt_marks_int' WHERE enrollment_no='$enroll' AND subject_id = $sub_id");
            }
          
          if (!$update) {
            break;
          }
        }
      }
    }
  }


  if ($add || $update) {
    echo json_encode(['status' => 200, 'message' => 'Marks added successlly!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
?>