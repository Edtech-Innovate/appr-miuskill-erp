<?php if (isset($_GET['id'])) {
  session_start();
  require '../../../../includes/db-config.php';
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  $id = intval($_GET['id']);
  
  // Check if a specific role filter was passed from the dropdown (Self vs Sub-Center)
  $role_filter = isset($_GET['role']) ? $_GET['role'] : '';

  $added_for = array();
  
  if ($role_filter == 'Center') {
      // If "Self" is selected, only look for students added by this specific center ID
      $added_for[] = $id;
  } else if ($role_filter == 'Sub-Center') {
      // If "Sub-Center" is selected, only look for downlines (exclude the parent center)
      $downlines = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting = $id");
      while ($downline = $downlines->fetch_assoc()) {
        $added_for[] = $downline['User_ID'];
      }
  } else {
      // Default behavior (if no role filter is passed yet): include both self and downlines
      $added_for[] = $id;
      $downlines = $conn->query("SELECT `User_ID` FROM University_User WHERE Reporting = $id");
      while ($downline = $downlines->fetch_assoc()) {
        $added_for[] = $downline['User_ID'];
      }
  }
  
  $users = !empty($added_for) ? implode(",", array_filter($added_for)) : $id;

  $already = array();
  $already_ids = array();
  $invoices = $conn->query("SELECT Student_ID, Invoices.Duration FROM Invoices LEFT JOIN Payments ON Invoices.Invoice_No = Payments.Transaction_ID WHERE `User_ID` = $id AND Invoices.University_ID = " . $_SESSION['university_id'] . " AND ((Payments.Type = 1 AND Payments.Status != 2) OR (Payments.`Type` = 2 AND Payments.Status = 1))");
  while ($invoice = $invoices->fetch_assoc()) {
    $already[$invoice['Student_ID']] = $invoice['Duration'];
    $already_ids[] = $invoice['Student_ID'];
  }
  
  $students = $conn->query("SELECT Students.*,Courses.Name as Course_Name, Sub_Courses.Name as Sub_Course_Name FROM Students LEFT JOIN Courses on Courses.ID=Students.Course_ID LEFT JOIN Sub_Courses on Sub_Courses.ID=Students.Sub_Course_ID WHERE Students.University_ID = " . $_SESSION['university_id'] . " AND Students.Added_For IN ($users) AND Step = 4 AND Students.Process_By_Center IS NULL");
  if ($students->num_rows == 0) { ?>
    <div class="row">
      <div class="col-lg-12 text-center">
        No student(s) found!
      </div>
    </div>
  <?php } else {
    ?>
    <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') { ?>
      <div class="row m-b-20">
        <div class="col-md-12 d-flex justify-content-end">
          <div>
            <?php if (isset($_SESSION['gateway'])) { ?>
              <button type="button" class="btn btn-primary" onclick="pay('Online')"> Pay Online</button>
            <?php } ?>
            <button type="button" class="btn btn-primary" onclick="pay('Offline')">Pay Offline</button>
            <!--<button type="button" class="btn btn-primary" onclick="pay('wallet')">Pay Wallet</button>-->
          </div>
        </div>
      </div>
    <?php } ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th></th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Owner</th>
                <th>Course</th>
                <th>Total Course(Without Sharing)</th>
                <th>Total Course Fee Payable</th>
                <th>Course Fee (Duration wise without sharing)</th>
                <th>Payable(Duration Wise)</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              
              while ($student = $students->fetch_assoc()) {
                
                if (isset($student['Added_For'])) {
                  $roleQuery = $conn->query("SELECT Name, Code,Role FROM Users Where ID =" . $student['Added_For'] . "");
                  $roleArr = $roleQuery->fetch_assoc();
                  $code = isset($roleArr['Code']) ? $roleArr['Code'] : '';

                  if ($roleArr['Role'] == "Center" && ($_SESSION['Role'] == "Center" || $_SESSION['Role'] == "Administrator")) {
                    $added_by = "Self";
                  } else if ($_SESSION['Role'] == "Administrator" && $roleArr['Role'] == "Administrator") {

                    $added_by = isset($roleArr['Name']) ? $roleArr['Name'] : '';
                  } else {
                    $user_name = isset($roleArr['Name']) ? $roleArr['Name'] : '';
                    $added_by = $user_name . "(" . $code . ")";
                  }
                }

                $studentUniversityID = $student['University_ID'];
                $studentAddmissionID = $student['Admission_Session_ID'];
                $studentSubCourseId = $student['Sub_Course_ID'];
                $schemeData = $conn->query("select Scheme from Admission_Sessions where ID=$studentAddmissionID")->fetch_assoc();
                $schemeID = json_decode($schemeData['Scheme'], true)['schemes'][0];
                $feeStructure = $conn->query("select group_concat(ID) as feeStructure from Fee_Structures where University_ID = '$studentUniversityID'");
                $feeStructureID = $feeStructure->fetch_assoc()['feeStructure'];
                
                $courseFee = $conn->query("select sum(Fee) as totalFee from Fee_Constant where Scheme_ID=$schemeID and University_ID=$studentUniversityID and Fee_Structure_ID in($feeStructureID) and Sub_Course_ID=$studentSubCourseId");
                $totalCourseFee = $courseFee->fetch_assoc()['totalFee'];
                $addFeeUsers = ['AU15420','AU48542'];
                if(in_array($_SESSION['Code'],$addFeeUsers) && $_SESSION['university_id']==21 && $_SESSION['Role']=='Center'){
                    $totalCourseFee = intval($totalCourseFee)+2000;
                }elseif($code=='AU48542'){
                     $totalCourseFee = intval($totalCourseFee)+2000;
                }
                $userSharingAmountQuery = $conn->query("SELECT * FROM `User_Sub_Courses` WHERE User_ID = '" . $student['Added_For'] . "' and Sub_Course_ID = $studentSubCourseId and Scheme_ID = $schemeID and Admission_Session_ID=$studentAddmissionID and University_ID=$studentUniversityID");
                
                if (in_array($student['ID'], $already_ids) && $student['Duration'] == $already[$student['ID']]) {
                  continue;
                }
                $student_name = array_filter(array($student['First_Name'], $student['Middle_Name'], $student['Last_Name'])) ?>
                <tr>
                  <td>
                    <div class="form-check complete" style="margin-bottom: 0px;">
                      <input type="checkbox" class="student-checkbox" id="student-<?= $student['ID'] ?>" name="student_id"
                        value="<?= $student['ID'] ?>">
                      <label for="student-<?= $student['ID'] ?>" class="font-weight-bold"></label>
                    </div>
                  </td>
                  <td><b><?php echo !empty($student['Unique_ID']) ? $student['Unique_ID'] : $student['ID'] ?></b></td>
                  <td><?= implode(" ", $student_name) ?></td>
                  <td><?= $added_by ?></td>
                  <td><?= $student['Course_Name'].' ('.$student['Sub_Course_Name'].')' ?></td>
                  <td><?php
                      $balance = 0;
                      
                      $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1");
                     $total_bal = 0;
                      while ($ledger = $ledgers->fetch_assoc()) {
                        $fees = json_decode($ledger['Fee_Without_Sharing'], true);
                        foreach ($fees as $key => $value) {
                          $debit = $ledger['Type'] == 1 ? $value : 0;
                          $credit = $ledger['Type'] == 2 ? $value : 0;
                          $balance = ($balance + $credit) - $debit;
                          $total_bal += $balance;
                        }
                      }
                    
                    echo "&#8377; ".number_format(abs($balance), 2);

                    ?></td>
                   <td>
                    <?php
                      $balance = 0;
                      
                      $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1");
                      while ($ledger = $ledgers->fetch_assoc()) {
                        $fees = json_decode($ledger['Fee'], true);
                        foreach ($fees as $key => $value) {
                          $debit = $ledger['Type'] == 1 ? $value : 0;
                          $credit = $ledger['Type'] == 2 ? $value : 0;
                          $balance = ($balance + $credit) - $debit;
                        }
                      }
                    
                    echo "&#8377; ".number_format(abs($balance), 2);

                    ?>
                  </td>
                  <td>
                    <?php
                      $balance = 0;
                      
                      $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1 AND Duration <= " . $student['Duration']);
                      while ($ledger = $ledgers->fetch_assoc()) {
                        $fees = json_decode($ledger['Fee_Without_Sharing'], true);
                        foreach ($fees as $key => $value) {
                          $debit = $ledger['Type'] == 1 ? $value : 0;
                          $credit = $ledger['Type'] == 2 ? $value : 0;
                          $balance = ($balance + $credit) - $debit;
                        }
                      }
                    
                    echo "&#8377; ".number_format(abs($balance), 2);

                    ?>
                  </td>
                  <td>
                    <?php
                      $balance = 0;
                      
                      $ledgers = $conn->query("SELECT * FROM Student_Ledgers WHERE Student_ID = " . $student['ID'] . " AND Status = 1 AND Duration <= " . $student['Duration']);
                      while ($ledger = $ledgers->fetch_assoc()) {
                        $fees = json_decode($ledger['Fee'], true);
                        foreach ($fees as $key => $value) {
                          $debit = $ledger['Type'] == 1 ? $value : 0;
                          $credit = $ledger['Type'] == 2 ? $value : 0;
                          $balance = ($balance + $credit) - $debit;
                        }
                      }
                    
                    echo "&#8377; ".number_format(abs($balance), 2);

                    ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') { ?>
      <script src="https://ebz-static.s3.ap-south-1.amazonaws.com/easecheckout/easebuzz-checkout.js"></script>
      <script type="text/javascript">
        function pay(by) {
          if ($('.student-checkbox').filter(':checked').length == 0) {
            notification('danger', 'Please select Student');
          } else {
            var center = '<?= $id ?>';
            var ids = [];
            $.each($("input[name='student_id']:checked"), function () {
              ids.push($(this).val());
            });

            $.ajax({
              url: '/app/centers/ledgers/payable-amount',
              type: 'POST',
              data: {
                ids,
                center
              },
              dataType: 'json',
              success: function (data) {
                if (data.status) {
                  if (by == 'Online') {
                    payOnline(ids, data.amount, center);
                  } else if (by == 'Offline') {
                    payOffline(ids, data.amount, center);
                  } else if (by == 'wallet') {
                    payWallet(ids, data.amount, center);
                  }
                } else {
                  notification('danger', data.message);
                }
              }
            })
          }
        }
        function payWallet(ids, amount, center) {
          var by = 'wallet';
          $.ajax({
            url: '/app/wallet-payments/create-multiple',
            type: 'post',
            data: {
              ids,
              amount,
              center,
              by
            },
            success: function (data) {
              $("#lg-modal-content").html(data);
              $("#lgmodal").modal('show');
            }
          });
        }
        function payOnline(ids, amount, center) {
          $.ajax({
            url: '/app/easebuzz/pay-multiple',
            type: 'post',
            data: {
              ids,
              amount
            },
            dataType: "json",
            success: function (data) {
              if (data.status == 1) {
                $('.modal').modal('hide');
                initiatePayment(data.data, center)
              } else {
                notification('danger', data.error);
              }
            }
          });
        }

        function payOffline(ids, amount, center) {
          $.ajax({
            url: '/app/offline-payments/create-multiple',
            type: 'post',
            data: {
              ids,
              amount,
              center
            },
            success: function (data) {
              $("#lg-modal-content").html(data);
              $("#lgmodal").modal('show');
            }
          });
        }

        function initiatePayment(data, center) {
          var easebuzzCheckout = new EasebuzzCheckout('<?= $_SESSION['access_key']??"" ?>', 'prod')
          var options = {
            access_key: data,
            dataType: 'json',
            onResponse: (response) => {
              updatePayment(response, center);
              if (response.status == 'success') {
                Swal.fire({
                  title: 'Thank You!',
                  text: "Your payment is successfull!",
                  icon: 'success',
                  showCancelButton: false,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'OK'
                }).then((result) => {
                  if (result.isConfirmed) {
                    getLedger(center);
                  }
                })
              } else {
                Swal.fire(
                  'Payment Failed',
                  'Please try again!',
                  'error'
                )
              }
            },
            theme: "#272B35" // color hex
          }
          easebuzzCheckout.initiatePayment(options);
        }

        function updatePayment(response, center) {
          $.ajax({
            url: '/app/easebuzz/response',
            type: 'POST',
            data: {
              response
            },
            dataType: 'json',
            success: function (response) {
              if (response.status) {
                getLedger(center);
              } else {
                notification('danger', data.message);
              }
            },
            error: function (response) {
              console.error(response);
            }
          })
        }
      </script>
    <?php } ?>
  <?php }
}
?>