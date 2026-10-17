<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';

  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);
  $check = $conn->query("SELECT id FROM e_books WHERE id = $id");
  if ($check->num_rows > 0) {
    $check = $check->fetch_assoc();
    $delete = $conn->query("DELETE FROM e_books WHERE id = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'E-Book deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'E-Book not exists!']);
  }
}
