<?php
  if(isset($_GET['admission_type_id']) && isset($_GET['sub_course_id'])){
    require '../../includes/db-config.php';
    session_start();

    $admission_type_id = intval($_GET['admission_type_id']);
    $sub_course_id = intval($_GET['sub_course_id']);

    if(empty($admission_type_id) || empty($sub_course_id)){
      echo '<option value="">Please add sub-course</option>';
      exit();
    }

    $sub_course = $conn->query("SELECT * FROM Sub_Courses WHERE ID = $sub_course_id");
    $sub_course = $sub_course->fetch_assoc();
    // print_r($sub_course);die;
    $sub_course = json_decode($sub_course['Admission_Type'], true);
    
    $durations = $sub_course[$admission_type_id];
    // $durations = array($sub_course['Min_Duration']);
    
    $option = "";
    foreach($durations as $duration){
      $option .= '<option value="'.$duration.'">'.$duration.'</option>';
    }

    echo $option;
  }
