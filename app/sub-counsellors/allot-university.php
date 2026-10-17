<?php
  if(isset($_GET['id'])){
    require '../../includes/db-config.php';
    session_start();
    $id = intval($_GET['id']);
    $alloted = array();
    $alloted_universities = $conn->query("SELECT University_ID FROM University_User WHERE `User_ID` = $id");
    while($alloted_university = $alloted_universities->fetch_assoc()){
      $alloted[] = $alloted_university['University_ID'];
    }
?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left mb-4">
    <h5>Allot <span class="semi-bold"></span>Vertical(s)</h5>
  </div>
  <div class="modal-body">

    <?php if(!empty($alloted)){ 
      print !empty($alloted) ? '<dt class="text-success mb-2">Alloted</dt>' : '' ?>
      <div class="row">
        <?php
          $alloted_query = !empty($alloted) ? " WHERE ID IN (".implode(',', $alloted).")" : "";
          $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name, Logo FROM Universities $alloted_query");
          while($university = $universities->fetch_assoc()){ ?>
            <div class="col-md-3 cursor-pointer" onclick="step2('<?=$id?>', '<?=$university['ID']?>', '<?=$university['Name']?>');">
              <div class="card">
                <div class="card-body">
                  <center>
                    <img src="<?=$university['Logo']?>" alt="logo" data-src="<?=$university['Logo']?>" data-src-retina="<?=$university['Logo']?>" height="70px">
                    <p class="bold mt-2"><?=$university['Name']?></p>
                  </center>
                </div>
              </div>
            </div>
        <?php }
        ?>
      </div>
    <?php } ?>


    <?php $not_alloted_query = !empty($alloted) ? " WHERE ID NOT IN (".implode(',', $alloted).")" : "";
      $universities = $conn->query("SELECT ID, CONCAT(Universities.Short_Name, ' (', Universities.Vertical, ')') as Name, Logo FROM Universities $not_alloted_query");
      if($universities->num_rows>0){ ?>
        <dt class="text-primary <?php print !empty($alloted) ? 'mt-4' : '' ?> mb-2">Not Alloted</dt>
        <div class="row">
          <?php
            while($university = $universities->fetch_assoc()){ ?>
              <div class="col-md-3 cursor-pointer" onclick="step2('<?=$id?>', '<?=$university['ID']?>', '<?=$university['Name']?>');">
                <div class="card">
                  <div class="card-body">
                    <center>
                      <img src="<?=$university['Logo']?>" alt="logo" data-src="<?=$university['Logo']?>" data-src-retina="<?=$university['Logo']?>" height="70px">
                      <p class="bold mt-2"><?=$university['Name']?></p>
                    </center>
                  </div>
                </div>
              </div>
          <?php } ?>
        </div>  
    <?php } ?>
  </div>

  <script>
    function step2(id, university_id, name){
      var modal = 'full';
      $.ajax({
        url:'/app/sub-counsellors/step-2',
        type:'POST',
        data:{id:id, university_id:university_id, name:name},
        success: function(data) {
          $('#'+modal+'-modal-content').html(data);
        }
      })
    }

    <?php if($_SESSION['Role']!='Administrator'){ ?> step2('<?=$id?>', '<?=$_SESSION["university_id"]?>', '<?=$_SESSION["university_name"]?>') <?php } ?>
  </script>
<?php } ?>
