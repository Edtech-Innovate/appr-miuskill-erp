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

  $university = $conn->query("SELECT ID FROM Universities WHERE (Name LIKE '$university' OR Short_Name LIKE '$university') AND Vertical LIKE '$vertical' AND Has_Unique_StudentID = 1");
  if ($university->num_rows > 0) {
    $university = $university->fetch_assoc();
    $university_id = $university['ID'];
  } else {
    http_response_code(404);
    exit(json_encode(["status" => false, "message" => "University not found!"]));
  }

  $options = array();
  $admission_sessions = $conn->query("SELECT Admission_Sessions.ID, Admission_Sessions.Name, Admission_Sessions.Current_Status FROM Admission_Sessions WHERE University_ID = $university_id AND Status = 1 ORDER BY Admission_Sessions.Name ASC");
  if($admission_sessions->num_rows==0){
    http_response_code(404);
    exit(json_encode(['status'=>false, 'message'=>'Admission Session not exists!']));
  }

  $selected = "";
  while($admission_session = $admission_sessions->fetch_assoc()){
    $options[$admission_session['ID']] = $admission_session['Name'];
    $selected = $admission_session['Current_Status'] && empty($selected) ? $admission_session['ID'] : "";
  }

  asort($options);

  echo json_encode(['status'=>true, 'options'=>$options, 'selected'=>$selected]);
