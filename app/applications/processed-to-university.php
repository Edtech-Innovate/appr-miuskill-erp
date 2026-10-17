<?php
  if(isset($_POST['id'])){
    require '../../includes/db-config.php';
    session_start();

    if($_SESSION['Role']!='Center'){
      $id = mysqli_real_escape_string($conn, $_POST['id']);
      $id = base64_decode($id);
      $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

      $update = $conn->query("UPDATE Students SET Processed_To_University = now() WHERE ID = $id");
      if($update){
        echo json_encode(['status'=>200, 'message'=>'Application is processed!']);
      }else{
        echo json_encode(['status'=>400, 'message'=>'Sorry, Something went wrong!']);
      }
    }else{
      echo json_encode(['status'=>403, 'message'=>'You are not authorized!']);
    }
  }
