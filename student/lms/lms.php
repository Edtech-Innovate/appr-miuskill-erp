<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link rel="stylesheet" href="/assets/css/new-style.css" />
<link rel="stylesheet" href="/assets/css/themify-icons/themify-icons.css" />
<style>
    .profile_img {
        width: 150px;
        height: 150px;
        object-fit: fill;
        margin: 10px auto;
        border: 5px solid #ccc;
        border-radius: 50%;
    }

    .subject-name {
        font-size: 20px !important;
        padding: 50px 20px !important;
    }
</style>
<link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.5.0/viewer.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.5.0/viewer.js"></script>
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
                        <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                            <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                            for ($i = 1; $i <= count($breadcrumbs); $i++) {
                                if (count($breadcrumbs) == $i):
                                    $active = "active";
                                    $crumb = explode("?", $breadcrumbs[$i]);
                                    echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                                endif;
                            }
                            ?>
                            <div>

                            </div>
                        </ol>
                        <!-- END BREADCRUMB -->
                    </div>
                </div>
            </div>
            <!-- END JUMBOTRON -->
            <?php $duration = $_SESSION['Duration'];
            // echo "<pre>"; print_r($_SESSION);
            
            ?>

            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-center">
                        
                            <div class="col-md-6">
                                <div class="form-group form-group-default required">
                                    <label>Semester</label>
                                    <select class="full-width" style="border: transparent;" id="semester"
                                        onchange="getTable()">
                                        <option value="">Choose</option>
                                    </select>
                                </div>
                            </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="row all_subject">
                        <?php
                        $sub_counts = $conn->query("SELECT Syllabi.Name AS subject,ID FROM Syllabi WHERE Course_ID='" . $_SESSION['Course_ID'] . "' AND Sub_Course_ID='" . $_SESSION['Sub_Course_ID'] . "' AND Semester = '" . $_SESSION['Duration'] . "' AND University_ID ='" . $_SESSION['university_id'] . "'");

                        if ($sub_counts->num_rows == 0) {
                            ?>
                            <div class="col-md-12" id="student_e_books_not">
                                <p class="text-center">Please Select Semesters</p>
                            </div>
                        <?php } else {
                            while ($subResArr = $sub_counts->fetch_assoc()) {
                                // echo "<pre>";
                                // print_r($subResArr);

                                ?>
                                <div class="col-md-3">
                                    <div class="card info-box p-0">
                                        <a
                                            href="/student/lms/subjects?id=<?= $subResArr['ID'] ?>&type=1&duration=<?= $duration ?>">
                                            <div class="card-img-top">
                                                <p class="subject-name"><?= $subResArr['subject']; ?></p>
                                            </div>
                                        </a>
                                        <div class="card-footer">
                                            <div class="row justify-content-between align-items-center">

                                                <?php
                                                $ebook_query = $conn->query("SELECT id FROM e_books WHERE subject_id = '" . $subResArr['ID'] . "' AND Course_ID='" . $_SESSION['Course_ID'] . "'  AND Sub_Course_ID='" . $_SESSION['Sub_Course_ID'] . "' and Status = 1");
                                                $ebook_count = ($ebook_query->num_rows > 0) ? $ebook_query->num_rows : 0;

                                                $video_query = $conn->query("SELECT id FROM video_lectures WHERE subject_id = '" . $subResArr['ID'] . "' AND Course_ID='" . $_SESSION['Course_ID'] . "'  AND Sub_Course_ID='" . $_SESSION['Sub_Course_ID'] . "' and Status = 1");
                                                $video_count = ($video_query->num_rows > 0) ? $video_query->num_rows : 0;
                                                ?>
                                                <div class="col-md-4 text-center">
                                                    <a
                                                        href="/student/lms/subjects?id=<?= $subResArr['ID'] ?>&type=1&duration=<?= $duration ?>"><i
                                                            class="ti-book mr-2"></i><span><?= $ebook_count; ?></span></a>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <a
                                                        href="/student/lms/subjects?id=<?= $subResArr['ID'] ?>&type=2&duration=<?= $duration ?>"><i
                                                            class="ti- ti-video-clapper mr-2"></i><span><?= $video_count; ?></span></a>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <a
                                                        href="/student/lms/subjects?id=<?= $subResArr['ID'] ?>&type=3&duration=<?= $duration ?>"><i
                                                            class=" ti-clipboard mr-2"></i><span>0</span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } ?>
                    </div>
                    <div class="row student_e_books" id="student_e_books">
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script>
        var gradientArray = ['bg-yellow-gradient', 'bg-purple-gradient', 'bg-green-gradient', 'bg-red-gradient', 'bg-aqua-gradient',
            'bg-maroon-gradient', 'bg-teal-gradient', 'bg-blue-gradient'
        ];

        $(document).ready(function () {
            $('.card-img-top').each(function (index) {
                $(this).addClass(gradientArray[index % gradientArray.length]);
            })
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            getSemester('<?= $_SESSION['Sub_Course_ID'] ?>', val = "onload");
        })
        function getSemester(id, val = null) {

            $.ajax({
                url: '/app/subjects/semester?id=' + id + "&onload=" + val,
                type: 'GET',
                success: function (data) {
                    $("#semester").html(data);
                }
            })
        }


    </script>

    <script type="text/javascript">
        function getTable() {

            $('#student_e_books_not').hide();
            var course_id = '<?= $_SESSION['Sub_Course_ID'] ?>';
                var semester = $('#semester').val();
           
            if (course_id.length > 0 && semester.length > 0) {
                $.ajax({
                    url: '/app/subjects/syllabus?course_id=' + course_id + '&semester=' + semester + '&lms=lms',
                    type: 'GET',
                    success: function (data) {
                        $('.student_e_books').html(data);
                        var selected = $('#semester option:selected').text();
                        $(".all_subject").hide();


                    }
                })
            } else {
                $('#student_e_books').html('');
                $(".all_subject").show();
            }
        }

        function removeTable() {
            $('#student_e_books').html('');
            $(".all_subject").show();
        }
    </script>
    <script>
        function E_bookList(id, unit_id, sub_id,) {
            $.ajax({
                url: '/app/e-books/students/show-list',
                type: 'POST',
                data: {
                    "id": id,
                    "sub_id": sub_id,
                    "unit_id": unit_id
                },
                success: function (data) {
                    $("#lg-modal-content").html(data);
                    $("#lgmodal").modal('show');
                }
            })
        }
    </script>
    <script>
        function addAssessment(id, unit_id, sub_id) {
            $.ajax({
                url: '/app/e-books/assessments/create?unit_id=' + unit_id + '&syllabus_id=' + sub_id + '&ebook_id=' + id,
                type: 'GET',
                success: function (data) {
                    $("#md-modal-content").html(data);
                    $("#mdmodal").modal('show');
                }
            })
        }
    </script>
    <script>
        function assessmentList(assessment_id, ebook_id, unit_id, sub_id) {
            $.ajax({
                url: '/app/e-books/students/assessments/show-list',
                type: 'POST',
                data: {
                    "assessment_id": assessment_id,
                    "ebook_id": ebook_id,
                    "sub_id": sub_id,
                    "unit_id": unit_id
                },
                success: function (data) {
                    $("#md-modal-content").html(data);
                    $("#mdmodal").modal('show');
                }
            })
        }
    </script>

    <script>
        function startAssessment(assessment_id, e_book_id, unit_id, suject_id) {
            $('.modal').modal('hide');
            $.ajax({
                url: '/app/e-books/students/assessments/store',
                type: 'POST',
                data: {
                    "student_id": <?= $_SESSION['ID'] ?>,
                    "assessment_id": assessment_id,
                    "e_book_id": e_book_id,
                    "suject_id": suject_id,
                    "unit_id": unit_id
                },
                success: function (data) {
                    // $("#md-modal-content").html(data);
                    // $("#mdmodal").modal('show');
                    if (data.status == 200) {
                        notification('success', data.message);
                        localStorage.setItem('inserted_id', data.id);
                    } else {
                        notification('error', data.message);
                        $('#previous-button').click();
                    }
                },
                complete: function () {
                    window.location.href = "/student/examination/online-exam/start-exams";
                }
            });
        }
    </script>


    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>