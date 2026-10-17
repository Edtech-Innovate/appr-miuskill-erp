<?php
if ($_FILES['file']['name']) {
  if (!$_FILES['file']['error']) {
    $name = md5(rand(100, 200));
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $filename = $name .
      '.' . $ext;
    $destination = $filename; //change this directory
    $location = $_FILES["file"]["tmp_name"];
    move_uploaded_file($location, $destination);
    echo $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/app/components/emails/files/' . $filename;
  } else {
    echo $message = 'Ooops!  Your upload triggered the following error:  ' . $_FILES['file']['error'];
  }
}
