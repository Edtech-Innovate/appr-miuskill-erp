<?php
require '../../includes/db-config.php';
session_start();
$id = intval($_GET['id']);
$getdata = $conn->query("SELECT * FROM e_books WHERE id =$id ");
$data = $getdata->fetch_assoc();

?>

<!-- Modal -->
<div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
            class="pg-icon">close</i>
    </button>
    <h5>Edit <span class="semi-bold">E-books</span></h5>
</div>

<form role="form" id="form-add-e-book" action="/app/e-books/update" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
        <!-- University & Course -->
        <div class="row">
            <input type="hidden" value="<?= $data['id'] ?>" name="id">
            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>Course</label>
                    <select class="full-width select2" style="border: transparent;" id="course_id" name="course_id"
                        onchange="getSubCourse(this.value);">
                        <option value="">Select Course</option>
                        <?php
                        $programs = $conn->query("SELECT ID,Name,Short_Name FROM Courses WHERE Status=1 ORDER BY Name ASC");
                        while ($program = $programs->fetch_assoc()) { ?>
                            <option value="<?= $program['ID'] ?>" <?php if ($data['course_id'] == $program['ID']) {
                                  echo "selected";
                              } else {
                                  echo "";
                              } ?>>
                                <?= $program['Name'] . ' (' . $program['Short_Name'] . ')' ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>Specialization/Course</label>
                    <select class="full-width select2" style="border: transparent;" id="sub_course_id"
                        name="sub_course_id" onchange="getDuration(this.value);">
                        <option value="">Select</option>

                    </select>
                </div>
            </div>

 
            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>Duration </label>
                    <select class="full-width" style="border: transparent;"  onchange="getSubjects(this.value);" name="duration" id="duration"
                        data-placeholder="Choose Duration">
                        <option value="">Select Duration</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>Subject</label>
                    <select class="full-width" style="border: transparent;" id="subject_id" name="subject_id">
                        <option value="">Select Subject</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>E-book file *</label>
                    <input type="file" name="file" class="form-control"accept="image/png, image/jpg, image/jpeg, image/svg">
                    <?php if (!empty($id) && !empty($data['file_path'])) { ?>
                        <a href="/../<?php print !empty($id) ? $data['file_path'] : '' ?>" download=""><i class="uil uil-down-arrow"></i>
                        </a>
                   <input type="hidden" value="<?= $data['file_path'] ?>" name="updated_file_path">
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group form-group-default required">
                    <label>E-book Name</label>
                    <input type="text" name="ebook_name" class="form-control" value="<?= $data['title'] ?>"
                        placeholder="Enter E-book Name" required>
                </div>
            </div>
        </div>
        <div class="modal-footer clearfix justify-content-center">
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

<script>
    $(function () {
        $("#eligibilities").select2();
        $("#course_category").select2();
        getSubCourse('<?= $data['course_id'] ?>')
    })

    function getSubCourse(course_id) {
        $.ajax({
            url: '/app/e-books/sub_course',
            type: 'POST',
            dataType: 'text',
            data: {
                'course_id': course_id
            },
            success: function (result) {
                $('#sub_course_id').html(result);
                $("#sub_course_id").val('<?= $data['sub_course_id'] ?>');
                var sub_course = $("#sub_course_id").val();
                // getSubjects(sub_course);
                getDuration(sub_course);
            }
        })
    }

    function getSubjects(duration) {
      var sub_course_id   = $("#sub_course_id").val();
    //   alert(sub_course_id);
        $.ajax({
            url: '/app/e-books/subjects',
            type: 'POST',
            dataType: 'text',
            data: {
                'duration': duration,
                'sub_course_id': sub_course_id
            },
            success: function (result) {
                $('#subject_id').html(result);
                $("#subject_id").val('<?= $data['subject_id'] ?>');
            }
        })
    }

    function getDuration(id) {
        $.ajax({
            url: '/app/subjects/get-duration',
            data: { id: id },
            type: 'POST',
            success: function (data) {
                $("#duration").html(data);
                $("#duration").val('<?= $data['semester_id'] ?>');
                var duration = $("#duration").val();
                getSubjects(duration,id);

            }
        })
    }

    $(function () {
        $('#form-add-e-book').validate({
            rules: {
                course_id: {
                    required: true
                },
                subject_id: {
                    required: true
                },
                sub_course_id: {
                    required: true
                },
                duration: {
                    required: true
                },
                <?php print (!empty($id) && empty($data['file_path'])) ? "file: {required:true}," : "" ?>
                <?php print empty($id) ? "file: {required:true}," : "" ?>
            },
            highlight: function (element) {
                $(element).closest('.form-control').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-control').removeClass('has-error');
            }
        });
    })

    $("#form-add-e-book").on("submit", function (e) {
        if ($('#form-add-e-book').valid()) {
            $(':input[type="submit"]').prop('disabled', true);
            var formData = new FormData(this);
            e.preventDefault();
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
                        $('#e_books-table').DataTable().ajax.reload(null, false);
                    } else {
                        notification('danger', data.message);
                    }
                },
                error: function (data) {
                    notification('danger', 'Server is not responding. Please try again later');
                }
            });
        } else {
            //notification('danger', 'Invalid form information.');
        }
    });
</script>