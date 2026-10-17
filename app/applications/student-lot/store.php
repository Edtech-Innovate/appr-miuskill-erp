<?php
  if(isset($_POST['id']) && $_POST['lot_date']  && $_POST['student_lot']){
    require '../../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $lot_date = mysqli_real_escape_string($conn, $_POST['lot_date']);
    $student_lot = intval($_POST['student_lot']);

    if(empty($lot_date)){
      echo json_encode(['status'=>400, 'message'=>'Lot Date  is required.']);
      exit();
    }
    
    if(empty($student_lot)){
      echo json_encode(['status'=>400, 'message'=>'Student Lot No. is required.']);
      exit();
    }


    $update = $conn->query("UPDATE Students SET lot_date = '$lot_date', student_lot= $student_lot  WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Student Lot No. alloted successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
