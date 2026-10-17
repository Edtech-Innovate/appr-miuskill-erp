<?php
  if($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])){
    require '../../../includes/db-config.php';
    session_start();
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $attachments = $conn->query("SELECT ID, Attachments FROM Email_Templates WHERE ID = $id");
    if($attachments->num_rows>0){
      $attachments = $attachments->fetch_assoc();
      $attachments = !empty($attachments['Attachments']) ? json_decode($attachments['Attachments'], true) : array();
      if(count(array_filter($attachments))>0){
        foreach($attachments as $attachment){
          unlink("../../..".$attachment['path']);
        }
      }
      
      $delete = $conn->query("DELETE FROM Email_Templates WHERE ID = $id");
      if($delete){
        echo json_encode(['status'=>200, 'message'=>'Template deleted successfully!']);
      }else{
        echo json_encode(['status'=>302, 'message'=>'Somethong went wrong!']);
      }
    }else{
      echo json_encode(['status'=>302, 'message'=>'Template not exists!']);
    }
  }
