<?php
session_start();
if (isset($_SESSION["Password"]) || isset($_SESSION["Unique_ID"])) {
  header("Location: /dashboard");
}
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Login | <?= $app_title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no" />
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-touch-fullscreen" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta content="" name="description" />
  <meta content="" name="author" />
  <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
  <link class="main-stylesheet" href="pages/css/pages.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/toastr.min.css" rel="stylesheet" type="text/css" />
  <style>
    .form-group-default {
      padding-bottom: 20px;
    }

    .form-group-default.focused {
      border: none !important;
      border-bottom: solid 1px grey !important;

    }
  </style>
  <script type="text/javascript">
    window.onload = function() {
      // fix for windows 8
      if (navigator.appVersion.indexOf("Windows NT 6.2") != -1)
        document.head.innerHTML += '<link rel="stylesheet" type="text/css" href="pages/css/windows.chrome.fix.css" />'
    }
  </script>
</head>

<body class="fixed-header ">
  <div class="login-wrapper row p-0 m-0">
    <div class="col-xl-4 col-lg-4 p-0 m-0 login_col1" style="height: 100vh;">
      <div class="login-container bg-white h-100  d-flex  align-items-center justify-content-center">
        <div class="p-l-50 p-r-50 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
          <div class="new_custom_h d-flex  align-items-start">
            <div class="university_new_logo">
              <img src="assets/img/university/1739959794.png" class="img-fluid" alt="">
              <!--<p class="universityname_t text-center">IEC University</p>-->

            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 p-0 m-0 login_col2" style="height: 100vh;">
      <div class="bg-pic login_form_new d-flex h-100 justify-content-center align-items-center " style="background-image: url('<?= $login_cover ?>'); background-repeat: no-repeat; background-size: cover;">
        <div class="">
          <div class="card card-body custom_login_form_card m-0">
           <!-- <?php if (!empty($dark_logo)) { ?>
              <img src="<?= $dark_logo ?>" alt="logo" data-src="<?= $dark_logo ?>" data-src-retina="<?= $dark_logo ?>" height="50">
            <?php } ?>-->
            <h2 class="mt-3 fw-bold title_text_login">Welcome</h2>
            <p class="mb-4 title_text_login">Sign in to your account</p>
            <form id="form-login" class="" role="form" autocomplete="off" action="app/login/login">

              <div class="form-group form-group-default custom_new_fields">
                <label class="mb-2">User Name</label>
                <div class="controls">
                  <input type="text" name="username" style="text-transform: uppercase" placeholder="Username" class="form-control" required>
                </div>
              </div>
              <div class="form-group form-group-default custom_new_fields">
                <label class="mb-2">Password</label>
                <div class="controls">
                  <input type="password" class="form-control" name="password" placeholder="Credentials" required>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12 no-padding sm-p-l-10 d-flex justify-content-between">
                  <div class="form-check">
                    <input type="checkbox" checked value="1" id="checkbox1">
                    <label for="checkbox1">Remember me</label>
                  </div>
                  <a href="#" class="normal newlog_btn1">Lost your password?</a>

                </div>
                <div class="col-md-12 d-flex align-items-center justify-content-center">
                  <button aria-label="" class="btn newlog_btn btn-lg m-t-10 w-50 rounded-pill custom_login_btn" type="submit">Sign in</button>
                </div>

                <!-- <div class="col-md-12 new_custom_social_media_s">
                  <div class="d-flex w-100 mb-2">
                    <div class="login_hr_line"></div>
                    <div class="login_hr_line1"><p class="mb-0">or</p></div>
                    <div class="login_hr_line"></div>
                  </div>
                  <div class="d-flex align-items-center justify-content-center" style="gap: 20px;">
                    <a href="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-facebook " viewBox="0 0 16 16">
                      <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                    </svg></a>
                    <a href="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                      <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
                    </svg></a>
                    <a href="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
                      <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z" />
                    </svg></a>
                    <a href="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pinterest" viewBox="0 0 16 16">
                      <path d="M8 0a8 8 0 0 0-2.915 15.452c-.07-.633-.134-1.606.027-2.297.146-.625.938-3.977.938-3.977s-.239-.479-.239-1.187c0-1.113.645-1.943 1.448-1.943.682 0 1.012.512 1.012 1.127 0 .686-.437 1.712-.663 2.663-.188.796.4 1.446 1.185 1.446 1.422 0 2.515-1.5 2.515-3.664 0-1.915-1.377-3.254-3.342-3.254-2.276 0-3.612 1.707-3.612 3.471 0 .688.265 1.425.595 1.826a.24.24 0 0 1 .056.23c-.061.252-.196.796-.222.907-.035.146-.116.177-.268.107-1-.465-1.624-1.926-1.624-3.1 0-2.523 1.834-4.84 5.286-4.84 2.775 0 4.932 1.977 4.932 4.62 0 2.757-1.739 4.976-4.151 4.976-.811 0-1.573-.421-1.834-.919l-.498 1.902c-.181.695-.669 1.566-.995 2.097A8 8 0 1 0 8 0" />
                    </svg></a>
                  </div>
                </div> -->
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
  <!-- BEGIN VENDOR JS -->
  <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
  <!--  A polyfill for browsers that don't support ligatures: remove liga.js if not needed-->
  <script src="assets/plugins/liga.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery/jquery-3.2.1.min.js" type="text/javascript"></script>
  <script src="assets/plugins/modernizr.custom.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
  <script src="assets/plugins/popper/umd/popper.min.js" type="text/javascript"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery/jquery-easy.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-ios-list/jquery.ioslist.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-actual/jquery.actual.min.js"></script>
  <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js"></script>
  <script type="text/javascript" src="assets/plugins/select2/js/select2.full.min.js"></script>
  <script type="text/javascript" src="assets/plugins/classie/classie.js"></script>
  <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
  <!-- END VENDOR JS -->
  <script src="pages/js/pages.min.js"></script>
  <script src="assets/js/toastr.min.js"></script>

  <script>
    toastr.options = {
      "closeButton": false,
      "debug": false,
      "newestOnTop": false,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "preventDuplicates": false,
      "onclick": null,
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "3000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
  </script>

  <script>
    $(function() {
      $('#form-login').validate();
      $("#form-login").on("submit", function(e) {
        if ($('#form-login').valid()) {
          $(':input[type="submit"]').prop('disabled', true);
          var formData = new FormData(this);
          $.ajax({
            url: this.action,
            type: 'post',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(data) {
              if (data.status == 200) {
                toastr.success(data.message);
                window.setTimeout(function() {
                  window.location.href = data.url;
                }, 1000);
              } else {
                $(':input[type="submit"]').prop('disabled', false);
                toastr.error(data.message);
              }
            }
          });
          e.preventDefault();
        }
      });
    })
  </script>
</body>

</html>