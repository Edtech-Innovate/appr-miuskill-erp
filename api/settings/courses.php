<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET');
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header('Content-Type: application/json; charset=utf-8');

  $data = file_get_contents('php://input');
  $data = json_decode($data, true);

  if(empty($data)){
    exit(json_encode(['status'=>false, 'message'=>'Invalid Request!']));
  }

  // DB
  require '../../includes/db-config.php';

  $university = array_key_exists('key', $data) ? $data['key'] : '';

  if(empty($university)){
    exit(json_encode(['status'=>false, 'message'=>'Key is missing!']));
  }

  $university = $conn->query("SELECT ID FROM Universities WHERE Api_Key = '$university'");
  if ($university->num_rows > 0) {
    $university = $university->fetch_assoc();
    $university_id = $university['ID'];
  } else {
    http_response_code(404);
    exit(json_encode(["status" => false, "message" => "Vertical not found!"]));
  }

  $options = array();
  $courses = $conn->query("SELECT Courses.ID, Courses.Name FROM Courses WHERE University_ID = $university_id AND Status = 1 ORDER BY Courses.Name ASC");
  if($courses->num_rows==0){
    http_response_code(404);
    exit(json_encode(['status'=>false, 'message'=>'Course not exists!']));
  }

  while($course = $courses->fetch_assoc()){
    $options[$course['ID']] = $course['Name'];
  }

  echo json_encode(['status'=>true, 'options'=>$options]);
