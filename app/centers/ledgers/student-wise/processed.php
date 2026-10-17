<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';

  $id = intval($_GET['id']);
  $centerID = intval($_GET['id']);


  $sessionQuery = "";
  if (isset($_GET['admission_session_id']) && !empty($_GET['admission_session_id'])) {
    $admission_session_id = intval($_GET['admission_session_id']);
    $sessionQuery = " AND Students.Admission_Session_ID = " . $admission_session_id;
  }

  if ($_SESSION["Role"] == "Sub-Center") {
    $id = intval($_GET['id']);
  } else {
    $subcenter_id = array();
    $subcenter = $conn->query("SELECT Sub_Center FROM `Center_SubCenter` WHERE Center=$id ");
    while ($subcenterArr = $subcenter->fetch_assoc()) {
      $subcenter_id[] = $subcenterArr['Sub_Center'];
    }
    if (!empty($subcenter_id)) {
      $id .= "," . implode(",", array_filter($subcenter_id));
    }
  }
  $id = isset($id) ? $id : '';

//   $students_wallets = $conn->query("SELECT wallet_invoices.ID, wallet_payments.Transaction_ID, wallet_payments.Gateway_ID, wallet_invoices.Amount, wallet_invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID, wallet_invoices.Created_At , Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM wallet_invoices LEFT JOIN wallet_payments ON wallet_invoices.Invoice_No = wallet_payments.Transaction_ID LEFT JOIN Students ON wallet_invoices.Student_ID = Students.ID WHERE wallet_invoices.`User_ID` IN ($id) AND wallet_invoices.University_ID = " . $_SESSION['university_id'] . " AND wallet_payments.Status = 1 $sessionQuery ORDER BY Students.ID DESC");
//   if ($_SESSION['Role'] == "Administrator") {
//     $students_wallets = $conn->query("SELECT wallet_invoices.ID, wallet_payments.Transaction_ID, wallet_payments.Gateway_ID, wallet_invoices.Amount, wallet_invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID, wallet_invoices.Created_At , Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM wallet_invoices LEFT JOIN wallet_payments ON wallet_invoices.Invoice_No = wallet_payments.Transaction_ID LEFT JOIN Students ON wallet_invoices.Student_ID = Students.ID WHERE wallet_payments.Added_By IN ($id) AND wallet_invoices.University_ID = " . $_SESSION['university_id'] . " AND wallet_payments.Status = 1 $sessionQuery ORDER BY Students.ID DESC");
//   }
$students_wallets = $conn->query("SELECT Wallet_Invoices.ID, Wallet_Payments.Transaction_ID, Wallet_Payments.Gateway_ID, Wallet_Invoices.Amount, Wallet_Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID, Wallet_Invoices.Created_At , Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Wallet_Invoices LEFT JOIN Wallet_Payments ON Wallet_Invoices.Invoice_No = Wallet_Payments.Transaction_ID LEFT JOIN Students ON Wallet_Invoices.Student_ID = Students.ID WHERE  Students.`Added_For`  IN ($id) AND Wallet_Invoices.University_ID = " . $_SESSION['university_id'] . " AND Wallet_Payments.Status = 1 $sessionQuery ORDER BY Students.ID DESC");
  if ($_SESSION['Role'] == "Administrator") {
    $students_wallets = $conn->query("SELECT Wallet_Invoices.ID, Wallet_Payments.Transaction_ID, Wallet_Payments.Gateway_ID, Wallet_Invoices.Amount, Wallet_Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID, Wallet_Invoices.Created_At , Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Wallet_Invoices LEFT JOIN Wallet_Payments ON Wallet_Invoices.Invoice_No = Wallet_Payments.Transaction_ID LEFT JOIN Students ON Wallet_Invoices.Student_ID = Students.ID WHERE Wallet_Payments.Added_By IN ($id) AND Wallet_Invoices.University_ID = " . $_SESSION['university_id'] . " AND Wallet_Payments.Status = 1 $sessionQuery ORDER BY Students.ID DESC");
  }
  $studentsArrData =array();

  while ($studentsArr = $students_wallets->fetch_assoc()) {
    $studentsArrData[] = $studentsArr;
  }

  $students = $conn->query("SELECT Invoices.ID, Payments.Transaction_ID, Payments.Gateway_ID, Invoices.Amount, Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID,  Invoices.Created_At , Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE Students.`Added_For` IN ($id) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 1 $sessionQuery ORDER BY Students.ID DESC");
  if ($students->num_rows == 0) {
    $students = $conn->query("SELECT Invoices.ID, Payments.Transaction_ID, Payments.Gateway_ID, Invoices.Amount, Invoices.Duration, Students.First_Name, Students.Middle_Name, Students.Last_Name, (RIGHT(CONCAT('000000', Students.ID), 6)) as Student_ID, Students.Unique_ID,  Invoices.Created_At, Students.Added_By, Students.Course_ID, Students.Sub_Course_ID FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID LEFT JOIN Students ON Invoices.Student_ID = Students.ID WHERE Payments.Added_By IN ($id) AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND Payments.Status = 1 $sessionQuery ORDER BY Students.ID DESC");
  }
  while ($students_offline_Arr = $students->fetch_assoc()) {
    $studentsArrData[] = $students_offline_Arr;
  }

 
