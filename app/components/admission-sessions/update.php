<?php
  if(isset($_POST['name']) && isset($_POST['university_id']) && isset($_POST['id'])){
    require '../../../includes/db-config.php';

    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $schemes = is_array($_POST['scheme']) ? array_filter($_POST['scheme']) : array();
    $start_dates = is_array($_POST['start_date']) ? array_filter($_POST['start_date']) : array();
    $university_id = intval($_POST['university_id']);
    $id = intval($_POST['id']);
    
    if(empty($name) || empty($university_id) || empty($start_dates) || empty($schemes)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM Admission_Sessions WHERE Name LIKE '$name' AND University_ID = $university_id AND ID <> $id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=> $name.' already exists!']);
      exit();
    }

    // Date Check
    $dates = array();
    foreach($schemes as $scheme){
      if(empty($start_dates[$scheme])){
        echo json_encode(['status'=>400, 'message'=> 'Start Date is required!']);
      }else{
        $dates[$scheme] = date("Y-m-d", strtotime($start_dates[$scheme]));
      }
    }

    $schemeData = json_encode(['schemes'=>$schemes, 'dates'=>$dates]);
    
    $add = $conn->query("UPDATE `Admission_Sessions` SET `Name` = '$name', `Scheme` = '$schemeData' WHERE ID = $id");
    if($add){
      echo json_encode(['status'=>200, 'message'=>$name.' added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
