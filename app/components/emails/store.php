<?php
if (isset($_POST['name']) && isset($_POST['subject']) && isset($_POST['university'])) {

  include '../../../includes/db-config.php';
  session_start();

  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $subject = mysqli_real_escape_string($conn, $_POST['subject']);
  $university = mysqli_real_escape_string($conn, $_POST['university']);
  $template = mysqli_real_escape_string($conn, $_POST['template']);

  if (empty($template)) {
    echo json_encode(['status' => 302, 'message' => "Template can't be empty!"]);
    exit();
  }

  if (empty($university) || empty($template) || empty($name) || empty($subject)) {
    echo json_encode(['status' => 302, 'message' => 'All fields are required.']);
    exit();
  }

  // Attachments
  $fileExtensions = array("jpeg", "jpg", "png", "gif", "JPG", "PNG", "JPEG", "doc", "pdf", "docx", "xls", "xlsx", "csv", "txt", "text");
  $folder = "../../../assets/files/emails/";
  $attachment = NULL;
  $attchmentOriginlName = NULL;
  $attachments = array();
  $attachmentsOriginalName = array();

  // Number of files
  if (count(array_filter($_FILES['attachments']['tmp_name'])) > 4) {
    echo json_encode(['status' => 302, 'message' => 'Only 4 files can be attached!']);
    exit();
  }

  if (isset($_FILES["attachments"]["tmp_name"]) && count(array_filter($_FILES['attachments']['tmp_name'])) > 0) {
    foreach ($_FILES["attachments"]["tmp_name"] as $key => $tmp_name) {
      $fileSize = $_FILES["attachments"]['size'][$key];
      if ($fileSize > 1954202) {
        echo json_encode(['status' => 400, 'message' => 'File size cannot exceed more than 2MB']);
        exit();
      }
      $attachment = uniqid() . '_' . $_FILES["attachments"]["name"][$key];
      $attachmentOriginalName = $_FILES["attachments"]["name"][$key];
      $tmpName = $_FILES["attachments"]["tmp_name"][$key];
      $mime_type = mime_content_type($tmpName);
      $attachmentExtension = pathinfo($attachment, PATHINFO_EXTENSION);
      if (in_array($attachmentExtension, $fileExtensions)) {
        if (file_exists($folder . $attachment)) {
          $attachment = time() . $attachment;
        }
        if (move_uploaded_file($tmpName, $folder . $attachment)) {
          $attachments[$key]['path'] = "/assets/files/emails/" . $attachment;
          $attachments[$key]['name'] = $attachmentOriginalName;
          $attachments[$key]['type'] = $mime_type;
        } else {
          echo json_encode(['status' => 503, 'message' => 'Unable to upload attachment!']);
          exit();
        }
      } else {
        echo json_encode(['status' => 302, 'message' => 'File extension not matched!']);
        exit();
      }
    }
  }

  $style = '<style>
    .table {
      width: max(50%, min(500px, 100%));
    }

    .p {
      text-align: justify;
      text-justify: inter-word;
    }
  </style>';

  $template = $style . '<center>' . $template . '</center>';

  $attachments = !empty($attachments) ? json_encode($attachments) : json_encode($attachments);

  $check = $conn->query("SELECT ID FROM Email_Templates WHERE `Name` LIKE '$name' AND University_ID = $university");
  if ($check->num_rows > 0) {
    echo json_encode(['status' => 302, 'message' => 'Name already exists!']);
    exit();
  } else {
    $add = $conn->query("INSERT INTO Email_Templates (`Name`, `Subject`, `University_ID`, `Template`, `Attachments`) VALUES ('$name', '$subject', '$university', '$template', '$attachments')");
    if ($add) {
      echo json_encode(['status' => 200, 'message' => 'Template added successfully!']);
    } else {
      echo json_encode(['status' => 400, 'message' => mysqli_error($conn)]);
    }
  }
}
