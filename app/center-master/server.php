<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
  $orderby = "ORDER BY Users.ID DESC";
}
$vertical = intval($_SESSION['vertical']);
$center_query = "";
$vertical_query = "";
if(in_array($_SESSION['Role'], ['Counsellor','Sub-Counsellor'])){
  $center_query = " AND Users.Created_By = ".$_SESSION['ID'];
}elseif($_SESSION['Role']!="Administrator"){
  $check_has_unique_center_code = $conn->query("SELECT Center_Suffix FROM Universities WHERE ID = ".$_SESSION['university_id']." AND Has_Unique_Center = 1");
  if($check_has_unique_center_code->num_rows>0){
    $center_suffix = mysqli_fetch_assoc($check_has_unique_center_code);
    $center_suffix = $center_suffix['Center_Suffix'];
    $center_query = " AND Code LIKE '$center_suffix%' AND Users.Is_Unique = 1 ";
  }else{
    $center_query = " AND Users.Is_Unique = 0";
  }
}
if($_SESSION['Role']!="Administrator"){
    $vertical_query = " and Users.vertical in($vertical)";
}

$center_query = "";
$counselloer_query = "";

if ($_SESSION['Role'] == 'University Head') {

    $user_id = intval($_SESSION['ID']);
    
    $sc_row = $center_row = '';
    // print_r($user_id);die;
    //  Get User_IDs of counselors who report to the University Head
    $getcounselloer = $conn->query("SELECT User_ID FROM University_User WHERE Reporting = $user_id");
    if ($getcounselloer && $getcounselloer->num_rows > 0) {

        $center_ids = array_column($getcounselloer->fetch_all(MYSQLI_ASSOC), 'User_ID');
        $center_ids_str = implode(',', array_map('intval', $center_ids)); 
        // print_r($center_ids_str);die;
        //  Get Center IDs that report to those counselors
        $counselloer_query = " AND Reporting IN ($center_ids_str)";
        $result = $conn->query("
            SELECT GROUP_CONCAT(University_User.User_ID) AS sc_ids 
            FROM Users 
            LEFT JOIN University_User ON Users.ID = University_User.User_ID 
            WHERE Role = 'Center' $counselloer_query
        ");

        if ($result && $result->num_rows > 0) {
            $sc_row = $result->fetch_assoc()['sc_ids'] ?? '';
        }
        // print_r($sc_row);die;
    }

    //  Get Center IDs that are standalone (not assigned)
    $getQuery = $conn->query("
        SELECT GROUP_CONCAT(Users.ID) AS ids 
        FROM Users 
        LEFT JOIN University_User ON Users.ID = University_User.User_ID OR Users.ID = University_User.Reporting 
        WHERE Role = 'Center' AND Reporting IS NULL AND User_ID IS NULL
    ");

    if ($getQuery && $getQuery->num_rows > 0) {
        $center_row = $getQuery->fetch_assoc()['ids'] ?? '';
    }

    //  Merge both Center ID lists and form final WHERE clause
    $merged_ids = array_filter(array_merge(
        explode(',', $sc_row ?? ''),
        explode(',', $center_row ?? '')
    ));

    if (!empty($merged_ids)) {
        $query_ids = implode(',', array_map('intval', $merged_ids));
        $center_query .= " AND Users.ID IN ($query_ids) $vertical_query";
    }
}
// print_r($center_query);die;

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%' OR CONCAT(Reporting.Name, ' (', Reporting.Code, ')') LIKE '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Users WHERE Role = 'Center'  $vertical_query $center_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Users.ID) as filtered FROM Users LEFT JOIN Users as Reporting on Users.Created_By = Reporting.ID WHERE Users.Role = 'Center'  $vertical_query $center_query $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Users.ID, Users.Name, Users.CanCreateSubCenter, Users.Email, Users.Code, Users.Status, Users.Photo, CAST(AES_DECRYPT(Users.Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password, CONCAT(Reporting.Name, ' (', Reporting.Code, ')') as Created_By FROM Users LEFT JOIN Users as Reporting on Users.Created_By = Reporting.ID WHERE Users.Role = 'Center'  $vertical_query $center_query $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  
    $data[] = array( 
      "Photo"=> $row['Photo'],
      "Name" => $row['Name'],
      "Email" => $row['Email'],
      "Code" => $row['Code'],
      "Password" => $row['password'],
      "CanCreateSubCenter" => $row['CanCreateSubCenter'],
      "Status"  => $row["Status"],
      "Created_By" => $row['Created_By'],
      "ID"      => $row["ID"],
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
