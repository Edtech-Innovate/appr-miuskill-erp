<?php
require '../../includes/db-config.php';
require '../../includes/helpers.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
// exit;
$enroll = isset($_POST['enroll']) ? $_POST['enroll'] : "";
$duration = isset($_POST['duration']) ? $_POST['duration'] : "";
$centerQuery = "";

if ($_SESSION['university_id'] == 41) {

    $user_code = isset($_POST['user_code']) ? $_POST['user_code'] : "";

    if (!empty($user_code)) {

        $check = $conn->query("SELECT ID, Role, Code FROM Users WHERE Code='$user_code'");
        if ($check->num_rows > 0) {

            $userArr = $check->fetch_assoc();

            if ($userArr['Role'] == "Sub-Center") {
                $center_id = getCenterIdFunc($conn, $userArr['ID']);
                $checkCenter = $conn->query("SELECT Code FROM Users WHERE ID='$center_id' AND Role='Center'");
                if ($checkCenter->num_rows > 0) {
                    $centerArr = $checkCenter->fetch_assoc();
                    $user_code = $centerArr['Code'];
                }
            }

            // Encode for JSON_CONTAINS
            $user_code = json_encode($user_code, JSON_UNESCAPED_SLASHES);

            // Final condition for query
            $centerQuery = " AND JSON_CONTAINS(sy.User_ID, '$user_code')";
//             echo "<pre>";
// print_r($centerQuery);
// echo "</pre>";
// exit;
        }
    }
}

if (!empty($enroll) && !empty($duration)) {
    if($_SESSION['university_id']==41){
        $centerid = $conn->query("select Added_For,Sub_Course_ID from Students where Enrollment_No='$enroll'");
        // print_r($centerid->fetch_assoc());die;
        $centerid = $centerid->fetch_assoc();
        $staticCondition = "";
        // print_r($centerid);
        if($centerid['Added_For']==1557 && $centerid['Sub_Course_ID']==1217){
            $staticCondition = " and sy.Code IN ('CDHMCS-0016','CDHMCS-0017','CDHMCS-0018','CDHMCS-0019','CDHMCS-0020','CDHMCS-0021','CDHMCS-0022','CDHMCS-0023','CDHMCS-0024','CDHMCS-0025')";
        }elseif($centerid['Added_For']==1525 && $centerid['Sub_Course_ID']==1076){
            $staticCondition = " and sy.Code IN ('CDST-016','CDST-017','CDST-018','CDST-019','CDST-020','CDST-021','CDST-022','CDST-023','CDST-024','CDST-025')";
        }
    }
    // print_r($staticCondition);
    // print_r("select sy.Min_Marks,sy.Max_Marks, sy.Name as subject_name,sy.Paper_Type, sy.Code, sy.ID from Syllabi as sy left join Students on sy.Sub_Course_ID= Students.Sub_Course_ID AND sy.Course_ID= Students.Course_ID where Enrollment_No = '$enroll' AND sy.Semester = '$duration' $staticCondition $centerQuery   and (sy.exam_type='center' or sy.exam_type is null)");die;
    $getSubject = $conn->query("select sy.Min_Marks,sy.Max_Marks, sy.Name as subject_name,sy.Paper_Type, sy.Code,sy.User_ID, sy.ID from Syllabi as sy left join Students on sy.Sub_Course_ID= Students.Sub_Course_ID AND sy.Course_ID= Students.Course_ID where Enrollment_No = '$enroll' AND sy.Semester = '$duration' $staticCondition and (sy.exam_type='Center' or sy.exam_type is null)");
    if ($getSubject->num_rows == 0) {
        $data = ['status' => 400, 'message' => 'Subject Not Uploaded of this Duration & Sub-Course.'];
    } else {
        $data = ['status' => 200];
    }
} else {
    $data = ['status' => 400, 'message' => 'Something went wrong. Please try again!'];
}

?>

