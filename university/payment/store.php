<?php
if($_POST['amount']!='' && $_POST['date_of_transaction']!='' && $_POST['urn_no']!='' && $_POST['mode_of_payment']!='')
{
    require '../../includes/db-config.php';
    $payment_type = $_POST['payment_type'];
    $paidFeeQuery = "select sum(amount) as paidAmount from university_payment where  payment_type = $payment_type and student_id=".$_POST['id'];
    $paidAmount = $conn->query($paidFeeQuery)->fetch_assoc();
    if($payment_type==1){
       $totalFeeQuery = "select SUM(University_Fee) AS University_fee from Student_Ledgers where  Type=1 and Source ='Miscellaneous Type Fee' AND Student_ID=".$_POST['id'];
    }else{
       $totalFeeQuery = "select University_fee from Sub_Courses where ID=".$_POST['courseId'];
    }
   
    $totalFee = $conn->query($totalFeeQuery)->fetch_assoc();
   
    if($paidAmount['paidAmount']>0)
    {   
        if((intval($paidAmount['paidAmount'])+intval($_POST['amount']))>$totalFee['University_fee'])
        {
            echo json_encode(array('status'=>'error','message'=>'Amount cannot more than pending amount','code'=>'1001'));
            exit;
        }
    }
    else
    {
        if($totalFee['University_fee']<$_POST['amount'])
        {
            echo json_encode(array('status'=>'error','message'=>'Amount cannot more than pending amount','code'=>'1002'));
            exit;
        }
    }
    $payment_type = isset($_POST['payment_type']) ? $_POST['payment_type']:'NULL';
    $submitQuery = "INSERT INTO `university_payment` (`student_id`, `amount`, `date_of_transaction`, `mode_of_payment`, `urn_no`, `payment_type`) VALUES ('".$_POST['id']."', '".$_POST['amount']."', '".$_POST['date_of_transaction']."', '".$_POST['mode_of_payment']."', '".$_POST['urn_no']."', $payment_type)";
    $runQuery = $conn->query($submitQuery);
    if($runQuery)
    {
        echo json_encode(array('status'=>'success','message'=>'University payment added successfully','code'=>'1003'));
        exit;
    }
    else
    {
        echo json_encode(array('status'=>'error','message'=>'Error in qury','code'=>'1004'));
        exit;
    }
}
else
{
    echo json_encode(array('status'=>'error','message'=>'All fields are mandatory','code'=>'1003'));
    exit;
}

?>