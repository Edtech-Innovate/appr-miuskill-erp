<?php
  if(isset($_POST['name']) && isset($_POST['university']) && isset($_POST['id'])){

    include '../../../includes/db-config.php';
    session_start();
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $university = intval($_POST['university']);
    $template = mysqli_real_escape_string($conn, $_POST['template']);
    
    if(empty($template)){
      echo json_encode(['status'=>302, 'message'=>"Template can't be empty!"]);
      exit();
    }

    if(empty($id) || empty($university) || empty($template) || empty($name)){
      echo json_encode(['status'=>302, 'message'=>'All fields are required.']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM WhatsApp_Templates WHERE `Name` LIKE '$name' AND University_ID = $university AND ID <> $id");
    if($check->num_rows>0){
      echo json_encode(['status'=>302, 'message'=>'Template already exists!']);
      exit();
    }else{
      $add = $conn->query("UPDATE WhatsApp_Templates SET `Name` = '$name', `University_ID` = '$university', `Template` = '$template' WHERE ID = $id");
      if($add){
        echo json_encode(['status'=>200, 'message'=>'Template updated successfully!']);
      }else{
        echo json_encode(['status'=>400, 'message'=>mysqli_error($conn)]);
      }
    }
  }
