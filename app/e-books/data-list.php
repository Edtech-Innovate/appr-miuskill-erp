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
  $orderby = "ORDER BY e_books.id ASC";
}

// Admin Query
$query = "";
$query = " AND e_books.University_ID = ".$_SESSION['university_id'];

## Search 
$searchQuery = " ";
if ($searchValue != '') {
  $searchQuery = " AND (Syllabi.Name like '%" . $searchValue . "%' OR Sub_Courses.Name like '%" . $searchValue . "%' OR Sub_Courses.Short_Name like '%" . $searchValue . "%' OR Courses.Name like '%" . $searchValue . "%' OR Courses.Short_Name like '%" . $searchValue . "%')";
}

## Total number of records without filtering

$all_count = $conn->query("SELECT COUNT(e_books.id) as allcount FROM e_books LEFT JOIN Sub_Courses ON Sub_Courses.ID = e_books.sub_course_id  LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID LEFT JOIN Syllabi ON Syllabi.ID = e_books.subject_id  WHERE e_books.status!=2 $query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(e_books.id) as filtered FROM e_books  LEFT JOIN Sub_Courses ON Sub_Courses.ID = e_books.course_id  LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID  LEFT JOIN Syllabi ON Syllabi.ID = e_books.subject_id WHERE e_books.status!=2 $searchQuery $query");
$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT e_books.`id`, e_books.`file_type`,e_books.`title`,e_books.`semester_id` as duration, Sub_Courses.`Name` as sub_course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, e_books.`status`, e_books.`semester_id` as duration, Courses.Name as course_name FROM e_books  LEFT JOIN Sub_Courses ON Sub_Courses.ID = e_books.sub_course_id  LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID LEFT JOIN Syllabi ON Syllabi.ID = e_books.subject_id WHERE e_books.status !=2  $searchQuery $query $orderby LIMIT " . $row . "," . $rowperpage;
$results = mysqli_query($conn, $result_record);
$data = array();


while ($row = mysqli_fetch_assoc($results)) {

  $data[] = array(
    "sub_course_name" => ucwords(strtolower($row["sub_course_name"])),
    "course_name" => ucwords(strtolower($row["course_name"])),
    "subject_name" => ucwords(strtolower($row["subject_name"])),
    "file_type" => strtoupper($row["file_type"]),
    "title" => ucwords(strtolower($row['title'])),
    "status" => $row["status"],
    "duration" => $row["duration"],
    "ID" => $row["id"],
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
