<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<head>
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

</head>

<body>
    <div class="text-center mt-5">
        <?php
        ini_set('display_errors', 1);
        require '../../includes/db-config.php';
      //  require '../../includes/helpers.php';
        session_start();
        $webQuery ='';
        $semQuery ='';

        $sem  ='';
        if (isset($_GET['year_sem'])) {
            $sem = $_GET['year_sem'];
            $semQuery=  "&year_sem=".$sem;
        }
        if (isset($_GET['user_id']) && isset($_GET['password'])) {
            $user_id = $_GET['user_id'];
            $password = $_GET['password'];
            $webQuery ="?user_id=".$user_id."&password=".$password.$semQuery;
        }else{
            $webQuery ="?enroll_no=".$_SESSION['Enrollment_No'].$semQuery;
        }

     
        $rows =[];
   //     $row =[];
        
        $get_result_data_url = "https://aparni.edtechinnovate.in/student/examination/api".$webQuery;
        $result = file_get_contents($get_result_data_url);
        $rows = json_decode($result, true);
        //  echo "<pre>"; print_r($result);die;
        if(isset($rows['status']) &&$rows['status']==1){
        $row = $rows['data'];
    //   echo "<pre>"; print_r($_SESSION['university_id']);die;
             $row['SubCourseName'] = '-';

if (!empty($row['Sub_Course_ID'])) {
    $subCourseId = (int) $row['Sub_Course_ID'];

    $res = $conn->query("SELECT Name FROM Sub_Courses WHERE ID = $subCourseId");
    if ($res && $sc = $res->fetch_assoc()) {
        $row['SubCourseName'] = $sc['Name'];
    }
}
        ?>
        <?php 
        if($_SESSION['university_id']!="20"){
         ?>
        <div class="text-center mt-5">
            <div class="col-md-3" style="margin: -38px 0px 0px 25px;">
                <select class="form-control" name="year_semester" id="year_semester">
                    <option value="">Select</option>
                    <?php  for($i =1; $i <= $row['Min_Duration']; $i++){?>
                    <option value="<?= $i ?>" <?= ($i == $sem) ? 'selected' : '' ?>>Semester <?= $i ?></option>
                    
                <?php } ?>
                </select>
            </div>
        </div>
      <?php
      }
      ?>
        <div id="contentpdf" class="html-content" style="background: #fff;">
          <div class="result_section">
            <div class="mt-5 body"
                style="border:3px solid #1e1919;height: 1145px;; width: 900px; margin: 0 auto; background-position: center; background-size: contain; background-repeat: no-repeat; padding: 0px;">
                <!--<div class="" style="display:flex; justify-content:center;">-->
                    <!--<img src="https://aparni.edtechinnovate.in/assets/img/1739959794.png" alt="" style="margin-top: 15px;width:11%">-->
                <!--</div>-->
                  <div class="" style="display:flex; justify-content:center;">
                    <!--<img src="https://wilpvocarni.edtechinnovate.in/assets/img/1739959794.png" alt="" style="margin-top: 15px;width:11%">-->
                     <p style="font-size:40px;font-weight:bold;margin-top:20px;">Result</p>
                </div>
               <p class="text-center text-dark fw-bold">  <?= ucwords(strtolower($row['program_Type'] )) ?> ( <?= $row['SubCourseName'] ?>)</p>
                    
                <!--<p style="margin-top:2%;text-align: center;font-weight: 700;font-size: 20px!important;color:black !important;">(Arni University Established by HP Act 23 of 2009)</p>-->
                <div class="main-result-box" style="padding: 0px; height: 0px; margin: 0 auto; position: relative; top: 0px; right: 0px;">
                    <!--<p class="text-center text-dark fw-bold">Statement Of Marks</p>-->
                     <!--<p class="text-center text-dark fw-bold"> <?= ucwords(strtolower($row['duration_val'] )) ?>  <?=  ucwords(strtolower($row['course']))  ?></p>-->
                   
                    <!--<p class="text-center text-dark fw-bold"> <?= ucwords(strtolower($row['duration_val'] )) ?>  <?=  $row['course'] ?></p>-->
                    <!--<p class="text-center text-dark fw-bold"> <?= ucwords($row['course']) ?></p>-->
                    <!--<p class="text-center text-dark fw-bold">  <?= ucwords(strtolower($row['program_Type'] )) ?> ( <?= $row['SubCourseName'] ?>)</p>-->
                    <!--<p class="text-center text-dark fw-bold">Admission Session :<?= ucwords($row['Admission_Session'])  ?></p>-->
                    <!--<img src="https://aparni.edtechinnovate.in/<?=$row['Photo'] ?>" alt="" width="100" height="100" class="img-pp">-->
                    <div class="row">
                        <div class="col-lg-12 mb-1">
                            <div class="table-resposive">
                                <table class="table-bordered mb-3">
                                    <tbody>
                                        <tr>
                                            <td class="col text-start" style="width:600px; height:40px;"><span class="fw-bold " style="color: #05519E;">Name:</span> <span class="text-dark fw-bold"><?=$row['stu_name'] ?></span></td>
                                            <td class="col text-start" style="width:400px; height:40px;"><span class="fw-bold " style="color: #05519E;"> Enrollment No:</span><span class="text-dark fw-bold"> <?=$row['Enrollment_No'] ?></span></td>
                                        </tr>
                                      
                                        <tr>
                                            <!--<td class="col text-start" style="width:600px; height:40px;"><span class="fw-bold " style="color: #05519E;">Father Name:</span> <span class="text-dark fw-bold"><?= $row['Father_Name']?></span></td>-->
                                            <td class="col text-start" style="width:400px; height:40px;"><span class="fw-bold " style="color: #05519E;"><?= $row['mode_type'] ?> :</span><span class="text-dark fw-bold"> <?= $row['durMonthYear'] ?> </span></td>
                                            <td class="col text-start" style="width:400px; height:40px;"><span class="fw-bold " style="color: #05519E;">Exam Session:</span><span class="text-dark fw-bold"> <?= $row['stu_exam_session'] ?> </span></td>
                                          
                                      </tr>
                                        <!--<tr>
                                            <td class="col text-start" style="width:600px; height:40px;"><span class="fw-bold " style="color: #05519E;">School:</span> <span class="text-dark fw-bold">Glocal School Of <?= $row['university_name']?></span></td>
                                            <td class="col text-start" style="width:400px; height:40px;"><span class="fw-bold " style="color: #05519E;">Exam Session:</span><span class="text-dark fw-bold"> <?= $row['stu_exam_session'] ?> </span></td>
                                        </tr> -->
                               

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="table-box">
                        <table width="100%" style="border-collapse: collapse;border: 2px solid #8b8b8b;width: 100%;">
                            <?php if($row['University_ID']==41){ ?>
                                    <tbody>
                                <tr class="text-center" style="color: #05519E; font-weight: 700;">
                                    <th style="width:119px;border: 2px solid #8b8b8b;" rowspan="2">Subject Code</th>
                                    <th style="width: 350px; border: 2px solid #8b8b8b;">Subject Name</th>
                                    <th style="border: 2px solid #8b8b8b;" rowspan="2">Obtained Marks</th>
                                    <th style="border: 2px solid #8b8b8b;" rowspan="2">MIN. MARKS</th>
                                    <th style="border: 2px solid #8b8b8b;" rowspan="2">MAX. MARKS</th>
                                    <th style="border: 2px solid #8b8b8b;" rowspan="2">REMARKS</th>
                                </tr>
                                <tr class="text-center" style="color: #05519E; font-weight: 700;"> </tr> 
                                <?php foreach ($row['marks'] as $result) { ?>
                                <tr class="text-center" style="font-weight: 700;">                    
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;" class="text-dark"><?= $result['Code']?></td>
                                    <td class="text-left text-dark" style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;text-align:start !important;"><?= $result['subject_name']?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;" class="text-dark"><?= $result['obt_marks_ext']?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;" class="text-dark"><?= $result['minimum_marks']?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;" class="text-dark"><?= $result['Max_Marks']?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;" class="text-dark"><?= ucwords(strtolower($result['remarks_status']))?></td>
                                </tr>
                                <?php } ?>
                            </tbody> 
                            
                            <?php } else{ ?>
                                
                                    <tbody>
                               <tr class="text-center border-bottom-0">
                                    <th scope="col" class="col blue" style="width: 10%;border-bottom: 1px solid #fff;"> Subject Code</th>
                                    <th scope="col" class="col blue" style="width: 28%;border-bottom: 1px solid #fff;">Subject Name</th>
                                    <th scope="col" colspan="2" class="col blue" style="width: 10%;">Internal</th>
                                    <th scope="col" colspan="2" class="col blue" style="width: 10%;">External</th>
                                    <th scope="col" colspan="2" class="col blue" style="width: 10%;">Total</th>
                                </tr>
                                <tr class="border-top-0 text-center">
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col" class="col border-top-1 blue"style="border-top: 1px solid #8080804d;">OBT</th>
                                    <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">Max</th>
                                    <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">OBT</th>
                                    <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">Max</th>
                                    <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">OBT</th>
                                    <th scope="col" class="col border-top-1 blue" style="border-top: 1px solid #8080804d;">Max</th>
                                </tr>
                                <?php foreach ($row['marks'] as $result) { ?>
                                <tr class="text-center" style="font-weight: 700;">
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                        class="text-dark"><?= $result['Code']?></td>
                                    <td class="text-left text-dark"
                                        style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;text-align:start !important;">
                                        <?= $result['subject_name']?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                        class="text-dark"><?= $result['obt_marks_int']?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                        class="text-dark"><?= $result['Min_Marks']?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                        class="text-dark"><?= $result['obt_marks_ext']?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                        class="text-dark"><?= $result['Max_Marks']?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                        class="text-dark"><?= ($result['total_obtain_ext_int'] ) ?></td>
                                    <td style="padding: 6px;border-left: 2px solid #8b8b8b;border-radius: 2px solid #8b8b8b;font-size: 14px;"
                                        class="text-dark"><?= ($result['grand_total_ext_int'])?></td>
                                </tr>
                                 <?php }?>
                            </tbody>    
                            <?php }
                            
                            ?> 
                            
                        </table>    
                    </div>
                    <p class="text-center mt-3 mb-3" style="text-align:center;font-size: 22px; font-weight: 900; color: #05519E;"> AGGREGATE MARKS</p>
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
                                    <td rowspan="2" style="border: 2px solid #8b8b8b;" class="text-dark"><?= $row['result_status'] ?></td>
                                    <td rowspan="2" style="border: 2px solid #8b8b8b;" class="text-dark"><?= $row['percentage'] ?></td>
                                </tr>
                                <tr>
                                    <th style="border: 2px solid #8b8b8b;" class="text-dark"> Maximum Mark</th>
                                    <td style="border: 2px solid #8b8b8b;" class="text-dark"><?= $row['total_max'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="des">
                        <p class="p-0 m-0"style="position: relative;font-size: 20px; top: 14px; right: 10px;color: #05519E;font-weight: 700;display: inline-block;"> <span class="top-heading-u"></span>Disclaimer :</p>
                        <p style="position: relative; top: 10px;color: #05519E;font-weight: 700;display: inline-block;"><span class="top-heading-u"></span> The published result is provisional only.  University is not responsible for any inadvertent error that may have crept in the data / results being published online.This is being published just for the immediate information to the examinees. The final mark sheet(s) issued by  University will only be treated authentic &amp; final in this regard.</p>
                    </div>
                </div>
            </div>
          </div>
        </div>


        <div class="text-center no-print mb-4 mt-3">
            <button type="button" class="btn btn-primary" id="cmd" onclick="printDiv('contentpdf')">Download as PDF/Print</button>
        </div>
<?php } else{?>
    <div class="mt-5 mb-4 text-center" style="margin-top:220px;"><h5>
      	<?php
      		echo $rows['msg'];
      ?>
      </h5></div>
    <?php }?>


        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
        <script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
        <?php if (isset($_SESSION['Role']) &&  $_SESSION['Role'] =="Student") { ?>
          
               <script type="text/javascript">
                $(document).ready(function () {
                    $("#year_semester").change(function () {
                        var year_sem = $("#year_semester").val();
                        window.location.href = '/student/examination/results?year_sem=' + year_sem;
                    });
                });
            </script>
        <?php } else { ?>
            <script type="text/javascript">
                $("#year_semester").change(function () {
                    var year_sem =  $("#year_semester").val();
                    var user_id ='<?=$user_id  ?>';
                    var password ='<?=$password ?>';
                    $.ajax({
                      
                        url: 'https://aparni.edtechinnovate.in/student/examination/get-sem-data.php',
                        method: 'POST',
                        data: {
                            year_sem: year_sem,
                            user_id: user_id,
                            password: password
                        },  
                        success: function (response) {
                            $(".result_section").html(response);
                        },
                    });
                });
            </script>
        <?php } ?>


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