<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';

    $id = intval($_GET['id']);

    $course = $conn->query("SELECT Department_ID FROM Courses WHERE ID = $id");
    $course = $course->fetch_assoc();
    $departmentIds = !empty($course['Department_ID']) ? json_decode($course['Department_ID'], true) : [];

    if(empty($departmentIds)){
      echo '<option value="">Department(s) not assigned!</option>';
      exit;
    }

    $departmentIds = implode(",", $departmentIds);

    $options = '<option value="">Choose</option>';
    $departments = $conn->query("SELECT ID, Name FROM Departments WHERE ID IN ($departmentIds)");
    while($department = $departments->fetch_assoc()){
      $options .= '<option value="' . $department['ID'] . '">' . $department['Name'] .'</option>';
    }

    echo $options;
  }
