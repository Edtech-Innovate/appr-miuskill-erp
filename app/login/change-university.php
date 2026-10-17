<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_POST['id']);
  $val = 0;
  $condition = "";
  if ($_SESSION['Role'] != 'Administrator') {
    $condition = " WHERE `User_ID` = " . $_SESSION['ID'];
  }
  
  $universities = $conn->query("SELECT University_User.University_ID, Universities.Is_B2C, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') AS Name, Universities.Has_Unique_Center, Universities.Has_Unique_StudentID, Universities.Logo, Universities.Course_Allotment, Universities.Has_LMS FROM University_User LEFT JOIN Universities ON University_User.University_ID = Universities.ID $condition");
  while ($university = $universities->fetch_assoc()) {
    if ($university['University_ID'] == $id) {
      $_SESSION['university_id'] = $university['University_ID'];
      $_SESSION['university_name'] = $university['Name'];
      $_SESSION['unique_center'] = $university['Has_Unique_Center'];
      $_SESSION['student_id'] = $university['Has_Unique_StudentID'];
      $_SESSION['is_vocational'] = $university['Course_Allotment'];
      $_SESSION['university_logo'] = $university['Logo'];
      $_SESSION['has_lms'] = $university['Has_LMS'];
      $_SESSION['crm'] = $university['Is_B2C'];
      $val = 1;
      break;
    }
  }

  // RolesQuery
  $role_query = " AND {{ table }}.{{ column }} = " . $_SESSION['ID'];

  if ($_SESSION['Role'] === 'Administrator' || $_SESSION['Role'] == 'Operations' || $_SESSION['Role'] == 'Accountant') {
    $role_query = " AND {{ table }}.{{ column }} IS NOT NULL";
  } elseif ($_SESSION['Role'] === 'University Head') {
    $center_list = array($_SESSION['ID']);
    $counsellorIds = array();
    $subCounsellorIds = array();
    $centersIds = array();

    $counsellors = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting = " . $_SESSION['ID'] . " AND University_ID = " . $_SESSION['university_id']);
    while ($counsellor = $counsellors->fetch_assoc()) {
      $center_list[] = $counsellor['User_ID'];
      $counsellorIds[] = $counsellor['User_ID'];
    }

    if(!empty($counsellorIds)){
      $sub_counsellors = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (".implode(",", $counsellorIds).") AND University_ID = " . $_SESSION['university_id']);
      while ($sub_counsellor = $sub_counsellors->fetch_assoc()) {
        $center_list[] = $sub_counsellor['User_ID'];
        $subCounsellorIds[] = $sub_counsellor['User_ID'];
      }

      $centers = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (".implode(",", $counsellorIds).") AND University_ID = " . $_SESSION['university_id']);
      while ($center = $centers->fetch_assoc()) {
        $center_list[] = $center['User_ID'];
        $centersIds[] = $center['User_ID'];
      }

      if(!empty($centersIds)){
        $sub_centers = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (".implode(",", $counsellorIds).") AND University_ID = " . $_SESSION['university_id']);
        while ($sub_center = $sub_centers->fetch_assoc()) {
          $center_list[] = $sub_center['User_ID'];
        }
      }
    }

    $role_query = " AND {{ table }}.{{ column }} IN (".implode(",", $center_list).")";

  } elseif ($_SESSION['Role'] == 'Counsellor') {
    $center_list = array($_SESSION['ID']);
    $counsellorIds = array($_SESSION['ID']);
    $subCounsellorIds = array();
    $centersIds = array();

    if(!empty($counsellorIds)){
      $sub_counsellors = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (".implode(",", $counsellorIds).") AND University_ID = " . $_SESSION['university_id']);
      while ($sub_counsellor = $sub_counsellors->fetch_assoc()) {
        $center_list[] = $sub_counsellor['User_ID'];
        $subCounsellorIds[] = $sub_counsellor['User_ID'];
      }

      $centers = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (".implode(",", $counsellorIds).") AND University_ID = " . $_SESSION['university_id']);
      while ($center = $centers->fetch_assoc()) {
        $center_list[] = $center['User_ID'];
        $centersIds[] = $center['User_ID'];
      }

      if(!empty($centersIds)){
        $sub_centers = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (".implode(",", $counsellorIds).") AND University_ID = " . $_SESSION['university_id']);
        while ($sub_center = $sub_centers->fetch_assoc()) {
          $center_list[] = $sub_center['User_ID'];
        }
      }
    }

    $role_query = " AND {{ table }}.{{ column }} IN (".implode(",", $center_list).")";

  } elseif ($_SESSION['Role'] === 'Sub-Counsellor') {
    $center_list = array($_SESSION['ID']);
    $subCounsellorIds = array($_SESSION['ID']);

    $centers = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (".implode(",", $subCounsellorIds).") AND University_ID = " . $_SESSION['university_id']);
    while ($center = $centers->fetch_assoc()) {
      $center_list[] = $center['User_ID'];
      $centersIds[] = $center['User_ID'];
    }

    if(!empty($centersIds)){
      $sub_centers = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (".implode(",", $counsellorIds).") AND University_ID = " . $_SESSION['university_id']);
      while ($sub_center = $sub_centers->fetch_assoc()) {
        $center_list[] = $sub_center['User_ID'];
      }
    }

    $role_query = " AND {{ table }}.{{ column }} IN (".implode(",", $center_list).")";
    
  } elseif ($_SESSION['Role'] === 'Center') {
    $center_list = array($_SESSION['ID']);
    $centersIds = array($_SESSION['ID']);

    $sub_centers = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting IN (".implode(",", $centersIds).") AND University_ID = " . $_SESSION['university_id']);
    while ($sub_center = $sub_centers->fetch_assoc()) {
      $center_list[] = $sub_center['User_ID'];
    }

    $role_query = " AND {{ table }}.{{ column }} IN (".implode(",", $center_list).")";
  } elseif ($_SESSION['Role'] === 'Sub-Center') {
    $role_query = " AND {{ table }}.{{ column }} = '" . $_SESSION['ID'] . "'";
  }else{
    $role_query = " AND {{ table }}.{{ column }} = '" . $_SESSION['ID'] . "'";
  }

  $_SESSION['RoleQuery'] = $role_query;
  
  $gateway = $conn->query("SELECT * FROM Payment_Gateways WHERE University_ID = " . $_SESSION['university_id']);
  if ($gateway->num_rows > 0) {
    $gateway = $gateway->fetch_assoc();
    $_SESSION['gateway'] = $gateway['Type'];
    $_SESSION['access_key'] = $gateway['Access_Key'];
    $_SESSION['secret_key'] = $gateway['Secret_Key'];
  } else {
    unset($_SESSION['gateway']);
    unset($_SESSION['access_key']);
    unset($_SESSION['secret_key']);
  }

  if ($val == 1) {
    echo json_encode(['status' => '200', 'message' => 'University updated successfully!']);
  } else {
    echo json_encode(['status' => 403, 'message' => 'University not found!']);
  }
}
