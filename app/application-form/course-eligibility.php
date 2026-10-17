<?php
  if(isset($_GET['id']) && isset($_GET['admission_type'])){
    require '../../includes/db-config.php';
    session_start();

    $id = intval($_GET['id']);
    $admission_type_id = intval($_GET['admission_type']);

    $eligibility = $conn->query("SELECT Eligibility FROM Sub_Courses WHERE ID = $id");
    $eligibility = $eligibility->fetch_assoc();
    $eligibility = !empty($eligibility['Eligibility']) ? json_decode($eligibility['Eligibility'], true) : [];

    $required = $eligibility[$admission_type_id]['required'];
    $optional = array_key_exists('optional', $eligibility[$admission_type_id]) ? $eligibility[$admission_type_id]['optional'] : array();

    $all = array_merge($required, $optional);
    
    if(count($eligibility)>0){
      echo json_encode(['status'=>true, 'required'=>$required, 'optional'=>$optional, 'count'=>count($all)]);
    }else{
      echo json_encode(['status'=>false, 'eligibility'=>$eligibility, 'count'=>count($eligibility)]);
    }
  }
