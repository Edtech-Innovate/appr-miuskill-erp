<?php
if (isset($_POST['inserted_id'])) {
  require '../../includes/db-config.php';
  session_start();

  $inserted_id = intval($_POST['inserted_id']);

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
  if (!$_SESSION['crm']) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['status' => 400, 'message' => 'Invalid Email!']);
      exit();
    }
    $email = strtolower($email);
    $emailQuery = ", Email = '$email'";

    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    // if (!validateMobile($contact)) {
    //   echo json_encode(['status' => 400, 'message' => 'Invalid Mobile!']);
    //   exit();
    // }

    $contactQuery = ", Contact = '$contact'";
  }


  $alternate_email = mysqli_real_escape_string($conn, $_POST['alternate_email']);
  if (!empty($alternate_email) && !filter_var($alternate_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 400, 'message' => 'Invalid Alternate Email!']);
    exit();
  }
     $contact = mysqli_real_escape_string($conn, $_POST['contact']);
     if(strlen($contact)>10 || strlen($contact)<10){
      echo json_encode(['status' => 400, 'message' => 'Contact Number Should be 10 digit']);
        exit();
  }
    $existContact = $conn->query("select count(ID) as count from Students where  (Contact='$contact' or Alternate_Contact=$contact) and ID !=$inserted_id ")->fetch_assoc();
  
  if($existContact['count']>0){
      echo json_encode(['status' => 400, 'message' => 'Contact Number already Exists!']);
        exit();
  }
  $alternate_email = strtolower($alternate_email);

  $alternate_contact = mysqli_real_escape_string($conn, $_POST['alternate_contact']);
//   if (!empty($alternate_contact) && !validateMobile($alternate_contact)) {
//     echo json_encode(['status' => 400, 'message' => 'Invalid Alternate Mobile!']);
//     exit();
//   }

  $address = mysqli_real_escape_string($conn, $_POST['address']);
  $address = strtoupper(strtolower($address));
  $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
  $city = mysqli_real_escape_string($conn, $_POST['city']);
  $city = strtoupper(strtolower($city));
  $district = mysqli_real_escape_string($conn, $_POST['district']);
  $district = strtoupper(strtolower($district));
  $state = mysqli_real_escape_string($conn, $_POST['state']);
  $state = strtoupper(strtolower($state));

  $address = json_encode(['present_address' => $address, 'present_pincode' => $pincode, 'present_city' => $city, 'present_district' => $district, 'present_state' => $state]);

  $update = $conn->query("UPDATE Students SET  Alternate_Email = '$alternate_email', Alternate_Contact = '$alternate_contact', Address = '$address' $emailQuery $contactQuery $step_query WHERE ID = $inserted_id");

  if ($update) {
    echo json_encode(['status' => 200, 'message' => 'Step 2 details saved successfully!']);
  } else {
    echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
  }
}
