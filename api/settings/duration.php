<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET');
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header('Content-Type: application/json; charset=utf-8');

  $data = file_get_contents('php://input');
  $data = json_decode($data, true);

  // DB
  require '../../includes/db-config.php';

  $university = array_key_exists('university', $data) ? $data['university'] : '';
  $vertical = array_key_exists('vertical', $data) ? $data['vertical'] : '';

  if(empty($university) || empty($vertical)){
    exit(json_encode(['status'=>false, 'message'=>'University is missing!']));
  }

  $admission_type_id = array_key_exists('admission_type', $data) ? $data['admission_type'] : '';
  $sub_course_id = array_key_exists('sub_course', $data) ? $data['sub_course'] : '';

  if(empty($admission_type_id) || empty($sub_course_id)){
    exit(json_encode(['status'=>false, 'message'=>'Madatory Fields cannot be empty!']));
  }

  $university = $conn->query("SELECT ID FROM Universities WHERE (Name LIKE '$university' OR Short_Name LIKE '$university') AND Vertical LIKE '$vertical' AND Has_Unique_StudentID = 1");
  if ($university->num_rows > 0) {
    $university = $university->fetch_assoc();
    $university_id = $university['ID'];
  } else {
    http_response_code(404);
    exit(json_encode(["status" => false, "message" => "University not found!"]));
  }

  $admission_type = $conn->query("SELECT Name FROM Admission_Types WHERE ID = $admission_type_id");
  $admission_type = mysqli_fetch_assoc($admission_type);
  $admission_type = $admission_type['Name'];

  $column = "1";
  if(strcasecmp($admission_type, 'lateral')==0){
    $column = "LE_Start";
  }
  if(strcasecmp($admission_type, 'credit transfer')==0){
    $column = "CT_Start";
  }

  $duration = $conn->query("SELECT $column FROM Sub_Courses WHERE ID = $sub_course_id");
  $duration = mysqli_fetch_assoc($duration);
  $duration = $duration[$column];

  $durations = explode(',', $duration);

  $options = [];
  foreach($durations as $duration){
    $options[$duration] = $duration;
  }
  
  echo json_encode(['status'=>true, 'options'=>$options]);
