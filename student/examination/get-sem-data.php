<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
session_start();
ini_set('display_errors', 1);
require '../../includes/db-config.php';
require '../../includes/helpers.php';
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');

    body {
        font-family: 'Roboto', sans-serif;
    }

    .top-box-inline {
        height: 115px;
    }

    .img-pp {
        position: absolute;
        right: 1px;
        top: 10px;

    }

    .table-box-bottom table {
        border-collapse: collapse;
        border: 2px solid #8b8b8b;
        width: 100%;
    }

    .table-box-bottom th {
        border: 2px solid #8b8b8b;
    }

    .table-box-bottom td {
        border: 2px solid #8b8b8b;
    }

    body {
        margin: 0.7cm;
    }

    .text-black {
        color: black;
    }

    td.col {
        padding: 10px;
        border: 2px solid #8b8b8b;
    }

    th {
        padding: 9px;
        border-bottom: 2px solid #8b8b8b;
        border-left: 2px solid #8b8b8b;
    }

    .main-result-box {
        width: 92%;
    }

    .des {

        text-align: justify;
    }
</style>
<?php

$url = "https://wilpvocarni.edtechinnovate.in/";
$passFail = "PASS";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $typoArr = ["th", "st", "nd", "rd", "th", "th", "th", "th", "th"];


    $webQuery = '';
    $semQuery = '';

    $sem = '';
    if (isset($_POST['year_sem'])) {
        $sem = $_POST['year_sem'];
        $semQuery = "&year_sem=" . $sem;
    } else {
        $sem = 1;
        $semQuery = "&year_sem=" . $sem;
    }
    if (isset($_POST['user_id']) && isset($_POST['password'])) {
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];
        $webQuery = "?user_id=" . $user_id . "&password=" . $password . $semQuery;
    }


    $rows = [];
    $get_result_data_url = "https://erpgurukashi.edtechinnovate.com/student/examination/api" . $webQuery;
    $result = file_get_contents($get_result_data_url);
    $rows = json_decode($result, true);
    if (isset($rows['status']) && $rows['status'] == 1) {
        $row = $rows['data'];
        // echo "<pre>"; print_r($row);die;
        ?>
        <div id="contentpdf" class="html-content" style="background: #fff;">
            <div class="mt-5 body"
                style="border:3px solid #1e1919;height: 1145px;; width: 900px; margin: 0 auto; background-position: center; background-size: contain; background-repeat: no-repeat; padding: 0px;">
                <div class="" style="display:flex; justify-content:center;">
                    <img src="https://erpgurukashi.edtechinnovate.com/assets/img/university/gku_universitylogo1.jpg" alt=""
                        style="margin-top: 15px;width:27%">
                </div>
                <p style="margin-top:1%;text-align: center;font-weight: 700;font-size: 20px!important;color:black !important;">
                    (A University Established by UP Act 23 of 2009)</p>
                <div class="main-result-box"
                    style="padding: 0px; height: 0px; margin: 0 auto; position: relative; top: 0px; right: 0px;">
                    <p class="text-center text-dark fw-bold">Statement Of Marks</p>
                    <p class="text-center text-dark fw-bold"> <?= ucwords(strtolower($row['duration_val'])) ?>in
                        <?= ucwords(strtolower($row['course'])) ?>
                    </p>
                    <p class="text-center text-dark fw-bold">Admission Session :<?= ucwords($row['Admission_Session']) ?></p>
                    <img src="https://erpgurukashi.edtechinnovate.com/<?= $row['Photo'] ?>" alt="" width="100" height="100"
                        class="img-pp">
                    <div class="row">
                        <div class="col-lg-12 mb-1">
                            <div class="table-resposive">
                                <table class="table-bordered mb-3">
                                    <tbody>
                                        <tr>
                                            <td class="col  text-start" style="width:600px; height:40px;"><span class="fw-bold "
                                                    style="color: #05519E;">Name:</span> <span
                                                    class="text-dark fw-bold"><?= $row['stu_name'] ?></span></td>
                                            <td class="col  text-start" style="width:400px; height:40px;"><span class="fw-bold "
                                                    style="color: #05519E;"> Enrollment No:</span><span
                                                    class="text-dark fw-bold"> <?= $row['Enrollment_No'] ?></span></td>
                                        </tr>
                                        
                                            <tr>
                                                <!-- <td class="col text-start" style="width:600px; height:40px;"><span class="fw-bold "
                                                        style="color: #05519E;">Father Name:</span> <span
                                                        class="text-dark fw-bold"><?= ucwords(strtolower($row['Father_Name'])) ?></span>
                                                </td> -->
                                                <td class="col text-start" style="width:400px; height:40px;"><span class="fw-bold "
                                                        style="color: #05519E;"><?= $row['mode_type'] ?> :</span><span
                                                        class="text-dark fw-bold"> <?= $row['durMonthYear'] ?> </span></td>
                                                        <td class="col text-start" style="width:400px; height:40px;"><span class="fw-bold "
                                                        style="color: #05519E;">Exam Session:</span><span class="text-dark fw-bold">
                                                        <?= $row['stu_exam_session'] ?> </span></td>
                                            </tr>
                                            <!-- <tr>
                                                <td class="col text-start" style="width:600px; height:40px;"><span class="fw-bold "
                                                        style="color: #05519E;">School:</span> <span
                                                        class="text-dark fw-bold">Glocal School Of
                                                        <?= $row['university_name'] ?></span></td>
                                                <td class="col text-start" style="width:400px; height:40px;"><span class="fw-bold "
                                                        style="color: #05519E;">Exam Session:</span><span class="text-dark fw-bold">
                                                        <?= $row['stu_exam_session'] ?> </span></td>
                                            </tr> -->
                                     
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="table-box">
                        <table width="100%" style="border-collapse: collapse;border: 2px solid #8b8b8b;width: 100%;">
                    
                                <tbody>
                                    <tr class="text-center border-bottom-0">
                                        <th scope="col" class="col blue" style="width: 10%;border-bottom: 1px solid #fff;"> Subject
                                            Code</th>
                                        <th scope="col" class="col blue" style="width: 28%;border-bottom: 1px solid #fff;">Subject
                                            Name</th>
                                        <th scope="col" colspan="2" class="col blue" style="width: 10%;">Internal</th>
                                        <th scope="col" colspan="2" class="col blue" style="width: 10%;">External</th>
                                        <th scope="col" colspan="2" class="col blue" style="width: 10%;">Total</th>
                                    </tr>
                                    <tr class="border-top-0 text-center">
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">OBT
                                        </th>
                                        <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">Max
                                        </th>
                                        <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">OBT
                                        </th>
                                        <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">Max
                                        </th>
                                        <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">OBT
                                        </th>
                                        <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">Max
                                        </th>
                                    </tr>
                                    <?php foreach ($row['marks'] as $result) { ?>
                                        <tr class="text-center" style="font-weight: 700;">
                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                class="text-dark"><?= $result['Code'] ?></td>
                                            <td class="text-left text-dark"
                                                style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;text-align:start !important;">
                                                <?= $result['subject_name'] ?>
                                            </td>
                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                class="text-dark"><?= $result['obt_marks_int'] ?></td>
                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                class="text-dark"><?= $result['Min_Marks'] ?></td>
                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                class="text-dark"><?= $result['obt_marks_ext'] ?></td>
                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                class="text-dark"><?= $result['Max_Marks'] ?></td>
                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                class="text-dark"><?= ($result['total_obtain_ext_int']) ?></td>
                                            <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                                class="text-dark"><?= ($result['grand_total_ext_int']) ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                       
                        </table>
                    </div>
                    <p class="text-center mt-3 mb-3"
                        style="text-align:center;font-size: 22px; font-weight: 900; color: #05519E;"> AGGREGATE MARKS</p>
                    <div class="table-box-bottom">
                        <table class="text-center" style="border-collapse: collapse;border: 2px solid #8b8b8b;width: 100%;">
                            <tbody>
                                <tr style="color: #05519E; font-weight: 700;">
                                    <th style="border: 2px solid #8b8b8b;">Marks</th>
                                    <th style="border: 2px solid #8b8b8b;">GRAND TOTAL</th>
                                    <th style="border: 2px solid #8b8b8b;">RESULT</th>
                                    <th style="border: 2px solid #8b8b8b;">PERCENTAGE</th>
                                </tr>
                                <tr>
                                    <th style="border: 2px solid #8b8b8b;" class="text-dark">Obtained Mark</th>
                                    <td style="border: 2px solid #8b8b8b;" class="text-dark"><?= $row['total_obt'] ?></td>
                                    <td rowspan="2" style="border: 2px solid #8b8b8b;" class="text-dark">
                                        <?= $row['result_status'] ?>
                                    </td>
                                    <td rowspan="2" style="border: 2px solid #8b8b8b;" class="text-dark">
                                        <?= $row['percentage'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="border: 2px solid #8b8b8b;" class="text-dark"> Maximum Mark</th>
                                    <td style="border: 2px solid #8b8b8b;" class="text-dark"><?= $row['total_max'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="des">
                        <p class="p-0 m-0"
                            style="position: relative;font-size: 20px; top: 14px; right: 10px;color: #05519E;font-weight: 700;display: inline-block;">
                            <span class="top-heading-u"></span>Disclaimer :
                        </p>
                        <p style="position: relative; top: 10px;color: #05519E;font-weight: 700;display: inline-block;"><span
                                class="top-heading-u"></span> The published result is provisional only. Gurukashi University is not
                            responsible for any inadvertent error that may have crept in the data / results being published
                            online.This is being published just for the immediate information to the examinees. The final mark
                            sheet(s) issued by Gurukashi University will only be treated authentic &amp; final in this regard.</p>
                    </div>
                </div>
            </div>
        </div>
    


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
    <script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }

        function toRoman(type) {
            var roman = ["st", "nd", "rd", "th", "th", "th", "th", "th"];
            $('.semsyear').text(roman[type - 1]);
        }
    </script>
    <?php } else { ?>
        <div class="mt-5 mb-4 text-center" style="margin-top:220px;">
            <h5>Result Not Publish Yet!</h5>
        </div>
    <?php }
    ?>
<?php }
?>