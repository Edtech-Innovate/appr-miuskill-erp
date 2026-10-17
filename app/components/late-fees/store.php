<?php
  if(isset($_POST['fee']) && isset($_POST['university_id']) && isset($_POST['admission_session']) && isset($_POST['start_date'])){
    
    require '../../../includes/db-config.php';

    $fee = intval($_POST['fee']);
    $university_id = intval($_POST['university_id']);
    $admission_session = is_array($_POST['admission_session']) ? array_filter($_POST['admission_session']) : [];
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $start_date = date("Y-m-d", strtotime($start_date));
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $end_date = !empty($end_date) ? date("Y-m-d", strtotime($end_date)) : '';

    if(empty($admission_session)){
      exit(json_encode(['status'=>false, 'message' =>'Please select Admission Session(s)']));
    }
    
    $prevEndDate = $conn->query("SELECT End_Date FROM Late_Fees WHERE University_ID = $university_id ORDER BY End_Date DESC LIMIT 1");
    if($prevEndDate->num_rows>0){
      $prevEndDate = $prevEndDate->fetch_assoc();
      $prevEndDate = $prevEndDate['End_Date'];
    }else{
      $prevEndDate = "";
    }

    $prevStartDate = $conn->query("SELECT Start_Date FROM Late_Fees WHERE University_ID = $university_id ORDER BY Start_Date DESC LIMIT 1");
    if($prevStartDate->num_rows > 0){
      $prevStartDate = $prevStartDate->fetch_assoc();
      $prevStartDate = $prevStartDate['Start_Date'];
    }else{
      $prevStartDate = "";
    }

    if(!empty($prevStartDate) && $start_date<$prevStartDate){
      exit(json_encode(['status'=>false, 'message' =>'Start Date can not be less than previous date!']));
    }

    if(!empty($prevEndDate) && $start_date<$prevEndDate){
      exit(json_encode(['status'=>false, 'message' =>'Start Date can not be less than previous date!']));
    }

    if(!empty($end_date) && !empty($prevEndDate) && $end_date<$prevEndDate){
      exit(json_encode(['status'=>false, 'message' =>'End Date can not be less than previous date!']));
    }

    $end_date = !empty($end_date) ? "'".$end_date."'" : 'NULL';

    $admission_session = json_encode($admission_session);

    $add = $conn->query("INSERT INTO Late_Fees (`Fee`, `Admission_Session`, `Start_Date`, `End_Date`, `University_ID`) VALUES ($fee, '$admission_session', '$start_date', $end_date, $university_id)");
    if($add){
      echo json_encode(['status'=>true, 'message'=>'Late Fee added successlly!']);
    }else{
      echo json_encode(['status' =>false, 'message' => 'Something went wrong!']);
    }
  }
