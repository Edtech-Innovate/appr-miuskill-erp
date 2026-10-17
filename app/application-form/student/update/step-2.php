<?php
if (isset($_POST['email'])) {
  require '../../../../includes/db-config.php';
  session_start();

  $inserted_id = $_SESSION['Student_Table_ID'];

  if (empty($inserted_id)) {
    echo json_encode(['status' => 400, 'message' => 'ID is required.']);
    exit();
  }

  $step = $conn->query("SELECT Step FROM Students WHERE ID = $inserted_id");
  $step = mysqli_fetch_array($step);
  $step = $step['Step'];

  $step_query = "";
  if ($step < 2) {
    $step_query = ", `Step` = 2";
  }

  $emailQuery = "";
  $contactQuery = "";
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 400, 'message' => 'Invalid Email!']);
    exit();
  }
  $email = strtolower($email);
  $emailQuery = ", Email = '$email'";

  $contact = mysqli_real_escape_string($conn, $_POST['contact']);
  if (!validateMobile($contact)) {
    echo json_encode(['status' => 400, 'message' => 'Invalid Mobile!']);
    exit();
  }
  $contactQuery = ", Contact = '$contact'";

  $alternate_email = mysqli_real_escape_string($conn, $_POST['alternate_email']);
  if (!empty($alternate_email) && !filter_var($alternate_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 400, 'message' => 'Invalid Alternate Email!']);
    exit();
  }

  $alternate_email = strtolower($alternate_email);

  $alternate_contact = mysqli_real_escape_string($conn, $_POST['alternate_contact']);
  if (!empty($alternate_contact) && !validateMobile($alternate_contact)) {
    echo json_encode(['status' => 400, 'message' => 'Invalid Alternate Mobile!']);
    exit();
  }

  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $address = strtoupper(strtolower($address));
  $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
  $city = mysqli_real_escape_string($conn, $_POST['city']);
  $city = strtoupper(strtolower($city));
  $district = mysqli_real_escape_string($conn, $_POST['district']);
  $district = strtoupper(strtolower($district));
  $state = mysqli_real_escape_string($conn, $_POST['state']);
  $state = strtoupper(strtolower($state));

  // Lead Update
  $leadStatusId = $conn->query("SELECT Lead_Status_ID FROM Students WHERE ID = " . $_SESSION['Student_Table_ID']);
  $leadStatusId = $leadStatusId->fetch_assoc();
  $leadStatusId = $leadStatusId['Lead_Status_ID'];
  $conn->query("UPDATE Leads SET Email = '$email', Mobile = '$contact' WHERE ID = (SELECT Lead_ID FROM Lead_Status WHERE ID = $leadStatusId)");

  $address = json_encode(['present_address' => $address, 'present_pincode' => $pincode, 'present_city' => $city, 'present_district' => $district, 'present_state' => $state]);

  $update = $conn->query("UPDATE Students SET  Alternate_Email = '$alternate_email', Alternate_Contact = '$alternate_contact', Address = '$address' $emailQuery $contactQuery $step_query WHERE ID = $inserted_id");

  if ($update) {
    $_SESSION['Step'] = 3;
    echo json_encode(['status' => 200, 'message' => 'Step 2 details saved successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
