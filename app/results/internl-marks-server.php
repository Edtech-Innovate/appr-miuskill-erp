<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
ini_set('display_errors',1);
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
  $orderby = "ORDER BY Students.ID DESC";
}

$filterByUsers = "";
if (isset($_SESSION['usersFilter'])) {
  $filterByUsers = $_SESSION['usersFilter'];
}

$filterBysubCourse = "";
if (isset($_SESSION['subCourseFilter'])) {
  $filterBysubCourse = $_SESSION['subCourseFilter'];
}

$filterBySubCourses = "";
if (isset($_SESSION['filterBySubCourses'])) {
  $filterBySubCourses = $_SESSION['filterBySubCourses'];
}


$filterByExamStatus = "";
if (isset($_SESSION['filterByExamStatus'])) {
  $filterByExamStatus = $_SESSION['filterByExamStatus'];
}

$filterByDuration = "";
if (isset($_SESSION['filterByDuration'])) {
    $filterByDuration = $_SESSION['filterByDuration'];
}

$filterByDuration = "";
if (isset($_SESSION['filterByDuration'])) {
    $filterByDuration = $_SESSION['filterByDuration'];
}

$filterByVerticalType = "";
if (isset($_SESSION['filterByVerticalType'])) {
  $filterByVerticalType = $_SESSION['filterByVerticalType'];
}
## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Students.First_Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Name like '%".$searchValue."%' OR Sub_Courses.Name like '%".$searchValue."%' OR Students.Enrollment_No like '%".$searchValue."%'  OR Students.Unique_ID like '%".$searchValue."%'  OR Students.Duration like '%".$searchValue."%' OR Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%')";
}
$userQuery ='';
$subcenterlist = [];
if($_SESSION['Role']=="Center"){
  $getall  = $conn->query("SELECT Sub_Center FROM Center_SubCenter WHERE Center = ".$_SESSION['ID']);
  array_push($subcenterlist, $_SESSION['ID']);
  while($subcenter = $getall->fetch_assoc()){
    $subcenterlist[] = $subcenter['Sub_Center'];
  }
   $userQuery = " AND Students.Added_For IN(".implode(',', $subcenterlist).")";
} else if($_SESSION['Role']=="Sub-Center") {
  $userQuery = " AND Students.Added_For = ".$_SESSION['ID'];
}

$filterByUniversity = " AND Students.University_ID =".$_SESSION['university_id']." AND Enrollment_No IS NOT NULL";
$searchQuery .= $filterByUniversity. $filterBysubCourse.$filterBySubCourses .$filterByUsers.$userQuery.$filterByExamStatus.$filterByDuration.$filterByVerticalType;


## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(Students.ID) as allcount  FROM Students LEFT JOIN Sub_Courses on Students.Sub_Course_ID =  Sub_Courses.ID  LEFT JOIN Courses on Students.Course_ID =  Courses.ID  LEFT JOIN Users on Students.Added_For =  Users.ID  WHERE 1=1 $searchQuery");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Students.ID) as filtered FROM Students LEFT JOIN Sub_Courses on Students.Sub_Course_ID =  Sub_Courses.ID  LEFT JOIN Courses on Students.Course_ID =  Courses.ID  LEFT JOIN Users on Students.Added_For =  Users.ID  WHERE 1=1  $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Students.ID, Students.Duration, Students.Enrollment_No,Students.Unique_ID, Students.Mother_Name, Students.Father_Name,  CONCAT(TRIM(CONCAT(Students.First_Name, ' ', Students.Middle_Name, ' ', Students.Last_Name))) AS full_name, Sub_Courses.Name AS sub_course_name,Courses.Short_Name as course_short_name, Users.Code, Users.Name as user_name, Users.Code as user_code  FROM Students LEFT JOIN Sub_Courses on Students.Sub_Course_ID =  Sub_Courses.ID  LEFT JOIN Courses on Students.Course_ID =  Courses.ID  LEFT JOIN Users on Students.Added_For=  Users.ID  WHERE 1=1  $searchQuery  $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  $enroll = $row['Enrollment_No'];
        $duration = $row['Duration'];
   
    $data[] = array( 
      "ID"=> $row['ID'],
      "Unique_ID"=> $row['Unique_ID'],
      "full_name" => $row['full_name'],
      "sub_course_name" => ucwords(strtolower($row['sub_course_name'])),
      "Enrollment_No" => trim($row['Enrollment_No']),
      "course_short_name" => $row['course_short_name'],
      "Duration"      => $duration,
      "Mother_Name"      => $row["Mother_Name"],
      "Father_Name"      => $row["Father_Name"],
      "user_code"      => $row["user_code"],
      "user_name"      => $row["user_name"].'('.$row['Code'].')',
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
