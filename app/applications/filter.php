<?php
if (isset($_POST['id']) && isset($_POST['by'])) {
  session_start();
  require '../../includes/db-config.php';

  $by = $_POST['by'];
  $id = intval($_POST['id']);
  $sub_center_name = "";
  $option = '';
  if ($by == 'departments') {
    $courseIds = $conn->query("SELECT GROUP_CONCAT(ID) as ID FROM Courses WHERE Course_Type_ID = $id AND University_ID = " . $_SESSION['university_id']);
    if ($courseIds->num_rows > 0) {
      $courseIds = $courseIds->fetch_assoc();
      $courseIds = $courseIds['ID'];
      $_SESSION['filterByDepartment'] = !empty($courseIds) ? " AND Students.Course_ID IN ($courseIds)" : " AND Students.ID IS NULL";
    } else {
      $_SESSION['filterByDepartment'] = " AND Students.ID IS NULL";
    }
  } elseif ($by == 'sub_courses') {
    $_SESSION['filterBySubCourses'] = " AND Students.Sub_Course_ID = $id";
  } elseif ($by == 'date') {
    $startDate = date("Y-m-d 00:00:00", strtotime($_POST['startDate']));
    $endDate = date("Y-m-d 23:59:59", strtotime($_POST['endDate']));
    $_SESSION['filterByDate'] = " AND Students.Created_At BETWEEN '$startDate' AND '$endDate'";
  } elseif ($by == 'application_status') {
    if ($id == 1) {
      $_SESSION['filterByStatus'] = " AND Document_Verified IS NOT NULL ";
    } elseif ($id == 2) {
      $_SESSION['filterByStatus'] = " AND Payment_Received IS NOT NULL ";
    } elseif ($id == 3) {
      $_SESSION['filterByStatus'] = " AND Document_Verified IS NOT NULL AND Payment_Received IS NOT NULL ";
    }
  } elseif ($by == "users") {
    $check = $conn->query("SELECT ID, Role, CanCreateSubCenter FROM Users WHERE ID = $id");
    if ($check->num_rows > 0) {
      $user = $check->fetch_assoc();
      // echo "<pre>"; print_r($user);die;
      $role_query = " AND Students.Added_For = $id";

      if (strtolower($user['Role']) == 'center') { // center
        if ($user['CanCreateSubCenter'] == 1) {
          $subCenter = $conn->query("SELECT * FROM Center_SubCenter WHERE Center=$id");
          
          if ($subCenter->num_rows > 0) {
            while ($row = $subCenter->fetch_assoc()) {
              $sub_center_name .= $row['Sub_Center'] . ", ";
            }
            $sub_center_name = rtrim($sub_center_name, ", "); // sub_center_ids

            // start sub_center option 
            $getSubCourseName = $conn->query("SELECT ID, Name FROM Users WHERE ID IN ($sub_center_name) AND Status = 1");
            if ($getSubCourseName->num_rows == 0) {
              $option = "<option value=''>No Sub-Center Found</option>";
            }
            $option = "<option value=''>Choose Sub-Center</option>";
            while ($row = $getSubCourseName->fetch_assoc()) {
              $option .= "<option value='" . $row['ID'] . "'>" . ucwords(strtolower($row['Name'])) . "</option>";
            }
            // end sub center option 
            $centersubcenterids = $sub_center_name . ',' . $id; // center and sub-center ids
            
            $role_query = " AND Students.Added_For IN ($centersubcenterids)";
          } else {
              $role_query = " AND Students.Added_For = $id";
          }
          
        }else{
          $role_query = " AND Students.Added_For = $id";
        }
      }
      $_SESSION['filterByUser'] = $role_query;
    }
  }
  echo json_encode(['status' => true, 'subCenterName' => $option]);
}