<?php if ($data['status'] == 200) { ?>
    <div class="row m-t-10">
        <div class="col-md-4"><span style="font-weight:500; font-size:14px">Subject Name</span></div>
        <div class="col-md-2"><span style="font-weight:500; font-size:14px">Min/Max Marks</span></div>
        <?php 
        // print_r($_SESSION['university_id']);die;
            if( $_SESSION['university_id'] != 41){
                        ?>
                        <div class="col-md-3"><span style="font-weight:500; font-size:14px">Obtain Internal  Marks</span></div>
                        <?php
                    }
        ?>

        
        <div class="col-md-3"><span style="font-weight:500; font-size:14px">Obtain External  Marks</span></div>
    </div>
    <input type="hidden" name="enroll" value="<?= $enroll ?>">
    <div class="row mt-4">
        <?php $readonly ="";
        while ($row = $getSubject->fetch_assoc()) {
            $getmarks = $conn->query("SELECT obt_marks_int,obt_marks_ext FROM marksheets WHERE enrollment_no= '$enroll' AND subject_id = '" . $row['ID'] . "'");
            $marks = $getmarks->fetch_assoc();
            $obt_marks_int = isset($marks['obt_marks_int']) ? $marks['obt_marks_int'] : "";
            $obt_marks_ext = isset($marks['obt_marks_ext']) ? $marks['obt_marks_ext'] : "";
			$min_int_marks = ($row['Min_Marks']) * 40 / 100;
			
            if( $obt_marks_int>0 && $getmarks->num_rows > 0 && ($_SESSION['Role']=='Center' || $_SESSION['Role']=='Sub-Center')){
                $readonly = "readonly";
            }
            if( $_SESSION['university_id'] != 41){
                
                $min_ext_marks =0;
            
			   $min_ext_marks = ($row['Max_Marks']) * 40 / 100;   
			   
			   
            }
            
            ?>
            <div class="col-md-4">
                <?= $row['subject_name'].' ('.$row['Code'].')' ?>
            </div>
            <div class="col-md-2">
                <?php
                    if( $_SESSION['university_id'] == 41){
                        echo $row['Min_Marks'].'/'.$row['Max_Marks'];
                    }else{
                        echo $min_int_marks.'/'.$row['Min_Marks'];
                    }
                ?>
                
            </div>
            <div class="col-md-3">
                <div class="form-group form-group-default required">
                         <?php  if ($_SESSION['university_id'] == 41) {?>
                    <input type="text" onkeyup="markStatus(this.value, '<?= $row['Min_Marks'] ?>','<?= $row['ID'] ?>', '<?= $row['Max_Marks'] ?>')" name="obt_ext_marks[<?= $row['ID'] ?>]" value="<?= $obt_marks_ext ?>"
                        class="form-control" placeholder="Enter marks less than <?= $row['Max_Marks'] ?>"  <?=  $readonly_ext ?> required>
                        <div class="marks_status_<?= $row['ID'] ?>"></div>
                <?php } else{ ?>
                    <input type="text" onkeyup="markStatus(this.value, '<?= $min_int_marks ?>','<?= $row['ID'] ?>', '25')" name="obt_marks_int[<?= $row['ID'] ?>]" value="<?= $obt_marks_int ?>"
                        class="form-control" placeholder="Enter marks less than <?=  25 ?>" <?=  $readonly ?>  required>
                        <div class="marks_status_<?= $row['ID'] ?>"></div>
                <?php } ?>
                    <!--<input type="text" onkeyup="markStatus(this.value, '<?= $min_int_marks ?>','<?= $row['ID'] ?>', '<?= $row['Min_Marks'] ?>')" name="obt_marks_int[<?= $row['ID'] ?>]" value="<?= $obt_marks_int ?>"-->
                    <!--    class="form-control" placeholder="Enter marks less than <?= $row['Min_Marks'] ?>" <?= !empty($obt_marks_int)&&$obt_marks_int>0?"readonly":"" ?> required>-->
                        <!--<div class="marks_status_<?= $row['ID'] ?>"></div>-->
                    <input type="hidden" name="max_marks[<?= $row['ID'] ?>]" value="<?= $row['Max_Marks'] ?>" >    
                </div>
            </div>
            <?php if((trim($row['Paper_Type'])==="Practical" || trim($row['Paper_Type'])==="Project") && $_SESSION['university_id'] != 41){   $readonly ="";?>
            <div class="col-md-3">
                <div class="form-group form-group-default required">
                    
                    <input type="text"   name="obt_marks_ext[<?= $row['ID'] ?>]" value="<?= $obt_marks_ext ?>"
                        class="form-control " placeholder=" <?= "Enter marks less than ".$row['Max_Marks']  ; ?>" onkeyup="extmarkStatus(this.value, '<?= $row['Min_Marks'] ?>','<?= $row['ID'] ?>', '<?= $row['Max_Marks'] ?>')" <?= !empty($obt_marks_ext)&&$obt_marks_ext>0?"readonly":"" ?> required>
                        <div class="ext_marks_status_<?= $row['ID'] ?>"></div>
                        <input type="hidden" name="ext_max_marks[<?= $row['ID'] ?>]"  value="<?= $row['Min_Marks']+$row['Max_Marks'] ?>">    
                  
                </div>
            </div>
        <?php } } ?>
    </div>
<?php } else { ?>
    <div class="row mt-2">
        <div class="col-md-12">
            <p style="color:red; text-align:center"><?= $data['message'] ?></p>
        </div>
    </div>
<?php } ?>