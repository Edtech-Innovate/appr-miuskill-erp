<?php
  if(isset($_GET['university_id']) && isset($_GET['session_id']) && isset($_GET['admission_type_id']) && isset($_GET['course_id']) && isset($_GET['center'])){
    require '../../includes/db-config.php';
    session_start();

    $university_id = intval($_GET['university_id']);
    $session_id = intval($_GET['session_id']);
    $admission_type_id = intval($_GET['admission_type_id']);
    $course_id = intval($_GET['course_id']);
    $user_id = intval($_GET['center']);

    if(empty($course_id)){
      echo '<option value="">Please add course</option>';
      exit();
    }

    if(empty($admission_type_id)){
      echo '<option value="">Please add admission-type</option>';
      exit();      
    }

    $scheme = $conn->query("SELECT Scheme FROM Admission_Sessions WHERE ID = $session_id");
    if($scheme->num_rows==0){
      echo '<option value="">Please add Scheme</option>';
      exit();
    }

    $scheme = mysqli_fetch_assoc($scheme);
    $schemes = json_decode($scheme['Scheme'], true);

    $scheme_id = 0;
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

    $is_vocational = $conn->query("SELECT ID FROM Universities WHERE Course_Allotment = 1 AND ID = $university_id");
    if($is_vocational->num_rows>0){
      
      $subCourseIds = array();
      $sub_courses = $conn->query("SELECT Sub_Course_ID FROM User_Sub_Courses WHERE User_ID = $user_id AND University_ID = $university_id");
      while($sub_course = $sub_courses->fetch_assoc()){
        $subCourseIds[] = $sub_course['Sub_Course_ID'];
      }

      $subCourseIds = array_intersect($subCourseIds, $allotedSubCoursesOnScheme);

      $ids = array();
      $sub_courses = $conn->query("SELECT ID, Admission_Type FROM Sub_Courses WHERE ID IN (".implode(",", $subCourseIds).")");
      while($sub_course = $sub_courses->fetch_assoc()){
        $admissionTypes = json_decode($sub_course['Admission_Type'], true);
        if(in_array($admission_type_id, array_keys($admissionTypes))){
          $ids[] = $sub_course['ID'];
        }
      }

      if(empty($ids)){
        echo '<option value="">Please add sub-course</option>';
        exit();
      }

      $sub_courses = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE ID IN (".implode(',', $ids).") AND University_ID = $university_id AND Course_ID = $course_id");
    }else{
      $ids = array();
      $sub_courses = $conn->query("SELECT ID, Admission_Type FROM Sub_Courses WHERE University_ID = $university_id AND Course_ID = $course_id");
      while($sub_course = $sub_courses->fetch_assoc()){
        $admissionTypes = json_decode($sub_course['Admission_Type'], true);
        if(in_array($admission_type_id, array_keys($admissionTypes))){
          $ids[] = $sub_course['ID'];
        }
      }

      $sub_courses = $conn->query("SELECT ID, Name FROM Sub_Courses WHERE ID IN (".implode(",", $ids).") AND University_ID = $university_id AND Course_ID = $course_id");
    }
    
    if($sub_courses->num_rows==0){
      echo '<option value="">Please add sub-course</option>';
      exit();
    }

    while($sub_course = $sub_courses->fetch_assoc()){
      echo '<option value="'.$sub_course['ID'].'">'.$sub_course['Name'].'</option>';
    }
  }
