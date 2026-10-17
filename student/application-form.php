<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link href="/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" media="screen">
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
<style type="text/css">
  input {
    text-transform: uppercase;
  }
</style>
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
        <div class=" container-fluid   sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <!-- START BREADCRUMB -->
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) : $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                endif;
              }
              ?>
            </ol>
            <!-- END BREADCRUMB -->
          </div>
        </div>
      </div>

      <!-- START CONTAINER FLUID -->
      <div class=" container-fluid  ">
        <?php
        if (!isset($_SESSION['Added_For']) || empty($_SESSION['Added_For'])) { ?>
          <div class="row d-flex justify-content-center">
            <div class="col-md-4 col-sm-12">
              <div class="card">
                <div class="card-body">
                  <form id="centerCodeFrom" action="/app/application-form/student/center-code">
                    <div class="form-group form-group-default required">
                      <label>Co-Ordinator Code</label>
                      <input type="text" name="code" class="form-control" placeholder="ex: SAUXXXX" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                  </form>
                  <div class="row m-t-20">
                    <div class="col-md-12 text-center">
                      <p class="cursor-pointer hint-text small-text" onclick="setDefaultCoordinator()">Don't know the code, Skip <i class="uil uil-arrow-right"></i> </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } else {
          $step = (int)$_SESSION['Step'] == 0 ? 1 : $_SESSION['Step'];
          include '../app/application-form/student/step-' . $step . '.php';
        } ?>
      </div>
    </div>
    <!-- END CONTAINER FLUID -->
  </div>
  <!-- END PAGE CONTENT -->
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
  <script src="/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>
  <script type="text/javascript" src="/assets/plugins/jquery-inputmask/jquery.inputmask.min.js"></script>
  <script>
    $('#centerCodeFrom').validate();
    $('#centerCodeFrom').submit(function(e) {
      e.preventDefault();
      if ($('#centerCodeFrom').valid()) {
        var formData = new FormData(this);
        $.ajax({
          url: $(this).attr('action'),
          type: "POST",
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              notification('success', data.message);
              setTimeout(function() {
                window.location.reload();
              }, 3000);
            } else {
              notification('danger', data.message);
            }
          },
          error: function(data) {
            notification('danger', 'Server is not responding. Please try again later');
          }
        });
      }
    });

    function setDefaultCoordinator() {
      $.ajax({
        url: '/app/application-form/student/default-center-code',
        type: 'POST',
        success: function(data) {
          window.location.reload();
        }
      })
    }

    function goStepBack() {
      $.ajax({
        url: '/app/application-form/student/step-back',
        type: 'POST',
        success: function(data) {
          window.location.reload();
        }
      })

    }
  </script>

  <?php if ($step == 1) { ?>

    <script>
      $(function() {
        $("#dob").mask("99-99-9999")
        $("#aadhar").mask("9999-9999-9999")
        $('#dob').datepicker({
          format: 'dd-mm-yyyy',
          autoclose: true,
          endDate: '-15y'
        });
      });
    </script>

    <script>
      function getAdmissionSession(university_id) {
        $.ajax({
          url: '/app/application-form/admission-session?university_id=' + university_id + '&form=<?php print !empty($id) ? 1 : "" ?>',
          type: 'GET',
          success: function(data) {
            $('#admission_session').html(data);
            $('#admission_session').val(<?php print !empty($id) ? $student['Admission_Session_ID'] : '' ?>);
            getAdmissionType($('#admission_session').val());
          }
        })
      }

      getAdmissionSession(<?= $_SESSION['university_id'] ?>);

      function getAdmissionType(session_id) {
        const university_id = '<?= $_SESSION['university_id'] ?>';
        $.ajax({
          url: '/app/application-form/admission-type?university_id=' + university_id + '&session_id=' + session_id,
          type: 'GET',
          success: function(data) {
            $('#admission_type').html(data);
            <?php if (!empty($_SESSION['Admission_Type_ID'])) { ?>
              $('#admission_type').val(<?= $_SESSION['Admission_Type_ID'] ?>);
            <?php } ?>
            getCourse();
          }
        })
      }

      function getCourse() {
        var center = '<?= $_SESSION['Added_For'] ?>';
        const university_id = '<?= $_SESSION['university_id'] ?>';
        const session_id = $('#admission_session').val();
        const admission_type_id = $('#admission_type').val();
        $.ajax({
          url: '/app/application-form/course?center=' + center + '&session_id=' + session_id + '&admission_type_id=' + admission_type_id + '&university_id=' + university_id,
          type: 'GET',
          success: function(data) {
            $('#course').html(data);
            $('#course').val(<?= $_SESSION['Course_ID'] ?>)
            getSubCourse();
          }
        })
      }

      function getSubCourse() {
        var center = $('#center').val();
        const university_id = '<?= $_SESSION['university_id'] ?>';
        const session_id = $('#admission_session').val();
        const admission_type_id = $('#admission_type').val();
        const course_id = $('#course').val();
        $.ajax({
          url: '/app/application-form/sub-course?center=' + center + '&session_id=' + session_id + '&admission_type_id=' + admission_type_id + '&university_id=' + university_id + '&course_id=' + course_id,
          type: 'GET',
          success: function(data) {
            $('#sub_course').html(data);
            $('#sub_course').val(<?= $_SESSION['Sub_Course_ID'] ?>);
            getMode();
          }
        })
      }

      function getMode() {
        const sub_course_id = $('#sub_course').val();
        $.ajax({
          url: '/app/application-form/mode?sub_course_id=' + sub_course_id,
          type: 'GET',
          success: function(data) {
            $('#mode').html(data);
            getDuration();
          }
        })
      }

      function getDuration() {
        const admission_type_id = $('#admission_type').val();
        const sub_course_id = $('#sub_course').val();
        $.ajax({
          url: '/app/application-form/duration?admission_type_id=' + admission_type_id + '&sub_course_id=' + sub_course_id,
          type: 'GET',
          success: function(data) {
            $('#duration').html(data);
            $('#duration').val(<?php print !empty($id) ? $student['Duration'] : '' ?>)
          }
        })
      }
    </script>

    <script>
      // Aadhar Validator
      // multiplication table d
      var d = [
        [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
        [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
        [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
        [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
        [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
        [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
        [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
        [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
        [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
        [9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
      ];
      // permutation table p
      var p = [
        [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
        [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
        [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
        [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
        [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
        [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
        [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
        [7, 0, 4, 6, 9, 1, 3, 2, 5, 8]
      ];
      // inverse table inv
      var inv = [0, 4, 3, 2, 1, 5, 6, 7, 8, 9];
      // converts string or number to an array and inverts it
      function invArray(array) {

        if (Object.prototype.toString.call(array) == "[object Number]") {
          array = String(array);
        }

        if (Object.prototype.toString.call(array) == "[object String]") {
          array = array.split("").map(Number);
        }

        return array.reverse();

      }
      // generates checksum
      function generate(array) {

        var c = 0;
        var invertedArray = invArray(array);

        for (var i = 0; i < invertedArray.length; i++) {
          c = d[c][p[((i + 1) % 8)][invertedArray[i]]];
        }

        return inv[c];
      }
      // validates checksum
      function validate(array) {
        var c = 0;
        var invertedArray = invArray(array);

        for (var i = 0; i < invertedArray.length; i++) {
          c = d[c][p[(i % 8)][invertedArray[i]]];
        }
        return (c === 0);
      }

      function validateAadhar(adhar) {
        adhar = adhar.replace(/-/g, '');
        //pretty dumb but the easiest solution to know if the number is 12 digit or not :)
        if (adhar >= 100000000000 && adhar <= 999999999999) {
          if (validate(adhar) == false) {
            return false;
          } else {
            return true;
          }
        } else {
          return false;
        }
      }
    </script>

    <script type="text/javascript">
      $(document).ready(function() {

        $.validator.addMethod('validateAadharNumber', function(value, element) {
          return validateAadhar(value)
        }, 'Invalid Aadhaar Number!');

        $('#step_1').validate({
          rules: {
            center: {
              required: true
            },
            admission_session: {
              required: true
            },
            admission_type: {
              required: true
            },
            course: {
              required: true
            },
            sub_course: {
              required: true
            },
            duration: {
              required: true
            },
            full_name: {
              required: true
            },
            first_name: {
              required: true
            },
            last_name: {
              required: true
            },
            father_name: {
              required: true
            },
            mother_name: {
              required: true
            },
            dob: {
              required: true
            },
            gender: {
              required: true
            },
            category: {
              required: true
            },
            employment_status: {
              required: true
            },
            aadhar: {
              required: true,
              validateAadharNumber: true
            },
            nationality: {
              required: true
            },
          },
          highlight: function(element) {
            $(element).addClass('error');
            $(element).closest('.form-control').addClass('has-error');
          },
          unhighlight: function(element) {
            $(element).removeClass('error');
            $(element).closest('.form-control').removeClass('has-error');
          }
        });
      });

      $('#step_1').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
          url: $(this).attr('action'),
          type: "POST",
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          dataType: 'json',
          success: function(data) {
            if (data.status == 200) {
              notification('success', data.message);
              setTimeout(function() {
                window.location.reload();
              }, 3000);
            } else {
              notification('danger', data.message);
            }
          },
          error: function(data) {
            notification('danger', 'Server is not responding. Please try again later');
          }
        });
      });
    </script>
  <?php } elseif ($step == 2) { ?>
    <script src="https://ebz-static.s3.ap-south-1.amazonaws.com/easecheckout/easebuzz-checkout.js"></script>
    <script>
      function getPaymentType() {
        $.ajax({
          url: '/app/application-form/student/payment-type',
          type: 'POST',
          success: function(data) {
            $("#payment_type").html(data);
          }
        })
      }

      getPaymentType();

      function getFeeTable() {
        var payment_type = $("#payment_type").val();
        $.ajax({
          url: '/app/application-form/student/fee-table',
          type: 'POST',
          data: {
            payment_type
          },
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              $("#fee_table").html(data.table);
              $("#amount").val(data.amount);
            }
          }
        })
      }

      function getRegion(pincode) {
        if (pincode.length == 6) {
          $.ajax({
            url: '/app/regions/cities?pincode=' + pincode,
            type: 'GET',
            success: function(data) {
              $('#city').html(data);
            }
          });

          $.ajax({
            url: '/app/regions/districts?pincode=' + pincode,
            type: 'GET',
            success: function(data) {
              $('#district').html(data);
            }
          });

          $.ajax({
            url: '/app/regions/state?pincode=' + pincode,
            type: 'GET',
            success: function(data) {
              $('#state').val(data);
            }
          })
        }
      }

      function initiatePayment() {
        if ($("#step_2").valid()) {
          var payment_type = $("#payment_type").val();
          var mobile = $("#contact").val();
          var email = $("#email").val();
          $.ajax({
            url: '/app/application-form/student/fee-table',
            type: 'POST',
            data: {
              payment_type
            },
            dataType: 'json',
            success: function(data) {
              if (data.status) {
                $.ajax({
                  url: '/app/application-form/student/payment/create',
                  type: 'POST',
                  data: {
                    "amount": data.amount,
                    "email": email,
                    "mobile": mobile,
                    "productInfo": data.name
                  },
                  dataType: 'json',
                  success: function(data) {
                    var easebuzzCheckout = new EasebuzzCheckout(data.accessKey, 'prod')
                    var options = {
                      access_key: data.orderId,
                      onResponse: (response) => {
                        if (response.status == 'success') {
                          Swal.fire(
                            'Success!',
                            'Payment is success!',
                            'success'
                          )
                          $("#step_2").submit();
                          updatePaymentStatus(response);
                        } else {
                          Swal.fire(
                            'Failed',
                            'Payment is ' + response.status + '!',
                            'error'
                          )
                          $("#step_2").submit();
                          updatePaymentStatus(response);
                        }
                      },
                      theme: "#123456"
                    }
                    easebuzzCheckout.initiatePayment(options);
                  },
                  error: function(data) {
                    notification('danger', 'Server is not responding. Please try again later');
                  }
                })
              }
            }
          })
        } else {
          notification('danger', 'Please fill the required fields!');
        }
      }
    </script>

    <script>
      function updatePaymentStatus(response) {
        $.ajax({
          url: '/app/easebuzz/response',
          type: 'POST',
          data: response,
          success: function(data) {
            console.log(data);
          }
        })
      }
    </script>

    <script>
      $('#step_2').validate({
        rules: {
          email: {
            required: true
          },
          contact: {
            required: true
          },
          address: {
            required: true
          },
          pincode: {
            required: true
          },
          city: {
            required: true
          },
          district: {
            required: true
          },
          state: {
            required: true
          },
        },
        highlight: function(element) {
          $(element).addClass('error');
          $(element).closest('.form-control').addClass('has-error');
        },
        unhighlight: function(element) {
          $(element).removeClass('error');
          $(element).closest('.form-control').removeClass('has-error');
        }
      });

      $('#step_2').submit(function(e) {
        e.preventDefault();
        if ($('#step_2').valid()) {
          var formData = new FormData(this);
          $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
              if (data.status == 200) {
                notification('success', data.message);
                setTimeout(function() {
                  window.location.reload();
                }, 3000);
              } else {
                notification('danger', data.message);
              }
            },
            error: function(data) {
              notification('danger', 'Server is not responding. Please try again later');
            }
          });
        }
      });
    </script>
  <?php } elseif ($step == 3) { ?>
    <script>
      function getEligibility() {
        $.ajax({
          url: '/app/application-form/student/course-eligibility',
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            if (data.status) {
              var col_size = data.count == 1 ? 10 : data.count == 2 ? 5 : data.count == 3 ? 3 : data.count == 4 ? 2 : 2

              if (data.required.includes('High School')) {
                highDetailsRequired();
                $("#high_school_column").css('display', 'block');
                $("#high_school_column").addClass('col-md-' + col_size);
              } else if (data.optional.includes('High School')) {
                highDetailsNotRequired();
                $("#high_school_column").css('display', 'block');
                $("#high_school_column").addClass('col-md-' + col_size);
              } else {
                highDetailsNotRequired();
                $("#high_school_column").css('display', 'none');
              }

              if (data.required.includes('Intermediate')) {
                interDetailsRequired();
                $("#intermediate_column").css('display', 'block');
                $("#intermediate_column").addClass('col-md-' + col_size);
              } else if (data.optional.includes('Intermediate')) {
                interDetailsNotRequired();
                $("#intermediate_column").css('display', 'block');
                $("#intermediate_column").addClass('col-md-' + col_size);
              } else {
                interDetailsNotRequired();
                $("#intermediate_column").css('display', 'none');
              }

              if (data.required.includes('UG')) {
                ugDetailsRequired();
                $("#ug_column").css('display', 'block');
                $("#ug_column").addClass('col-md-' + col_size);
              } else if (data.optional.includes('UG')) {
                ugDetailsNotRequired();
                $("#ug_column").css('display', 'block');
                $("#ug_column").addClass('col-md-' + col_size);
              } else {
                ugDetailsNotRequired();
                $("#ug_column").css('display', 'none');
              }

              if (data.required.includes('PG')) {
                pgDetailsRequired();
                $("#pg_column").css('display', 'block');
                $("#pg_column").addClass('col-md-' + col_size);
              } else if (data.optional.includes('PG')) {
                pgDetailsNotRequired();
                $("#pg_column").css('display', 'block');
                $("#pg_column").addClass('col-md-' + col_size);
              } else {
                pgDetailsNotRequired();
                $("#pg_column").css('display', 'none');
              }

              if (data.required.includes('Other')) {
                otherDetailsRequired();
                $("#other_column").css('display', 'block');
                $("#other_column").addClass('col-md-' + col_size);
              } else if (data.optional.includes('Other')) {
                otherDetailsNotRequired();
                $("#other_column").css('display', 'block');
                $("#other_column").addClass('col-md-' + col_size);
              } else {
                otherDetailsNotRequired();
                $("#other_column").css('display', 'none');
              }
            } else {
              notification('danger', 'Eligibility is not configured for this course!');
            }
          }
        })
      }

      getEligibility();

      function fileValidation(id) {
        var fi = document.getElementById(id);
        if (fi.files.length > 0) {
          for (var i = 0; i <= fi.files.length - 1; i++) {
            var fsize = fi.files.item(i).size;
            var file = Math.round((fsize / 1024));
            // The size of the file.
            if (file >= 500) {
              $('#' + id).val('');
              alert("File too Big, each file should be less than or equal to 500KB");
            }
          }
        }
      }

      function highDetailsRequired() {
        $('.high_school').addClass('required');
        $('#high_subject').validate();
        $('#high_subject').rules('add', {
          required: true
        });
        $('#high_year').validate();
        $('#high_year').rules('add', {
          required: true
        });
        $('#high_board').validate();
        $('#high_board').rules('add', {
          required: true
        });
        $('#high_total').validate();
        $('#high_total').rules('add', {
          required: true
        });
        <?php if (empty($high_marksheet)) { ?>
          $('#high_marksheet').validate();
          $('#high_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function highDetailsNotRequired() {
        $('.high_school').removeClass('required');
        $('#high_subject').rules('remove', 'required');
        $('#high_year').rules('remove', 'required');
        $('#high_board').rules('remove', 'required');
        $('#high_total').rules('remove', 'required');
        $('#high_marksheet').rules('remove', 'required');
      }

      function interDetailsRequired() {
        $('.intermediate').addClass('required');
        $('#inter_subject').validate();
        $('#inter_subject').rules('add', {
          required: true
        });
        $('#inter_year').validate();
        $('#inter_year').rules('add', {
          required: true
        });
        $('#inter_board').validate();
        $('#inter_board').rules('add', {
          required: true
        });
        $('#inter_total').validate();
        $('#inter_total').rules('add', {
          required: true
        });
        <?php if (empty($inter_marksheet)) { ?>
          $('#inter_marksheet').validate();
          $('#inter_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function interDetailsNotRequired() {
        $('.intermediate').removeClass('required');
        $('#inter_subject').rules('remove', 'required');
        $('#inter_year').rules('remove', 'required');
        $('#inter_board').rules('remove', 'required');
        $('#inter_total').rules('remove', 'required');
        $('#inter_marksheet').rules('remove', 'required');
      }

      function ugDetailsRequired() {
        $('.ug-program').addClass('required');
        $('#ug_subject').validate();
        $('#ug_subject').rules('add', {
          required: true
        });
        $('#ug_year').validate();
        $('#ug_year').rules('add', {
          required: true
        });
        $('#ug_board').validate();
        $('#ug_board').rules('add', {
          required: true
        });
        $('#ug_total').validate();
        $('#ug_total').rules('add', {
          required: true
        });
        <?php if (empty($ug_marksheet)) { ?>
          $('#ug_marksheet').validate();
          $('#ug_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function ugDetailsNotRequired() {
        $('.ug-program').removeClass('required');
        $('#ug_subject').rules('remove', 'required');
        $('#ug_year').rules('remove', 'required');
        $('#ug_board').rules('remove', 'required');
        $('#ug_total').rules('remove', 'required');
        $('#ug_marksheet').rules('remove', 'required');
      }

      function pgDetailsRequired() {
        $('.pg-program').addClass('required');
        $('#pg_subject').validate();
        $('#pg_subject').rules('add', {
          required: true
        });
        $('#pg_year').validate();
        $('#pg_year').rules('add', {
          required: true
        });
        $('#pg_board').validate();
        $('#pg_board').rules('add', {
          required: true
        });
        $('#pg_total').validate();
        $('#pg_total').rules('add', {
          required: true
        });

        <?php if (empty($pg_marksheet)) { ?>
          $('#pg_marksheet').validate();
          $('#pg_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function pgDetailsNotRequired() {
        $('.pg-program').removeClass('required');
        $('#pg_subject').rules('remove', 'required');
        $('#pg_year').rules('remove', 'required');
        $('#pg_board').rules('remove', 'required');
        $('#pg_total').rules('remove', 'required');
        $('#pg_marksheet').rules('remove', 'required');
      }

      function otherDetailsRequired() {
        $('.other-program').addClass('required');
        $('#other_subject').validate();
        $('#other_subject').rules('add', {
          required: true
        });
        $('#other_year').validate();
        $('#other_year').rules('add', {
          required: true
        });
        $('#other_board').validate();
        $('#other_board').rules('add', {
          required: true
        });
        $('#other_total').validate();
        $('#other_total').rules('add', {
          required: true
        });
        <?php if (empty($other_marksheet)) { ?>
          $('#other_marksheet').validate();
          $('#other_marksheet').rules('add', {
            required: true
          });
        <?php } ?>
      }

      function otherDetailsNotRequired() {
        $('.other-program').removeClass('required');
        $('#other_subject').rules('remove', 'required');
        $('#other_year').rules('remove', 'required');
        $('#other_board').rules('remove', 'required');
        $('#other_total').rules('remove', 'required');
        $('#other_marksheet').rules('remove', 'required');
      }

      $('#step_3').validate();

      function checkHighMarks() {
        var obtained = parseInt($('#high_obtained').val());
        var max = parseInt($("#high_max").val());
        var alerted = localStorage.getItem('alertedHigh') || '';
        if (obtained > max) {
          if (alerted != 'yes') {
            alert("Obtained marks can not be higher than Maximum marks");
            $(':input[type="submit"]').prop('disabled', true);
            localStorage.setItem('alertedHigh', 'yes');
          }
        } else {
          localStorage.setItem('alertedHigh', 'no');
          $(':input[type="submit"]').prop('disabled', false);
          if ($('#high_obtained').val().length > 0) {
            var percentage = (obtained / max) * 100;
            $('#high_total').val(percentage.toFixed(2));
            $("#high_total").prop("readonly", true);
          } else if ($('#high_obtained').val().length == 0) {
            $("#high_total").prop("readonly", false);
            $('#high_total').val('');
          }
        }
      }

      function checkInterMarks() {
        var obtained = parseInt($('#inter_obtained').val());
        var max = parseInt($("#inter_max").val());
        var alerted = localStorage.getItem('alertedInter') || '';
        if (obtained > max) {
          if (alerted != 'yes') {
            alert("Obtained marks can not be higher than Maximum marks");
            $(':input[type="submit"]').prop('disabled', true);
            localStorage.setItem('alertedInter', 'yes');
          }
        } else {
          localStorage.setItem('alertedInter', 'no');
          $(':input[type="submit"]').prop('disabled', false);
          if ($('#inter_obtained').val().length > 0) {
            var percentage = (obtained / max) * 100;
            $('#inter_total').val(percentage.toFixed(2));
            $("#inter_total").prop("readonly", true);
          } else if ($('#inter_obtained').val().length == 0) {
            $("#inter_total").prop("readonly", false);
            $('#inter_total').val('');
          }
        }
      }

      function checkUGMarks() {
        var obtained = parseInt($('#ug_obtained').val());
        var max = parseInt($("#ug_max").val());
        var alerted = localStorage.getItem('alertedUG') || '';
        if (obtained > max) {
          if (alerted != 'yes') {
            alert("Obtained marks can not be higher than Maximum marks");
            $(':input[type="submit"]').prop('disabled', true);
            localStorage.setItem('alertedUG', 'yes');
          }
        } else {
          localStorage.setItem('alertedUG', 'no');
          $(':input[type="submit"]').prop('disabled', false);
          if ($('#ug_obtained').val().length > 0) {
            var percentage = (obtained / max) * 100;
            $('#ug_total').val(percentage.toFixed(2));
            $("#ug_total").prop("readonly", true);
          } else if ($('#ug_obtained').val().length == 0) {
            $("#ug_total").prop("readonly", false);
            $('#ug_total').val('');
          }
        }
      }

      function checkPGMarks() {
        var obtained = parseInt($('#pg_obtained').val());
        var max = parseInt($("#pg_max").val());
        var alerted = localStorage.getItem('alertedPG') || '';
        if (obtained > max) {
          if (alerted != 'yes') {
            alert("Obtained marks can not be higher than Maximum marks");
            $(':input[type="submit"]').prop('disabled', true);
            localStorage.setItem('alertedPG', 'yes');
          }
        } else {
          localStorage.setItem('alertedPG', 'no');
          $(':input[type="submit"]').prop('disabled', false);
          if ($('#pg_obtained').val().length > 0) {
            var percentage = (obtained / max) * 100;
            $('#pg_total').val(percentage.toFixed(2));
            $("#pg_total").prop("readonly", true);
          } else if ($('#pg_obtained').val().length == 0) {
            $("#pg_total").prop("readonly", false);
            $('#pg_total').val('');
          }
        }
      }

      function checkOtherMarks() {
        var obtained = parseInt($('#other_obtained').val());
        var max = parseInt($("#other_max").val());
        var alerted = localStorage.getItem('alertedOther') || '';
        if (obtained > max) {
          if (alerted != 'yes') {
            alert("Obtained marks can not be higher than Maximum marks");
            $(':input[type="submit"]').prop('disabled', true);
            localStorage.setItem('alertedOther', 'yes');
          }
        } else {
          localStorage.setItem('alertedOther', 'no');
          $(':input[type="submit"]').prop('disabled', false);
          if ($('#other_obtained').val().length > 0) {
            var percentage = (obtained / max) * 100;
            $('#other_total').val(percentage.toFixed(2));
            $("#other_total").prop("readonly", true);
          } else if ($('#other_obtained').val().length == 0) {
            $("#other_total").prop("readonly", false);
            $('#other_total').val('');
          }
        }
      }

      $('#step_3').submit(function(e) {
        e.preventDefault();
        if ($("#step_3").valid()) {
          var formData = new FormData(this);
          $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
              if (data.status == 200) {
                notification('success', data.message);
                setTimeout(function() {
                  window.location.reload();
                }, 3000)
              } else {
                notification('danger', data.message);
              }
            },
            error: function(data) {
              notification('danger', 'Server is not responding. Please try again later');
            }
          });
        }
      });
    </script>
  <?php } elseif ($step == 4) { ?>
    <script>
      $('#step_4').validate({
        rules: {
          photo: {
            required: true
          },
          'aadhar[]': {
            required: true
          },
          student_signature: {
            required: true
          },
        },
        highlight: function(element) {
          $(element).addClass('error');
          $(element).closest('.form-control').addClass('has-error');
        },
        unhighlight: function(element) {
          $(element).removeClass('error');
          $(element).closest('.form-control').removeClass('has-error');
        }
      });

      function fileValidation(id) {
        var fi = document.getElementById(id);
        if (fi.files.length > 0) {
          for (var i = 0; i <= fi.files.length - 1; i++) {
            var fsize = fi.files.item(i).size;
            var file = Math.round((fsize / 1024));
            // The size of the file.
            if (file >= 500) {
              $('#' + id).val('');
              alert("File too Big, each file should be less than or equal to 500KB");
            }
          }
        }
      }

      $('#step_4').submit(function(e) {
        e.preventDefault();
        if ($("#step_4").valid()) {
          var formData = new FormData(this);
          $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            dataType: 'json',
            success: function(data) {
              if (data.status == 200) {
                notification('success', data.message);
                setTimeout(function() {
                  window.location.reload();
                }, 3000);
              } else {
                notification('danger', data.message);
              }
            },
            error: function(data) {
              notification('danger', 'Server is not responding. Please try again later');
            }
          });
        }
      });
    </script>
  <?php } elseif ($step == 5) { ?>
    <script>
      function printForm(studentId) {
        window.open('/forms/<?= $_SESSION['university_id'] ?>/?student_id=' + studentId);
      }
    </script>
  <?php } ?>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>