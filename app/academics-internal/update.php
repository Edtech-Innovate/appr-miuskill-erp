<?php
  if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['id'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      echo json_encode(['status'=>400, "message"=>"Invalid email!"]);
      exit();
    }

    if(empty($name) || empty($email) || empty($contact) || empty($id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }
    
    $photo = '';
    if(isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"]!=''){
      $temp = explode(".", $_FILES["photo"]["name"]);
      $filename = round(microtime(true)) . '.' . end($temp);
      $tempname = $_FILES["photo"]["tmp_name"];
      $folder = "../../assets/img/academics-internal/".$filename; 
      if(move_uploaded_file($tempname, $folder)){ 
        $filename = "/assets/img/academics-internal/".$filename;
      }else{
        echo json_encode(['status'=>403, 'message'=>'Unable to save photo!']);
        exit();
      }
      $photo = " , Photo = '$filename'";
    }

    $add = $conn->query("UPDATE `Users` SET `Name` = '$name', `Email` = '$email', `Mobile` = '$contact' $photo WHERE ID = $id");
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Academics Internal updated successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
