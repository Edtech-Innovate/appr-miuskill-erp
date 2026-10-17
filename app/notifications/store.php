<?php
  if(isset($_POST['send_to']) && isset($_POST['heading'])){
    require '../../includes/db-config.php';
    session_start();

    $heading = mysqli_real_escape_string($conn, $_POST['heading']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $send_to = mysqli_real_escape_string($conn, $_POST['send_to']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);

    if(empty($heading) || empty($content) || empty($send_to) || empty($date)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }
    
    if(isset($_FILES["file"]["name"]) && $_FILES["file"]["name"]!=''){
      $temp = explode(".", $_FILES["file"]["name"]);
      $filename = round(microtime(true)) .'-'.$send_to.'.'.end($temp);
      $tempname = $_FILES["file"]["tmp_name"];
      $folder = "../../uploads/notifications/".$filename; 
      if(is_uploaded_file($tempname)){ 
        move_uploaded_file($tempname,$folder);
        $filename = "/uploads/notifications/".$filename;
      }else{
        echo json_encode(['status'=>400, 'message'=>'Unable to save file!']);
        exit();
      }
    }else{
      $filename = "";
    }

    $add = $conn->query("INSERT INTO `Notifications_Generated` (`Heading`, `Content`, `Send_To`, `Noticefication_Created_on`, `Attachment`) VALUES ('$heading', '$content', '$send_to', '$date', '".$filename."') ");
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Notification added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
