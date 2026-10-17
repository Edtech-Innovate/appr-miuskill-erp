<?php
## Database configuration
include '../../includes/db-config.php';
ini_set('display_errors',1);
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length'] ?? "all";// Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][0]['column']; // Column index
  $columnName = 'Syllabi.'.$_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}


$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Syllabi.ID DESC";
}
$query='';
// Admin Query
$query = " AND Syllabi.University_ID = ".$_SESSION['university_id'];


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Syllabi.Name like '%".$searchValue."%' OR Syllabi.Code like '%".$searchValue."%' OR Syllabi.Paper_Type like '%".$searchValue."%' )";
}
$filterBysubCourse = "";
if (isset($_SESSION['subCourseFilter'])) {
  $searchQuery .= $_SESSION['subCourseFilter'];
}

$filterByDuration = "";
if (isset($_SESSION['durationFilter'])) {
  $searchQuery.= $_SESSION['durationFilter'];
}

if($_SESSION['Role']=='Faculty'){
  $searchQuery.= '  AND Faculty_ID = '.$_SESSION['ID'];
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Syllabi WHERE ID IS NOT NULL $query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];
     if ($rowperpage == "all") {
        $rowperpage = $totalRecords;
      }

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Syllabi.ID) as filtered FROM Syllabi LEFT JOIN Courses ON Syllabi.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID WHERE Syllabi.University_ID = 1  $searchQuery");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];
## Fetch records
$result_record = "SELECT Syllabi.Name,Syllabi.Syllabus,Syllabi.Sub_Course_ID,Users.Name as faculty_name,Syllabi.Semester,Syllabi.University_ID, Syllabi.ID, Syllabi.Category ,Credit,Syllabi.Code, Paper_Type, Semester,CONCAT(Min_Marks,'/',Max_Marks) as marks , CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as course_name, groups.Name as group_name, CASE WHEN Syllabi.Category=0 THEN 'Compulsory' ELSE 'Elective' END AS Category FROM Syllabi LEFT JOIN Courses ON Syllabi.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID LEFT JOIN groups ON Syllabi.Group_ID = groups.ID LEFT JOIN Users ON Syllabi.Faculty_ID = Users.ID AND Role = 'Faculty' WHERE Syllabi.University_ID = 1  $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$results = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($results)) {

    $data[] = array( 
      "Code" => $row["Code"],
      "Name" => $row["Name"],
      "faculty_name" => isset($row["faculty_name"]) ? $row['faculty_name']: "",
      "course_name" => $row["course_name"],
      "Sub_Course_ID" => $row["Sub_Course_ID"],
      "Paper_Type"=>$row["Paper_Type"],
      "Marks" => $row["marks"],
      "Group_ID" => $row["group_name"],
      "Category"=> $row["Category"],
      "Credit"  => $row["Credit"],
      "ID"      => $row["ID"],
      "Semester"      => $row["Semester"],
      "uni_id"      => $row["University_ID"],
      "files"      => $row["Syllabus"],

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
