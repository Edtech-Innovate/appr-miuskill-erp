<?php
  if(isset($_GET['university_id']) && isset($_GET['user_id'])){
    require '../../../includes/db-config.php';

    $user_id = intval($_GET['user_id']);
    $university_id = intval($_GET['university_id']);

    if(empty($user_id) || empty($university_id)){
      exit;
    }

    // Current Session
    $session = $conn->query("SELECT ID, Scheme FROM Admission_Sessions WHERE University_ID = $university_id AND Current_Status = 1");
    if($session->num_rows==0){
      exit(json_encode(['status'=>false, 'message'=>'Please configure session!']));
    }

    $session = $session->fetch_assoc();
    $session_id = $session['ID'];
    $schemes = json_decode($session['Scheme'], true);

    foreach($schemes['dates'] as $schemeId=>$date){
      if(date("Y-m-d")>=$date){
        $scheme_id = $schemeId;
      }
    }

    // Scheme - SubCourses
    $allotedSubCoursesOnScheme = array();
    $schemeSubCourses = $conn->query("SELECT Sub_Course_ID FROM Scheme_Sub_Courses WHERE Scheme_ID = $scheme_id");
    while($schemeSubCourse = $schemeSubCourses->fetch_assoc()){
      $allotedSubCoursesOnScheme[] = $schemeSubCourse['Sub_Course_ID'];
    }

    $has_course_allotment = $conn->query("SELECT ID, Sharing FROM Universities WHERE ID = $university_id AND Course_Allotment = 1");
    if($has_course_allotment->num_rows>0){

      $subCourseIds = array();
      $sub_courses = $conn->query("SELECT Sub_Course_ID FROM User_Sub_Courses WHERE User_ID = $user_id AND University_ID = $university_id");
      while($sub_course = $sub_courses->fetch_assoc()){
        $subCourseIds[] = $sub_course['Sub_Course_ID'];
      }

      $subCourseIds = array_intersect($subCourseIds, $allotedSubCoursesOnScheme);

      $ids = array();
      $sub_courses = $conn->query("SELECT DISTINCT Course_ID as Course_ID FROM Sub_Courses WHERE ID IN (".implode(",", $subCourseIds).") AND University_ID = $university_id");
      while($sub_course = $sub_courses->fetch_assoc()){
        $ids[] = $sub_course['Course_ID'];
      }

      $options = "";
      $courses = $conn->query("SELECT ID, Name FROM Courses WHERE University_ID = $university_id AND ID IN (".implode(",", $ids).")");
      while($course = $courses->fetch_assoc()){
        $options .= '<option value="'.$course['ID'].'">'.$course['Name'].'</option>';
      }

      echo json_encode(['status'=>true, 'options'=>$options]);
    }else{
      // Sub Courses
      $ids = array();
      $sub_courses = $conn->query("SELECT ID FROM Sub_Courses WHERE University_ID = $university_id");
      while($sub_course = $sub_courses->fetch_assoc()){
        $ids[] = $sub_course['ID'];
      }

      $ids = array_intersect($ids, $allotedSubCoursesOnScheme);

      $courseIds = array();
      $sub_courses = $conn->query("SELECT DISTINCT Course_ID as Course_ID FROM Sub_Courses WHERE ID IN (".implode(",", $ids).")");
      while($sub_course = $sub_courses->fetch_assoc()){
        $courseIds[] = $sub_course['Course_ID'];
      }

      $options = "";
      $courses = $conn->query("SELECT ID, Name FROM Courses WHERE Status = 1 AND University_ID = $university_id AND ID IN (".implode(",", $courseIds).")");
      while($course = $courses->fetch_assoc()){
        $options .= '<option value="'.$course['ID'].'">'.$course['Name'].'</option>';
      }

      echo json_encode(['status'=>true, 'options'=>$options]);
    }
  }
