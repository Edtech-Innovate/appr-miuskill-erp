<?php
  if(isset($_POST['id'])){
    require '../../includes/db-config.php';

    $id = intval($_POST['id']);
    $allot = array_key_exists('allot', $_POST) ? $_POST['allot'] : [];

    $allot = json_encode($allot);

    $update = $conn->query("UPDATE Courses SET Department_ID = '$allot' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Department(s) alloted successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
