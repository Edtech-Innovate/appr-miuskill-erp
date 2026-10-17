<?php
  if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])){
    include '../../../includes/db-config.php';
    session_start();
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $check = $conn->query("SELECT ID FROM WhatsApp_Templates WHERE ID = $id");
    if($check->num_rows>0){
      $delete = $conn->query("DELETE FROM WhatsApp_Templates WHERE ID = $id");
      if($delete){
        echo json_encode(['status'=>200, 'message'=>'Template deleted successfully!']);
      }else{
        echo json_encode(['status'=>302, 'message'=>mysqli_error($conn)]);
      }
    }else{
      echo json_encode(['status'=>302, 'message'=>'Template not exists!']);
    }
  }
