<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

  if(isset($_GET['pincode'])){
    require '../../includes/db-config.php';

    $pincode = intval($_GET['pincode']);

    $options = array();

    $districts = $conn->query("SELECT District FROM Regions WHERE Pincode = $pincode GROUP BY District ORDER BY ID DESC");
    while($district = $districts->fetch_assoc()){
      $options[$district['District']] = $district['District'];
    }

    echo json_encode(['status'=>true, 'options'=>$options]);
  }
