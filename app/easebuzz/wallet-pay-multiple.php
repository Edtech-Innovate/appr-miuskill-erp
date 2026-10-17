<?php
  ini_set('display_errors', 1); 

if (isset($_POST['online_amount'])) {
  session_start();
  require '../../includes/db-config.php';
//   include '../../includes/helpers.php';

  $amount = sprintf("%.1f", $_POST['online_amount']);
  $transaction_id = strtoupper(strtolower(uniqid()));
  $idstd = 0;
  $university_share = 0;
  $id = 0;
  $duration = 1;
  $balance = $amount;
  $add = $conn->query("INSERT INTO Wallet_Invoices (`User_ID`, `Student_ID`, `Duration`, `University_ID`, `Invoice_No`, `Amount`) VALUES (" . $_SESSION['ID'] . ", $id, $duration, " . $_SESSION['university_id'] . ", '$transaction_id', $amount)");
  $key = $_SESSION['access_key'];
  $salt = $_SESSION['secret_key'];
  $product_info = 'Fee Payment';
  if($amount > 20){
      if($_SESSION['vertical'] == 2){
            $edtech_share = $amount;
            $split_accounts = json_encode(array("IITS LLP Paramedical"=>$edtech_share));
        }else{
            $edtech_share = $amount;
            $split_accounts = json_encode(array("Edtech Innovate Pvt Ltd." => $edtech_share));
        }
    // print_r($_SESSION);die;
    // $edtech_share = $amount;
    //     $split_accounts = json_encode(array("Edtech Innovate Pvt Ltd." => $edtech_share));
  } else {
    echo json_encode(['status' => 400, 'message' => 'Amount must be grater than Rs.20!']);
    exit();
  }
  
  //$split_accounts = json_encode(array("IITS LLP Paramedical" => $iits_share, "Glocal University" => $university_share));

  $value = $key . '|' . $transaction_id . '|' . $amount . '|' . $product_info . '|' . trim($_SESSION['Name']) . '|' . trim($_SESSION['Email']) . '|||||||||||' . $salt;
  
  $hash = hash('sha512', $value);
    
  $conn->query("INSERT INTO Wallets (`Type`, `Amount`, `Transaction_ID`, `Added_By`, `University_ID`) VALUES (2, '$amount', '$transaction_id', '" . $_SESSION['ID'] . "', '" . $_SESSION['university_id'] . "')");

    // print_r($value);
    
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://pay.easebuzz.in/payment/initiateLink',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
      'key' => $key,
      'txnid' => $transaction_id,
      'amount' => $amount,
      'productinfo' => $product_info,
      'firstname' => trim($_SESSION['Name']),
      'phone' => $_SESSION['Mobile'],
      'email' => trim($_SESSION['Email']),
      'surl' => 'http://localhost:3000/wallet-response.php',
      'furl' => 'http://localhost:3000/wallet-response.php',
      'udf1' => "",
      'udf2' => "",
      'udf3' => "",
      'udf4' => "",
      'udf5' => "",
      'udf6' => "",
      'udf7' => "",
      "split_payments" => $split_accounts,
      'hash' => $hash
    ),
    CURLOPT_HTTPHEADER => array(
      'Cookie: csrftoken=snWOPdXYVpBAqLuFIUxANDKiKw3slBhr'
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);
  echo $response;
}
