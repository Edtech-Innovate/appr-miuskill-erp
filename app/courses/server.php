<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if (isset($_POST['order'])) {
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn, $_POST['search']['value']); // Search value

if (isset($columnSortOrder)) {
  $orderby = "ORDER BY $columnName $columnSortOrder";
} else {
  $orderby = "ORDER BY Courses.ID ASC";
}

// Admin Query
$query = $_SESSION['Role'] != "Administrators" ? " AND Courses.University_ID = " . $_SESSION['university_id'] : "";
$departmentQuery = $_SESSION['Role'] != "Administrators" ? " AND Departments.University_ID = " . $_SESSION['university_id'] : "";

// Departments
$allDepartments = array();
$departments = $conn->query("SELECT ID, Name FROM Departments WHERE ID IS NOT NULL $departmentQuery");
while ($department = $departments->fetch_assoc()) {
  $allDepartments[$department['ID']] = $department['Name'];
}

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " AND (Courses.Name like '%" . $searchValue . "%' OR Courses.Short_Name like '%" . $searchValue . "%' OR Universities.Name like '%" . $searchValue . "%')";
}

## Total number of records without filtering
$all_count = $conn->query("SELECT COUNT(ID) as allcount FROM Courses WHERE ID IS NOT NULL $query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Courses.ID) as filtered FROM Courses LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Universities ON Courses.University_ID = Universities.ID WHERE Courses.ID IS NOT NULL $searchQuery $query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Courses.`ID`, Courses.Department_ID, Courses.`Name`, Courses.`Short_Name`, Course_Types.Name as CourseType, Courses.`Status`, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM Courses LEFT JOIN Course_Types ON Courses.Course_Type_ID = Course_Types.ID LEFT JOIN Universities ON Courses.University_ID = Universities.ID WHERE Courses.ID IS NOT NULL $searchQuery $query $orderby LIMIT " . $row . "," . $rowperpage;
$results = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($results)) {

  $allotedDepartments = array();
  if (!empty($row['Department_ID'])) {
    $departments = json_decode($row['Department_ID'], true);
    foreach ($departments as $department) {
      $allotedDepartments[] = $allDepartments[$department];
    }
  }

  $data[] = array(
    "Name" => $row["Name"],
    "Short_Name" => $row["Short_Name"],
    "Type" => $row["CourseType"],
    "University" => $row["University"],
    "Status" => $row["Status"],
    "Department_ID" => implode(',<br>', $allotedDepartments),
    "ID" => $row["ID"],
  );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
