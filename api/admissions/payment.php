<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST,GET');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

  if(isset($_POST['status']) && isset($_POST['txnid'])){

    require '../../includes/db-config.php';

    $inserted_id = intval($_POST['udf1']);

    if($_POST['status']!='success'){
      $amount = intval($_POST['amount']);
      $txnid = mysqli_real_escape_string($conn, $_POST['txnid']);
      
      $student = $conn->query("SELECT Added_For, University_ID, Duration FROM Students WHERE ID = $inserted_id");
      $student = $student->fetch_assoc();

      $invoice = $conn->query("INSERT INTO Invoices (Invoice_No, `User_ID`, Student_ID, Duration, University_ID, Amount) VALUES ('$txnid', ".$student['Added_For'].", ".$inserted_id.", ".$student['Duration'].", ".$student['University_ID'].", ".(-1)*$amount.")");
      if($invoice){
        $meta = json_encode(['msg'=>$_POST]);
        $payment = $conn->query("INSERT INTO Payments (Type, Transaction_ID, Gateway_ID, Payment_Mode, Amount, Added_By, University_ID, Source, Sub_Source, Meta, Status) VALUES (2, '".$_POST['txnid']."', '".$_POST['easepayid']."', '".$_POST['mode']."', '".$_POST['amount']."', '".$student['Added_For']."', '".$student['University_ID']."', '".$_POST['udf6']."', '".$_POST['udf7']."', '$meta', 1)");
        if($payment){
          $fee = json_encode(['Paid'=>$_POST['amount']]);
          $date = date("Y-m-d");
          $ledger = $conn->query("INSERT INTO Student_Ledgers (Date, Student_ID, Duration, University_ID, Type, Source, Transaction_ID, Fee, Status) VALUES ('$date', $inserted_id, ".$student['Duration'].", ".$student['University_ID'].", 2, '".$_POST['udf7']."', '".$_POST['easepayid']."', '$fee', 1)");
          if($ledger){
            $update = $conn->query("UPDATE Students SET Process_By_Center = now(), Step = 2 WHERE ID = $inserted_id");
            if($update){
              echo json_encode(['status'=>true, 'message'=>'Payment added successlly!']);
            }else{
              echo json_encode(['status'=>false,'message'=>'Something went wrong!']);
            }
          }else{
            echo json_encode(['status'=>false,'message'=>'Something went wrong!']);
          }
        }else{
          echo json_encode(['status'=>false,'message'=>'Something went wrong!']);
        }
      }else{
        echo json_encode(['status'=>false,'message'=>'Something went wrong!']);
      }
    }else{
      exit(json_encode(['status'=>false, 'message'=>'Payment Failed!']));
    }
  }
