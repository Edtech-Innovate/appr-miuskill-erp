<?php
## Database configuration
include '../../includes/db-config.php';
session_start();
## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
if(isset($_POST['order'])){
  $columnIndex = $_POST['order'][10]['column']; // Column index
  $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
  $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
}
$searchValue = mysqli_real_escape_string($conn,$_POST['search']['value']); // Search value

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Users.ID ASC";
}

$university_query  = '';
$session_query = '';
if($_SESSION['Role']!='Administrator'){
  $university_query = " AND University_User.University_ID = ".$_SESSION['university_id'];

  $current_session = $conn->query("SELECT ID FROM Admission_Sessions WHERE University_ID = ".$_SESSION['university_id']." AND Current_Status = 1");
  if($current_session->num_rows>0){
    $current_session = mysqli_fetch_assoc($current_session);
    $session_query = " AND Admission_Session_ID = ".$current_session['ID'];
  }
}
$session_query = '';
$center_query ="";
if($_SESSION['Role']=='University Head'){
  $center_ids = [];
  $user_ids = [];
  $getcounselloer = $conn->query("SELECT User_ID FROM University_User WHERE Reporting = " . intval($_SESSION['ID']));
  if ($getcounselloer->num_rows > 0) {
      $centerids = $getcounselloer->fetch_all(MYSQLI_ASSOC);
      $center_ids = array_column($centerids, 'User_ID');
  }
  
  $getnotalloteduser = $conn->query("SELECT Users.ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Users.Role = 'University Head' AND University_User.User_ID IS NULL");
  if ($getnotalloteduser->num_rows > 0) {
      while ($dataArr = $getnotalloteduser->fetch_assoc()) {
          $user_ids[] = $dataArr['ID'];
      }
  }
  $centers = array_unique(array_merge($center_ids, $user_ids));
  if (!empty($centers)) {
      $center_ids_str = implode(',', array_map('intval', $centers));
     $center_query .= " AND Users.ID IN ($center_ids_str) ";
  } 
  

}

## Search 
$searchQuery = " ".$center_query;
if($searchValue != ''){
  $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%')";
}

$universityManagerQuery = "";
if($_SESSION['Role']!="Administrator")
{
   $universityManagerQuery =  "and University_User.Reporting=".$_SESSION['ID'];
}
## Total number of records without filtering
$all_count=$conn->query("SELECT Users.ID   FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Counsellor' $university_query GROUP BY Users.ID ");
// $records = mysqli_fetch_assoc($all_count);
$totalRecords = $all_count->num_rows ;

## Total number of record with filtering
$filter_count = $conn->query("SELECT Users.ID FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Counsellor' $university_query $searchQuery GROUP BY Users.ID ");
// $records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $filter_count->num_rows;

## Fetch records
$result_record = "SELECT `ID`, `Name`, `Email`, `Mobile`, `Code`, `Status`, `Photo`, CAST(AES_DECRYPT(Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Counsellor' $universityManagerQuery $university_query $searchQuery GROUP BY Users.ID $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  $alloted = [];
  $alloted_universities = $conn->query("SELECT CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as University FROM University_User LEFT JOIN Universities ON University_User.University_ID = Universities.ID WHERE `User_ID` = " . $row['ID'] . "");
  if ($alloted_universities->num_rows > 0) {
    while ($alloted_university = $alloted_universities->fetch_assoc()) {
      $alloted[] = $alloted_university['University'];
    }
  }

  $data[] = array( 
    "Photo"=> $row['Photo'],
    "Name" => $row['Name'],
    "Email" => $row['Email'],
    "Mobile" => $row['Mobile'],
    "Code" => $row['Code'],
    "Password" => $row['password'],
    "University" => implode('<br>', $alloted),
    "Status"  => $row["Status"],
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
