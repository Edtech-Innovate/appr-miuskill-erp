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
  $orderby = "ORDER BY Fee_Dropdowns.ID ASC";
}

$feeHeads = array();
$fee_heads = $conn->query("SELECT ID, Name FROM Fee_Structures WHERE University_ID = $university_id");
while($fee_head = $fee_heads->fetch_assoc()){
  $feeHeads[$fee_head['ID']] = $fee_head['Name'];
}

## Search 
$searchQuery = " ";
if($searchValue != ''){
  $searchQuery = " AND (Fee_Dropdowns.Name like '%".$searchValue."%' OR Admission_Types.Name like '%".$searchValue."%')";
}

## Total number of records without filtering
$all_count=$conn->query("SELECT COUNT(ID) as allcount FROM Fee_Dropdowns WHERE University_ID = $university_id");
$records = mysqli_fetch_assoc($all_count);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$filter_count = $conn->query("SELECT COUNT(Fee_Dropdowns.ID) as filtered FROM Fee_Dropdowns LEFT JOIN Admission_Types ON Fee_Dropdowns.Admission_Type_ID = Admission_Types.ID WHERE Fee_Dropdowns.University_ID = $university_id $searchQuery");

$records = mysqli_fetch_assoc($filter_count);
$totalRecordwithFilter = $records['filtered'];

## Fetch records
$result_record = "SELECT Fee_Dropdowns.ID, Fee_Dropdowns.Status, Fee_Dropdowns.Name, Admission_Types.Name as Admission_Type_ID, Duration, Fee_Structure, Semester, Coupon, Late_Fee FROM Fee_Dropdowns LEFT JOIN Admission_Types ON Fee_Dropdowns.Admission_Type_ID = Admission_Types.ID WHERE Fee_Dropdowns.University_ID = $university_id $searchQuery $orderby LIMIT ".$row.",".$rowperpage;
$empRecords = mysqli_query($conn, $result_record);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {

  $fee_structures = json_decode($row['Fee_Structure'], true);
  $semesters = !empty($row['Semester']) ? json_decode($row['Semester'], true) : [];

  $structures = array();
  foreach($fee_structures as $fee_structure){
    $sem = array_key_exists($fee_structure, $semesters) ? implode(", ",$semesters[$fee_structure]) : '';
    $structures[] = $feeHeads[$fee_structure].' - '.$sem;
  }


  $data[] = array( 
    "Name" => $row["Name"],
    "Status" => $row["Status"],
    "Admission_Type_ID" => $row["Admission_Type_ID"],
    "Duration" => $row["Duration"],
    "Fee_Structure" => implode("<br>", $structures),
    "Coupon" => $row["Coupon"],
    "Late_Fee" => $row["Late_Fee"],
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
