<?php
## Database configuration
include '../../../includes/db-config.php';
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
$university_id = intval($_POST['university_id']);

if(isset($columnSortOrder)){
  $orderby = "ORDER BY $columnName $columnSortOrder";
}else{
  $orderby = "ORDER BY Schemes.ID ASC";
}

// Fee Structures
$structures = array();
$fee_structures = $conn->query("SELECT ID, Name FROM Fee_Structures WHERE University_ID = $university_id");
while($fee_structure = $fee_structures->fetch_assoc()){
  $structures[$fee_structure['ID']] = $fee_structure['Name'];
}


## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Schemes.Name like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Schemes WHERE University_ID = $university_id");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(ID) as filtered FROM Schemes WHERE University_ID = $university_id $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT `ID`, `Status`, `Name`, `Fee_Structure` FROM Schemes WHERE University_ID = $university_id $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

  $alloted = array();
  $fee_structures = !empty($row['Fee_Structure']) ? json_decode($row['Fee_Structure'], true) : array();
  foreach($fee_structures as $fee_structure){
    $alloted[] = $structures[$fee_structure];
  }
  

  $data[] = array( 
    "Name" => $row["Name"],
    "Status" => $row["Status"],
    "Fee_Structure" => implode(", ", $alloted),
    "ID"   => $row["ID"],
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
