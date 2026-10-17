<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

  if(isset($_GET['pincode'])){
    require '../../includes/db-config.php';

    $pincode = intval($_GET['pincode']);

    $options = array();

    $state = $conn->query("SELECT State FROM Regions WHERE Pincode = $pincode");
    $state = mysqli_fetch_assoc($state);

    $options[$state['State']] = $state['State'];
    
    echo json_encode(['status'=>true, 'options'=>$options]);
  }
