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
  $orderby = "ORDER BY Users.ID ASC";
}

$university_query  = '';
if($_SESSION['Role']=='University Head'){
   //$university_query = " AND University_ID IN (".$_SESSION['all_university_ids'].")";

 //$university_query = " AND University_User.University_ID = ".$_SESSION['university_id'];
}elseif($_SESSION['Role']=='Center'){
  $university_query = " AND Center_SubCenter.Center = ".$_SESSION['ID'];
}
$center_query = '';
if ($_SESSION['Role'] == 'University Head') {
    $uid = intval($_SESSION['ID']);
    $center_query = '';

    // Step 1: Get counselors under the University Head
    $result = $conn->query("SELECT User_ID, University_ID FROM University_User WHERE Reporting = $uid");
    $counselor_data = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    $counselor_ids = array_column($counselor_data, 'User_ID');
    $university_ids = array_column($counselor_data, 'University_ID');

    $subcenter_ids = [];

    if (!empty($counselor_ids)) {
        $counselor_ids_str = implode(',', array_map('intval', $counselor_ids));
        $university_ids_str = implode(',', array_map('intval', $university_ids));

        // Step 2: Get Center IDs under those counselors
        $sc_q = $conn->query("SELECT GROUP_CONCAT(University_User.User_ID) AS sc_ids FROM Users 
                              LEFT JOIN University_User ON Users.ID = University_User.User_ID 
                              WHERE Role = 'Center' AND Reporting IN ($counselor_ids_str) 
                              AND University_User.University_ID IN ($university_ids_str)");
        $sc_ids_str = $sc_q ? $sc_q->fetch_assoc()['sc_ids'] : '';

        // Step 3: Get Sub-Center IDs under those Centers
        if (!empty($sc_ids_str)) {
            $sub_q = $conn->query("SELECT GROUP_CONCAT(Sub_Center) AS sub_center_ids FROM Center_SubCenter 
                                   WHERE Center IN ($sc_ids_str)");
            $sc_ids = $sub_q ? explode(',', $sub_q->fetch_assoc()['sub_center_ids']) : [];
            $subcenter_ids = array_merge($subcenter_ids, $sc_ids);
        }
    }

    // Step 4: Get all Sub-Center user IDs
    $sub_uq = $conn->query("SELECT GROUP_CONCAT(ID) as ids FROM Users WHERE Role = 'Sub-Center'");
    $user_sub_ids = $sub_uq ? explode(',', $sub_uq->fetch_assoc()['ids']) : [];

    // Final merge and build center_query
    $final_ids = array_unique(array_filter(array_merge($subcenter_ids, $user_sub_ids)));

    if (!empty($final_ids)) {
        $center_query = " AND Users.ID IN (" . implode(',', array_map('intval', $final_ids)) . ") GROUP BY Users.ID";
    }

}


//echo $center_query; die;

## Search 
$searchQuery = " ".$center_query;
if($searchValue != ''){
  $searchQuery = " AND (Users.Name like '%".$searchValue."%' OR Users.Code like '%".$searchValue."%' OR Users.Email like '%".$searchValue."%' OR Users.Mobile like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count= $conn->query("SELECT COUNT(Users.ID) as allcount FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center LEFT JOIN University_User ON Users.ID = University_User.User_ID WHERE Role = 'Sub-Center' $university_query");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT Users.ID FROM Users LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center  LEFT JOIN University_User ON Users.ID = University_User.User_ID LEFT JOIN Users as U1 ON Center_SubCenter.Center = U1.ID WHERE Users.Role = 'Sub-Center' $university_query $searchQuery");
//$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter =$filter_count->num_rows;

## Fetch records
 $result_record = "SELECT Users.`ID`, Users.`Name`, Users.`Email`, Users.`Mobile`, Users.`Code`, Users.`Status`, Users.`Photo`, CONCAT(Users.Name, ' (', Users.Code, ')') AS `Reporting`, CAST(AES_DECRYPT(Users.Password, '60ZpqkOnqn0UQQ2MYTlJ') AS CHAR(50)) password FROM Users 
LEFT JOIN Center_SubCenter ON Users.ID = Center_SubCenter.Sub_Center 
LEFT JOIN University_User ON Users.ID = University_User.User_ID 
WHERE Users.Role = 'Sub-Center' $university_query $searchQuery $orderby LIMIT ".$row.",".$rowperpage;


$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
  
  $admissions = $conn->query("SELECT COUNT(ID) as Applications FROM Students WHERE Added_For = ".$row['ID']."");
  $admissions = mysqli_fetch_assoc($admissions);

  $data[] = array( 
    "Photo"=> $row['Photo'],
    "Name" => $row['Name'],
    "Email" => $row['Email'],
    "Mobile" => $row['Mobile'],
    "Code" => $row['Code'],
    "Reporting" => $row['Reporting'],
    "Admission" => $admissions['Applications'],
    "Password" => $row['password'],
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
