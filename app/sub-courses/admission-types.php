<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  if(isset($_GET['university_id']) && isset($_GET['min_duration'])){
    require '../../includes/db-config.php';
    $university_id = intval($_GET['university_id']);
    $duration = intval($_GET['min_duration']);

    if(empty($university_id) || empty($duration)){
      exit;
    }

    $types = array();
    $selectedEligibility = array();
    if(isset($_GET['id'])){
      $id = intval($_GET['id']);
      $admissionTypes = $conn->query("SELECT Admission_Type, Eligibility FROM Sub_Courses WHERE ID = $id");
      $admissionType = $admissionTypes->fetch_assoc();
      $types = json_decode($admissionType['Admission_Type'], true);
      $selectedEligibility = json_decode($admissionType['Eligibility'], true);
    }

    $eligibilities = array("High School", "Intermediate", "UG", "PG", "Other");

    $admission_types = $conn->query("SELECT ID, Name FROM Admission_Types WHERE University_ID = $university_id");
    if($admission_types->num_rows==0){ ?>
      <div class="row">
        <div class="col-md-12">
          <center><h4>Please create Admission Type!</h4></center>
        </div>
      </div>
    <?php }else{
      while($admission_type = $admission_types->fetch_assoc()){ ?>
        <center><h6>Admission Type(s)</h6></center>
        <div class="row m-t-20">
          <div class="col-md-12">
            <div class="form-check m-t-20">
              <input type="checkbox" id="admission_type_<?=$admission_type['ID']?>" value="<?=$admission_type['ID']?>" <?php echo !empty($types) && array_key_exists($admission_type['ID'], $types) ? 'checked' : '' ?> name="admission_types[]">
              <label for="admission_type_<?=$admission_type['ID']?>"><?=$admission_type['Name']?></label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group form-group-default required">
              <label>Admission Duration</label>
              <select class="full-width" style="border: transparent;" multiple id="admission_type_duration_<?=$admission_type['ID']?>" name="admission_type_duration[<?=$admission_type['ID']?>][]">
                <?php for($i = 1; $i<=$duration; $i++){ ?>
                  <option value="<?=$i?>" <?php echo !empty($types) && array_key_exists($admission_type['ID'], $types) && in_array($i, $types[$admission_type['ID']]) ? 'selected' : '' ?>><?=$i?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group form-group-default form-group-default-select2 required">
              <label style="z-index:9999">Required Academic(s)</label>
              <select class=" full-width" data-init-plugin="select2" id="eligibilities_required_<?=$admission_type['ID']?>" name="eligibilities[<?=$admission_type['ID']?>][required][]" onchange="disableOptions('eligibilities_optional_<?=$admission_type['ID']?>', this.value)" multiple>
                <?php foreach($eligibilities as $eligibility){ ?>
                  <option value="<?=$eligibility?>" <?php echo !empty($selectedEligibility) && array_key_exists($admission_type['ID'], $selectedEligibility) && in_array($eligibility, $selectedEligibility[$admission_type['ID']]['required']) ? 'selected' : '' ?>><?=$eligibility?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group form-group-default form-group-default-select2">
              <label style="z-index:9999">Optional Academic(s)</label>
              <select class=" full-width" data-init-plugin="select2" id="eligibilities_optional_<?=$admission_type['ID']?>" name="eligibilities[<?=$admission_type['ID']?>][optional][]" multiple>
                <?php foreach($eligibilities as $eligibility){ ?>
                  <option value="<?=$eligibility?>" <?php echo !empty($selectedEligibility) && array_key_exists($admission_type['ID'], $selectedEligibility) && in_array($eligibility, $selectedEligibility[$admission_type['ID']]['optional']??[]) ? 'selected' : '' ?>><?=$eligibility?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>

        <script>
          $(function(){
            $("#eligibilities_required_<?=$admission_type['ID']?>").select2({
              placeholder: 'Choose',
            });

            $("#eligibilities_optional_<?=$admission_type['ID']?>").select2({
              placeholder: 'Choose',
            });

            $("#admission_type_duration_<?=$admission_type['ID']?>").select2({
              placeholder: 'Choose',
            });
          })
        </script>
      <?php }
    }
  }
