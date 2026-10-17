<?php
if (isset($_POST['inserted_id'])) {
  require '../../includes/db-config.php';
  session_start();

  $inserted_id = intval($_POST['inserted_id']);

  if (empty($inserted_id)) {
    echo json_encode(['status' => 400, 'message' => 'ID is required.']);
    exit();
  }

  // Fetch current Step of the student
  $step_result = $conn->query("SELECT Step FROM Students WHERE ID = $inserted_id");
  if ($step_result && $step_result->num_rows > 0) {
    $student_data = mysqli_fetch_array($step_result);
    $step = $student_data['Step'];
  } else {
    echo json_encode(['status' => 400, 'message' => 'Student not found.']);
    exit();
  }

  // CONDITIONAL DELETE: If step < 1, null, empty, or ''
  if ($step === null || $step === '' || intval($step) <= 2) {
    // Check and delete from Student_Academics
    $checkAcademics = $conn->query("SELECT COUNT(*) as count FROM Student_Academics WHERE Student_ID = $inserted_id")->fetch_assoc();
    if ($checkAcademics['count'] > 0) {
      $conn->query("DELETE FROM Student_Academics WHERE Student_ID = $inserted_id");
    }

    // Check and delete from Student_Documents
    $checkDocuments = $conn->query("SELECT COUNT(*) as count FROM Student_Documents WHERE Student_ID = $inserted_id")->fetch_assoc();
    if ($checkDocuments['count'] > 0) {
      $conn->query("DELETE FROM Student_Documents WHERE Student_ID = $inserted_id");
    }
  }

  $step_query = "";
  if ($step === null || $step === '' || intval($step) < 2) {
    $step_query = ", `Step` = 2";
  }

  $emailQuery = "";
  $contactQuery = "";
  if (!$_SESSION['crm']) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['status' => 400, 'message' => 'Invalid Email!']);
      exit();
    }
    $email = strtolower($email);
    $emailQuery = ", Email = '$email'";

    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $contactQuery = ", Contact = '$contact'";
  }

  $alternate_email = mysqli_real_escape_string($conn, $_POST['alternate_email']);
  if (!empty($alternate_email) && !filter_var($alternate_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 400, 'message' => 'Invalid Alternate Email!']);
    exit();
  }
  
  $contact = mysqli_real_escape_string($conn, $_POST['contact']);
  if (strlen($contact) > 10 || strlen($contact) < 10) {
    echo json_encode(['status' => 400, 'message' => 'Contact Number Should be 10 digit']);
    exit();
  }
  
  $existContact = $conn->query("SELECT count(ID) as count FROM Students WHERE (Contact='$contact' OR Alternate_Contact='$contact') AND ID != $inserted_id")->fetch_assoc();
  
  if ($existContact['count'] > 0) {
    echo json_encode(['status' => 400, 'message' => 'Contact Number already Exists!']);
    exit();
  }
  $alternate_email = strtolower($alternate_email);

  $alternate_contact = mysqli_real_escape_string($conn, $_POST['alternate_contact']);

  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $address = strtoupper($address);
  $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
  $city = mysqli_real_escape_string($conn, $_POST['city']);
  $city = strtoupper($city);
  $district = mysqli_real_escape_string($conn, $_POST['district']);
  $district = strtoupper($district);
  $state = mysqli_real_escape_string($conn, $_POST['state']);
  $state = strtoupper($state);

  $address = json_encode(['present_address' => $address, 'present_pincode' => $pincode, 'present_city' => $city, 'present_district' => $district, 'present_state' => $state]);

  // Update Student details
  $update = $conn->query("UPDATE Students SET Alternate_Email = '$alternate_email', Alternate_Contact = '$alternate_contact', Address = '$address' $emailQuery $contactQuery $step_query WHERE ID = $inserted_id");

  if ($update) {
    echo json_encode(['status' => 200, 'message' => 'Step 2 details saved successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}