<?php
if (isset($_POST['inserted_id'])) {
  require '../../includes/db-config.php';
  session_start();

  // Enable errors for debugging
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);

  $allowed_file_extensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG");
  
  $folders = array(
    'High School' => '../../uploads/marksheet/high_school/',
    'Intermediate' => '../../uploads/marksheet/intermediate/',
    'UG' => '../../uploads/marksheet/under_graduate/',
    'PG' => '../../uploads/marksheet/post_graduate/',
    'Other' => '../../uploads/marksheet/other/'
  );

  $inserted_id = intval($_POST['inserted_id']);

  if (empty($inserted_id)) {
    echo json_encode(['status' => 400, 'message' => 'ID is required.']);
    exit();
  }

  $step_query = $conn->query("SELECT Step, Sub_Courses.Eligibility, Admission_Type_ID FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID WHERE Students.ID = $inserted_id");
  $step_data = mysqli_fetch_array($step_query);
  $eligibility = $step_data['Eligibility'];
  $admission_type_id = $step_data['Admission_Type_ID'];
  $current_step = $step_data['Step'];

  $eligibility = !empty($eligibility) ? json_decode($eligibility, true) : array();
  if (count($eligibility) == 0) {
    echo json_encode(['status' => 400, 'message' => 'Please configure eligibility criteria.']);
    exit();
  }

  // Get required levels from eligibility configuration
  $required_levels = isset($eligibility[$admission_type_id]['required']) ? $eligibility[$admission_type_id]['required'] : array();

  // Map prefix names used in POST requests
  $sections = array(
    'High School'  => 'high',
    'Intermediate' => 'inter',
    'UG'           => 'ug',
    'PG'           => 'pg',
    'Other'        => 'other'
  );

  $overall_success = true;

  foreach ($sections as $type => $prefix) {
    $total_key = $prefix . '_total';
    $subject_key = $prefix . '_subject';
    
    $total_val = isset($_POST[$total_key]) ? trim($_POST[$total_key]) : '';
    $subject_val = isset($_POST[$subject_key]) ? trim($_POST[$subject_key]) : '';
    
    $is_required = in_array($type, $required_levels);

    // RULE 1: If it is strictly required by eligibility, and the user left it empty -> THROW ERROR
    if ($is_required && empty($total_val)) {
      echo json_encode(['status' => 400, 'message' => "$type details are required!"]);
      exit();
    }

    // RULE 2: If it is NOT required, and the user left both fields completely blank -> SKIP IT
    if (!$is_required && empty($total_val) && empty($subject_val)) {
      continue;
    }

    // RULE 3: If it's NOT required, but the user DID provide data -> STORE IT
    $folder = $folders[$type];

    $subject = strtoupper(strtolower(mysqli_real_escape_string($conn, $subject_val)));
    $year = mysqli_real_escape_string($conn, $_POST[$prefix . '_year'] ?? '');
    $board = strtoupper(strtolower(mysqli_real_escape_string($conn, $_POST[$prefix . '_board'] ?? '')));
    $obtained = array_key_exists($prefix . '_obtained', $_POST) && $_POST[$prefix . '_obtained'] !== '' ? mysqli_real_escape_string($conn, $_POST[$prefix . '_obtained']) : NULL;
    $max_marks = array_key_exists($prefix . '_max', $_POST) && $_POST[$prefix . '_max'] !== '' ? mysqli_real_escape_string($conn, $_POST[$prefix . '_max']) : NULL;
    $total = strtoupper(strtolower($total_val));

    // File Handling
    $file_field = $prefix . '_marksheet';
    $marksheet_path = '';

    if (isset($_FILES[$file_field]["tmp_name"]) && $_FILES[$file_field]['tmp_name'] != '' && count(array_filter($_FILES[$file_field]['tmp_name'])) > 0) {
      $marksheets_array = array();
      foreach ($_FILES[$file_field]["tmp_name"] as $key => $tmp_name) {
        $original_name = $_FILES[$file_field]["name"][$key];
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        $new_name = $inserted_id . "_" . ucfirst($prefix) . "_Marksheet_" . $key . "." . $ext;

        if (in_array($ext, $allowed_file_extensions)) {
          if (file_exists($folder . $new_name)) {
            unlink($folder . $new_name);
          }
          if (move_uploaded_file($tmp_name, $folder . $new_name)) {
            $marksheets_array[] = str_replace('../..', '', $folder) . $new_name;
          } else {
            echo json_encode(['status' => 503, 'message' => "Unable to upload $type marksheet!"]);
            exit();
          }
        } else {
          echo json_encode(['status' => 302, 'message' => "$type Marksheet should be an image!"]);
          exit();
        }
      }
      $marksheet_path = implode("|", $marksheets_array);
    } else {
      $check_doc = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = '$type'");
      if ($check_doc->num_rows == 0) {
        if ($is_required) {
          echo json_encode(['status' => 400, 'message' => "$type Marksheet is required!"]);
          exit();
        } else {
          $marksheet_path = ''; 
        }
      } else {
        $doc_row = mysqli_fetch_assoc($check_doc);
        $marksheet_path = $doc_row['Location'];
      }
    }

    // Format obtained and max marks safely for SQL (avoids syntax errors with decimals)
    $obtained_sql = ($obtained !== NULL && $obtained !== '') ? "'$obtained'" : "NULL";
    $max_sql = ($max_marks !== NULL && $max_marks !== '') ? "'$max_marks'" : "NULL";

    // Save or Update Student Academics Details
    $check_acad = $conn->query("SELECT ID FROM Student_Academics WHERE Student_ID = $inserted_id AND Type = '$type'");
    if ($check_acad->num_rows > 0) {
      $update_details = $conn->query("UPDATE Student_Academics SET `Year` = '$year', Subject = '$subject', `Board/Institute` = '$board', `Marks_Obtained` = $obtained_sql, `Max_Marks` = $max_sql, `Total_Marks` = '$total' WHERE Student_ID = $inserted_id AND Type = '$type'");
    } else {
      $update_details = $conn->query("INSERT INTO Student_Academics (Student_ID, Type, `Year`, Subject, `Board/Institute`, `Marks_Obtained`, `Max_Marks`, `Total_Marks`) VALUES ($inserted_id, '$type', '$year', '$subject', '$board', $obtained_sql, $max_sql, '$total')");
    }

    if (!$update_details) {
      $overall_success = false;
      break;
    }

    // Save or Update Documents if path is available
    if (!empty($marksheet_path)) {
      $check_doc_exists = $conn->query("SELECT ID FROM Student_Documents WHERE Student_ID = $inserted_id AND Type = '$type'");
      if ($check_doc_exists->num_rows > 0) {
        $conn->query("UPDATE Student_Documents SET Location = '$marksheet_path' WHERE Student_ID = $inserted_id AND Type = '$type'");
      } else {
        $conn->query("INSERT INTO Student_Documents (Student_ID, Type, Location) VALUES ($inserted_id, '$type', '$marksheet_path')");
      }
    }
  }

  if ($overall_success) {
    if ($current_step < 3) {
      $conn->query("UPDATE Students SET Step = 3 WHERE ID = $inserted_id");
    }
    echo json_encode(['status' => 200, 'message' => 'Step 3 details saved successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong while saving details!']);
  }
}