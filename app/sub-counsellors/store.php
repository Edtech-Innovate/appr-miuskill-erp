<?php
  if(isset($_POST['name']) && isset($_POST['email'])){
    require '../../includes/db-config.php';
    session_start();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $name = ucwords($name);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $reporting = intval($_POST['reporting']);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      echo json_encode(['status'=>400, "message"=>"Invalid email!"]);
      exit();
    }

    if(empty($name) || empty($email) || empty($contact)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    if($_SESSION['Role']=='Administrator'){
      $university_id = $conn->query("SELECT University_ID FROM University_User WHERE `User_ID` = $reporting");
      $university_id = mysqli_fetch_assoc($university_id);
      $university_id = $university_id['University_ID'];
    }else{
      $university_id = $_SESSION['university_id'];
    }

    if(isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"]!=''){
      $temp = explode(".", $_FILES["photo"]["name"]);
      $filename = round(microtime(true)) . '.' . end($temp);
      $tempname = $_FILES["photo"]["tmp_name"];
      $folder = "../../assets/img/sub-counsellors/".$filename; 
      if(move_uploaded_file($tempname, $folder)){ 
        $filename = "/assets/img/sub-counsellors/".$filename;
      }else{
        echo json_encode(['status'=>400, 'message'=>'Unable to save photo!']);
        exit();
      }
    }else{
      $filename = "/assets/img/default-user.png";
    }

    $code = generateUsername($conn, $userNamePrefix);

    $add = $conn->query("INSERT INTO `Users`(`Name`, `Code`, `Email`, `Mobile`, `Password`, `Photo`, `Role`, `Designation`, `Created_By`) VALUES ('$name', '$code', '$email', '$contact', AES_ENCRYPT('$contact','60ZpqkOnqn0UQQ2MYTlJ'), '$filename', 'Sub-Counsellor', 'Sub-Counsellor', ".$_SESSION['ID'].")");
    $add = $conn->query("INSERT INTO University_User (`University_ID`, `User_ID`, `Reporting`) VALUES ($university_id, ".$conn->insert_id.", $reporting)");
    
    if($add){
      echo json_encode(['status'=>200, 'message'=>'National Coordinator added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
