<?php
require '../../includes/db-config.php';
session_start();
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $getsubjects = $conn->query("SELECT * FROM Syllabi WHERE ID = $id");
    $data = $getsubjects->fetch_assoc();
    ?>
    <div class="modal-header clearfix text-left">
        <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                class="pg-icon">close</i>
        </button>
        <h5 class="text-center">Update <span class="semi-bold"></span>Subject</h5>
    </div>
    <div class="modal-body" style="height: 450px;">
        <form id="edit-subject-form" action="/app/subjects/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ID" value="<?php echo $data['ID'] ?>" id="ID">
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="coursetype">Course</label>
                        <select class="form-control" id="coursetype" onchange="getSpecialization(this.value)"
                            name="coursetype">
                            <option value="">Select Course</option>
                            <?php
                            $stmt = $conn->query("SELECT ID, Name FROM Courses WHERE Status=1 AND University_ID = " . $_SESSION['university_id'] . " ORDER BY Name ASC");
                            while ($row = $stmt->fetch_assoc()) {
                                $courseId = $row["ID"];
                                $courseName = $row["Name"];
                                $selected = ($courseId == $data['Course_ID']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($courseId) . '" ' . $selected . '>' . htmlspecialchars($courseName) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="subcourse_id">Sub Course</label>
                        <select class="form-control" id="subcourse_id" name="subcourse_id"
                            onchange="getsemester(this.value);">
                            <option value="">Select Sub Course</option>

                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="semester">Duration</label>
                        <select class="form-control" id="seme" name="seme">
                            <option value="">Select Duration Type</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="subjectcode">Subject Code</label>
                        <input type="text" class="form-control" name="subjectcode" id="subjectcode"
                            value="<?php echo $data['Code']; ?>">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="subjectname">Subject Name</label>
                        <input type="text" class="form-control" name="subjectname" id="subjectname"
                            value="<?php echo $data['Name']; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="paper_type">Select Paper Type</label>
                        <select class="form-control" name="paper_type" id="paper_type">
                            <option value="">Select Paper Type</option>
                            <option value="Theory" <?php echo (isset($data['Paper_Type']) && $data['Paper_Type'] == 'Theory') ? 'selected' : ''; ?>>Theory</option>
                            <option value="Practical" <?php echo (isset($data['Paper_Type']) && $data['Paper_Type'] == 'Practical') ? 'selected' : ''; ?>>Practical</option>
                            <option value="Project" <?php echo (isset($data['Paper_Type']) && $data['Paper_Type'] == 'Project') ? 'selected' : ''; ?>>Project</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="subjectcredit">Subject Credit</label>
                        <input type="text" class="form-control" name="subjectcredit" id="subjectcredit"
                            value="<?php echo $data['Credit']; ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="minMarks">Minimum Marks</label>
                    <input type="text" class="form-control" name="minMarks" id="minMarks"
                        value="<?php echo $data['Min_Marks']; ?>">
                </div>

                <div class="col-md-6">
                    <label for="maxMarks">Maximum Marks</label>
                    <input type="text" class="form-control" name="maxMarks" id="maxMarks"
                        value="<?php echo $data['Max_Marks']; ?>">
                </div>
            </div>
            </br>
            <button type="submit" id="update" class="btn btn-success btn btn-lg">Update</button>
            <button type="button" class="btn btn-danger btn btn-lg" data-dismiss="modal">Close</button>
        </form>
    </div>
<?php } ?>
<script>
    $(function () {
        $('#edit-subject-form').validate({
            rules: {
                coursetype: { required: true },
            },
            rules: {
                subcourse_id: { required: true },
            },
            rules: {
                seme: { required: true },
            },
            rules: {
                subjectcode: { required: true },
            },
            rules: {
                subjectname: { required: true },
            },
            rules: {
                paper_type: { required: true },
            },
            //   
            rules: {
                subjectcredit: { required: true },
            },
            rules: {
                minMarks: { required: true },
            },
            rules: {
                maxMarks: { required: true },
            },

            highlight: function (element) {
                $(element).addClass('error');
                $(element).closest('.form-control').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).removeClass('error');
                $(element).closest('.form-control').removeClass('has-error');
            }
        });
    })

    $("#edit-subject-form").on("submit", function (e) {
        if ($('#edit-subject-form').valid()) {
            $(':input[type="submit"]').prop('disabled', true);
            var formData = new FormData(this);
            formData.append('id', '<?= $id ?>');
            $.ajax({
                url: this.action,
                type: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (data) {
                    if (data.status == 200) {
                        $('.modal').modal('hide');
                        notification('success', data.message);
                        $('#users-table').DataTable().ajax.reload(null, false);
                    } else {
                        $(':input[type="submit"]').prop('disabled', false);
                        notification('danger', data.message);
                    }
                }
            });
            e.preventDefault();
        }
    });
</script>
<script>
    $(document).ready(function () {
        getSpecialization('<?= $data['Course_ID'] ?>');
        // getsemester('<?= $data['Sub_Course_ID'] ?>');
    })
    function getSpecialization(courseId) {
        $.ajax({
            type: 'POST',
            url: '/app/e-books/sub_course',
            data: {
                course_id: courseId
            },
            success: function (response) {
                $('#subcourse_id').html(response);
                getsemester('<?= $data['Sub_Course_ID'] ?>');
            },
        });
    }

    function getsemester(sub_course_id) {
        var sub_course_id = '<?= $data['Sub_Course_ID'] ?>'
        $.ajax({
            type: 'POST',
            url: '/app/subjects/get-duration',
            data: {
                sub_course_id: sub_course_id,
                edit: "edit",
            },
            success: function (response) {
                $('#seme').html(response);
                $('#seme').val('<?= $data['Semester'] ?>');
            },

        });
    }
</script>