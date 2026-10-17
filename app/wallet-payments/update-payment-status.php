<?php
if (isset($_POST['id']) && isset($_POST['value'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = intval($_POST['id']);
  $value = intval($_POST['value']);

  $payment = $conn->query("SELECT * FROM Wallets WHERE Type = 1 AND ID  = $id");
  $payment = $payment->fetch_assoc();
  $student_id = $payment['Added_For'];

  $update = $conn->query("UPDATE Wallets SET Status = $value, Approved_By = " . $_SESSION['ID'] . ", Approved_On = now() WHERE ID = $id");
  if ($update) {
        // NEW DATA
        $new      = $conn->query("SELECT * FROM Wallets WHERE ID = $id");
        $new_data = $new->fetch_assoc();
        // LOG
        addLog($conn, $_SESSION['university_id'], $_SESSION['ID'], 'update', 'Wallets', $id, 'Wallet status updated', json_encode($old_data), json_encode($new_data));

        if ($value == 1) {
            echo json_encode(['status' => 200, 'message' => 'Amount added to user wallet successfully!']);
        } else {
            echo json_encode(['status' => 200, 'message' => 'Payment status updated successfully!']);
        }

    } else {
        echo json_encode(['status' => 400, 'message' => 'Something went wrong!']);
    }
}
