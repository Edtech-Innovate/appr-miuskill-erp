<?php
  if(isset($_POST['name']) && isset($_POST['university'])){

    include '../../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $template = mysqli_real_escape_string($conn, $_POST['template']);
    $university = intval($_POST['university']);
    
    if(empty($template)){
      echo json_encode(['status'=>302, 'message'=>"Template can't be empty!"]);
      exit();
    }

    if(empty($university) || empty($template) || empty($name)){
      echo json_encode(['status'=>302, 'message'=>'All fields are required.']);
      exit();
    }

    $check = $conn->query("SELECT ID FROM WhatsApp_Templates WHERE `Name` LIKE '$name' AND University_ID = $university");
    if($check->num_rows>0){
      echo json_encode(['status'=>302, 'message'=>'Template already exists!']);
      exit();
    }else{
      $add = $conn->query("INSERT INTO WhatsApp_Templates (`Name`, `University_ID`, `Template`) VALUES ('$name', '$university', '$template')");
      if($add){
        echo json_encode(['status'=>200, 'message'=>'Template added successfully!']);
      }else{
        echo json_encode(['status'=>400, 'message'=>mysqli_error($conn)]);
      }
    }
  }
