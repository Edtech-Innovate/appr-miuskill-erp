<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php')?>
<?php $id = str_replace('W1Ebt1IhGN3ZOLplom9I', '', base64_decode($_REQUEST['id'])); ?>
<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); ?>
<div class="page-container ">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                
                //   echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                echo '<li class="breadcrumb-item ' . $active . '">University Fees</li>';
                endif;
              }
              ?>
              <div>
                
                
              </div>
            </ol>
            <!-- END BREADCRUMB -->

          </div>
        </div>
      </div>
      <div class=" container-fluid">
          <div class="card">
                <!-- <h5 class="card-header text-center text-md-start">Multilingual</h5> -->
                <div class="card-body">
                    <div class="row">
                        <?php
                        $query = "SELECT Students.*,Sub_Courses.* FROM `Students` LEFT JOIN Sub_Courses ON Sub_Courses.ID = Students.Sub_Course_ID WHERE Students.ID =  $id";
                        $courseData = $conn->query($query);
                        $record = $courseData->fetch_assoc();
                        $paidAmount = $conn->query("select sum(amount) as paidAmount from university_payment where payment_type=2 and student_id=" . $id)->fetch_assoc();

                        ?>
                        <div class="col-md-4">
                            <label for="student_name">Student Name</label>
                            <input type="text" class="form-control" name="name" id="name"
                                value="<?= $record['First_Name'] . ' ' . $record['Middle_Name'] . ' ' . $record['Last_Name'] ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="course">Course</label>
                            <select name="course_id" id="course_id" class="form-control">
                                <option value="">Choose</option>
                                <option value="<?= $record['ID'] ?>" selected><?= $record['Name'] ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <span> University Fee: <?= $paidAmount['paidAmount'] ?>/<?= $record['University_fee'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    if ($paidAmount['paidAmount'] < $record['University_fee']) {
                        ?>
                        <div class="row pull-right">
                            <div class="col-md-2">
                                <button class="btn btn-primary"
                                    onclick="addUniversityPayment(<?= $id ?>,<?= $record['ID'] ?>,2)">Add Payment</button>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <table class="table table-stripe">
                        <tbody>
                            <tr>
                                <th>Sno</th>
                                <th>Date of transaction</th>
                                <th>Mode of payment</th>
                                <th>URN No.</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </tbody>
                        <tbody>
                            <?php
                            $universityPaidAmountQuery = "select * from university_payment where payment_type=2 and student_id=" . $id;
                            $paidAmountData = $conn->query($universityPaidAmountQuery);
                            $amount = 0;
                            $i = 1;
                            while ($row = $paidAmountData->fetch_assoc()) {

                                $amount = $amount + $row['amount'];
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $row['date_of_transaction'] ?></td>
                                    <td><?= $row['mode_of_payment'] ?></td>
                                    <td><?= $row['urn_no'] ?></td>
                                    <td><?= $row['amount'] ?></td>
                                    <td><a href="javascript:void(0)"
                                            onclick="editPayment(<?= $row['id'] ?>,<?= $record['ID'] ?>)"><i
                                                class="uil uil-edit-alt text-primary ms-3"></i></a>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
                 <!-- start Miscellaneous university fee -->
            <!--<div class="card mt-4">-->
            <!--    <h4 class="card-header text-center mb-4 mt-4 ">Miscellaneous University Fee</h4>-->
            <!--    <div class="card-body mt-4">-->
            <!--        <div class="row">-->
               <?php
                      
                    //     $getMisUniFee = $conn->query("SELECT SUM(University_Fee) AS uni_misc_fee FROM Student_Ledgers where Student_ID = $id and Type = 1 AND Source = 'Miscellaneous Type Fee'");
                    //     $total_mis_uni_fee = $getMisUniFee->fetch_assoc()['uni_misc_fee'] ?? 0;
                    //     $paidAmount = $conn->query("select sum(amount) as paidAmount from university_payment where payment_type =1 and student_id=" . $id)->fetch_assoc();-->

                    //  $total_paid_mis_uni_fee = $paidAmount['paidAmount'] ?? 0;


                   ?>
            <!--            <div class="col-md-4">-->
            <!--                <label for="student_name">Student Name</label>-->
            <!--                <input type="text" class="form-control" name="name" id="name"-->
            <!--                    value="<?= $record['First_Name'] . ' ' . $record['Middle_Name'] . ' ' . $record['Last_Name'] ?>">-->
            <!--            </div>-->
            <!--            <div class="col-md-4">-->
            <!--                <label for="course">Course</label>-->
            <!--                <select name="course_id" id="course_id" class="form-control">-->
            <!--                    <option value="">Choose</option>-->
            <!--                    <option value="<?= $record['ID'] ?>" selected><?= $record['Name'] ?></option>-->
            <!--                </select>-->
            <!--            </div>-->
            <!--            <div class="col-md-4">-->
            <!--                <span> University Fee:-->
            <!--                    <?= $total_paid_mis_uni_fee ?>/<?= $total_mis_uni_fee ?></span>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--    <div class="card-body">-->
            <!--        <?php-->
            <!--        if ($total_paid_mis_uni_fee  < $total_mis_uni_fee) {-->
            <!--            ?>-->
            <!--            <div class="row pull-right">-->
            <!--                <div class="col-md-2">-->
            <!--                    <button class="btn btn-primary"-->
            <!--                        onclick="addUniversityPayment(<?= $id ?>,<?= $record['ID'] ?>,1)">Add Payment</button>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--            <?php-->
            <!--        }-->
            <!--        ?>-->
            <!--        <table class="table table-stripe">-->
            <!--            <tbody>-->
            <!--                <tr>-->
            <!--                    <th>Sno</th>-->
            <!--                    <th>Date of transaction</th>-->
            <!--                    <th>Mode of payment</th>-->
            <!--                    <th>URN No.</th>-->
            <!--                    <th>Amount</th>-->
            <!--                    <th>Action</th>-->
            <!--                </tr>-->
            <!--            </tbody>-->
            <!--            <tbody>-->
            <!--                <?php-->
            <!--                $universityPaidAmountQuery = "select * from university_payment where student_id=" . $id." AND payment_type =1";-->
            <!--                $paidAmountData = $conn->query($universityPaidAmountQuery);-->
            <!--                $amount = 0;-->
            <!--                $i = 1;-->
            <!--                while ($row = $paidAmountData->fetch_assoc()) {-->

            <!--                    $amount = $amount + $row['amount'];-->
            <!--                    ?>-->
            <!--                    <tr>-->
            <!--                        <td><?= $i ?></td>-->
            <!--                        <td><?= $row['date_of_transaction'] ?></td>-->
            <!--                        <td><?= $row['mode_of_payment'] ?></td>-->
            <!--                        <td><?= $row['urn_no'] ?></td>-->
            <!--                        <td><?= $row['amount'] ?></td>-->
            <!--                        <td><a href="javascript:void(0)"-->
            <!--                                onclick="editPayment(<?= $row['id'] ?>,<?= $record['ID'] ?>)"><i-->
            <!--                                    class="ri-edit-box-fill text-primary ms-3"></i></a>-->
            <!--                        </td>-->
            <!--                    </tr>-->
            <!--                    <?php-->
            <!--                    $i++;-->
            <!--                }-->
            <!--                ?>-->
            <!--            </tbody>-->
            <!--        </table>-->
            <!--    </div>-->
            <!--</div>-->
            <!-- end Miscellaneous university fee -->
      </div>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
<script>
        function addUniversityPayment(id, courseId, payment_type) {
            $.ajax({
                url: "/university/payment/make",
                type: "post",
                data: { id: id, courseId: courseId,payment_type:payment_type },
                success: function (res) {
                    $('#md-modal-content').html(res);
                    $('#mdmodal').modal('show');
                }
            })
        }
        function editPayment(id, courseId) {
            $.ajax({
                url: "/university/payment/edit",
                type: "post",
                data: { id: id, courseId: courseId },
                success: function (res) {
                    $('#md-modal-content').html(res);
                    $('#mdmodal').modal('show');
                }
            })
        }
    </script>
 <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>