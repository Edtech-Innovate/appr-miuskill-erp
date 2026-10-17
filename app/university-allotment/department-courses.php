<?php
  if(isset($_GET['department']) && isset($_GET['university']) && isset($_GET['user_id'])){
    require '../../includes/db-config.php';

    $user_id = intval($_GET['user_id']);
    $university_id = intval($_GET['university']);
    $department_ids = mysqli_real_escape_string($conn, $_GET['department']);

    if(empty($department_ids) || empty($university_id) || empty($user_id)){
      exit;
    }

    // Reporting
    $reportingQuery = "";
    $reportingSubCourses = array();
    if(isset($_GET['reporting'])){
      $reporting = intval($_GET['reporting']);
      $alloted_fees = $conn->query("SELECT Sub_Course_ID FROM User_Sub_Courses WHERE `User_ID` = $reporting AND `University_ID` = $university_id");
      while($alloted_fee = $alloted_fees->fetch_assoc()){
        $reportingSubCourses[] = $alloted_fee['Sub_Course_ID'];
      } 
      
      $reportingQuery = " AND Sub_Courses.ID IN (".implode(",", $reportingSubCourses).")";
    }

    $allotedSubCourses = array();
    $subCourses = $conn->query("SELECT Sub_Course_ID FROM User_Sub_Courses WHERE `User_ID` = $user_id AND `University_ID` = $university_id");
    while($subCourse = $subCourses->fetch_assoc()){
      $allotedSubCourses[] = $subCourse['Sub_Course_ID'];
    }

    $options = "";
    $sub_courses = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') AS Name FROM Sub_Courses LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE Sub_Courses.Department_ID IN ($department_ids) $reportingQuery");
    while($sub_course = $sub_courses->fetch_assoc()){
      $selected = in_array($sub_course['ID'], $allotedSubCourses) ? 'selected' : '';
      $options .= '<option value="'.$sub_course['ID'].'" '.$selected.'>'.$sub_course['Name'].'</option>';
    }

    echo $options;
  }
