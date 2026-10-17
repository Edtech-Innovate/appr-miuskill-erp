<?php
require '../../includes/db-config.php';
session_start();

$enroll = isset($_POST['enroll']) ? $_POST['enroll'] : "";
$current_duration = isset($_POST['current_duration']) ? $_POST['current_duration'] : "";
$user_code = isset($_POST['user_code']) ? $_POST['user_code'] : "";
$marks_type = ($_SESSION['university_id'] ==41) ? "Internal":"Internal";

?>
<style>.modal-dialog.modal-lg { width: 50%;}</style>

<div class="modal-header clearfix ">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
            class="pg-icon">close</i>
    </button>
    <h5 class="text-center"><span class="semi-bold"> Allot Obtain <?= $marks_type ?> Marks </span></h5>
</div>
<form role="form" id="form-add-results" action="/app/results/store-internal-marks" method="POST"
    enctype="multipart/form-data">
    <div class="modal-body">
        <?php if($_SESSION['university_id'] ==20){ ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="form-group form-group-default required">
                    <label for="internal_marks">Duration</label>
                    <select name="duration" id="duration" class="full-width" style="border: transparent;" onchange="getSubjects(this.value)">
                        <option value="">Select Duration</option>
                        <?php
                        
                       for ($i=1; $i <= $current_duration ; $i++) {
                        $selected = ($i == $current_duration) ? "selected" : '';

                            echo '<option value="'. $i. '" '.$selected.' >'. $i. '</option>';
                        }
                       ?>
                    </select>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="subject-box">
        </div>
    </div>
    <div class="modal-footer clearfix justify-content-center">
        <div class="col-md-4 m-t-10 sm-m-t-10">
              <?php $readonly = ($_SESSION['Role'] =="Operations") ? "disabled" : "";  ?>
            <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left" <?=$readonly ?>>
                <span>Save</span>
                <span class="hidden-block">
                    <i class="pg-icon">tick</i>
                </span>
            </button>
        </div>
    </div>
</form>
<script>

    function markStatus(mark, min_marks, sub_course_id, max_marks) {
        let status_sec = $(".marks_status_" + sub_course_id);
        let input_mark = parseFloat(mark);
        if (mark.trim() === "") {
            status_sec.empty();
            return;
        }
        if (!isNaN(input_mark)) {
            if (input_mark > max_marks) {
                status_sec.html('<span style="color:red;font-weight:600">Marks should be less than ' + max_marks + '</span>');
            } else if (input_mark < min_marks) {
                status_sec.html('<span style="color:red;font-weight:600">Fail</span>');
            } else {
                status_sec.html('<span style="color:green; font-weight:600">Pass</span>');
            }
        } else if (mark === "AB" || mark === "ab") {
            status_sec.html('<span style="color:red;font-weight:600">Fail</span>');
        } else {
            status_sec.html('<span style="color:red;font-weight:600">Invalid Input</span>');
        }
    }
    function extmarkStatus(mark, min_marks, sub_course_id, max_marks) {
        let status_sec = $(".ext_marks_status_" + sub_course_id);
        let input_mark = parseFloat(mark);
        if (mark.trim() === "") {
            status_sec.empty();
            return;
        }
        if (!isNaN(input_mark)) {
            if (input_mark > max_marks) {
                status_sec.html('<span style="color:red;font-weight:600">Marks should be less than ' + max_marks + '</span>');
            } else if (input_mark < min_marks) {
                status_sec.html('<span style="color:red;font-weight:600">Fail</span>');
            } else {
                status_sec.html('<span style="color:green; font-weight:600">Pass</span>');
            }
        } else if (mark === "AB" || mark === "ab") {
            status_sec.html('<span style="color:red;font-weight:600">Fail</span>');
        } else {
            status_sec.html('<span style="color:red;font-weight:600">Invalid Input</span>');
        }
    }
    $(document).ready(function () {
     getSubjects('<?= $current_duration ?>');
    })
    function getSubjects(duration) {
        var enroll = '<?= $enroll ?>';
        var user_code = '<?= $user_code ?>';

        $.ajax({
          url: '/app/results/internal-subjects-list',
          type: 'POST',
          data: { enroll: enroll, duration: duration,user_code:user_code},
          success: function (data) {
              $('.subject-box').html(data);
          }
        })
    }

        $(document).ready(function () {
        $("#form-add-results").on("submit", function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure to assign marks?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Process'
            }).then((result) => {
                if (result.isConfirmed) {

                    $(':input[type="submit"]').prop('disabled', true);
                    var formData = new FormData(this);

                    $.ajax({
                        url: $(this).attr('action'),
                        type: "POST",
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData: false,
                        dataType: 'json',
                        success: function (data) {
                            if (data.status == 200) {
                                notification('success', data.message);
                                $('.modal').modal('hide');
                            } else {
                                notification('danger', data.message);
                            }
                        },
                        error: function (data) {
                            notification('danger', 'Server is not responding. Please try again later');
                        }
                    });

                } else {
                    // $('.table').DataTable().ajax.reload(null, false);
                }
            });
        });
    });

</script>