<?php
  if(isset($_POST['id'])){
    require '../../../includes/db-config.php';

    $id = intval($_POST['id']);

    $status = $conn->query("SELECT Late_Fee FROM Fee_Dropdowns WHERE ID = $id");
    $status = mysqli_fetch_assoc($status);

    $status = $status['Late_Fee']==1 ? 0 : 1;

    $update = $conn->query("UPDATE Fee_Dropdowns SET Late_Fee = $status WHERE ID = $id");
    if($update){
      echo json_encode(['status'=>true, 'message'=>'Late Fee Status updated successfully!']);
    }else{
      echo json_encode(['status'=>false, 'message'=>'Something went wrong!']);
    }
  }
