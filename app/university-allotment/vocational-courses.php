<?php
  if(isset($_GET['ids']) && isset($_GET['user_id']) && isset($_GET['university_id']) && isset($_GET['session'])){
    require '../../includes/db-config.php';
    
    $user_id = intval($_GET['user_id']);
    $university_id = intval($_GET['university_id']);
    $department_ids = mysqli_real_escape_string($conn, $_GET['ids']);
    $session_ids = mysqli_real_escape_string($conn, $_GET['session']);

$roleQuery = $conn->query("select Role,CanCreateSubCenter from Users where ID=$user_id");
    $role = $roleQuery->fetch_assoc();
    
    if(empty($department_ids) || empty($session_ids)){
      exit();
    }

    // Reporting
    $reportingQuery = "";
    $reportingSubCourses = array();
    if(isset($_GET['reporting'])){
      $reporting = intval($_GET['reporting']);
      $alloted_fees = $conn->query("SELECT Fee, Sub_Course_ID, Admission_Session_ID, Scheme_ID FROM User_Sub_Courses WHERE `User_ID` = $reporting AND `University_ID` = $university_id");
      while($alloted_fee = $alloted_fees->fetch_assoc()){
        $reportingSubCourses[$alloted_fee['Sub_Course_ID']][$alloted_fee['Admission_Session_ID']][$alloted_fee['Scheme_ID']] = json_decode($alloted_fee['Fee'], true);
      } 
      
      $reportingQuery = " AND Sub_Courses.ID IN (".implode(",", array_keys($reportingSubCourses)).")";
    }

    $fees = [];
    $alloted_fees = $conn->query("SELECT Fee, Sub_Course_ID, Admission_Session_ID, Scheme_ID FROM User_Sub_Courses WHERE `User_ID` = $user_id AND `University_ID` = $university_id");
    while($alloted_fee = $alloted_fees->fetch_assoc()){
      $fees[$alloted_fee['Sub_Course_ID']][$alloted_fee['Admission_Session_ID']][$alloted_fee['Scheme_ID']] = json_decode($alloted_fee['Fee'], true);
    }

    // Admission Sessions
    $allotedSchemes = array();
    $sessions = array();
    $admission_sessions = $conn->query("SELECT ID, Name, Scheme FROM Admission_Sessions WHERE ID IN ($session_ids)");
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
              <th>Course</th>
              <?php
                foreach($sessions as $session_id=>$session){
                  foreach($allotedSchemes[$session_id] as $allotedScheme){
                    echo '<th class="text-center">'.$session.' ('. $schemesNames[$allotedScheme].')</th>';
                  }
                }
              ?>
            </tr> 
          </thead>
          <tbody>
            <?php
    print_r($role);die;
            if($role['Role']=='Counsellor')
                {
                    $fee_column = "Sub_Courses.counsellor_fee as paybleFee";
                }elseif($role['Role']=='Center')
                {
                    if($role['CanCreateSubCenter']==1)
                    {
                        $fee_column = "Sub_Courses.coordinator_fee as paybleFee";
                    }else
                    {
                        $fee_column = "Sub_Courses.center_fee as paybleFee";
                    }
                }elseif($role['Role']=='Sub-Center')
                {
                    $fee_column = "Sub_Courses.sub_center_fee as paybleFee";
                }
                else
                {
                     $fee_column = "Sub_Courses.center_fee as paybleFee";
                }
              $sub_courses = $conn->query("SELECT $fee_column,Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') AS Name FROM Sub_Courses LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE Sub_Courses.Department_ID IN ($department_ids) $reportingQuery");
              while($sub_course = $sub_courses->fetch_assoc()){ ?>
                <tr>
                  <td>
                    <div class="form-check m-t-10">
                      <input type="checkbox" id="sub_course_<?=$sub_course['ID']?>" value="<?=$sub_course['ID']?>" <?php echo !empty($fees) && in_array($sub_course['ID'], array_keys($fees)) ? 'checked' : '' ?> name="sub_course[]">
                      <label for="sub_course_<?=$sub_course['ID']?>"><?=$sub_course['Name']?></label>
                    </div>
                  </td>
                  <?php
                    foreach($sessions as $session_id=>$session){
                      foreach($allotedSchemes[$session_id] as $allotedScheme){ 
                        $feeHeadIds = array_intersect($variableStructureIds, $schemeFeeStructures[$allotedScheme]);
                        ?>
                        <td>
                          <div class="d-flex justify-content-start">
                          <?php
                            $width = round(100/count($feeHeadIds));
                            foreach($feeHeadIds as $feeHeadId){ ?>
                              <div class="m-r-10" style="width: <?=$width?>%">
                                <!--<input type="number" class="form-control" min="<?php echo !empty($reportingSubCourses) && in_array($sub_course['ID'], array_keys($reportingSubCourses)) && in_array($session_id, array_keys($reportingSubCourses[$sub_course['ID']])) && in_array($allotedScheme, array_keys($reportingSubCourses[$sub_course['ID']][$session_id])) && in_array($feeHeadId, array_keys($reportingSubCourses[$sub_course['ID']][$session_id][$allotedScheme])) ? $reportingSubCourses[$sub_course['ID']][$session_id][$allotedScheme][$feeHeadId] : '' ?>" placeholder="<?=$structures[$feeHeadId]?>" name="fee[<?=$sub_course['ID']?>][<?=$session_id?>][<?=$allotedScheme?>][<?=$feeHeadId?>]" value="<?php echo !empty($fees) && in_array($sub_course['ID'], array_keys($fees)) && in_array($session_id, array_keys($fees[$sub_course['ID']])) && in_array($allotedScheme, array_keys($fees[$sub_course['ID']][$session_id])) && in_array($feeHeadId, array_keys($fees[$sub_course['ID']][$session_id][$allotedScheme])) ? $fees[$sub_course['ID']][$session_id][$allotedScheme][$feeHeadId] : '' ?>">-->
                                <input type="number" class="form-control" min="<?php echo $sub_course['paybleFee']>0?$sub_course['paybleFee'] : 0 ?>" placeholder="<?=$structures[$feeHeadId]?>" name="fee[<?=$sub_course['ID']?>][<?=$session_id?>][<?=$allotedScheme?>][<?=$feeHeadId?>]" value="<?php echo $sub_course['paybleFee'] ?>" >
                              </div>
                            <?php }
                          ?>
                          </div>
                        </td>
                      <?php }
                    }
                  ?>
                </tr>
              <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php }
