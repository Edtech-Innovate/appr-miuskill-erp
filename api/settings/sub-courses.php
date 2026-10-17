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

  
  $course_id = array_key_exists('course', $data) ? $data['course'] : '';
  if(empty($course_id)){
    http_response_code(400);
    exit(json_encode(["status" => false, "message" => "Course is required!"]));
  }

  $options = array();
  $sub_courses = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE University_ID = $university_id AND Course_ID = $course_id");
  if($sub_courses->num_rows==0){
    http_response_code(404);
    exit(json_encode(['status'=>false, 'message'=>'Sub-Course not exists!']));
  }

  while($sub_course = $sub_courses->fetch_assoc()){
    $options[$sub_course['ID']] = $sub_course['Name'];
  }

  echo json_encode(['status'=>true, 'options'=>$options]);
