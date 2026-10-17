<?php
  if(isset($_POST['name']) && isset($_POST['department']) && isset($_POST['id'])){
    require '../../filestobeincluded/db_config.php';
    session_start();
    $id = str_replace("UZJkrI5snMyURJgpMWbM", "", base64_decode($_POST['id']));
    $lead_phone = $conn->query("SELECT Mobile, Alternate_Mobile FROM Lead_Status LEFT JOIN Leads ON Lead_Status.Lead_ID = Leads.ID WHERE Lead_Status.ID = $id");
    $lead_phone = $lead_phone->fetch_row();
    $phone_numbers = array_filter($lead_phone);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $department = intval($_POST['department']);
?>

<div class="modal-header">
  <h5 class="modal-title" id="myCenterModalLabel">Send WhatsApp to <?=$name?></h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
  <div class="modal-body">
  
    <div class="form-group row pb-2">
      <div class="col-lg-12">
        <select onchange="getTemplate(this.value)" id="templates" class="form-control">
          <option value="">Select</option>
          
        </select>
      </div>
    </div>

    <div class="form-group row pt-2 pb-2">
      <div class="col-lg-12">
        <?php for($i=0; $i < count($phone_numbers); $i++) { ?>
          <div class="form-check mb-1">
            <input type="radio" <?php print $i==0 ? "checked" : "" ?> value="<?php echo $phone_numbers[$i]?>" id="phone-<?=$phone_numbers[$i]?>" name="phone" class="form-check-input">
            <label class="form-check-label" for="phone-<?=$phone_numbers[$i]?>"><?=$phone_numbers[$i]?></label>
          </div>
        <?php } ?>
      </div>
    </div>

    <div class="form-group row pb-2">
      <div class="col-lg-12">
        <textarea id="template" name="template" readonly rows="10" placeholder="Template" class="form-control"></textarea>
      </div>
    </div>

  </div>
  <div class="modal-footer">
    <button type="button" onclick="sendWhatsApp()" class="btn btn-primary">Send</button>
  </div>

  <script>
    $(function(){
      $("#whatsapp_form").on("submit", function(e){
        var formData = new FormData(this);
        $.ajax({
            url: this.action,
            type: 'post',
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
              if(data.status==200){
                $('.modal').modal('hide');
                toastr.success(data.message);
                $('#whatsapp-table').DataTable().ajax.reload(null, false);;
              }else{
                toastr.error(data.message);
              }
            }
        });
        e.preventDefault();
      });
    });

    function getTemplates(id){
      $.ajax({
        url: 'ajax_admin/ajax_select/department_whatsapp?id='+id,
        type:'GET',
        success: function(data) {
          $('#templates').html(data);
        }
      })
    }

    getTemplates(<?=$department?>);

    function getTemplate(id){
      var lead_id = '<?=$id?>';
      var department = '<?=$department?>';
      $.ajax({
        url: 'ajax_admin/ajax_whatsapp/template?id='+id+'&lead_id='+lead_id+'&department='+department,
        type:'GET',
        success: function(data) {
          $('#template').val(data);
        }
      })
    }
  </script>

  <script>
    function sendWhatsApp(){
      var phone = $('input[name=phone]:checked').val();
      var template = $('#template').val();
      if(template.length==0){
        toastr.warning('Please select a template!');
      }else{
        const new_template = encodeURI(template);
        window.open('https://wa.me/'+phone+'?text='+new_template);
      }
    }
  </script>
<?php } ?>
