<?php
  if(isset($_GET['university_id']) && isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
    $university_id = intval($_GET['university_id']);

    // Reporting
    $reportingQuery = "";
    if(isset($_GET['reporting'])){
      $reporting = intval($_GET['reporting']);
      // Department to Show
      $reportingDepartments = array();
      $allotedDepartments = $conn->query("SELECT Department_ID FROM User_Departments WHERE User_ID = $reporting AND University_ID = $university_id");
      if($allotedDepartments->num_rows>0){
        while($allotedDepartment = $allotedDepartments->fetch_assoc()){
          $reportingDepartments[] = $allotedDepartment['Department_ID'];
        }
        $reportingQuery = " AND ID IN (".implode(",", $reportingDepartments).")";
      }
    }

    $departments_array = [];
    $alloted_departments = $conn->query("SELECT Department_ID FROM User_Departments WHERE `User_ID` = $id AND University_ID = $university_id");
    while($alloted_department = $alloted_departments->fetch_assoc()){
      $departments_array[] = $alloted_department['Department_ID'];
    }
?>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default form-group-default-select2 required">
          <label class="" style="z-index:9999">Deapartments</label>
          <select class="full-width" id="department" name="department[]" onchange="getVocationalCourse()" multiple>
            <?php
              $departments = $conn->query("SELECT ID, Name FROM Departments WHERE University_ID = $university_id $reportingQuery ORDER BY Name ASC");
              while ($department = $departments->fetch_assoc()){ ?>
                <option value="<?=$department['ID']?>" <?php echo in_array($department['ID'], $departments_array) ? 'selected' : '' ?>><?=$department['Name']?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="form-group form-group-default form-group-default-select2 required">
          <label class="" style="z-index:9999">Admission Sessions</label>
          <select class="full-width" id="admission_session" name="admission_session[]" onchange="getVocationalCourse()" multiple>
            <?php
              $admission_sessions = $conn->query("SELECT ID, Name, Current_Status FROM Admission_Sessions WHERE University_ID = $university_id ORDER BY ID DESC");
              while ($admission_session = $admission_sessions->fetch_assoc()){ ?>
                <option value="<?=$admission_session['ID']?>" <?php echo $admission_session['Current_Status'] ? 'selected' : ''?>><?=$admission_session['Name']?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>

    <div id="vocational_course" style="
    height: 400px !important;
    overflow-y: auto;
">

    </div>

    <script type="text/javascript">
      $('#department').select2({
        placeholder: "Choose",
        allowClear: true
      });
      
      $("#admission_session").select2({
        placeholder: "Choose",
        allowClear: true
      })
    </script>

    <script type="text/javascript">
      function getVocationalCourse(){
        var department_ids = $('#department').val();
        var sessions = $('#admission_session').val();
        var reporting = '<?php echo isset($_GET['reporting']) ? '&reporting='.$reporting : '' ?>';
        $.ajax({
          url: '/app/university-allotment/vocational-courses?ids='+department_ids+'&session='+sessions+'&university_id=<?=$university_id?>&user_id=<?=$id?>'+reporting,
          type:'GET',
          success: function(data) {
            $('#vocational_course').html(data);
          }
        })
      }

      getVocationalCourse();
    </script>


<?php } ?>
