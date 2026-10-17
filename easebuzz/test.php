<?php
 $api_key = 'XKKNMVGRVQ';
 $api_secret ='0UXO7NJU52';
 $amount = sprintf("%.1f", 1);
 $receipt = uniqid();
 $transactionId = strtoupper(strtolower(uniqid('SAU')));
 $hash = uniqid();
 $hash = hash('md5',$hash);
 

 include_once('easebuzz_payment_gateway.php');

 // set merchant key, salt and evn
 $MERCHANT_KEY = $api_key;
 $SALT = $api_secret;
 $ENV = "prod"; // "test for test enviroment or "prod" for production enviroment

 // create object and pass key, salt and env
 $easebuzzObj = new Easebuzz($MERCHANT_KEY, $SALT, $ENV);


 // data format
 $postData = array (
    "txnid" => $transactionId,
    "amount" => $amount,
    "firstname" => 'Test',
    "email" => 'test@test.com',
    "phone" => '98989899993',
    "productinfo" => "Fee",
    "surl" => "http://localhost",
    "furl" => "http://localhost",
    "hash" => $hash
 );

 // print_r($postData); exit();

 // call initiatePaymentAPI method and send data
 $easebuzzObj->initiatePaymentAPI($postData);


 // Note:- initiate payment API response will get for success URL or failure URL using HTTP form post

 // set $SALT
 $SALT = $api_secret;

 // create Easebuzz class object and pass $SALT
 $easebuzzObj = new Easebuzz($MERCHANT_KEY = null, $SALT, $ENV = null);

 // call Easebuzz class methods or functions
 $result = $easebuzzObj->easebuzzResponse( $_POST );

