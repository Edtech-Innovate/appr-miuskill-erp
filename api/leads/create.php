<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST,GET');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (empty($data)) {
  exit(json_encode(['status' => false, 'message' => 'Invalid Request!']));
}

// DB
require '../../includes/db-config.php';

$error = array();

$name = array_key_exists('name', $data) ? mysqli_real_escape_string($conn, $data['name']) : '';
if (empty($name)) {
  exit(json_encode(['status' => false, 'message' => 'Name is required!']));
}

$mobile = array_key_exists('mobile', $data) ? mysqli_real_escape_string($conn, $data['mobile']) : '';
if (empty($mobile)) {
  exit(json_encode(['status' => false, 'message' => 'Mobile is required!']));
}

// Email
$email = array_key_exists('email', $data) ? mysqli_real_escape_string($conn, $data['email']) : '';
if (empty($email)) {
  exit(json_encode(['status' => false, 'message' => 'Email is required!']));
}

$course = array_key_exists('course', $data) ? mysqli_real_escape_string($conn, $data['course']) : '';
if (empty($course)) {
  exit(json_encode(['status' => false, 'message' => 'Course is required!']));
}

$requested_source = array_key_exists('source', $data) ? mysqli_real_escape_string($conn, $data['source']) : '';
if (empty($requested_source)) {
  exit(json_encode(['status' => false, 'message' => 'Source is required!']));
}

$university = array_key_exists('key', $data) ? mysqli_real_escape_string($conn, $data['key']) : '';
if (empty($university)) {
  exit(json_encode(['status' => false, 'message' => 'University is required!']));
}

// Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['status' => false, 'message' => 'Not a valid email!']);
  exit();
}

// Mobile
if (strlen(filter_var($mobile, FILTER_SANITIZE_NUMBER_INT)) < 10) {
  echo json_encode(array("status" => false, "message" => "Not a valid mobile!"));
  exit();
}

if (!validateMobile($mobile)) {
  $response = json_encode(['status' => false, "message" => "Not a valid mobile!"]);
  exit($response);
}

// University
$university = $conn->query("SELECT ID, ID_Suffix, Max_Character FROM Universities WHERE Api_Key = '$university'");
if ($university->num_rows > 0) {
  $university = $university->fetch_assoc();
  $suffix = $university['ID_Suffix'];
  $characters = $university['Max_Character'];
  $university_id = $university['ID'];
} else {
  exit(json_encode(["status" => false, "message" => "University not found!"]));
}

// Course
$course = $conn->query("SELECT ID FROM Courses WHERE ID = '$course' AND University_ID = $university_id");
if ($course->num_rows == 0) {
  exit(json_encode(["status" => false, "message" => "Course not found!"]));
}

$course = $course->fetch_assoc();
$course_id = $course['ID'];

// Sub-Course
$sub_course = array_key_exists('sub_course', $data) ? mysqli_real_escape_string($conn, $data['sub_course']) : '';
if (!empty($sub_course)) {
  $sub_course = $conn->query("SELECT ID FROM Sub_Courses WHERE ID = $sub_course AND Course_ID = $course_id AND University_ID = $university_id");
  if ($sub_course->num_rows > 0) {
    $sub_course = $sub_course->fetch_assoc();
  } else {
    $sub_course = $conn->query("SELECT ID FROM Sub_Courses WHERE Course_ID = $course_id AND University_ID = $university_id");
    $sub_course = $sub_course->fetch_assoc();
  }
} else {
  $sub_course = $conn->query("SELECT ID FROM Sub_Courses WHERE Course_ID = $course_id AND University_ID = $university_id");
  $sub_course = $sub_course->fetch_assoc();
}

$sub_course_id = $sub_course['ID'];


// Source
$source = $conn->query("SELECT ID FROM Sources WHERE Name LIKE '$requested_source'");
if ($source->num_rows == 0) {
  $source = $conn->query("INSERT INTO Sources (`Name`) VALUES ('$requested_source')");
  $source_id = $conn->insert_id;
} else {
  $source = $source->fetch_assoc();
  $source_id = $source['ID'];
}

// Sub-Source
$sub_source_id = 'NULL';
$requested_sub_source = array_key_exists('sub_source', $data) ? mysqli_real_escape_string($conn, $data['sub_source']) : '';
if (!empty($requested_sub_source)) {
  $sub_source = $conn->query("SELECT ID FROM Sub_Sources WHERE Name LIKE '$requested_sub_source'");
  if ($sub_source->num_rows > 0) {
    $sub_source = $sub_source->fetch_assoc();
    $sub_source_id = $sub_source['ID'];
  } else {
    $sub_source_id = 'NULL';
  }
}

