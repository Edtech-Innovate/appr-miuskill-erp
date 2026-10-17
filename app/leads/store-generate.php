<?php
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['user']) && isset($_POST['course']) && isset($_POST['sub_course'])) {
  require '../../includes/db-config.php';
  session_start();

  $user = intval($_POST['user']);
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
  $course = intval($_POST['course']);
  $sub_course = intval($_POST['sub_course']);
  $source = "";
  $university_id = $_SESSION['university_id'];

  if (empty($name) || empty($mobile) || empty($email) || empty($university_id) || empty($course) || empty($sub_course)) {
    echo json_encode(['status' => 302, 'message' => "Name, Email, Mobile, & University fields are required!"]);
    exit();
  }

  if(strlen($mobile)<10){
    $response = json_encode(['status'=>302, "message"=>"Invalid mobile!"]);
    exit($response);
  }

  if(!validateMobile($mobile)){
    $response = json_encode(['status'=>false, "message"=>"Mobile No. should start with 6,7,8 & 9!"]);
    exit($response);
  }

  $has_student_id = $conn->query("SELECT ID_Suffix, Max_Character FROM Universities WHERE ID = $university_id AND Has_Unique_StudentID = 1");
  if ($has_student_id->num_rows == 0) {
    exit(json_encode(['status' => 400, 'message' => "Please configure Student ID!"]));
  }

  $has_student_id = $has_student_id->fetch_assoc();

  // Student ID
  $student_id = generateStudentID($conn, $has_student_id['ID_Suffix'], $has_student_id['Max_Character'], $_SESSION['university_id']);

  $get_first_stage = $conn->query("SELECT ID FROM Stages WHERE Is_First = 1");
  if ($get_first_stage->num_rows > 0) {
    $stage = mysqli_fetch_assoc($get_first_stage);
    $stage = $stage['ID'];
  } else {
    echo json_encode(['status' => 302, 'message' => 'Please configure lead stage!']);
    exit();
  }

  $university_id_query = " AND Lead_Status.University_ID = $university_id";

  if (!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['status' => 302, 'message' => 'Invalid email.']);
      exit();
    }
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Email LIKE '$email' OR Alternate_Email LIKE '$email') $university_id_query");
    if ($check->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'Email already exists!']);
      exit();
    }
  }else{
    echo json_encode(['status' => 302, 'message' => 'Invalid email.']);
    exit();
  }

  if (!empty($mobile)) {
    $check = $conn->query("SELECT Leads.ID FROM Leads LEFT JOIN Lead_Status ON Leads.ID = Lead_Status.Lead_ID WHERE (Mobile LIKE '$mobile' OR Alternate_Mobile LIKE '$mobile') $university_id_query");
    if ($check->num_rows > 0) {
      echo json_encode(['status' => 302, 'message' => 'Mobile already exists!']);
      exit();
    }
  }

  if (!empty($stage)) {
    $reason = $conn->query("SELECT ID FROM Reasons WHERE Stage_ID = $stage LIMIT 1");
    if ($reason->num_rows > 0) {
      $reason = mysqli_fetch_assoc($reason);
      $reason = $reason['ID'];
    } else {
      $reason = 'NULL';
    }
  }

  if (empty($source)) {
    $get_source = $conn->query("SELECT ID FROM Sources WHERE Name LIKE 'ERP'");
    if ($get_source->num_rows > 0) {
      $source = mysqli_fetch_assoc($get_source);
      $source = $source['ID'];
    } else {
      $source = $conn->query("INSERT INTO Sources (`Name`) VALUES ('ERP')");
      $source = $conn->insert_id;
    }
  }

  $column = "";
  $sub_source = "";

  $check = $conn->query("SELECT ID FROM Leads WHERE Email LIKE '$email' OR (Mobile LIKE '%$mobile' OR Alternate_Mobile LIKE '%$mobile')");
  if ($check->num_rows > 0) {
    $lead  = mysqli_fetch_assoc($check);
    $add_lead = true;
    $id = $lead['ID'];
  } else {
    $add_lead = $conn->query("INSERT INTO Leads (`Name`, `Email`, `Mobile`, `Source_ID`, `Created_By` $column) VALUES ('$name', '$email', '$mobile', $source, " . $_SESSION['ID'] . " $sub_source)");
    $id = $conn->insert_id;
  }
  if ($add_lead) {
    $add_lead_status = $conn->query("INSERT INTO Lead_Status (`Lead_ID`, `University_ID`, `Course_ID`, `Sub_Course_ID`, `Stage_ID`, `Reason_ID`, `User_ID`, `Unique_ID`) VALUES ($id, $university_id, $course, $sub_course, $stage, $reason, $user, '$student_id')");
    if ($add_lead_status) {
      $lead_id = $conn->insert_id;
      echo json_encode(['status' => 200, 'message' => 'Lead created successfully!', 'student_id' => $student_id, 'id'=> base64_encode("W1Ebt1IhGN3ZOLplom9I" . $lead_id . "W1Ebt1IhGN3ZOLplom9I")]);
    } else {
      echo json_encode(['status' => 302, 'message' => "Something went wrong!"]);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => "Something went wrong!"]);
  }
} else {
  echo json_encode(['status' => 302, 'message' => 'All fields are required!']);
}
