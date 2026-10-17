<?php 
  ini_set('display_errors', 1);
  if(isset($_GET['syllabus_id']) && isset($_GET['id']) && isset($_GET['date_sheet'])){
    require '../../includes/db-config.php';
    session_start();

    if($_SESSION['Exam']==0){
      echo '<center><h3>Please contact your Co-ordinator.</h3></center>';
      exit;
    }

    $date_sheet_id = intval($_GET['date_sheet']);
    $syllabus_id = intval($_GET['syllabus_id']);
    $student_id = intval($_GET['id']);

    if(empty($date_sheet_id) || empty($syllabus_id) || empty($student_id)){
      echo '<center><h3>Please contact your Co-ordinator.</h3></center>';
      exit;
    }

    $conn->query("INSERT INTO Exam_Attempts (Student_ID, Date_Sheet_ID, Start_Time, End_Time) VALUES ($student_id, $date_sheet_id, now(), now())");

    $date_sheet = $conn->query("SELECT * FROM Date_Sheets WHERE ID = $date_sheet_id");
    $date_sheet = $date_sheet->fetch_assoc();

    if(strtotime($date_sheet['End_Time']) < strtotime(date('H:i'))){
      echo '<center><h3>Exam Over.</h3></center>';
      exit;
    }

    $check = $conn->query("SELECT ID FROM Students_Answers WHERE Student_ID = $student_id AND Date_Sheet_ID = $date_sheet_id AND Syllabus_ID = $syllabus_id");
    if($check->num_rows==35){
      echo '<center><h3>You have successfully submitted your answers.</h3></center>';
      exit;
    }

    $questions = $conn->query("SELECT * FROM MCQs WHERE Syllabus_ID = $syllabus_id ORDER BY Question_No ASC");
    if($questions->num_rows==0){
      echo '<center><h3>Please contact your Co-ordinator.</h3></center>';
      exit;
    }else{
  ?>
    <div class="row col-md-12">
      <center><h6>Note: Please do not refresh the page!</h6></center>
    </div>
    <form role="form" id="exam-form" action="/app/exams/answers" method="post" enctype="multipart/form-data">
      <div class="row">
        <div class="col-md-9">
          <div class="card">
            <div class="card-body">
              <?php while($question = $questions->fetch_assoc()){
                $selected = "";
                $options = json_decode($question['Options'], true);
              ?>
                <div class="row m-t-20">
                  <div class="col-md-12 d-flex justify-content-between">
                    <div>
                      <p class="fs-14 font-weight-bold"><?=$question['Question_No'].'.&nbsp;&nbsp;&nbsp;&nbsp;'.$question['Question']?></p>
                    </div>
                    <div>
                      <b>(Marks: <?=$question['Marks']?>)</b>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <?php foreach($options as $key=>$value){ ?>
                      <div class="form-check">
                        <input type="radio" onclick="updateOverview();" name="answer[<?=$question['ID']?>]" id="option_<?=$question['ID'].'_'.$key?>" value="<?=$value?>" <?php $value==$selected ? 'checked' : '' ?>>
                        <label for="option_<?=$question['ID'].'_'.$key?>">
                          <?=$value?>
                        </label>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              <?php
              }
              ?>
            </div>
          </div>
        </div>  
        <div class="col-md-3">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header seperator">
                  <h6>Overview</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-borderless">
                      <tr>
                        <th width="60%">Final Submission Time</th>
                        <th width="2%">:</th>
                        <th><?=date('h:i A', strtotime($date_sheet['End_Time']))?></th>
                      </tr>
                      <tr>
                        <th width="60%">Total Questions</th>
                        <th width="2%">:</th>
                        <th><?=$questions->num_rows?></th>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <button type="submit" id="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </div>  
      </div>
    </form>

    <script type="text/javascript">
        $("#exam-form").on("submit", function(e){
          if($('#exam-form').valid()){
            $(':input[type="submit"]').prop('disabled', true);
            var formData = new FormData(this);
            formData.append('student_id', <?=$student_id?>);
            formData.append('date_sheet_id', <?=$date_sheet_id?>);
            formData.append('syllabus_id', <?=$syllabus_id?>);
            $.ajax({
              url: this.action,
              type: 'post',
              data: formData,
              cache:false,
              contentType: false,
              processData: false,
              dataType: "json",
              success: function(data) {
                if(data.status==200){
                  $('.modal').modal('hide');
                  notification('success', data.message);
                  $(':input[type="submit"]').prop('disabled', false);
                }else{
                  $(':input[type="submit"]').prop('disabled', false);
                  notification('danger', data.message);
                }
              }
            });
            e.preventDefault();
          }
        });

        function updateOverview(){
          $('#submit').click();
        }
    </script>
  <?php }
  }