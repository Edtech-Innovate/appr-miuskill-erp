<?php
## Database configuration
include '../../includes/db-config.php';
session_start();

## Read value
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
  $orderby = "ORDER BY Syllabi.ID DESC";
}



$filterByUsers = "";
if (isset($_SESSION['usersFilter'])) {
  $filterByUsers = $_SESSION['usersFilter'];
}

$filterBysubCourse = "";
if (isset($_SESSION['subCourseFilter'])) {
  $filterBysubCourse = $_SESSION['subCourseFilter'];
}

$filterByDuration = "";
if (isset($_SESSION['durationFilter'])) {
  $filterByDuration = $_SESSION['durationFilter'];
}
$filterCourseFileter = "";
if (isset($_SESSION['courseFilter'])) {
  $filterCourseFileter = $_SESSION['courseFilter'];
}

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Syllabi.Name like '%".$searchValue."%' OR Syllabi.Code like '%".$searchValue."%' OR Syllabi.Paper_Type like '%".$searchValue."%' OR Sub_Courses.Name like '%".$searchValue."%' OR Courses.Name like '%".$searchValue."%' OR Courses.Short_Name like '%".$searchValue."%' OR Sub_Courses.Short_Name like '%".$searchValue."%')";
}

$filterByUniversity = " AND Syllabi.University_ID =".$_SESSION['university_id'];
$searchQuery .= $filterByUniversity. $filterBysubCourse.$filterByDuration .$filterByUsers.$filterCourseFileter;


## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(Syllabi.ID) as allcount FROM  Syllabi LEFT JOIN Sub_Courses on Syllabi.Sub_Course_ID =  Sub_Courses.ID  LEFT JOIN Courses on Syllabi.Course_ID =  Courses.ID   WHERE 1=1 $searchQuery");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Syllabi.ID) as filtered FROM Syllabi LEFT JOIN Sub_Courses on Syllabi.Sub_Course_ID =  Sub_Courses.ID  LEFT JOIN Courses on Syllabi.Course_ID =  Courses.ID   WHERE 1= 1  $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Syllabi.ID,Syllabi.Semester,Syllabi.Syllabus as files,  Syllabi.Name as subject_name, Syllabi.Code,Sub_Courses.Name AS sub_course_name, Min_Marks, Max_Marks, Paper_Type, Credit, Courses.Name as course_name FROM Syllabi LEFT JOIN Sub_Courses on Syllabi.Sub_Course_ID =  Sub_Courses.ID LEFT JOIN Courses on Syllabi.Course_ID =  Courses.ID  WHERE 1=1  $searchQuery  $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();
// echo $result_record; die;

while ($row = mysqli_fetch_assoc($empRecords)) {

  $user_ids = isset($row['User_ID']) ? $row['User_ID'] : '';
  $center_ids = json_decode($user_ids, true);
  

    $data[] = array( 
      "ID"=> $row['ID'],
      "subject_name" => $row['subject_name'],
      "course_name" => ucwords(strtolower($row['course_name'])),
      "sub_course_name" => ucwords(strtolower($row['sub_course_name'])),
      "Code" => $row['Code'],
      "Marks" => $row['Min_Marks'].'/'.$row['Max_Marks'],
      "Paper_Type"  => $row["Paper_Type"],
      "Credit"      => $row["Credit"],
      "files"      => $row["files"],
      "Semester"      => $row["Semester"],
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
