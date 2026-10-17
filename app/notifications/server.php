<?php
$query = '';
$subquery = '';
if(isset($_GET['heading']) || isset($_GET['send_to'])){
 
  ## Database configuration
  include '../../includes/db-config.php';
  session_start();

  $heading = $_GET['heading'] ? $_GET['heading'] : '';
  $send_to = $_GET['send_to'] ? $_GET['send_to'] : '';


  if($heading != ''){
    $query = "WHERE  Heading =  '".$heading."'";
    if($send_to != ''){  
      $subquery = "AND Send_To = '".$send_to."'";
    }
  }

  if($heading == '' && $send_to != ''){
    $subquery = "WHERE Send_To = '".$send_to."'";
  }
}
?>
<div class="col-md-12">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Regarding</th>
            <th>Sent To</th>
            <th>Noticefication Sent On</th>
            <th>Attachment</th>
            <th>Content</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $result_record = $conn->query("SELECT * FROM Notifications_Generated $query $subquery");
          $data = array();
          if($result_record->num_rows ==0){
            echo "<tr><td colspan='5' style='text-align:center'>No Notification Found.</td></tr>";
            return;
          }
          while ($row = $result_record->fetch_assoc()) { ?>
            <tr>
              <td><?=ucfirst(strtolower($row['Heading']))?></td>
              <td><?=ucfirst(strtolower($row['Send_To']))?></td>
              <td><?=$row['Noticefication_Created_on']?></td>
              <td>
                  <?php
                    if(!empty($row['Attachment'])){
                            ?>
                            <a href="<?=$row['Attachment']?>" target="_blank" download="<?= $row['Heading'] ?>">Download</a>
                            <?php
                    }else{
                        echo "No Attachment";
                    }
                  ?>
              </td>
              <td><a type="btn" onclick="view_content('<?=$row['ID']?>');"><i class="uil uil-eye"></i></a></td>
            </tr>
            <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
<script type="text/javascript">
  function view_content(id) {
    $.ajax({
      url: '/app/notifications/contents?id=' + id,
      type: 'GET',
      success: function(data) {
        $("#md-modal-content").html(data);
        $("#mdmodal").modal('show');
      }
    })
  }
</script>