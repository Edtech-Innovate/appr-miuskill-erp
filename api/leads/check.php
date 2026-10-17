<?php
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST,GET');
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
    exit(json_encode(["status" => false, "message" => "Vertical not found!"]));
  }

  if(isset($_GET['mobile'])){

    $mobile = array_key_exists('mobile', $data) ? $data['mobile'] : '';

    if(empty($mobile)){
      exit(json_encode(['status'=>true, 'message'=>'Mobile is required!']));
    }

    if (strlen(filter_var($mobile, FILTER_SANITIZE_NUMBER_INT)) < 10) {
      echo json_encode(array("status" => true, "message" => "Not a valid mobile!"));
      exit();
    }
    
    if(!validateMobile($mobile)){
      $response = json_encode(['status'=>true, "message"=>"Not a valid mobile!"]);
      exit($response);
    }

    // Check Mobile
    $check = $conn->query("SELECT Lead_Status.ID, Lead_Status.Stage_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Leads.Mobile = '$mobile' AND Lead_Status.University_ID = $university_id");
    if($check->num_rows>0){
      exit(json_encode(['status'=>true, 'message'=>'Mobile already exist!']));
    }else{
      exit(json_encode(['status'=>false, 'message'=>'Mobile not exists!']));
    }
  }

  if(isset($_GET['email'])){

    $email = array_key_exists('email', $data) ? $data['email'] : '';

    if(empty($email)){
      exit(json_encode(['status'=>true, "message" => "Email is required!"]));
    }

    // Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['status' => true, 'message' => 'Not a valid email!']);
      exit();
    }

    // Check Email
    $check = $conn->query("SELECT Lead_Status.ID, Lead_Status.Stage_ID FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Leads.Email LIKE '$email' AND Lead_Status.University_ID = $university_id");
    if($check->num_rows>0){
      exit(json_encode(['status'=>true, 'message'=>'Email already exist!']));
    }else{
      exit(json_encode(['status'=>false, 'message'=>'Email not exists!']));
    }
  }