// Country
$country_id = 'NULL';
$requested_country = array_key_exists('country', $data) ? mysqli_real_escape_string($conn, $data['country']) : '';
if (!empty($requested_country)) {
  $country = $conn->query("SELECT ID FROM Countries WHERE Name LIKE '$requested_country'");
  if ($country->num_rows > 0) {
    $country_id = $country->fetch_assoc();
    $country_id = $country_id['ID'];
  } else {
    $country_id = 'NULL';
  }
}

// State
$state_id = 'NULL';
$requested_state = array_key_exists('state', $data) ? mysqli_real_escape_string($conn, $data['state']) : '';
if (!empty($requested_state)) {
  $state = $conn->query("SELECT ID FROM States WHERE Name LIKE '$requested_state'");
  if ($state->num_rows > 0) {
    $state_id = $state->fetch_assoc();
    $state_id = $state_id['ID'];
  } else {
    $state_id = 'NULL';
  }
}

// City
$city_id = 'NULL';
$requested_city = array_key_exists('city', $data) ? mysqli_real_escape_string($conn, $data['city']) : '';
if (!empty($requested_city)) {
  $city = $conn->query("SELECT ID FROM Cities WHERE Name LIKE '$requested_city'");
  if ($city->num_rows > 0) {
    $city_id = $city->fetch_assoc();
    $city_id = $city_id['ID'];
  } else {
    $city_id = 'NULL';
  }
}

// Stage
$stage = $conn->query("SELECT ID FROM Stages WHERE Is_First = 1");
$stage = $stage->fetch_assoc();
$stage_id = $stage['ID'];

// Re-Enquired Stage
$re_enquired_stage = $conn->query("SELECT ID FROM Stages WHERE Is_ReEnquired = 1");
$re_enquired_stage = $re_enquired_stage->fetch_assoc();
$re_enquired_stage_id = $re_enquired_stage['ID'];

// Reason
$reason = $conn->query("SELECT ID FROM Reasons WHERE Stage_ID = $stage_id");
if ($reason->num_rows > 0) {
  $reason = $reason->fetch_assoc();
  $reason_id = $reason['ID'];
} else {
  $reason_id = 'NULL';
}

// Checks

// Check Mobile & Email
$check = $conn->query("SELECT Lead_Status.ID, Lead_Status.Stage_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Leads.Email LIKE '$email' AND Leads.Mobile = '$mobile' AND Lead_Status.University_ID = $university_id");
if ($check->num_rows > 0) {
  exit(json_encode(['status' => false, 'message' => 'Email and Mobile already exist!']));
}

// Check Mobile
$check = $conn->query("SELECT Lead_Status.ID, Lead_Status.Stage_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Leads.Mobile = '$mobile' AND Lead_Status.University_ID = $university_id");
if ($check->num_rows > 0) {
  exit(json_encode(['status' => false, 'message' => 'Mobile already exist!']));
}

// Check Email
$check = $conn->query("SELECT Lead_Status.ID, Lead_Status.Stage_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Leads.Email LIKE '$email' AND Lead_Status.University_ID = $university_id");
if ($check->num_rows > 0) {
  exit(json_encode(['status' => false, 'message' => 'Email already exist!']));
}

// Add
$student_id = generateStudentID($conn, $suffix, $characters, $university_id);

// University Head
$owner = array_key_exists('owner', $data) ? mysqli_real_escape_string($conn, $data['owner']) : '';
if (!empty($owner)) {
  $user = $conn->query("SELECT Users.ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Users.Role = 'University Head' AND University_User.University_ID = $university_id");
} else {
  $user = $conn->query("SELECT Users.ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Code = '$owner' AND University_User.University_ID = $university_id");
}

if ($user->num_rows > 0) {
  $user = $user->fetch_assoc();
  $user_id = $user['ID'];
} else {
  $user_id = 1;
}

// Check Lead
$check = $conn->query("SELECT ID FROM Leads WHERE Leads.Email LIKE '$email' AND Leads.Mobile = '$mobile'");
if ($check->num_rows > 0) {
  $lead = $check->fetch_assoc();
  $add_lead_status = $conn->query("INSERT INTO Lead_Status (`Lead_ID`, `Unique_ID`, `University_ID`, `Course_ID`, `Sub_Course_ID`, `Stage_ID`, `Reason_ID`, `User_ID`) VALUES (" . $lead['ID'] . ", '$student_id', $university_id, $course_id, $sub_course_id, $stage_id, $reason_id, $user_id)");
  if ($add_lead_status) {
    exit(json_encode(['status' => true, 'message' => 'Lead created successfully!', 'student_id' => $student_id]));
  } else {
    exit(json_encode(['status' => false, 'message' => 'Something went wrong!', 'student_id' => '']));
  }
}

