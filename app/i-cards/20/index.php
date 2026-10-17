<?php
  use setasign\Fpdi\Fpdi;
  use setasign\Fpdi\PdfReader;

  if(isset($_GET['id'])){
    require '../../../includes/db-config.php';
    session_start();

    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $id = base64_decode($id);
    $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));

    $student = $conn->query("SELECT Students.ID, Students.First_Name, Students.Middle_Name, Students.Last_Name, Students.Father_Name, Students.Enrollment_No, Students.Contact, Students.Unique_ID, Students.DOB, Students.Address, Sub_Courses.Short_Name, Admission_Sessions.Name as Session FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID WHERE Students.ID = $id AND Students.University_ID = ".$_SESSION['university_id']."");
    if($student->num_rows==0){
      header('Location: /dashboard');
    }
    
    $student = $student->fetch_assoc();

    $file_extensions = array('.png', '.jpg', '.jpeg');

    $photo = "";
    $document = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = ".$student['ID']." AND `Type` = 'Photo'");
    if($document->num_rows>0){
      $photo = $document->fetch_assoc();
      $photo = "../../..".$photo['Location'];
    }
    $student_photo = base64_encode(file_get_contents($photo));
    $i = 0;
    $end = 3;
    while ($i < $end) {
      $data1 = base64_decode($student_photo); 
      $filename1 = $student['ID']."_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
      file_put_contents($filename1, $data1); //we save our new images to the path above
      $i++;
    }

    require_once('../../../extras/qrcode/qrlib.php');
    require_once('../../../extras/vendor/setasign/fpdf/fpdf.php');
    require_once('../../../extras/vendor/setasign/fpdi/src/autoload.php');
    
    $pdf = new Fpdi('L','mm', array(88.6,140));

    $pdf->SetTitle('ID Card');

    $pageCount = $pdf->setSourceFile('id-card-vocational.pdf');

    $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);
    $pdf->addPage();
    $pdf->useImportedPage($pageId, 0, 0, 140);

    $pdf->SetMargins(0,0,0);
    $pdf->SetAutoPageBreak(true, 1);
    
    $pdf->AddFont('Hondo','','hondo.php');
    $pdf->SetFont('Hondo','',9);

    $pdf->SetXY(25.5, 23.8);
    $pdf->Write(1, $student['Enrollment_No']);

    $student_id = empty($student['Unique_ID']) ? $student['ID'] : $student['Unique_ID'];
    $pdf->SetXY(102.5, 23.8);
    $pdf->Write(1, $student_id);

    $student_name = array($student['First_Name'],$student['Middle_Name'],$student['Last_Name']);
    $student_name = array_filter($student_name);
    $pdf->SetXY(33, 33.2);
    $pdf->Write(1, ucwords(strtolower(implode(" ", $student_name))));

    $pdf->SetXY(33, 38.2);
    $pdf->Write(1, ucwords(strtolower($student['Father_Name'])));

    $pdf->SetXY(33, 43.2);
    $pdf->Write(1, $student['Short_Name']);

    $pdf->SetXY(33, 48.2);
    $pdf->Write(1, $student['Session']);

    $pdf->SetXY(33, 53.4);
    $pdf->Write(1, date("d-M-Y", strtotime($student['DOB'])));

    $pdf->SetXY(33, 58.4);
    $pdf->Write(1, $student['Contact']);

    $address = json_decode($student['Address'], true);
    $full_address = array($address['present_address'], $address['present_city'], $address['present_district'], $address['present_state']);
    $full_address = array_filter($full_address);
    $pdf->SetXY(33, 63.4);
    $pdf->Write(1, substr(implode(", ", $full_address),0,30));
    $pdf->SetXY(33, 68.4);
    $pdf->Write(1, substr(implode(", ", $full_address),30,30));
    $pdf->SetXY(33, 73.4);
    $pdf->Write(1, substr(implode(", ", $full_address),60,30));
    
    $pdf->SetY(29.6);
    $pdf->SetX(93.4);
    $pdf->SetLineWidth(.3);
    $pdf->Cell(28,33.7,'',1,1,'C');
    
    if (filetype($photo) === 'file' && file_exists($photo)) {
      try {
        $filename = $student['ID']."_Photo" . $file_extensions[0];
        $image = $filename;
        $pdf->Image($image, 94, 30.2, 26.7, 32.6);
        $photo = $image;
      } catch (Exception $e) {
        try {
          $filename = $student['ID']."_Photo" . $file_extensions[1];
          $image = $filename;
          $pdf->Image($image, 94, 30.2, 26.7, 32.6);
          $photo = $image;
        } catch (Exception $e) {
          try {
            $filename = $student['ID']."_Photo" . $file_extensions[2];
            $image = $filename;
            $pdf->Image($image, 94, 30.2, 26.7, 32.6);
            $photo = $image;
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
      if(!empty($student_photo)){
        $filename = $student['ID']."_Photo" . $file_extensions[$i]; //$file_extensions loops through the file extensions
        unlink($filename);
      }
      $i++;
    }

    $pageId = $pdf->importPage(2, PdfReader\PageBoundaries::MEDIA_BOX);
    $pdf->addPage();
    $pdf->useImportedPage($pageId, 0, 0, 140);

    // QRcode
    $qr_text = "Student ID: ".$student_id.", Enrollment No.: ".$student['Enrollment_No'].", Name: ".implode(" ", $student_name).", Fathers Name: ".$student['Father_Name'].", Course: ".$student['Short_Name'].", Batch: ".$student['Session'].", DOB: ".date("d-M-Y", strtotime($student['DOB'])).", Contact: ".$student['Contact'].", Address: ".implode(", ", $full_address)."";
    $qr_file = $student['ID'].'_qrcode_.png';
    QRcode::png($qr_text, $qr_file);
    $pdf->Image($qr_file, 55, 45, 35, 35);
    unlink($qr_file);
    
    $pdf->Output('I', 'ID Card.pdf');
  }
