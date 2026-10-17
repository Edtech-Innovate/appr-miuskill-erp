<?php
  if(isset($_POST['name']) && isset($_POST['course']) && isset($_POST['university_id']) && isset($_POST['scheme']) || isset($_POST['mode']) && isset($_POST['min_duration']) && isset($_POST['max_duration']) && isset($_POST['id']) && isset($_POST['department'])){
    require '../../includes/db-config.php';

    session_start();

    $id = intval($_POST['id']);
    $university_id = intval($_POST['university_id']);
    $course = intval($_POST['course']);
    $department = intval($_POST['department']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $short_name = mysqli_real_escape_string($conn, $_POST['short_name']);
    $mode = intval($_POST['mode']);
    $min_duration = intval($_POST['min_duration']);
    $max_duration = intval($_POST['max_duration']);
    $University_fee=intval($_POST['University_fee']);
    if(empty($name) || empty($short_name) || empty($course) || empty($university_id) || empty($mode) || empty($id)){
      echo json_encode(['status'=>403, 'message'=>'All fields are mandatory!']);
      exit();
    }

    if(!isset($_POST['admission_types'])){
      exit(json_encode(['status'=>403, 'message'=>'Please select Admission Type!']));
    }

    // Admission Types
    $admission_types =  is_array($_POST['admission_types']) ? array_filter($_POST['admission_types']) : array();
    $admission_type_duration = isset($_POST['admission_type_duration']) && is_array($_POST['admission_type_duration']) ? array_filter($_POST['admission_type_duration']) : array();

    // Eligibilities
    $eligibilities = isset($_POST['eligibilities']) && is_array($_POST['eligibilities']) ? array_filter($_POST['eligibilities']) : [];

    $academics = array();
    $admission_duration = array();

    foreach($admission_types as $admission_type){
      if(empty($admission_type_duration[$admission_type])){
        exit(json_encode(['status'=>403, 'message'=>'Please select Admission Duration!']));
      }else{
        $admission_duration[$admission_type] = $admission_type_duration[$admission_type];
      }

      if(empty($eligibilities[$admission_type]['required'])){
        exit(['status'=>403, 'message'=>'Please select required Academics!']);
      }else{
        $academics[$admission_type] = $eligibilities[$admission_type];
      }
    }

    $check = $conn->query("SELECT ID FROM Sub_Courses WHERE (Name like '$name' OR Short_Name LIKE '$short_name') AND University_ID = $university_id AND Course_ID = $course AND ID <> $id");
    if($check->num_rows>0){
      echo json_encode(['status'=>400, 'message'=>$short_name.' already exists!']);
      exit();
    }

    $eligibilities = json_encode($academics);
    $types = json_encode($admission_duration);
    $counsellor_fee = $_POST['counsellor_fee'];
    $sub_counsellor_fee = $_POST['sub_counsellor_fee'];   
    $center_fee    = $_POST['center_fee'];
    $coordinator_fee    = $_POST['coordinator_fee'];
    $sub_center_fee    = $_POST['sub_center_fee'];
    $update = $conn->query("UPDATE `Sub_Courses` SET `sub_center_fee`=$sub_center_fee,`coordinator_fee`=$coordinator_fee,`center_fee`=$center_fee,`sub_counsellor_fee`=$sub_counsellor_fee,`counsellor_fee`=$counsellor_fee, `Name` = '$name', `Short_Name` = '$short_name', `Course_ID` = $course, Department_ID = $department, `Mode_ID` = $mode, `Min_Duration` = $min_duration, `Max_Duration` = $max_duration, Eligibility = '$eligibilities', Admission_Type = '$types',University_fee='$University_fee' WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>200, 'message'=>$short_name.' updated successlly!']);
    }else{
      echo json_encode(['status'=>400, 'message'=>'Something went wrong!']);
    }
  }
