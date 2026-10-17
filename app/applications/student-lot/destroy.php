<?php
  if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])){
    require '../../../includes/db-config.php';
    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

    $update = $conn->query("UPDATE Students SET student_lot = NULL , lot_date=NULL WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>'Student Lot No deleted successfully!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }

  }
