<?php
ini_set('display_errors', 0);

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if (isset($_GET['student_id'])) {
  require '../../includes/db-config.php';
  session_start();

  if ($_SESSION['university_id'] != 20) {
    header('Location: /ams/');
  }

  $id = mysqli_real_escape_string($conn, $_GET['student_id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
  $student = $conn->query("SELECT Students.*, Courses.Name as Course, Sub_Courses.Name as Sub_Course, Admission_Sessions.Name as Session, Admission_Types.Name as Type, Universities.Vertical, TIMESTAMPDIFF(YEAR, DOB, CURDATE()) AS Age FROM Students LEFT JOIN Universities ON Students.University_ID = Universities.ID LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.ID = $id");
  $student = mysqli_fetch_assoc($student);
  $address = json_decode($student['Address'], true);


  require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

  $pdf = new Fpdi();

  $pdf->SetTitle('Application Form');
  $pageCount = $pdf->setSourceFile('form.pdf');
  $pdf->AddFont('Hondo', '', 'hondo.php');
  $pdf->SetFont('Hondo', '', 10);

  // Tick Image
  $check = '../../assets/img/forms/checked.png';

  // Extensions
  $file_extensions = array('.png', '.jpg', '.jpeg');

  //this folder will have there images.
  $path = "photos/";

  // Photo
  $student_photo = "";
  $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
  if ($photo->num_rows > 0) {
    $photo = mysqli_fetch_assoc($photo);
    $photo = "../.." . $photo['Location'];
    $student_photo = base64_encode(file_get_contents($photo));
    $i = 0;
    $end = 3;
    while ($i < $end) {
      $data1 = base64_decode($student_photo);
      $filename1 = $id . "_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      file_put_contents($path . $filename1, $data1); //we save our new images to the path above
      $i++;
    }
  } else {
    $photo = "";
  }

  // Signature
  $student_signature = "";
  $signature = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Student Signature'");
  if ($signature->num_rows > 0) {
    $signature = mysqli_fetch_assoc($signature);
    $signature = "../.." . $signature['Location'];
    $student_signature = base64_encode(file_get_contents($signature));
    $i = 0;
    $end = 3;
    while ($i < $end) {
      $data2 = base64_decode($student_signature);
      $filename2 = $id . "_Signature" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      file_put_contents($path . $filename2, $data2); //we save our new images to the path above
      $i++;
    }
  } else {
    $signature = "";
  }

  // Page 1
  $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);

  // Photo
  if (filetype($photo) === 'file' && file_exists($photo)) {
    try {
      $filename = $id . "_Photo" . $file_extensions[0];
      $image = $path . $filename;
      $pdf->Image($image, 170.5, 45, 24.5, 30.7);
      $photo = $image;
    } catch (Exception $e) {
      try {
        $filename = $id . "_Photo" . $file_extensions[1];
        $image = $path . $filename;
        $pdf->Image($image, 170.5, 45, 24.5, 30.7);
        $photo = $image;
      } catch (Exception $e) {
        try {
          $filename = $id . "_Photo" . $file_extensions[2];
          $image = $path . $filename;
          $pdf->Image($image, 170.5, 45, 24.5, 30.7);
          $photo = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  // Form No.
  $pdf->SetXY(126, 54.2);
  $pdf->Write(1, $student['Unique_ID']);

  // Registration No.
  $pdf->SetXY(132, 63.9);
  $pdf->Write(1, $student['Enrollment_No']);

  // Session
  $pdf->SetXY(52, 90.2);
  $pdf->Write(1, $student['Session']);

  // School
  $pdf->SetXY(142, 90.2);
  $pdf->Write(1, $student['Vertical']);

  // Course
  $pdf->SetXY(45, 98.4);
  $pdf->Write(1, $student['Course']);

  // Specialization
  $pdf->SetXY(58, 106.5);
  $pdf->Write(1, $student['Sub_Course']);

  // Name
  $student_name = array($student['First_Name'], $student['Middle_Name'], $student['Last_Name']);
  $pdf->SetXY(38, 129);
  $pdf->Write(1, implode(" ", $student_name));

  // Father Name
  $pdf->SetXY(38, 138);
  $pdf->Write(1, $student['Father_Name']);

  // Mother Name
  $pdf->SetXY(38, 146.8);
  $pdf->Write(1, $student['Mother_Name']);

  // DOB
  $pdf->SetXY(36, 156.4);
  $pdf->Write(1, date("d-m-Y", strtotime($student['DOB'])));

  // Age
  $pdf->SetXY(176, 156.4);
  $pdf->Write(1, $student['Age']);

  // Gender
  $pdf->SetXY(30, 165.9);
  $pdf->Write(1, $student['Gender']);

  // Nationality
  $pdf->SetXY(85, 165.9);
  $pdf->Write(1, $student['Nationality']);

  // Marital Status
  $pdf->SetXY(146, 165.9);
  $pdf->Write(1, $student['Marital_Status']);

  // Category
  if ($student['Category'] == 'SC') {
    $pdf->Image($check, 47, 174, 3, 3);
  } else if ($student['Category'] == 'ST') {
    $pdf->Image($check, 59.4, 174, 3, 3);
  } else if ($student['Category'] == 'OBC') {
    $pdf->Image($check, 71.4, 174, 3, 3);
  } else if ($student['Category'] == 'SBC') {
    $pdf->Image($check, 85.9, 174, 3, 3);
  } else if ($student['Category'] == 'General') {
    $pdf->Image($check, 100.5, 174, 3, 3);
  } else if ($student['Category'] == 'Minority') {
    $pdf->Image($check, 123, 174, 3, 3);
  } else if ($student['Category'] == 'Other') {
    $pdf->Image($check, 145.9, 174, 3, 3);
  }

  // Correspondence Address
  $address = json_decode($student['Address'], true);

  $pdf->SetXY(52, 207.9);
  $pdf->Write(1, substr($address['present_address'], 0, 70));

  if (strlen($address['present_address']) > 70) {
    $pdf->SetXY(13, 217.9);
    $pdf->Write(1, substr($address['present_address'], 70, 87));
  }

  // Student Mobile No
  $pdf->SetXY(44, 228.2);
  $pdf->Write(1, $student['Contact']);

  // Alternate
  $pdf->SetXY(134, 228.2);
  $pdf->Write(1, $student['Alternate_Contact']);

  // Email
  $pdf->SetXY(34, 249.2);
  $pdf->Write(1, $student['Email']);

  // Page 2
  $pageId = $pdf->importPage(2, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);

  $academis = array('High School', 'Intermediate', 'Under Graduation', 'Post Graduation', 'Other');

  $y = '70';
  foreach ($academis as $academic) {
    $x = '40';

    // Details
    $type = $academic == 'Under Graduation' ? 'UG' : ($academic == 'Post Graduation' ? 'PG' : $academic);
    $data = $conn->query("SELECT * FROM Student_Academics WHERE Student_ID = $id AND Type = '$type'");
    if ($data->num_rows > 0) {

      $data = mysqli_fetch_assoc($data);

      $pdf->SetXY($x, $y);
      $pdf->Write(1, substr($data['Board/Institute'], 0, 15));

      $x += 81;
      $pdf->SetXY($x, $y);
      $pdf->Write(1, substr($data['Year'], 0, 15));

      $x += 14;
      $pdf->SetXY($x, $y);
      $pdf->Write(1, substr($data['Subject'], 0, 8));

      $x += 49;
      $pdf->SetXY($x, $y);
      $pdf->Write(1, substr($data['Total_Marks'], 0, 4));
    }
    $y += 13.8;
  }


  // Page 3
  $pageId = $pdf->importPage(3, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);

  // Student Name
  $pdf->SetXY(30, 216.8);
  $pdf->Write(1, implode(" ", $student_name));

  // Father Name
  $pdf->SetXY(105, 216.8);
  $pdf->Write(1, $student['Father_Name']);

  // Date
  // $pdf->SetXY(30, 266);
  // $pdf->Write(1, date("d-m-Y", strtotime($student['Created_At'])));

  // Signature
  if (filetype($signature) === 'file' && file_exists($signature)) {
    try {
      $filename = $id . "_Signature" . $file_extensions[0];
      $image = $path . $filename;
      $pdf->Image($image, 152, 257, 38, 10.5);
      $student_signature = $image;
    } catch (Exception $e) {
      try {
        $filename = $id . "_Signature" . $file_extensions[1];
        $image = $path . $filename;
        $pdf->Image($image, 152, 257, 38, 10.5);
        $student_signature = $image;
      } catch (Exception $e) {
        try {
          $filename = $id . "_Signature" . $file_extensions[2];
          $image = $path . $filename;
          $pdf->Image($image, 152, 257, 38, 10.5);
          $student_signature = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  // Page 4
  $pageId = $pdf->importPage(4, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);

  // 10th MarkSheet
  $high_school_marksheet = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'High School'");
  if ($high_school_marksheet->num_rows > 0) {
    $high_school_marksheet = $high_school_marksheet->fetch_assoc();
    $high_school_marksheet = $high_school_marksheet['Location'];
    $high_school_marksheet = explode("|", $high_school_marksheet);
    $pdf->Image($check, 15.4, 55.8, 3, 3);
    if (count($high_school_marksheet) > 1) {
      $pdf->Image($check, 56.8, 55.8, 3, 3);
    }
  }


  // 12th MarkSheet
  $inter_marksheet = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Intermediate'");
  if ($inter_marksheet->num_rows > 0) {
    $pdf->Image($check, 15.4, 64.2, 3, 3);
    $inter_marksheet = $inter_marksheet->fetch_assoc();
    $inter_marksheet = $inter_marksheet['Location'];
    $inter_marksheet = explode("|", $inter_marksheet);
    if (count($inter_marksheet) > 1) {
      $pdf->Image($check, 56.8, 64.2, 3, 3);
    }
  }

  // Graduation
  $graduation = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'UG'");
  if ($graduation->num_rows > 0) {
    $pdf->Image($check, 15.4, 72.8, 3, 3);
  }

  // Post Graduation
  $post_graduation = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'PG'");
  if ($post_graduation->num_rows > 0) {
    $pdf->Image($check, 15.4, 81.4, 3, 3);
  }

  // Photo
  $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
  if ($photo->num_rows > 0) {
    $pdf->Image($check, 56.8, 106.9, 3, 3);
  }

  // Aadhar
  $aadhar = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Aadhar'");
  if ($aadhar->num_rows > 0) {
    $pdf->Image($check, 93.8, 106.9, 3, 3);
  }

  // Student Name
  $pdf->SetXY(30, 134.8);
  $pdf->Write(1, implode(" ", $student_name));

  // Date
  // $pdf->SetXY(30, 269);
  // $pdf->Write(1, date("d-m-Y", strtotime($student['Created_At'])));

  // Signature
  if (filetype($signature) === 'file' && file_exists($signature)) {
    try {
      $filename = $id . "_Signature" . $file_extensions[0];
      $image = $path . $filename;
      $pdf->Image($image, 152, 259, 38, 10.5);
      $student_signature = $image;
    } catch (Exception $e) {
      try {
        $filename = $id . "_Signature" . $file_extensions[1];
        $image = $path . $filename;
        $pdf->Image($image, 152, 259, 38, 10.5);
        $student_signature = $image;
      } catch (Exception $e) {
        try {
          $filename = $id . "_Signature" . $file_extensions[2];
          $image = $path . $filename;
          $pdf->Image($image, 152, 259, 38, 10.5);
          $student_signature = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  $i = 0;
  $end = 3;
  while ($i < $end) {
    // Delete Photos
    if (!empty($student_photo)) {
      $filename = $id . "_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      unlink($path . $filename);
    }

    // Delete Signatures
    if (!empty($student_signature)) {
      $filename = $id . "_Signature" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      unlink($path . $filename);
    }
    $i++;
  }

  $pdf->Output('I', 'Application Form.pdf');
}
