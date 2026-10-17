<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
## Read value

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Students.ID Desc";
}

// Admin Query
$query = " AND Students.University_ID = ".$_SESSION['university_id'];

$userQuery="";
// center students ids 
if($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center' ){
  $getStudentQuery = $conn->query("SELECT Enrollment_No FROM Students WHERE Added_For = '".$_SESSION['ID']."'");
  if($getStudentQuery->num_rows > 0){
    $studentIDs = [];
    while($student = $getStudentQuery->fetch_assoc()){
      $studentIDs[] = "'" . $student['Enrollment_No'] . "'";
    }
    $userQuery.= " AND marksheets.enrollment_no IN (".implode(',', $studentIDs).")";
  }
}

$filterByVerticalType = "";
if (isset($_SESSION['filterByVerticalType'])) {
  $filterByVerticalType = $_SESSION['filterByVerticalType'];
}

## Search 
$searchQuery = " ".$filterByVerticalType;
if($searchValue != ''){
  $searchQuery = " AND ( Students.Enrollment_No like '%".$searchValue."%' OR Students.Unique_ID like '%".$searchValue."%' OR Sub_Courses.Name like '%".$searchValue."%' OR Sub_Courses.Short_Name like '%".$searchValue."%' OR Students.First_Name LIKE '%".$searchValue."%' OR Sub_Courses.Name  like '%".$searchValue."%' OR Sub_Courses.Short_Name  like '%".$searchValue."%'  OR c.Short_Name  like '%".$searchValue."%' OR c.Name  like '%".$searchValue."%')";
}

## Total number of records without filtering

$all_count = $conn->query("SELECT COUNT(Students.ID) as allcount FROM Students  JOIN Courses AS c ON Students.Course_ID = c.ID JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID  JOIN marksheets ON Students.Enrollment_No = marksheets.enrollment_no   WHERE  Sub_Courses.Status=1 $userQuery $query GROUP BY marksheets.enrollment_no ");
// $records = mysqli_fetch_assoc($all_count);
$totalRecords = $all_count->num_rows;

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Students.ID) as filtered FROM Students JOIN Courses AS c ON Students.Course_ID = c.ID JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID  JOIN marksheets ON Students.Enrollment_No = marksheets.enrollment_no   WHERE Sub_Courses.Status=1 $userQuery  $query  $searchQuery GROUP BY marksheets.enrollment_no");
// $records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $filter_count->num_rows;

## Fetch records
$result_record = "SELECT Students.ID,Students.Exam,Students.University_ID AS uni_id, Students.Unique_ID, Students.Enrollment_No,Students.Duration, c.Short_Name AS course_name, Sub_Courses.Name AS subcourse_name,  CONCAT(Students.First_Name, ' ', Students.Middle_Name,' ',Students.Last_Name) AS student_name,DATE_FORMAT(marksheets.created_at, '%d-%m-%Y') as published_on FROM Students JOIN Courses AS c ON Students.Course_ID = c.ID JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID JOIN marksheets ON Students.Enrollment_No = marksheets.enrollment_no  WHERE  Sub_Courses.Status=1 $userQuery $query $searchQuery GROUP BY marksheets.enrollment_no $orderby LIMIT ".$row.",".$rowperpage;
$results = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($results)) {
    $duration = $row['Duration'];
    $data[] = array( 
      "student_name" => ucwords(strtolower($row["student_name"])),
      "Unique_ID" => $row["Unique_ID"],
      "Enrollment_No" => $row["Enrollment_No"],
      "subcourse_name" => ucwords(strtolower($row["subcourse_name"])).'('.strtoupper($row["course_name"]).')',
      "published_on"=>$row["published_on"],
      "course_name" => ucwords(strtolower($row["course_name"])),
      "Exam" => $row["Exam"],
      "duration"  =>  $duration,
      "stu_id"      => $row["ID"],
      "ID" => base64_encode($row['ID'] . 'W1Ebt1IhGN3ZOLplom9I'),
    );
}

## Response
$response = array(
  "draw" => intval($draw),
  "iTotalRecords" => $totalRecords,
  "iTotalDisplayRecords" => $totalRecordwithFilter,
  "aaData" => $data
);

// ECHO "<PRE>";
// print_r($response );DIE;

echo json_encode($response);
