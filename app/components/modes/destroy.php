<?php
  if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])){
    require '../../../includes/db-config.php';
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $check = $conn->query("SELECT ID, University_ID, LMS_ID FROM Modes WHERE ID = $id");
    if($check->num_rows>0){
      $check = $check->fetch_assoc();
      $delete = $conn->query("DELETE FROM Modes WHERE ID = $id");
      if($delete){
        echo json_encode(['status'=>200, 'message'=>'Mode deleted successfully!']);
      }else{
        echo json_encode(['status'=>302, 'message'=>'Mode is associated!']);
      }
    }else{
      echo json_encode(['status'=>302, 'message'=>'Mode not exists!']);
    }
  }
