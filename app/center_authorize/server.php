<?php
include '../../includes/db-config.php';

header('Content-Type: application/json');

$draw   = intval($_POST['draw'] ?? 1);
$start  = intval($_POST['start'] ?? 0);
$length = intval($_POST['length'] ?? 10);
$searchValue = $_POST['search']['value'] ?? '';
$batch = $_POST['batch_id'] ?? '';
$baseQuery = "FROM center_authorize WHERE 1=1";

// ✅ Batch filter
if (!empty($batch)) {
    $batch = mysqli_real_escape_string($conn, $batch);
    $baseQuery .= " AND batch = '$batch'";
}

// ✅ Search filter
$searchSQL = "";
if (!empty($searchValue)) {
    $searchValue = mysqli_real_escape_string($conn, $searchValue);
    $searchSQL = " AND (center_name LIKE '%$searchValue%' 
                    OR address LIKE '%$searchValue%' 
                    OR programs LIKE '%$searchValue%')";
}

$totalRecordsQuery = "SELECT COUNT(*) as total FROM center_authorize";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['total'];

$totalFilteredQuery = "SELECT COUNT(*) as total $baseQuery $searchSQL";
$totalFilteredResult = mysqli_query($conn, $totalFilteredQuery);
$totalFiltered = mysqli_fetch_assoc($totalFilteredResult)['total'];

$dataQuery = "SELECT id, center_name, type_id, date_of_issue, recieved_vertical,receiving_date, center_doc, dispatch_date, status, address, programs, batch, payment_type, amount, payment_proof
              $baseQuery $searchSQL 
              ORDER BY id DESC 
              LIMIT $start, $length";
$dataResult = mysqli_query($conn, $dataQuery);

$data = [];
while ($row = mysqli_fetch_assoc($dataResult)) {
    $data[] = $row;
}

$response = [
    "draw" => $draw,
    "recordsTotal" => intval($totalRecords),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

echo json_encode($response);

mysqli_close($conn);
