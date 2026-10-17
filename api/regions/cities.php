<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

  if(isset($_GET['pincode'])){
    require '../../includes/db-config.php';

    $pincode = intval($_GET['pincode']);

    $options = array();

    $cities = $conn->query("SELECT City FROM Regions WHERE Pincode = $pincode");
    while($city = $cities->fetch_assoc()){
      $options[$city['City']] = $city['City'];
    }

    echo json_encode(['status'=>true, 'options'=>$options]);
  }
