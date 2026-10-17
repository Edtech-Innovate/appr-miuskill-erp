<!-- Modal -->
<div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5><span class="semi-bold"></span>Notification</h5>
</div>
  <div class="modal-body">
    <div class="row">
    <?php
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            ## Database configuration
            include '../../includes/db-config.php';
            session_start();
            $content = $conn->query("SELECT * FROM Notifications_Generated WHERE ID = $id");

        }
        while ($row = $content->fetch_assoc()) {
            echo "<p>".nl2br("$row[Content]")."</p>";
        }
    ?>

    </div>
  </div>
