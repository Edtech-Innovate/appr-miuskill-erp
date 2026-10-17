<?php

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if(isset($_GET['student_id'])){
  require '../../includes/db-config.php';
  session_start();

  if($_SESSION['university_id']!=14){
    header('Location: /');
  }

  $id = mysqli_real_escape_string($conn, $_GET['student_id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
  $student = $conn->query("SELECT Students.*, Courses.Name as Course, Sub_Courses.Name as Sub_Course, Admission_Sessions.Name as Session, Admission_Types.Name as Type FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.ID = $id");
  $student = mysqli_fetch_assoc($student);
  $address = json_decode($student['Address'], true);


  require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

  $pdf = new Fpdi();

  $pdf->SetTitle('SVGU Application Form');
  $pageCount = $pdf->setSourceFile('form.pdf');
  $pdf->SetFont('Arial','',11);

  // Tick Image
  $check = '../../assets/img/forms/checked.png';

  // Extensions
  $file_extensions = array('.png', '.jpg', '.jpeg');

  //this folder will have there images.
  $path = "photos/";

  // Photo
  $student_photo = "";
  $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Photo'");
  if($photo->num_rows>0){
    $photo = mysqli_fetch_assoc($photo);
    $photo = "../..".$photo['Location'];
    $student_photo = base64_encode(file_get_contents($photo));
    $i = 0;
    $end = 3;
    while ($i < $end) {
      $data1 = base64_decode($student_photo); 
      $filename1 = $id."_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      file_put_contents($path . $filename1, $data1); //we save our new images to the path above
      $i++;
    }
  }else{
    $photo = "";
  }

  // Signature
  $student_signature = "";
  $signature = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = $id AND Type = 'Student Signature'");
  if($signature->num_rows>0){
    $signature = mysqli_fetch_assoc($signature);
    $signature = "../..".$signature['Location'];
    $student_signature = base64_encode(file_get_contents($signature));
    $i = 0;
    $end = 3;
    while ($i < $end) {
      $data2 = base64_decode($student_signature);
      $filename2= $id."_Signature" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      file_put_contents($path . $filename2, $data2); //we save our new images to the path above
      $i++;
    }
  }else{
    $signature = "";
  }

  // Page 1
  $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);

  // Enrollment No.
  $enrollment_no = str_split($student['Enrollment_No']);
  $x = 34.8;
  foreach ($enrollment_no as $enroll){
    $pdf->SetXY($x, 50.8);
    $pdf->Write(1, $enroll);
    $x += 3.97;
  }

  // Course Code

  // Programme
  $pdf->SetXY(100, 61.5);
  $pdf->Write(1, $student['Course']);

  // Specialization
  $pdf->SetXY(37, 70);
  $pdf->Write(1, $student['Sub_Course']);

  // Lateral
  $pdf->Image($check,strcasecmp($student['Type'], 'Lateral')==0 ? '45.7' : '59' ,77,3,3);

  // Photo
  if (filetype($photo) === 'file' && file_exists($photo)) {
    try {
      $filename = $id."_Photo" . $file_extensions[0];
      $image = $path.$filename;
      $pdf->Image($image, 166.8, 31.3, 30.5, 35.9);
      $photo = $image;
    } catch (Exception $e) {
      try {
        $filename = $id."_Photo" . $file_extensions[1];
        $image = $path.$filename;
        $pdf->Image($image, 166.8, 31.3, 30.5, 35.9);
        $photo = $image;
      } catch (Exception $e) {
        try {
          $filename = $id."_Photo" . $file_extensions[2];
          $image = $path.$filename;
          $pdf->Image($image, 166.8, 31.3, 30.5, 35.9);
          $photo = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  // Signature
  if (filetype($signature) === 'file' && file_exists($signature)) {
    try {
      $filename = $id."_Signature" . $file_extensions[0];
      $image = $path.$filename;
      $pdf->Image($image, 167, 69.4, 30.2, 11.3);
      $student_signature = $image;
    } catch (Exception $e) {
      try {
        $filename = $id."_Signature" . $file_extensions[1];
        $image = $path.$filename;
        $pdf->Image($image, 167, 69.4, 30.2, 11.3);
        $student_signature = $image;
      } catch (Exception $e) {
        try {
          $filename = $id."_Signature" . $file_extensions[2];
          $image = $path.$filename;
          $pdf->Image($image, 167, 69.4, 30.2, 11.3);
          $student_signature = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  // Student Name
  $student_name = str_split(str_replace('  ', ' ', $student['First_Name']." ".$student['Middle_Name']." ".$student['Last_Name']));
  $x = 36.9;
  foreach ($student_name as $name){
    $pdf->SetXY($x, 97.6);
    $pdf->Write(1, $name);
    $x += 3.9;
  }

  // Father Name
  $father_name = str_split($student['Father_Name']);
  $x = 36.9;
  foreach ($father_name as $name){
    $pdf->SetXY($x, 105.6);
    $pdf->Write(1, $name);
    $x += 3.9;
  }

  // Mother Name
  $mother_name = str_split($student['Mother_Name']);
  $x = 36.9;
  foreach ($mother_name as $name){
    $pdf->SetXY($x, 113.8);
    $pdf->Write(1, $name);
    $x += 3.9;
  }

  // Gender
  $pdf->Image($check,strcasecmp($student['Gender'], 'Male')==0 ? '44.2' : '69' ,121,3,3);

  // DOB
  $dob = str_split($student['DOB']);
  // Day
  $pdf->SetXY(121, 122.6);
  $pdf->Write(1, $dob[8]);
  $pdf->SetXY(125, 122.6);
  $pdf->Write(1, $dob[9]);
  // Month
  $pdf->SetXY(132.5, 122.6);
  $pdf->Write(1, $dob[5]);
  $pdf->SetXY(136.5, 122.6);
  $pdf->Write(1, $dob[6]);
  // Year
  $pdf->SetXY(144.5, 122.6);
  $pdf->Write(1, $dob[0]);
  $pdf->SetXY(148.3, 122.6);
  $pdf->Write(1, $dob[1]);
  $pdf->SetXY(152.3, 122.6);
  $pdf->Write(1, $dob[2]);
  $pdf->SetXY(156.3, 122.6);
  $pdf->Write(1, $dob[3]);

  // Permanent Address
  $permanent_address = str_split($address['present_address']);
  $x = 32;
  for($i=0; $i<17; $i++){
    $pdf->SetXY($x, 136.2);
    $pdf->Write(1, $permanent_address[$i]);
    $x += 3.87;
  }

  $condition = count($permanent_address)>=39 ? 39 : count($permanent_address);
  $x = 12;
  for($i=17; $i<$condition; $i++){
    $pdf->SetXY($x, 143.2);
    $pdf->Write(1, $permanent_address[$i]);
    $x += 3.92;
  }

  if(count($permanent_address)>39){
    $condition = count($permanent_address)>=61 ? 61 : count($permanent_address);
    $x = 12;
    for($i=39; $i<$condition; $i++){
      $pdf->SetXY($x, 150.2);
      $pdf->Write(1, $permanent_address[$i]);
      $x += 3.92;
    } 
  }

  if(count($permanent_address)>61){
    $condition = count($permanent_address)>=71 ? 71 : count($permanent_address);
    $x = 12;
    for($i=61; $i<$condition; $i++){
      $pdf->SetXY($x, 157);
      $pdf->Write(1, $permanent_address[$i]);
      $x += 3.92;
    } 
  }

  // Pincode
  $permanent_pincode = str_split($address['present_pincode']);
  $x = 67.3;
  for($i=0; $i<count($permanent_pincode); $i++){
    $pdf->SetXY($x, 157);
    $pdf->Write(1, $permanent_pincode[$i]);
    $x += 3.92;
  }

  // City
  $pdf->SetFont('Arial','',10);
  $pdf->SetXY(19, 162);
  $pdf->Write(1, substr($address['present_city'], 0, 15));

  // City
  $pdf->SetFont('Arial','',10);
  $pdf->SetXY(62, 162);
  $pdf->Write(1, substr($address['present_state'], 0, 18));

  // Mobile
  $pdf->SetFont('Arial','',10);
  $pdf->SetXY(62, 172.5);
  $pdf->Write(1, $student['Contact']);

  // Email
  $pdf->SetFont('Arial','',10);
  $pdf->SetXY(23, 178.5);
  $pdf->Write(1, $student['Email']);

  // Nationality
  $pdf->Image($check,strcasecmp($student['Nationality'], 'Indian')==0 ? '48.5' : '67' ,193.6,3,3);

  // Category
  $pdf->Image($check,strcasecmp($student['Category'], 'General')==0 ? '43.2' : (strcasecmp($student['Category'], 'SC')==0 ? '56.2' : (strcasecmp($student['Category'], 'ST')==0 ? '69' : (strcasecmp($student['Category'], 'OBC')==0 ? '83.7' : '194'))) ,202,3,3);

  // Academics
  $academis = array('High School', 'Intermediate', 'Under Graduation', 'Post Graduation', 'Other');
  
  $y = '240.5';
  foreach($academis as $academic){
    $x = '23';
    
    // Details
    $type = $academic == 'Under Graduation' ? 'UG' : ($academic == 'Post Graduation' ? 'PG' : $academic);
    $data = $conn->query("SELECT * FROM Student_Academics WHERE Student_ID = $id AND Type = '$type'");
    if($data->num_rows>0){

      $data = mysqli_fetch_assoc($data);

      $pdf->SetXY($x, $y);
      $pdf->Write(1, $academic);

      // Roll No
      $x += 48; 
      $pdf->SetXY($x, $y);
      $pdf->Write(1, !empty($data['Roll_No']) ? $data['Roll_No'] : '');

      $x += 26; 
      $pdf->SetXY($x, $y);
      $pdf->Write(1, !empty($data['Year']) ? $data['Year'] : '');

      $x += 46; 
      $pdf->SetXY($x, $y);
      $pdf->Write(1, !empty($data['Board/Institute']) ? substr($data['Board/Institute'],0,28) : '');
    }
    $y += 8;
  }



  // Page 2
  // $pageId = $pdf->importPage(2, PdfReader\PageBoundaries::MEDIA_BOX);
  // $pdf->addPage();
  // $pdf->useImportedPage($pageId, 0, 0, 210);


  // Page 3
  // $pageId = $pdf->importPage(3, PdfReader\PageBoundaries::MEDIA_BOX);
  // $pdf->addPage();
  // $pdf->useImportedPage($pageId, 0, 0, 210);

  // // Date
  // $pdf->SetXY(100.5, 190.5);
  // $pdf->Write(1, date('d-m-Y'));

  // // Signature
  // if (filetype($signature) === 'file' && file_exists($signature)) {
  //   try {
  //     $filename = $id."_Signature" . $file_extensions[0];
  //     $image = $path.$filename;
  //     $pdf->Image($image, 155, 182.4, 30.2, 7.3);
  //     $student_signature = $image;
  //   } catch (Exception $e) {
  //     try {
  //       $filename = $id."_Signature" . $file_extensions[1];
  //       $image = $path.$filename;
  //       $pdf->Image($image, 155, 182.4, 30.2, 7.3);
  //       $student_signature = $image;
  //     } catch (Exception $e) {
  //       try {
  //         $filename = $id."_Signature" . $file_extensions[2];
  //         $image = $path.$filename;
  //         $pdf->Image($image, 155, 182.4, 30.2, 7.3);
  //         $student_signature = $image;
  //       } catch (Exception $e) {
  //         echo 'Message: ' . $e->getMessage();
  //       }
  //     }
  //   }
  // }
  

  $i = 0;
  $end = 3;
  while ($i < $end) {
    // Delete Photos
    if(!empty($student_photo)){
      $filename = $id."_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      unlink($path.$filename);
    }

    // Delete Signatures
    if(!empty($student_signature)){
      $filename= $id."_Signature" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      unlink($path.$filename);
    }
    $i++;
  }

  $pdf->Output('I', 'SVGU Application Form.pdf');
}
