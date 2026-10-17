<?php
if (isset($_POST['university_id']) && isset($_POST['id']) && isset($_POST['reporting'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_POST['id']);
  $university_id = intval($_POST['university_id']);
  $reportings = is_array($_POST['reporting']) ? array_filter($_POST['reporting']) : array();
  $fee = is_array($_POST['fee']) ? array_filter($_POST['fee']) : array();
  $applicable = empty($_POST['applicable_in']) ? [] : $_POST['applicable_in'];

  if (empty($id) || empty($university_id) || empty($reportings)) {
    echo json_encode(['status' => 403, 'message' => 'All fields are required.']);
    exit();
  }

  $allotedSchemes = array();
  $sessions = array();
  $admission_sessions = $conn->query("SELECT ID, Name, Scheme FROM Admission_Sessions WHERE University_ID = $university_id");
  while($admission_session = $admission_sessions->fetch_assoc()){
    $sessions[] = $admission_session['ID'];
    $sessionSchemes = json_decode($admission_session['Scheme'], true);
    $allotedSchemes[$admission_session['ID']] = $sessionSchemes['schemes'];
  }

  // Check
  foreach($sessions as $session_id){
    foreach($allotedSchemes[$session_id] as $scheme){
      if(empty(array_filter($fee[$session_id][$scheme], 'is_numeric'))){
        exit(json_encode(['status'=>403, 'message'=>'Please allot Fee']));
      }
    }
  }

  // Course Allotment
  $has_course_allotment = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Course_Allotment = 1");
  if($has_course_allotment->num_rows > 0){
    $departments = isset($_POST['department']) && is_array($_POST['department']) ? $_POST['department'] : array();
    $sub_courses = isset($_POST['courses']) && is_array($_POST['courses']) ? array_filter($_POST['courses']) : array();
    
    if(empty($departments) || empty($sub_courses)){
      exit(json_encode(['status'=>403, 'message'=>'Please select course(s) to allot!']));
    }

    $conn->query("DELETE FROM User_Departments WHERE `User_ID` = $id AND University_ID = $university_id");
    foreach($departments as $department){
      $conn->query("INSERT INTO User_Departments (`User_ID`, `Department_ID`, `University_ID`) VALUES ($id, $department, $university_id)");
    }

    $conn->query("DELETE FROM User_Sub_Courses WHERE `User_ID` = $id AND University_ID = $university_id");
    foreach($sub_courses as $sub_course){
      $allot = $conn->query("INSERT INTO User_Sub_Courses (`User_ID`, `Sub_Course_ID`, `University_ID`) VALUES ($id, $sub_course, $university_id)");
    }
  }

  // Reporting
  $conn->query("DELETE FROM University_User WHERE User_ID = $id AND University_ID = $university_id");
  foreach($reportings as $key=>$reporting){
    $update = $conn->query("INSERT INTO University_User (User_ID, University_ID, Reporting, Level) VALUES ($id, $university_id, $reporting, $key)");
  }

  // Update
  $add_fee = false;
  foreach($sessions as $session_id){
    foreach($allotedSchemes[$session_id] as $scheme){
      $conn->query("DELETE FROM Fee_Variables WHERE Code = $id AND University_ID = $university_id AND Admission_Session_ID = $session_id AND Scheme_ID = $scheme");
      $feeData = json_encode($fee[$session_id][$scheme]);
      $add_fee = $conn->query("INSERT INTO Fee_Variables (`University_ID`, `Fee`, `Code`, `Admission_Session_ID`, `Scheme_ID`) VALUES ($university_id, '$feeData', $id, $session_id, $scheme)");
    }
  }

  if($add_fee){
    echo json_encode(['status'=>200, 'message'=>'Vertical alloted successfully!']);
  }else{
    echo json_encode(['status'=>403,'message'=>'Something went wrong!']);
  }
}