?> <?php 
if(count($studentsArrData)==0){
// if ($students->num_rows == 0) { 
  ?>
    <div class="row">
      <div class="col-lg-12 text-center">
        No student(s) found!
      </div>
    </div>
  <?php } else {
  ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Processed On</th>
                <th>Particular</th>
                <th>Transaction ID</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Duration</th>
                <th>Paid</th>
                <th></th>
              </tr>
            </thead>
            <tbody>


              <?php
              // while ($student = $students->fetch_assoc()) {
              foreach ($studentsArrData as $student) {
                $student_name = array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) ?>
                <tr>
                  <td><?= date("d-m-Y", strtotime($student['Created_At'])) ?></td>
                  <td><?= $student['Gateway_ID'] ?></td>
                  <td><?= $student['Transaction_ID'] ?></td>
                  <td><b><?php echo !empty($student['Unique_ID']) ? $student['Unique_ID'] : $student['Student_ID'] ?></b></td>
                  <td><?= implode(" ", $student_name) ?></td>
                  <td><?= $student['Duration'] ?></td>
                  <?php if ($_SESSION['Role'] == "Center" || $_SESSION['Role'] == "Administrator") {
                      $centerArr = array();
                      $center_fee_Query = $conn->query("SELECT Fee FROM `Center_Sub_Courses` WHERE `User_ID` = $centerID AND `Course_ID` = " . $student['Course_ID'] . "  AND `Sub_Course_ID` = " . $student['Sub_Course_ID'] . " ");
                      $centerArr = $center_fee_Query->fetch_assoc();
                      $fee =number_format((-1) * $student['Amount'], 2)." &#8377;";
                     // $fee = number_format((-1) * $centerArr['Fee'], 2)." &#8377;";
                    } else {
                      $fee =number_format((-1) * $student['Amount'], 2)." &#8377;";

                    } ?>
                  <td><?= $fee; ?></td>
                  <td>
                    <center><span class="cursor-pointer text-danger font-weight-bold" onclick="cancelStudent('<?= $student['ID'] ?>', '<?= $id ?>')">Cancel</span></center>
                  </td>
                </tr>
              <?php } ?>


            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php } ?>

  <script>
    function cancelStudent(id, center) {
      $.ajax({
        url: '/app/centers/ledgers/cancel/create?id=' + id + '&center=' + center,
        type: 'GET',
        success: function(data) {
          $('#md-modal-content').html(data);
          $("#mdmodal").modal('show');
        }
      })
    }
  </script>

<?php } ?>