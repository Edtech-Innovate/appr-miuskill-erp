<?php
if ($_POST['amount'] != '' && $_POST['date_of_transaction'] != '' && $_POST['urn_no'] != '' && $_POST['mode_of_payment'] != '') {
    require '../../includes/db-config.php';
    $payment_type = isset($_POST['payment_type']) ? $_POST['payment_type'] : 'NULL';
    $paidFeeQuery = "select sum(amount) as paidAmount from university_payment where payment_type = $payment_type and student_id=" . $_POST['stu_id'];

    $edit_paid_fee_sql = $conn->query("select amount from university_payment where id =".$_POST['id'])->fetch_assoc();
    $edit_paid_fee = $edit_paid_fee_sql['amount'] ?? 0;
    // echo "<pre>"; 
    // print_r($edit_paid_fee);


    $paidAmount = $conn->query($paidFeeQuery)->fetch_assoc();
    if ($payment_type == 1) {
        $totalFeeQuery = "select SUM(University_Fee) AS University_fee from Student_Ledgers where  Type=1 and Source ='Miscellaneous Type Fee' AND Student_ID=" . $_POST['stu_id'];
    } else {
        $totalFeeQuery = "select University_fee from Sub_Courses where ID=" . $_POST['courseId'];
    }
    $totalFee = $conn->query($totalFeeQuery)->fetch_assoc();
   
  

    if ($paidAmount['paidAmount'] > 0) {
        if ((intval($paidAmount['paidAmount']) + intval($_POST['amount']) - intval($edit_paid_fee)) > $totalFee['University_fee']) {
            echo json_encode(array('status' => 'error', 'message' => 'Amount cannot more than pending amount ', 'code' => '1001'));
            exit;
        }
    } else {
        if ($totalFee['University_fee'] < $_POST['amount']) {
            echo json_encode(array('status' => 'error', 'message' => 'Amount cannot more than pending amount', 'code' => '1002'));
            exit;
        }
    }

    $submitQuery = "update `university_payment` set `amount`='" . $_POST['amount'] . "',`date_of_transaction`='" . $_POST['date_of_transaction'] . "',`mode_of_payment`='" . $_POST['mode_of_payment'] . "',`urn_no`='" . $_POST['urn_no'] . "' where id=" . $_POST['id'];
    $runQuery = $conn->query($submitQuery);
    if ($runQuery) {
        echo json_encode(array('status' => 'success', 'message' => 'University payment added successfully', 'code' => '1003'));
        exit;
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Error in qury', 'code' => '1004'));
        exit;
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'All fields are mandatory', 'code' => '1003'));
    exit;
}

?>