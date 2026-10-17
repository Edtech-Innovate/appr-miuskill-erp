<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<div class="page-container ">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
  <!-- START PAGE CONTENT WRAPPER -->
  <div class="page-content-wrapper ">
    <!-- START PAGE CONTENT -->
    <div class="content ">
      <!-- START JUMBOTRON -->
      <div class="jumbotron" data-pages="parallax">
        <div class=" container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb">
              <li class="breadcrumb-item active">Generate Lead</li>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>
      <!-- END JUMBOTRON -->
      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid">
        <!-- BEGIN PlACE PAGE CONTENT HERE -->
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body" id="replaceable">
                <form action="/app/leads/store-generate" id="lead-form">
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group form-group-default required">
                        <label>Owner</label>
                        <select class="full-width" style="border: transparent;" name="user" id="user" onchange="getCourse()" required>
                          <option value="">Choose</option>
                          <?php
                            $role_query = str_replace("{{ table }}", "Users", $_SESSION['RoleQuery']);
                            $role_query = str_replace("{{ column }}", "ID", $role_query);

                            $centers = $conn->query("SELECT ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Users WHERE Role NOT IN ('Administrator','Accountant','Operations','University Head','Academic Head') $role_query");
                            while ($center = $centers->fetch_assoc()) {
                              echo '<option value="' . $center['ID'] . '">' . $center['Name'] . '</option>';
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group form-group-default required">
                        <label>Full Name</label>
                        <input type="text" style="text-transform: uppercase" class="form-control" autocomplete="off" name="name" placeholder="Jhon Doe" required />
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group form-group-default required">
                        <label>Email</label>
                        <input type="email" class="form-control" style="text-transform: lowercase" autocomplete="off" id="email" name="email" placeholder="jhon@example.com" onkeyup="checkLeadEmail(this.value, 'leadEmailError')" required>
                      </div>
                      <p class="text-danger font-weight-bold" id="leadEmailError"></p>
                    </div>
                    <div class="col-lg-6">
                      <div class="form-group form-group-default required">
                        <label>Mobile</label>
                        <input type="tel" class="form-control" autocomplete="off" maxlength="10" id="mobile" name="mobile" onkeypress="return isNumberKey(event)" placeholder="789654XXXX" required />
                      </div>
                      <p class="text-danger font-weight-bold" id="leadMobileError"></p>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-lg-6">
                      <div class="form-group form-group-default required">
                        <label>Program</label>
                        <select class="full-width" style="border: transparent;" name="course" id="course" onchange="getSubCourse(this.value)" required>
                          <option value="">Choose</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-group form-group-default required">
                        <label>Specialization</label>
                        <select class="full-width" style="border: transparent;" name="sub_course" id="sub_course" required>
                          <option value="">Choose</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer clearfix text-end mt-2">
                    <div class="col-md-4 m-t-10 sm-m-t-10">
                      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
                        <span>Save</span>
                        <span class="hidden-block">
                          <i class="pg-icon">tick</i>
                        </span>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- END PLACE PAGE CONTENT HERE -->
      </div>
      <!-- END CONTAINER FLUID -->
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

    <script type="text/javascript">
      $("#mobile").keyup(function() {
        var error_id = "leadMobileError";
        var mobile = $(this).val();
        if (mobile.length == 10) {
          var university = '<?= $_SESSION['university_id'] ?>';
          $.ajax({
            url: '/app/leads/check_lead_mobile?mobile=' + mobile + '&university=' + university,
            type: 'GET',
            dataType: 'JSON',
            success: function(data) {
              if (data.status == 302) {
                $('#leadMobileError').html(data.message);
                $(':input[type="submit"]').prop('disabled', true);
              } else {
                $(':input[type="submit"]').prop('disabled', false);
                $('#leadMobileError').html('');
              }
            }
          })
        } else {
          $(':input[type="submit"]').prop('disabled', false);
          $('#' + error_id).html('');
        }
      });

      function checkLeadEmail(value, error_id) {
        if (isEmail(value)) {
          var university = '<?= $_SESSION['university_id'] ?>';
          $.ajax({
            url: '/app/leads/check_lead_email?email=' + value + '&university=' + university,
            type: 'GET',
            dataType: 'JSON',
            success: function(data) {
              if (data.status == 302) {
                $('#' + error_id).html(data.message);
                $(':input[type="submit"]').prop('disabled', true);
              } else {
                $(':input[type="submit"]').prop('disabled', false);
                $('#' + error_id).html('');
              }
            }
          })
        } else {
          $(':input[type="submit"]').prop('disabled', false);
          $('#' + error_id).html('');
        }
      }

      function getCourse() {
        var university_id = '<?=$_SESSION['university_id']?>';
        var user_id = $("#user").val();
        $.ajax({
          url: '/app/users/leads/courses?university_id='+university_id+'&user_id='+user_id,
          type: 'GET',
          dataType:'json',
          success: function(data) {
            if(data.status){
              $('#course').html(data.options);
              getSubCourse($('#course').val());
            }else{
              notification('danger', data.message);
            }
          }
        })
      }

      getCourse();

      function getSubCourse(id) {
        var university_id = '<?=$_SESSION['university_id']?>';
        var user_id = $("#user").val();
        $.ajax({
          url: '/app/users/leads/sub-courses?university_id='+university_id+'&user_id='+user_id+'&course_id='+id,
          type: 'GET',
          dataType:'json',
          success: function(data) {
            if(data.status){
              $('#sub_course').html(data.options);
            }else{
              notification('danger', data.message);
            }
          }
        })
      }

      $("#lead-form").validate();

      $(function() {
        $("#lead-form").on("submit", function(e) {
          var formData = new FormData(this);
          $.ajax({
            url: this.action,
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
              if (data.status == 200) {
                notification('success', data.message);
                $("#replaceable").html('<div class="row">\
                  <div class="col-md-12 text-center">\
                    <h1>'+data.student_id+'</h1><br>\
                    <a href="/leads/generate" type="button" class="btn btn-primary">Submit Another</a>\
                    <a href="/admissions/application-form?lead_id='+data.id+'" type="button" class="btn btn-primary">Fill Application Form</a>\
                  </div>\
                </div>');
                if (data.hasOwnProperty('url')) {
                  window.location.href = data.url;
                }
              } else {
                notification('danger', data.message);
              }
            }
          });
          e.preventDefault();
        });
      })
    </script>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
