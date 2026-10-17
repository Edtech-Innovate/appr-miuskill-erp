<?php 
  if(isset($_POST['university_id']) && isset($_POST['id']) && isset($_POST['reporting']) && isset($_POST['fee']) && isset($_POST['department']) && isset($_POST['sub_course'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_POST['id']);
    $university_id = intval($_POST['university_id']);
    $reportings = is_array($_POST['reporting']) ? array_filter($_POST['reporting']) : array();
    $departments = is_array($_POST['department']) ? $_POST['department'] : array();
    $admission_sessions = is_array($_POST['admission_session']) ? array_filter($_POST['admission_session']) : array();
    $sub_courses = is_array($_POST['sub_course']) ? array_filter($_POST['sub_course']) : array();
    $fees = is_array($_POST['fee']) ? array_filter($_POST['fee']) : array();

    if(empty($fees) || empty($reportings) || empty($university_id) || empty($id) || empty($departments) || empty($admission_sessions) || empty($sub_courses)){
      echo json_encode(['status'=>403, 'message'=>'Missing required field(s)!']);
      exit();
    }

    // Sessions - Schemes
    $allotedSchemes = array();
    $schemes = $conn->query("SELECT ID, Scheme FROM Admission_Sessions WHERE ID IN (".implode(",", $admission_sessions).")");
    while($scheme = $schemes->fetch_assoc()){
      $sessionSchemes = json_decode($scheme['Scheme'], true);
      $allotedSchemes[$scheme['ID']] = $sessionSchemes['schemes'];
    }

    // Check Fee
    foreach($sub_courses as $sub_course){
      foreach($admission_sessions as $admission_session){
        foreach($allotedSchemes[$admission_session] as $scheme){
          if(empty(array_filter($fees[$sub_course][$admission_session][$scheme]))){
            exit(json_encode(['status'=>404, 'message'=>'Please allot Fee']));
          }
        }
      }
    }

    // Reporting
    $conn->query("DELETE FROM University_User WHERE User_ID = $id AND University_ID = $university_id");
    foreach($reportings as $key=>$reporting){
      $conn->query("INSERT INTO University_User (User_ID, University_ID, Reporting, Level) VALUES ($id, $university_id, $reporting, $key)");
    }

    $conn->query("DELETE FROM User_Departments WHERE `User_ID` = $id AND University_ID = $university_id");
    foreach($departments as $department){
      $conn->query("INSERT INTO User_Departments (`User_ID`, `Department_ID`, `University_ID`) VALUES ($id, $department, $university_id)");
    }

    // Remove Previous
    $conn->query("DELETE FROM User_Sub_Courses WHERE `User_ID` = $id AND University_ID = $university_id AND Admission_Session_ID IN (".implode(",", $admission_sessions).")");
    foreach($sub_courses as $sub_course){
      foreach($admission_sessions as $admission_session){
        foreach($allotedSchemes[$admission_session] as $scheme){
          $fee = json_encode($fees[$sub_course][$admission_session][$scheme]);
          $allot = $conn->query("INSERT INTO User_Sub_Courses (`Fee`, `User_ID`, `Sub_Course_ID`, `Admission_Session_ID`, `Scheme_ID`, `University_ID`) VALUES ('$fee', $id, $sub_course, $admission_session, $scheme, $university_id)");
        }
      }
    }

    if($allot){
      echo json_encode(['status'=>200, 'message'=>'Vertical alloted successfully!']);
    }else{
      echo json_encode(['status'=>403, 'message'=>'Unable to allot vertical!']);
    }
  }else{
    echo json_encode(['status'=>403, 'message'=>'Missing required field(s)!']);
  }
