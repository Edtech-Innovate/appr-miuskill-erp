<?php
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

if(isset($_GET['student_id'])){
  require '../../includes/db-config.php';
  session_start();

  if($_SESSION['university_id']!=16){
    header('Location: /');
  }

  $id = mysqli_real_escape_string($conn, $_GET['student_id']);
  $id = base64_decode($id);
  $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
  $student = $conn->query("SELECT Students.*, RIGHT(CONCAT('000000', Students.ID), 6) as Student_ID, Courses.Name as Course, Courses.Short_Name as Course_Short_Name, Sub_Courses.Name as Sub_Course, Sub_Courses.Short_Name as Sub_Course_Short_Name, Admission_Sessions.Name as Session, Admission_Types.Name as Type FROM Students LEFT JOIN Courses ON Students.Course_ID = Courses.ID LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID WHERE Students.ID = $id");
  $student = mysqli_fetch_assoc($student);
  $address = json_decode($student['Address'], true);

  $all_courses = array();
  $courses = $conn->query("SELECT Courses.Short_Name FROM Courses WHERE University_ID = ".$_SESSION['university_id']."");
  while ($course = $courses->fetch_assoc()){
    $all_courses[] = $course['Short_Name'];
  }


  require_once('../../extras/vendor/setasign/fpdf/fpdf.php');
  require_once('../../extras/vendor/setasign/fpdi/src/autoload.php');

  $pdf = new Fpdi('P','mm', array(277.9,210));

  $pdf->SetTitle('MU Application Form');
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

  $pdf->SetAutoPageBreak(true, 5);  

  // Application Numbers
  $pdf->SetFont('Helvetica','B',10);
  $pdf->SetXY(158, 9.5);
  $pdf->Write(1, $student['Student_ID']);

  // Programmes

  // BA General
  if(strcasecmp($student['Course_Short_Name'], 'BA')==0 && strcasecmp($student['Sub_Course'], 'General')==0){
    $pdf->Image($check, 8,102,3,3);
  }

  // BA Other
  if(strcasecmp($student['Course_Short_Name'], 'BA')==0 && strcasecmp($student['Sub_Course'], 'General')!=0){
    $pdf->Image($check, 55,102,3,3);
  }

  // MA
  if(strcasecmp($student['Course_Short_Name'], 'MA')==0){
    $pdf->Image($check, 129,102,3,3);
  }

  // BSc
  if(strcasecmp($student['Course_Short_Name'], 'BSc')==0 && strcasecmp($student['Sub_Course'], 'Information Technology')!=0){
    $pdf->Image($check, 8,117.5,3,3);
  }

  // MSc
  if(strcasecmp($student['Course_Short_Name'], 'MSc')==0 && strcasecmp($student['Sub_Course'], 'Information Technology')!=0){ 
    $pdf->Image($check, 55,117.5,3,3);
  }

  // MBA
  if(strcasecmp($student['Course_Short_Name'], 'MBA')==0){
    $pdf->Image($check, 129,117.5,3,3);
  }

  // BBA
  if(strcasecmp($student['Course_Short_Name'], 'BBA')==0){
    $pdf->Image($check, 8,126.5,3,3);
  }

  // BCom
  if(strcasecmp($student['Course_Short_Name'], 'B.Com')==0){
    $pdf->Image($check, 55,126.5,3,3);
  }

  // MCom
  if(strcasecmp($student['Course_Short_Name'], 'M.Com')==0){
    $pdf->Image($check, 97,126.5,3,3);
  }

  // DCA
  if(strcasecmp($student['Course_Short_Name'], 'DCA')==0){
    $pdf->Image($check, 8,135.5,3,3);
  }

  // BSc IT
  if(strcasecmp($student['Course_Short_Name'], 'BSc')==0 && strcasecmp($student['Sub_Course'], 'Information Technology')==0){
    $pdf->Image($check, 55,135.5,3,3);
  }

  // MSc IT
  if(strcasecmp($student['Course_Short_Name'], 'MSc')==0 && strcasecmp($student['Sub_Course'], 'Information Technology')==0){
    $pdf->Image($check, 97,135.5,3,3);
  }

  // Other
  if(!in_array($student['Course_Short_Name'], $all_courses)){
    $pdf->Image($check, 129,135.5,3,3);
    $pdf->SetXY(154, 136);
    $pdf->Write(1, $student['Course_Short_Name']);
  }

  // Photo
  if (filetype($photo) === 'file' && file_exists($photo)) {
    try {
      $filename = $id."_Photo" . $file_extensions[0];
      $image = $path.$filename;
      $pdf->Image($image, 173, 61.7, 29.4, 34.6);
      $photo = $image;
    } catch (Exception $e) {
      try {
        $filename = $id."_Photo" . $file_extensions[1];
        $image = $path.$filename;
        $pdf->Image($image, 173, 61.7, 29.4, 34.6);
        $photo = $image;
      } catch (Exception $e) {
        try {
          $filename = $id."_Photo" . $file_extensions[2];
          $image = $path.$filename;
          $pdf->Image($image, 173, 61.7, 29.4, 34.6);
          $photo = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  // Gender
  $pdf->Image($check,strcasecmp($student['Gender'], 'Male')==0 ? '36' : (strcasecmp($student['Gender'], 'Female')==0 ? '55' : '75') ,154.5,3,3);

  $pdf->SetFont('Helvetica','',10);

  // DOB
  $dob = str_split($student['DOB']);
  // Day
  $pdf->SetXY(128, 155.5);
  $pdf->Write(1, $dob[8]);
  $pdf->SetXY(134, 155.5);
  $pdf->Write(1, $dob[9]);
  // Month
  $pdf->SetXY(142.5, 155.5);
  $pdf->Write(1, $dob[5]);
  $pdf->SetXY(148, 155.5);
  $pdf->Write(1, $dob[6]);
  // Year
  $pdf->SetXY(156.5, 155.5);
  $pdf->Write(1, $dob[0]);
  $pdf->SetXY(162, 155.5);
  $pdf->Write(1, $dob[1]);
  $pdf->SetXY(167.5, 155.5);
  $pdf->Write(1, $dob[2]);
  $pdf->SetXY(173, 155.5);
  $pdf->Write(1, $dob[3]);

  // Student Name
  $student_name = str_split(str_replace('  ', ' ', $student['First_Name']." ".$student['Middle_Name']." ".$student['Last_Name']));
  $x = 48;
  foreach ($student_name as $name){
    $pdf->SetXY($x, 164);
    $pdf->Write(1, $name);
    $x += 5.2;
  }

  // Father Name
  $father_name = str_split($student['Father_Name']);
  $x = 48;
  foreach ($father_name as $name){
    $pdf->SetXY($x, 177);
    $pdf->Write(1, $name);
    $x += 5.2;
  }

  // Mother Name
  $mother_name = str_split($student['Mother_Name']);
  $x = 48;
  foreach ($mother_name as $name){
    $pdf->SetXY($x, 184.5);
    $pdf->Write(1, $name);
    $x += 5.2;
  }

  // Aadhar
  $aadhar = str_split(str_replace("-", "", $student['Aadhar_Number']));
  $x = 48;
  foreach ($aadhar as $ad){
    $pdf->SetXY($x, 191.4);
    $pdf->Write(1, $ad);
    $x += 5.4;
  }

  // Marital Status
  $pdf->SetXY(144, 191.4);
  $pdf->Write(1, $student['Marital_Status']);

  // Email
  $email = str_split($student['Email']);
  $x = 48;
  foreach ($email as $em){
    $pdf->SetXY($x, 198.5);
    $pdf->Write(1, $em);
    $x += 5.4;
  }

  // Mobile
  $mobile = str_split($student['Contact']);
  $x = 48;
  foreach ($mobile as $mb){
    $pdf->SetXY($x, 206);
    $pdf->Write(1, $mb);
    $x += 5.4;
  }

  // Alternate Mobile
  $mobile = str_split($student['Alternate_Contact']);
  $x = 144;
  foreach ($mobile as $mb){
    $pdf->SetXY($x, 206);
    $pdf->Write(1, $mb);
    $x += 5.4;
  }

  // Category
  $pdf->Image($check,strcasecmp($student['Category'], 'SC')==0 ? '40' : (strcasecmp($student['Category'], 'ST')==0 ? '55.5' : (strcasecmp($student['Category'], 'OBC')==0 ? '72' : (strcasecmp($student['Category'], 'General')==0 ? '90' : '194'))) ,211.7,3,3);

  // Nationality
  $pdf->SetXY(144, 213.5);
  $pdf->Write(0, strtoupper($student['Nationality']));

  // Religion
  $pdf->Image($check,strcasecmp($student['Religion'], 'Hindu')==0 ? '44' : (strcasecmp($student['Religion'], 'Muslim')==0 ? '67.5' : (strcasecmp($student['Religion'], 'Sikh')==0 ? '86' : (strcasecmp($student['Religion'], 'Christian')==0 ? '108' : (strcasecmp($student['Religion'], 'Jain')==0 ? '127' : '145')))) ,218.5,3,3);

  // Date
  // $pdf->SetXY(16, 248);
  // $pdf->Write(0, date("d-m-Y", strtotime($student['Created_At'])));

  // Student ID
  $pdf->SetXY(154, 248);
  $pdf->Write(0, $student['Student_ID']);

  // Student Name
  $student_name = str_replace('  ', ' ', $student['First_Name']." ".$student['Middle_Name']." ".$student['Last_Name']);
  $pdf->SetXY(18, 255.4);
  $pdf->Write(0, $student_name);

  // Father Name
  $pdf->SetXY(118, 255.4);
  $pdf->Write(0, $student['Father_Name']);

  // Program
  $pdf->SetXY(36,264.5);
  $pdf->Write(0, $student['Sub_Course_Short_Name']);

  // Signature
  if (filetype($signature) === 'file' && file_exists($signature)) {
    try {
      $filename = $id."_Signature" . $file_extensions[0];
      $image = $path.$filename;
      $pdf->Image($image, 117, 259, 34, 6.5);
      $student_signature = $image;
    } catch (Exception $e) {
      try {
        $filename = $id."_Signature" . $file_extensions[1];
        $image = $path.$filename;
        $pdf->Image($image, 117, 259, 34, 6.5);
        $student_signature = $image;
      } catch (Exception $e) {
        try {
          $filename = $id."_Signature" . $file_extensions[2];
          $image = $path.$filename;
          $pdf->Image($image, 117, 259, 34, 6.5);
          $student_signature = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  // Page 2
  $pageId = $pdf->importPage(2, PdfReader\PageBoundaries::MEDIA_BOX);
  $pdf->addPage();
  $pdf->useImportedPage($pageId, 0, 0, 210);

  // Permanent Address
  $permanent_address = substr($address['present_address'],0,45);
  $pdf->SetXY(9, 23.5);
  $pdf->Write(1, $permanent_address);

  $permanent_address = substr($address['present_address'],45,85);
  $pdf->SetXY(9, 30);
  $pdf->Write(1, $permanent_address);

  // City
  $pdf->SetXY(16, 37);
  $pdf->Write(1, substr($address['present_city'], 0, 15));

  // State
  $pdf->SetXY(64, 37);
  $pdf->Write(1, substr($address['present_state'], 0, 18));

  // Pincode
  $permanent_pincode = $address['present_pincode'];
  $pdf->SetXY(23, 43);
  $pdf->Write(1, $permanent_pincode);

  // Permanent Mobile
  $pdf->SetXY(123, 51);
  $pdf->Write(1, $student['Contact']);

  // Parent's Name
  $pdf->SetXY(43, 59.5);
  $pdf->Write(1, $student['Father_Name']);

  // Parent's Contact
  $pdf->SetXY(50, 67.5);
  $pdf->Write(1, $student['Alternate_Contact']);

  // Parent's Email
  $pdf->SetXY(134, 67.5);
  $pdf->Write(1, $student['Alternate_Email']);

  // Academics
  $academis = array('High School', 'Intermediate', 'Under Graduation', 'Post Graduation', 'Other');
  
  $y = '92';
  foreach($academis as $academic){
    $x = '47';
    
    // Details
    $type = $academic == 'Under Graduation' ? 'UG' : ($academic == 'Post Graduation' ? 'PG' : $academic);
    $data = $conn->query("SELECT * FROM Student_Academics WHERE Student_ID = $id AND Type = '$type'");
    if($data->num_rows>0){

      $data = mysqli_fetch_assoc($data);

      $pdf->SetXY($x, $y);
      $pdf->Write(1, substr($data['Board/Institute'],0,15));

      $x += 76;
      $pdf->SetXY($x, $y);
      $pdf->Write(1, substr($data['Total_Marks'],0,15));

      $x += 16;
      $pdf->SetXY($x, $y);
      $pdf->Write(1, substr($data['Year'],0,15));

      $x += 16;
      $pdf->SetXY($x, $y);
      $pdf->Write(1, substr($data['Subject'],0,15));

      
    }
    $y += 8.5;
  }

  // Signature
  if (filetype($signature) === 'file' && file_exists($signature)) {
    try {
      $filename = $id."_Signature" . $file_extensions[0];
      $image = $path.$filename;
      $pdf->Image($image, 47, 196, 34, 4.5);
      $student_signature = $image;
    } catch (Exception $e) {
      try {
        $filename = $id."_Signature" . $file_extensions[1];
        $image = $path.$filename;
        $pdf->Image($image, 47, 196, 34, 4.5);
        $student_signature = $image;
      } catch (Exception $e) {
        try {
          $filename = $id."_Signature" . $file_extensions[2];
          $image = $path.$filename;
          $pdf->Image($image, 47, 196, 34, 4.5);
          $student_signature = $image;
        } catch (Exception $e) {
          echo 'Message: ' . $e->getMessage();
        }
      }
    }
  }

  // Parent's Name
  $pdf->SetXY(143, 199);
  $pdf->Write(1, $student['Father_Name']);

  // Date
  // $pdf->SetXY(162, 207);
  // $pdf->Write(0, date("d-m-Y", strtotime($student['Created_At'])));
  

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

  $pdf->Output('I', 'MU Application Form.pdf');
}
