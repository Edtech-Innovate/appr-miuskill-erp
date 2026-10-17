<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../includes/db-config.php';
session_start();

## Read DataTables parameters
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

## Column mapping
$columns = [
    0 => 'ID',           // S.No.
    1 => 'file_name',
    2 => 'file_path',
    3 => 'ID',           // Action column
];

## Sorting
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'ID';
$orderDir = isset($_POST['order'][0]['dir']) && in_array(strtolower($_POST['order'][0]['dir']), ['asc', 'desc']) ? $_POST['order'][0]['dir'] : 'DESC';
$orderby = "ORDER BY $orderColumn $orderDir";

## Search Filter
$searchQuery = "";
$paramTypes = "";
$params = [];

if (!empty($searchValue)) {
    $searchQuery .= " AND (file_name LIKE ? OR file_path LIKE ?)";
    $paramTypes .= "ss";
    $searchTerm = "%$searchValue%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

## Total number of records without filtering
$totalQuery = "SELECT COUNT(ID) as total FROM marksheet_files WHERE ID IS NOT NULL";
$totalResult = $conn->query($totalQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];

## Total number of records with filtering
if (!empty($searchQuery)) {
    $filterQuery = "SELECT COUNT(ID) as filtered FROM marksheet_files WHERE ID IS NOT NULL $searchQuery";
    $stmt = $conn->prepare($filterQuery);
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $totalFiltered = $res->fetch_assoc()['filtered'];
    $stmt->close();
} else {
    $totalFiltered = $totalRecords;
}

## Fetch records
$dataQuery = "SELECT ID, file_name, file_path  
              FROM marksheet_files 
              WHERE ID IS NOT NULL $searchQuery 
              $orderby 
              LIMIT ?, ?";

$stmt = $conn->prepare($dataQuery);

// Add pagination params
$paramTypes .= "ii";
$params[] = $start;
$params[] = $length;
$stmt->bind_param($paramTypes, ...$params);

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "ID" => $row["ID"],
        "file_name" => htmlspecialchars($row["file_name"]),
        "file_path" => $row["file_path"],
    ];
}
$stmt->close();

## Response
$response = [
    "draw" => $draw,
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalFiltered,
    "aaData" => $data
];
// echo'<pre>';print_r($data);die;
header('Content-Type: application/json');
echo json_encode($response);
exit;
