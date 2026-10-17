<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (isset($_POST['inserted_id'])) {
  require '../../includes/db-config.php';
  session_start();

  $inserted_id = intval($_POST['inserted_id']);

  if (empty($inserted_id)) {
    echo json_encode(['status' => 400, 'message' => 'ID is required.']);
    exit();
  }

  $step_query = $conn->query("SELECT Step FROM Students WHERE ID = $inserted_id");
  $step_data = mysqli_fetch_array($step_query);
  $step = $step_data['Step'] ?? 0;

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG");
  $photo_folder = '../../uploads/photo/';
  $aadhar_folder = '../../uploads/aadhar/';
  $signature_folder = '../../uploads/signature/';
  $migration_folder = '../../uploads/migration/';
  $affidavit_folder = '../../uploads/affidavit/';
  $other_certificate_folder = '../../uploads/other_certificates/';
  $academics_folder = '../../uploads/marksheet/under_graduate/';

  $update = false;

  // 1. Photo
  if (isset($_FILES["photo"]['tmp_name']) && $_FILES["photo"]['tmp_name'] != '') {
    $photo = mysqli_real_escape_string($conn, $_FILES["photo"]['name']);
    $tmp_name = $_FILES["photo"]["tmp_name"];
    $photo_extension = pathinfo($photo, PATHINFO_EXTENSION);
    $photo = $inserted_id . "." . $photo_extension;
    if (in_array($photo_extension, $allowed_file_extensions)) {
      if (!move_uploaded_file($tmp_name, $photo_folder . $photo)) {
        echo json_encode(['status' => 503, 'message' => 'Unable to upload photo!']);
        exit();
      } else {
        $photo = str_replace('../..', '', $photo_folder) . $photo;
        $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Photo'");
        if ($check->num_rows > 0) {
          $update = $conn->query("UPDATE Student_Documents SET Location = '$photo' WHERE Student_ID = $inserted_id AND Type = 'Photo'");
        } else {
          $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Photo', '$photo')");
        }
      }
    } else {
      echo json_encode(['status' => 302, 'message' => 'Photo should be image!']);
      exit();
    }
  } else {
    $check = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Photo'");
    if ($check->num_rows == 0) {
      echo json_encode(['status' => 400, 'message' => 'Photo is required!']);
      exit();
    } else {
      $update = true;
    }
  }

  // Initialize tracking arrays for multi-file inputs
  $aadhars_front = array();
  $aadhars_back = array();
  $migrations = array();
  $affidavits = array();
  $other_certificates = array();
  $marksheetsarr_1 = array();
  $marksheetsarr_2 = array();
  $marksheetsarr_3 = array();
  $marksheetsarr_4 = array();

  // 2. Aadhar Front
  if (isset($_FILES["aadhar_front"]["tmp_name"]) && $_FILES["aadhar_front"]['tmp_name'] != '' && count(array_filter($_FILES["aadhar_front"]['tmp_name'])) > 0) {
    foreach ($_FILES["aadhar_front"]["tmp_name"] as $key => $tmp_name) {
      if (empty($tmp_name)) continue;
      $aadhar_front = mysqli_real_escape_string($conn, $_FILES["aadhar_front"]["name"][$key]);
      $aadhar_extension_front = pathinfo($aadhar_front, PATHINFO_EXTENSION);
      $aadhar_name_front = $inserted_id . "_Aadhar_Front_" . $key . "." . $aadhar_extension_front;
      if (in_array($aadhar_extension_front, $allowed_file_extensions)) {
        if (file_exists($aadhar_folder . $aadhar_name_front)) {
          unlink($aadhar_folder . $aadhar_name_front);
        }
        if (move_uploaded_file($tmp_name, $aadhar_folder . $aadhar_name_front)) {
          $aadhars_front[] = str_replace('../..', '', $aadhar_folder) . $aadhar_name_front;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload Aadhar Front!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'Aadhar Front should be an image!']);
        exit();
      }
    }
    if (!empty($aadhars_front)) {
      $aadhar_front_str = implode("|", $aadhars_front);
      $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Aadhar_Front'");
      if ($check->num_rows > 0) {
        $update = $conn->query("UPDATE Student_Documents SET Location = '$aadhar_front_str' WHERE Student_ID = $inserted_id AND Type = 'Aadhar_Front'");
      } else {
        $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Aadhar_Front', '$aadhar_front_str')");
      }
    }
  }
  
  // 3. Aadhar Back
  if (isset($_FILES["aadhar_back"]["tmp_name"]) && $_FILES["aadhar_back"]['tmp_name'] != '' && count(array_filter($_FILES["aadhar_back"]['tmp_name'])) > 0) {
    foreach ($_FILES["aadhar_back"]["tmp_name"] as $key => $tmp_name) {
      if (empty($tmp_name)) continue;
      $aadhar_back = mysqli_real_escape_string($conn, $_FILES["aadhar_back"]["name"][$key]);
      $aadhar_extension_back = pathinfo($aadhar_back, PATHINFO_EXTENSION);
      $aadhar_name_back = $inserted_id . "_Aadhar_Back_" . $key . "." . $aadhar_extension_back;
      if (in_array($aadhar_extension_back, $allowed_file_extensions)) {
        if (file_exists($aadhar_folder . $aadhar_name_back)) {
          unlink($aadhar_folder . $aadhar_name_back);
        }
        if (move_uploaded_file($tmp_name, $aadhar_folder . $aadhar_name_back)) {
          $aadhars_back[] = str_replace('../..', '', $aadhar_folder) . $aadhar_name_back;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload Aadhar Back!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'Aadhar Back should be an image!']);
        exit();
      }
    }
    if (!empty($aadhars_back)) {
      $aadhar_back_str = implode("|", $aadhars_back);
      $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Aadhar_Back'");
      if ($check->num_rows > 0) {
        $update = $conn->query("UPDATE Student_Documents SET Location = '$aadhar_back_str' WHERE Student_ID = $inserted_id AND Type = 'Aadhar_Back'");
      } else {
        $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Aadhar_Back', '$aadhar_back_str')");
      }
    }
  }

  // 4. Student's Signature
  if (isset($_FILES["student_signature"]['tmp_name']) && $_FILES["student_signature"]['tmp_name'] != '') {
    $student_signature = mysqli_real_escape_string($conn, $_FILES["student_signature"]['name']);
    $tmp_name = $_FILES["student_signature"]["tmp_name"];
    $student_signature_extension = pathinfo($student_signature, PATHINFO_EXTENSION);
    $student_signature = $inserted_id . "_Student_Signature." . $student_signature_extension;
    if (in_array($student_signature_extension, $allowed_file_extensions)) {
      if (!move_uploaded_file($tmp_name, $signature_folder . $student_signature)) {
        echo json_encode(['status' => 503, 'message' => 'Unable to upload Student Signature!']);
        exit();
      } else {
        $student_signature = str_replace('../..', '', $signature_folder) . $student_signature;
        $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Student Signature'");
        if ($check->num_rows > 0) {
          $update = $conn->query("UPDATE Student_Documents SET Location = '$student_signature' WHERE Student_ID = $inserted_id AND Type = 'Student Signature'");
        } else {
          $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Student Signature', '$student_signature')");
        }
      }
    } else {
      echo json_encode(['status' => 302, 'message' => 'Student Signature should be image!']);
      exit();
    }
  }

  // 5. Parent's Signature
  if (isset($_FILES["parent_signature"]['tmp_name']) && $_FILES["parent_signature"]['tmp_name'] != '') {
    $parent_signature = mysqli_real_escape_string($conn, $_FILES["parent_signature"]['name']);
    $tmp_name = $_FILES["parent_signature"]["tmp_name"];
    $parent_signature_extension = pathinfo($parent_signature, PATHINFO_EXTENSION);
    $parent_signature = $inserted_id . "_Parent_Signature." . $parent_signature_extension;
    if (in_array($parent_signature_extension, $allowed_file_extensions)) {
      if (!move_uploaded_file($tmp_name, $signature_folder . $parent_signature)) {
        echo json_encode(['status' => 503, 'message' => 'Unable to upload Parent Signature!']);
        exit();
      } else {
        $parent_signature = str_replace('../..', '', $signature_folder) . $parent_signature;
        $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Parent Signature'");
        if ($check->num_rows > 0) {
          $update = $conn->query("UPDATE Student_Documents SET Location = '$parent_signature' WHERE Student_ID = $inserted_id AND Type = 'Parent Signature'");
        } else {
          $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Parent Signature', '$parent_signature')");
        }
      }
    } else {
      echo json_encode(['status' => 302, 'message' => 'Parent Signature should be image!']);
      exit();
    }
  }

  // 6. Migration
  if (isset($_FILES["migration"]["tmp_name"]) && $_FILES["migration"]['tmp_name'] != '' && count(array_filter($_FILES["migration"]['tmp_name'])) > 0) {
    foreach ($_FILES["migration"]["tmp_name"] as $key => $tmp_name) {
      if (empty($tmp_name)) continue;
      $migration = mysqli_real_escape_string($conn, $_FILES["migration"]["name"][$key]);
      $migration_extension = pathinfo($migration, PATHINFO_EXTENSION);
      $migration_name = $inserted_id . "_Migration_" . $key . "." . $migration_extension;
      if (in_array($migration_extension, $allowed_file_extensions)) {
        if (file_exists($migration_folder . $migration_name)) {
          unlink($migration_folder . $migration_name);
        }
        if (move_uploaded_file($tmp_name, $migration_folder . $migration_name)) {
          $migrations[] = str_replace('../..', '', $migration_folder) . $migration_name;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload Migration!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'Migration should be image!']);
        exit();
      }
    }
    if (!empty($migrations)) {
      $migration_str = implode("|", $migrations);
      $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Migration'");
      if ($check->num_rows > 0) {
        $update = $conn->query("UPDATE Student_Documents SET Location = '$migration_str' WHERE Student_ID = $inserted_id AND Type = 'Migration'");
      } else {
        $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Migration', '$migration_str')");
      }
    }
  }

  // 7. Affidavit
  if (isset($_FILES["affidavit"]["tmp_name"]) && $_FILES["affidavit"]['tmp_name'] != '' && count(array_filter($_FILES["affidavit"]['tmp_name'])) > 0) {
    foreach ($_FILES["affidavit"]["tmp_name"] as $key => $tmp_name) {
      if (empty($tmp_name)) continue;
      $affidavit = mysqli_real_escape_string($conn, $_FILES["affidavit"]["name"][$key]);
      $affidavit_extension = pathinfo($affidavit, PATHINFO_EXTENSION);
      $affidavit_name = $inserted_id . "_Affidavit_" . $key . "." . $affidavit_extension;
      if (in_array($affidavit_extension, $allowed_file_extensions)) {
        if (file_exists($affidavit_folder . $affidavit_name)) {
          unlink($affidavit_folder . $affidavit_name);
        }
        if (move_uploaded_file($tmp_name, $affidavit_folder . $affidavit_name)) {
          $affidavits[] = str_replace('../..', '', $affidavit_folder) . $affidavit_name;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload affidavit!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'Affidavit should be image!']);
        exit();
      }
    }
    if (!empty($affidavits)) {
      $affidavit_str = implode("|", $affidavits);
      $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Affidavit'");
      if ($check->num_rows > 0) {
        $update = $conn->query("UPDATE Student_Documents SET Location = '$affidavit_str' WHERE Student_ID = $inserted_id AND Type = 'Affidavit'");
      } else {
        $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Affidavit', '$affidavit_str')");
      }
    }
  }

  // 8. Other Certificates
  if (isset($_FILES["other_certificate"]["tmp_name"]) && $_FILES["other_certificate"]['tmp_name'] != '' && count(array_filter($_FILES["other_certificate"]['tmp_name'])) > 0) {
    foreach ($_FILES["other_certificate"]["tmp_name"] as $key => $tmp_name) {
      if (empty($tmp_name)) continue;
      $other_certificate = mysqli_real_escape_string($conn, $_FILES["other_certificate"]["name"][$key]);
      $other_certificate_extension = pathinfo($other_certificate, PATHINFO_EXTENSION);
      $other_certificate_name = $inserted_id . "_other_certificate_" . $key . "." . $other_certificate_extension;
      if (in_array($other_certificate_extension, $allowed_file_extensions)) {
        if (file_exists($other_certificate_folder . $other_certificate_name)) {
          unlink($other_certificate_folder . $other_certificate_name);
        }
        if (move_uploaded_file($tmp_name, $other_certificate_folder . $other_certificate_name)) {
          $other_certificates[] = str_replace('../..', '', $other_certificate_folder) . $other_certificate_name;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload other_certificate!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'other_certificate should be image!']);
        exit();
      }
    }
    if (!empty($other_certificates)) {
      $other_certificate_str = implode("|", $other_certificates);
      $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = 'Other Certificate'");
      if ($check->num_rows > 0) {
        $update = $conn->query("UPDATE Student_Documents SET Location = '$other_certificate_str' WHERE Student_ID = $inserted_id AND Type = 'Other Certificate'");
      } else {
        $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, 'Other Certificate', '$other_certificate_str')");
      }
    }
  }

  // 9. Marksheets Semester 1 to 4
  $semester_files = ['marksheet_1', 'marksheet_2', 'marksheet_3', 'marksheet_4'];
  $arr_mapping = [
    'marksheet_1' => &$marksheetsarr_1,
    'marksheet_2' => &$marksheetsarr_2,
    'marksheet_3' => &$marksheetsarr_3,
    'marksheet_4' => &$marksheetsarr_4
  ];

  foreach ($semester_files as $index => $field_name) {
    $sem_num = $index + 1;
    $type_name = 'marksheet_semester_' . $sem_num;
    
    if (isset($_FILES[$field_name]["tmp_name"]) && $_FILES[$field_name]['tmp_name'] != '' && count(array_filter($_FILES[$field_name]['tmp_name'])) > 0) {
      foreach ($_FILES[$field_name]["tmp_name"] as $key => $tmp_name) {
        if (empty($tmp_name)) continue;
        $marksheets_file = mysqli_real_escape_string($conn, $_FILES[$field_name]["name"][$key]);
        $marksheets_extension = pathinfo($marksheets_file, PATHINFO_EXTENSION);
        $marksheets_file_name = $inserted_id . "_marksheet_semester_" . $sem_num . "_" . $key . "." . $marksheets_extension;
        
        if (in_array($marksheets_extension, $allowed_file_extensions)) {
          if (file_exists($academics_folder . $marksheets_file_name)) {
            unlink($academics_folder . $marksheets_file_name);
          }
          if (move_uploaded_file($tmp_name, $academics_folder . $marksheets_file_name)) {
            $arr_mapping[$field_name][] = str_replace('../..', '', $academics_folder) . $marksheets_file_name;
          } else {
            echo json_encode(['status' => 503, 'message' => 'Unable to upload marksheet!']);
            exit();
          }
        } else {
          echo json_encode(['status' => 302, 'message' => 'Marksheet should be an image!']);
          exit();
        }
      }
      
      if (!empty($arr_mapping[$field_name])) {
        $marksheet_str = implode("|", $arr_mapping[$field_name]);
        $check = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = '$type_name'");
        if ($check->num_rows > 0) {
          $update = $conn->query("UPDATE Student_Documents SET Location = '$marksheet_str' WHERE Student_ID = $inserted_id AND Type = '$type_name'");
        } else {
          $update = $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, '$type_name', '$marksheet_str')");
        }
      }
    }
  }

  // Final Completion Response
  if ($update) {
    $id = base64_encode('W1Ebt1IhGN3ZOLplom9I' . $inserted_id);
    if ($step < 4) {
      date_default_timezone_set('Asia/Kolkata');
      $form_completion_date = date("Y-m-d H:i:s");
      $conn->query("UPDATE Students SET Step = 4, form_completion_date='$form_completion_date' WHERE ID = $inserted_id");
    }
    echo json_encode(['status' => 200, 'message' => 'Step 4 details saved successfully!', 'print_id' => $id]);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
?>