$check = $conn->query("SELECT ID FROM Leads WHERE Leads.Email LIKE '$email'");
if ($check->num_rows > 0) {
  $lead = $check->fetch_assoc();
  $update_lead = $conn->query("UPDATE Leads SET Alternate_Mobile = '$mobile' WHERE ID = " . $lead['ID']);
  $add_lead_status = $conn->query("INSERT INTO Lead_Status (`Lead_ID`, `Unique_ID`, `University_ID`, `Course_ID`, `Sub_Course_ID`, `Stage_ID`, `Reason_ID`, `User_ID`) VALUES (" . $lead['ID'] . ", '$student_id', $university_id, $course_id, $sub_course_id, $stage_id, $reason_id, $user_id)");
  if ($add_lead_status) {
    exit(json_encode(['status' => true, 'message' => 'Lead created successfully!', 'student_id' => $student_id]));
  } else {
    exit(json_encode(['status' => false, 'message' => 'Something went wrong!', 'student_id' => '']));
  }
}


$check = $conn->query("SELECT ID FROM Leads WHERE Leads.Mobile = '$mobile'");
if ($check->num_rows > 0) {
  $lead = $check->fetch_assoc();
  $update_lead = $conn->query("UPDATE Leads SET Alternate_Email = '$email' WHERE ID = " . $lead['ID']);
  $add_lead_status = $conn->query("INSERT INTO Lead_Status (`Lead_ID`, `Unique_ID`, `University_ID`, `Course_ID`, `Sub_Course_ID`, `Stage_ID`, `Reason_ID`, `User_ID`) VALUES (" . $lead['ID'] . ", '$student_id', $university_id, $course_id, $sub_course_id, $stage_id, $reason_id, $user_id)");
  if ($add_lead_status) {
    exit(json_encode(['status' => true, 'message' => 'Lead created successfully!', 'student_id' => $student_id]));
  } else {
    exit(json_encode(['status' => false, 'message' => 'Something went wrong!', 'student_id' => '']));
  }
}

$add_lead = $conn->query("INSERT INTO Leads (`Name`, Email, Mobile, Source_ID, Sub_Source_ID, City_ID, State_ID, Country_ID) VALUES ('$name', '$email', '$mobile', $source_id, $sub_source_id, $city_id, $state_id, $country_id)");
if ($add_lead) {
  $lead_id = $conn->insert_id;
  $add_lead_status = $conn->query("INSERT INTO Lead_Status (`Lead_ID`, `Unique_ID`, `University_ID`, `Course_ID`, `Sub_Course_ID`, `Stage_ID`, `Reason_ID`, `User_ID`) VALUES ($lead_id, '$student_id', $university_id, $course_id, $sub_course_id, $stage_id, $reason_id, $user_id)");
  if ($add_lead_status) {
    $leadStatusId = $conn->insert_id;

    // Send Welcome Mail
    $mail = $conn->query("SELECT Subject as subject, Template as body, Attachments FROM Email_Templates WHERE University_ID = $university_id AND Name = 'Welcome'");
    if ($mail->num_rows > 0) {
      $mail = $mail->fetch_assoc();

      // Sender
      $university = $conn->query("SELECT Name as name, Email as email FROM Universities WHERE ID = $university_id");
      $sender = $university->fetch_assoc();

      $studentDetails = $conn->query("SELECT DATE_FORMAT(CURDATE(),'%d-%m-%Y') as `{{ current_date }}`, Lead_Status.Unique_ID AS`{{ student_id }}`,UPPER(Leads.`Name`)AS`{{ student_name }}`,Leads.Mobile as`{{ student_mobile }}`,Leads.Email as`{{ student_email }}`,UPPER(DATE_FORMAT(Students.DOB,'%d%b%Y'))as`{{ student_password }}`,Courses.`Name` as`{{ program }}`,Sub_Courses.`Name` as`{{ specialization }}`,Universities.`Name` as`{{ university_name }}`,Universities.Vertical as`{{ university_vertical }}` FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID=Leads.ID LEFT JOIN Universities ON Lead_Status.University_ID=Universities.ID LEFT JOIN Courses ON Lead_Status.Course_ID=Courses.ID LEFT JOIN Sub_Courses ON Lead_Status.Sub_Course_ID=Sub_Courses.ID LEFT JOIN Students ON Lead_Status.ID=Students.Lead_Status_ID WHERE Lead_Status.ID=$leadStatusId");
      if ($studentDetails->num_rows > 0) {
        $studentDetails = $studentDetails->fetch_assoc();
        $receivers[] = array('email' => $studentDetails['{{ student_email }}'], 'name' => $studentDetails['{{ student_name }}']);
        foreach ($studentDetails as $key => $value) {
          $mail['body'] = str_replace($key, $value, $mail['body']);
          $mail['subject'] = str_replace($key, $value, $mail['subject']);
        }
      }
    }

    sendMail($sender, $receivers, $mail);

    exit(json_encode(['status' => true, 'message' => 'Lead created successfully!', 'student_id' => $student_id]));
  } else {
    exit(json_encode(['status' => false, 'message' => 'Something went wrong!', 'student_id' => '']));
  }
}
