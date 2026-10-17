<?php
if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['id'])) {
  require '../../includes/db-config.php';
  session_start();

  $id = mysqli_real_escape_string($conn, $_GET['id']);
  

  $check = $conn->query("SELECT id,thumbnail_url,video_url FROM video_lectures WHERE id = $id");
  if ($check->num_rows > 0) {
    // $data =[];
    $data = $check->fetch_assoc();
    if (!empty($data['video_url'])) {
      $filePath = '../../'.$data['video_url'];
      if (file_exists('../../'.$data['video_url'])) {
        
        unlink($filePath);
      }
    }

    if (!empty($data['thumbnail_url'])) {
        $thumbnail_url_filePath = '../../'.$data['thumbnail_url'];
        if (file_exists('../../'.$data['thumbnail_url'])) {
          unlink($thumbnail_url_filePath);
        }
      }

    $delete = $conn->query("DELETE FROM video_lectures WHERE id = $id");
    if ($delete) {
      echo json_encode(['status' => 200, 'message' => 'Video deleted successfully!']);
    } else {
      echo json_encode(['status' => 302, 'message' => 'Something went wrong!']);
    }
  } else {
    echo json_encode(['status' => 302, 'message' => $data['Code'].' not exists!']);
  }
}
