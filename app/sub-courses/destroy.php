<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';

  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);

  $students = $conn->query("SELECT ID FROM Students WHERE Sub_Course_ID = $id");
  if ($students->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Students exists!']);
    exit();
  }

  $check = $conn->query("SELECT ID, University_ID FROM Sub_Courses WHERE ID = $id");
  if ($check->num_rows > 0) {
    $check = $check->fetch_assoc();
    $delete = $conn->query("DELETE FROM Fee_Constant WHERE Sub_Course_ID = $id");
    $delete = $conn->query("DELETE FROM Sub_Courses WHERE ID = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Specialization deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => 'Specialization not exists!']);
  }
}
