<?php
  ini_set('display_errors', 1);
  if(isset($_GET['university_id']) && isset($_GET['id'])){
    require '../../includes/db-config.php';
    $id = intval($_GET['id']);
    $university_id = intval($_GET['university_id']);

    // Mode
    $available_modes = array();
    $modes = $conn->query("SELECT Name FROM Modes WHERE University_ID = $university_id");
    while($mode = $modes->fetch_assoc()){
      $available_modes[] = $mode['Name'];
    }
    $mode = implode('/', $available_modes);

    // Duration
    $duration = $conn->query("SELECT MAX(Min_Duration) as Duration FROM Sub_Courses WHERE University_ID = $university_id");
    $duration = mysqli_fetch_assoc($duration);
    $durations = $duration['Duration'];
    
    if(empty($university_id) || empty($mode) || empty($durations)){
      exit();
    }

    // Check Has Course Allotment
    $course_allotment = 0;
    $has_course_allotment = $conn->query("SELECT ID FROM Universities WHERE ID = $university_id AND Course_Allotment = 1");
    if($has_course_allotment->num_rows > 0){
      $course_allotment = 1;
    }

    // Reporting
    $reportingQuery = "";
    $allotedReportingFee = array();
    if(isset($_GET['reporting'])){
      $reporting = intval($_GET['reporting']);
      // Department to Show
      $reportingDepartments = array();
      $allotedDepartments = $conn->query("SELECT Department_ID FROM User_Departments WHERE User_ID = $reporting AND University_ID = $university_id");
      if($allotedDepartments->num_rows > 0){
        while($allotedDepartment = $allotedDepartments->fetch_assoc()){
          $reportingDepartments[] = $allotedDepartment['Department_ID'];
        }
        $reportingQuery = " AND ID IN (".implode(",", $reportingDepartments).")";
      }else{
        $reportingQuery = "";
      }

      // Reporting Fee
      $reportingFees = $conn->query("SELECT Fee, Fee_Structure_ID, Admission_Session_ID, Scheme_ID FROM Fee_Variables WHERE Code = $reporting AND University_ID = $university_id");
      if($reportingFees->num_rows > 0){
        while($reportingFee = $reportingFees->fetch_assoc()){
          $allotedReportingFee[$reportingFee['Admission_Session_ID']][$reportingFee['Scheme_ID']][$reportingFee['Fee_Structure_ID']] = $reportingFee['Fee'];
        }
      }
    }

    $allotedFee = [];
    $alloted_fees = $conn->query("SELECT Fee, Admission_Session_ID, Scheme_ID FROM Fee_Variables WHERE Code = $id AND University_ID = $university_id");
    if($alloted_fees->num_rows > 0){
      while($alloted_fee = $alloted_fees->fetch_assoc()){
        $allotedFee[$alloted_fee['Admission_Session_ID']][$alloted_fee['Scheme_ID']] = json_decode($alloted_fee['Fee'], true);
      }
    }

    // Admission Sessions
    $allotedSchemes = array();
    $sessions = array();
    $admission_sessions = $conn->query("SELECT ID, Name, Scheme FROM Admission_Sessions WHERE University_ID = $university_id");
    while($admission_session = $admission_sessions->fetch_assoc()){
      $sessions[$admission_session['ID']] = $admission_session['Name'];
      $sessionSchemes = json_decode($admission_session['Scheme'], true);
      $allotedSchemes[$admission_session['ID']] = $sessionSchemes['schemes'];
    }

    // Schemes
    $schemesNames = array();
    $schemeFeeStructures = array();
    $schemes = $conn->query("SELECT ID, Name, Fee_Structure FROM Schemes WHERE University_ID = $university_id");
    while($scheme = $schemes->fetch_assoc()){
      $schemesNames[$scheme['ID']] = $scheme['Name'];
      $schemeFeeStructures[$scheme['ID']] = json_decode($scheme['Fee_Structure'], true);
    }

    // Sharing Fee
    $structures = array();
    $variableStructureIds = array();
    $sharingFeeStructures = array();
    $fee_structures = $conn->query("SELECT ID, Name, Sharing FROM Fee_Structures WHERE (Sharing = 1 OR Is_Constant = 0) AND University_ID = $university_id");
    while($fee_structure = $fee_structures->fetch_assoc()){
      $structures[$fee_structure['ID']] = $fee_structure['Name'];
      $variableStructureIds[] = $fee_structure['ID'];
      if($fee_structure['Sharing']){
        $sharingFeeStructures[] = $fee_structure['ID'];
      }
    }
  ?>
  <div class="row m-t-10 p-b-2">
    <div class="col-md-12">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Admission Sessions</th>
              <th class="text-center"></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach($sessions as $session_id=>$session){
                foreach($allotedSchemes[$session_id] as $allotedScheme){
                  $feeHeadIds = array_intersect($variableStructureIds, $schemeFeeStructures[$allotedScheme]);
                  ?>
                  <tr>
                    <td><?=$session.' ('.$schemesNames[$allotedScheme].')'?></td>
                    <td>
                      <div class="d-flex justify-content-start">
                        <?php
                          $width = round(100/count($feeHeadIds));
                          foreach($feeHeadIds as $feeHeadId){ 
                            $placeholder = in_array($feeHeadId, $sharingFeeStructures) ? $structures[$feeHeadId]." Share (%)" : $structures[$feeHeadId];
                            $max =  !empty($allotedReportingFee) && in_array($session_id, array_keys($allotedReportingFee)) && in_array($allotedScheme, array_keys($allotedReportingFee[$session_id])) && in_array($feeHeadId, array_keys($allotedReportingFee[$session_id][$allotedScheme])) ? 'max="'.$allotedReportingFee[$session_id][$allotedScheme][$feeHeadId].'"' : (in_array($feeHeadId, $sharingFeeStructures) ? 'max="100"' : '');
                            $value = !empty($allotedFee) && in_array($session_id, array_keys($allotedFee)) && in_array($allotedScheme, array_keys($allotedFee[$session_id])) && in_array($feeHeadId, array_keys($allotedFee[$session_id][$allotedScheme])) ? $allotedFee[$session_id][$allotedScheme][$feeHeadId] : "";
                            ?>
                            <div class="m-r-10" style="width: <?=$width?>%">
                              <input type="number" class="form-control" min="0" <?=$max?> value="<?=$value?>" placeholder="<?=$placeholder?>" name="fee[<?=$session_id?>][<?=$allotedScheme?>][<?=$feeHeadId?>]">
                            </div>
                          <?php }
                        ?>
                      </div>
                    </td>
                  </tr>
                  <?php
                    if($course_allotment){
                      $departments_array = array();
                      $alloted_departments = $conn->query("SELECT Department_ID FROM User_Departments WHERE `User_ID` = $id AND University_ID = $university_id");
                      while($alloted_department = $alloted_departments->fetch_assoc()){
                        $departments_array[] = $alloted_department['Department_ID'];
                      }
                    ?>
                      <tr><td colspan="2">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group form-group-default form-group-default-select2 required">
                              <label class="" style="z-index:9999">Deapartments</label>
                              <select class="full-width" id="department_<?=$session_id.$allotedScheme?>" name="department[]" onchange="getCourses()" multiple>
                                <?php
                                  $departments = $conn->query("SELECT ID, Name FROM Departments WHERE University_ID = $university_id $reportingQuery ORDER BY Name ASC");
                                  while ($department = $departments->fetch_assoc()){ ?>
                                    <option value="<?=$department['ID']?>" <?php echo in_array($department['ID'], $departments_array) ? 'selected' : '' ?>><?=$department['Name']?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group form-group-default form-group-default-select2 required">
                              <label class="" style="z-index:9999">Courses</label>
                              <select class="full-width" id="course_<?=$session_id.$allotedScheme?>" name="courses[]" multiple>
                                
                              </select>
                            </div>
                          </div>
                        </div>
                    
                        <script>
                          $(function(){
                            $("#department_<?=$session_id.$allotedScheme?>").select2({
                              placeholder:'Choose',
                              allowClear: true
                            });
                    
                            $("#course_<?=$session_id.$allotedScheme?>").select2({
                              placeholder:'Choose',
                              allowClear: true
                            })
                          })
                          function getCourses(){
                            var department = $("#department_<?=$session_id.$allotedScheme?>").val();
                            var reporting = '<?php echo isset($_GET['reporting']) ? '&reporting='.$reporting : '' ?>';
                            $.ajax({
                              url:'/app/university-allotment/department-courses?department='+department+'&university=<?=$university_id?>&user_id=<?=$id?>'+reporting,
                              type:'GET',
                              success: function(data){
                                $("#course_<?=$session_id.$allotedScheme?>").html(data);
                              }
                            })
                          }
                    
                          getCourses();
                        </script>
                      </td></tr>
                    <?php }
                  ?>
                <?php }
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php
  }
