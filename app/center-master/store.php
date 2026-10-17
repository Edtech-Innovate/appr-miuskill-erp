<?php
  if(isset($_POST['name']) && isset($_POST['email'])){
    require '../../includes/db-config.php';
    session_start();

    $user_type =  isset($_POST['user_type']) ? intval($_POST['user_type']) : 1;
    $center_type =  isset($_POST['center_type']) ? intval($_POST['center_type']) : 1;
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short_name = mysqli_real_escape_string($conn, $_POST['short_name']);
    $contact_person_name = mysqli_real_escape_string($conn, $_POST['contact_person_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $alternate_contact = mysqli_real_escape_string($conn, $_POST['alternate_contact']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $vertical = mysqli_real_escape_string($conn, $_POST['vertical']);
    
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      echo json_encode(['status'=>400, "message"=>"Invalid email!"]);
      exit();
    }

    if(empty($name) || empty($email) || empty($contact) || empty($short_name) || empty($contact_person_name) || empty($address) || empty($vertical)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    $code = generateUsername($conn, $userNamePrefix);
    
    if(isset($_FILES["photo"]["name"]) && $_FILES["photo"]["name"]!=''){
      $temp = explode(".", $_FILES["photo"]["name"]);
      $filename = round(microtime(true)) . '.' . end($temp);
      $tempname = $_FILES["photo"]["tmp_name"];
      $folder = "../../assets/img/centers/".$filename; 
      if(move_uploaded_file($tempname, $folder)){ 
        $filename = "/assets/img/centers/".$filename;
      }else{
        echo json_encode(['status'=>400, 'message'=>'Unable to save photo!']);
        exit();
      }
    }else{
      $filename = "/assets/img/default-user.png";
    }

    $add = $conn->query("INSERT INTO `Users`(`Name`, `Short_Name`, `Contact_Name`, `Code`, `Email`, `Mobile`, `Alternate_Mobile`, `Address`, `Pincode`, `City`, `District`, `State`, `Password`, `Photo`, `Role`, `Designation`, `Created_By`, `B2B_Partner`,`Center_Type`,`vertical`) VALUES ('$name', '$short_name', '$contact_person_name', '$code', '$email', '$contact', '$alternate_contact', '$address', '$pincode', '$city', '$district', '$state', AES_ENCRYPT($contact, '60ZpqkOnqn0UQQ2MYTlJ'), '$filename', 'Center', 'Center', ".$_SESSION['ID'].", '$user_type', '$center_type',$vertical)");
    if($add){
      echo json_encode(['status'=>200, 'message'=>'Center added successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
?>
