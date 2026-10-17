<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST,GET');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

$data = file_get_contents('php://input');
$data = json_decode($data, true);

// DB
require '../../includes/db-config.php';

if (empty($data)) {  
  exit(json_encode(["status" => false, "message" => "Data cannot be empty!"]));
}

$username = array_key_exists('username', $data) ? $data['username'] : '';
$password = array_key_exists('password', $data) ? $data['password'] : '';

$university = array_key_exists('university', $data) ? mysqli_real_escape_string($conn, $data['university']) : '';
if (empty($university)) {  
  exit(json_encode(['status' => false, 'message'=>'University is required!']));
}

$vertical = array_key_exists('vertical', $data) ? mysqli_real_escape_string($conn, $data['vertical']) : '';
if (empty($vertical)) {
  exit(json_encode(['status' => false, 'message'=>'Vertical is required!']));
}

if(empty($username) || empty($password)) {
  exit(json_encode(["status" => false, "message" => "Username or password cannot be empty!"]));
}

$university = $conn->query("SELECT ID FROM Universities WHERE (Name LIKE '$university' OR Short_Name LIKE '$university') AND Vertical LIKE '$vertical' AND Has_Unique_StudentID = 1");
if ($university->num_rows > 0) {
  $university = $university->fetch_assoc();
  $university_id = $university['ID'];
} else {
  exit(json_encode(["status" => false, "message" => "University not found!"]));
}

$lead = $conn->query("SELECT Lead_Status.ID, Leads.Name, Leads.Email, Leads.Mobile, Lead_Status.Course_ID, Lead_Status.Sub_Course_ID, Lead_Status.University_ID, Lead_Status.Unique_ID, Lead_Status.User_ID, Students.Step, Students.ID as Student_Table_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID LEFT JOIN Students ON Lead_Status.Unique_ID = Students.Unique_ID WHERE Leads.Mobile = '$password' AND Lead_Status.Unique_ID = '$username' AND Lead_Status.University_ID = $university_id");
if($lead->num_rows==0){
  exit(json_encode(["status" => false, "message" => "Invalid credentials!"]));
}

$lead = $lead->fetch_assoc();
echo json_encode(['status'=>true, 'lead'=>$lead]);
