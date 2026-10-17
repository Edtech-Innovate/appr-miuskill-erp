<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<link rel="stylesheet" href="/assets/css/new-style.css" />
<link rel="stylesheet" href="/assets/css/themify-icons/themify-icons.css" />
</script>
<link href="/assets/css/tabs.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    .subject_name {
        font-size: 18px !important;
        font-weight: 600;
    }

    .card.info-box.p-0 {
        box-shadow: unset !important;
        margin-bottom: 14px !important;
    }

    .stu-e-book-style {
        border-radius: 10px !important;
        height: 164px !important;
    }

    p.picon {
        padding-top: 9px !important;
    }

    .card-box1 {
        display: flex;
        gap: 14px;
    }

    .bg-yellow-gradient {
        background: #f39c12 !important;
    }

    .bg-purple-gradient {
        background: #605ca8 !important;
    }

    .bg-green-gradient {
        background: #00a65a !important;
    }

    .bg-red-gradient {
        background: #dd4b39 !important;
    }

    .bg-aqua-gradient {
        background: #008cd3 !important;
    }

    .bg-maroon-gradient {
        background: #f39c12 !important;
    }

    .bg-teal-gradient {
        background: #d81b60 !important;
    }

    .bg-blue-gradient {
        background: #39cccc !important;
    }

    .text-center.no_record {
        font-weight: 500 !important;
        font-size: 16px !important;
        padding: 11px !important;
    }
</style>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>
<!-- START PAGE-CONTAINER -->
<?php
$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/";


// $course_id=$_SESSION['Course_ID'];
$course_id = $_SESSION['Course_ID'];
$sub_course_id = $_SESSION['Sub_Course_ID'];

$student_id = $_SESSION['ID'];
$currentSem = $_SESSION['Duration'];	
$semesterArray = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
$searchQuery = '';
$mySyllabi = array();

    //  if ($_SESSION['university_id'] == 48) {
    //     if ($_SESSION['Duration'] == '11/advance-diploma') {
    //         $duration = '11/Advanced';
    //     } elseif ($_SESSION['Duration'] == '11/certified') {
    //         $duration = '11/certified';
    //     } elseif ($_SESSION['Duration'] == '6' && $_SESSION['Course_Category'] == 'certified') {
    //         $duration = '6/certified';
    //     } else {
    //         $duration = $_SESSION['Duration'];
    //     }
    // } else{
    //     $duration = $_SESSION['Duration'];
    //   }

if (isset($_GET['id'])) {
    $active = '';
    $mySyllabi[0] = $_GET['id'];
    $ids = $_GET['id'];
    $duration = $_GET['duration'];
} else {
  $ids = '';
    $active = "active";
    $Syllabi = "SELECT Sub_Courses.ID,Sub_Courses.Mode_Id,Sub_Courses.Min_Duration, Modes.Name as mode ,Syllabi.Name,Syllabi.ID as subject_id from Syllabi  LEFT JOIN Sub_Courses ON Syllabi.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Modes ON Sub_Courses.Mode_Id = Modes.ID  WHERE Syllabi.Sub_Course_ID = $course_id AND Syllabi.Semester='".$duration."'";
    $Syllabi = mysqli_query($conn, $Syllabi);
    $subjectData = array();
    while ($row = mysqli_fetch_assoc($Syllabi)) {
        $mySyllabi[] = $row['subject_id'];
        $subjectData[] = $row;
    }
}


 $query = "SELECT e_books.`id`, e_books.`file_type`, e_books.`file_path`,e_books.`title`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subject_name, e_books.`status` FROM e_books LEFT JOIN Sub_Courses ON Sub_Courses.ID = e_books.course_id LEFT JOIN Syllabi ON Syllabi.ID = e_books.subject_id WHERE e_books.subject_id IN ('" . implode("','", $mySyllabi) . "')  AND e_books.status =1 AND e_books.course_id=$course_id AND Syllabi.Semester='".$duration."'";

$results = mysqli_query($conn, $query);
$eBookData = array();
while ($row = mysqli_fetch_assoc($results)) {
    $eBookData[] = $row;
    // echo "<pre>"; print_r($eBookData);
}

$result_record = "SELECT video_lectures.`id`,video_lectures.`unit`,video_lectures.`description`,video_lectures.`semester`, video_lectures.`thumbnail_type`,video_lectures.`thumbnail_url`,video_lectures.`video_type`,video_lectures.`video_url`, Sub_Courses.`Name` as course_name, Sub_Courses.`Short_Name` as course_short_name, Syllabi.`Name` as subjects_name, video_lectures.`status` FROM video_lectures LEFT JOIN Sub_Courses ON Sub_Courses.ID = video_lectures.course_id LEFT JOIN Syllabi ON Syllabi.ID = video_lectures.subject_id WHERE video_lectures.subject_id IN ('" . implode("','", $mySyllabi) . "')  AND video_lectures.status =1  AND video_lectures.course_id=$course_id AND Syllabi.Semester='".$duration."' ";

$results_records = mysqli_query($conn, $result_record);
$videoData = array();
while ($rowArr = mysqli_fetch_assoc($results_records)) {
    $videoData[] = $rowArr;
}

?>


<?php // echo "<pre>";print_r($eBookData);  
?>
<div class="page-container ">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>
    <div class="page-content-wrapper ">
        <div class="content ">

            <div class="jumbotron" data-pages="parallax">
                <div class=" container-fluid sm-p-l-0 sm-p-r-0">
                    <div class="inner">
                        <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                            <?php
                            $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                            for ($i = 1; $i <= count($breadcrumbs); $i++) {
                                if (count($breadcrumbs) == $i) : $active = "active";
                                    $crumb = explode("?", $breadcrumbs[$i]);
                                    echo '<li class="breadcrumb-item ' . $active . '">' . $crumb[0] . '</li>';
                                endif;
                            }
                            ?>
                            <div>
                            </div>
                        </ol>

                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="card">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs nav-fill customtab2" role="tablist">
                            <li class="nav-item"> <a class="nav-link py-3 <?php echo (!isset($_GET['type']) || $_GET['type'] == '' || $_GET['type'] == 1) ? 'active' : ''; ?>" data-toggle="tab" href="#ebook" role="tab"><span class="hidden-sm-up"><i class="ti-agenda mr-2 h6"></i></span> <span class="h6 hidden-xs-down">Ebooks</span></a> </li>
                            <li class="nav-item"> <a class="nav-link py-3 <?php echo isset($_GET['type']) && $_GET['type'] == 2 ? 'active' : ''; ?>" data-toggle="tab" href="#video" role="tab"><span class="hidden-sm-up"><i class=" ti-video-clapper mr-2 h6"></i></span> <span class="h6 hidden-xs-down">Videos</span></a> </li>
                            <li class="nav-item"> <a class="nav-link py-3 <?php echo isset($_GET['type']) && $_GET['type'] == 4 ? 'active' : ''; ?>" data-toggle="tab" href="#notes" role="tab"><span class="hidden-sm-up"><i class="ti-write mr-2 h6"></i></span> <span class="h6 hidden-xs-down">Notes</span></a> </li>
                            <li class="nav-item"> <a class="nav-link py-3 <?php echo isset($_GET['type']) && $_GET['type'] == 3 ? 'active' : ''; ?>" data-toggle="tab" href="#subject" role="tab"><span class="hidden-sm-up"><i class="ti-write mr-2 h6"></i></span> <span class="h6 hidden-xs-down">Assessment</span></a> </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content tabcontent-border">
                            <div class="tab-pane <?php echo (!isset($_GET['type']) || $_GET['type'] == '' || $_GET['type'] == 1) ? 'active' : ''; ?>" id="ebook" role="tabpanel">
                                <div class="pad-20">
                                    <div class="row">
                                        <?php if (count($eBookData) > 0) {
                                            foreach ($eBookData as $eBook) {  ?>
                                                <div class="col-sm-6 col-md-3 mb-3 ">
                                                    <div class="card info-box p-0">
                                                        <div class="stu-e-book-style">
                                                            <p class="picon"><i class="uil uil-book-open e-book-icon"></i></p>
                                                            <p class="subject_name"><span><?php echo isset($eBook['title']) ? $eBook['title']: $eBook['subject_name']; ?></span></p>
                                                        </div>
                                                    </div>
                                                    <p class="mt-2 " style="text-align:center;"><a class="btn btn-dark" href="/student/lms/view-e-book?id=<?php echo $eBook['id']; ?>&sub_id=<?= $ids ?>">View </a></p>
                                                </div>
                                            <?php }
                                        } else { ?>
                                            <div class="col-md-12" id="student_e_books_not">
                                                <p class="text-center">No EBook Found !</p>
                                            </div>
                                        <?php }  ?>

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane p-20 <?php echo isset($_GET['type']) && $_GET['type'] == 2 ? 'active' : ''; ?>" id="video" role="tabpanel">
                                <div class="">
                                    <div class="row">
                                        <?php if ($results_records->num_rows > 0) { ?>
                                            <div class="col-md-8 border">
                                                <div class="video_box">
                                                    <video width="500" height="350" id="video_player" controls autopause controlsList="nodownload" src=" " type="video/"></video>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="list-group">
                                                    <?php
                                                    foreach ($videoData as $key => $value) {
                                                    ?>
                                                        <div class="card-box1">
                                                            <input type="checkbox" id="video<?= $key ?>" name="video" value="<?= $value['id'] ?>" onclick="getDataFunc('<?= $value['id'] ?>','<?= $value['video_url'] ?>'); uncheckOthers(this)">
                                                            <a href="#video<?= $key ?>" class="active_<?= $value['id'] ?> list-group-item list-group-item-action flex-column align-items-start">
                                                                <div class="d-flex w-100 justify-content-between align-items-center">
                                                                    <div>
                                                                        <input type="hidden" id="video_url_<?= $value['id'] ?>" name="video_url" value="<?= $value['video_url'] ?>">
                                                                        <h5 class="mb-0"><?= ucwords($value['subjects_name']) ?> : </b><?= ucwords($value['unit']) ?></h5>
                                                                        <small><i class="ti-timer mr-1"></i>45min</small>
                                                                    </div>
                                                                    <i class="fa fa-play-circle fa-lg"></i>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    <?php  }  ?>

                                                </div>
                                            </div><?php } else { ?>
                                            <div class="col-md-12" id="student_e_books_not">
                                                <p class="text-center no_record">Video Not Uploaded ! </p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane p-20 <?php echo isset($_GET['type']) && $_GET['type'] == 4 ? 'active' : ''; ?>" id="notes" role="tabpanel">
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p  class="text-center no_record">Coming Soon...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane p-20 <?php echo isset($_GET['type']) && $_GET['type'] == 3 ? 'active' : ''; ?>" id="subject" role="tabpanel">
                                <div class="">
                                    <div class="row">
                                        <!-- <div class="col-md-4">
                                            <div class="list-group">
                                                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start active">
                                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                                        <div>
                                                            <h5 class="mb-0">Assessment Name</h5>
                                                            <small><i class="ti-timer mr-1"></i>Dec 1, 2023</small>
                                                        </div>
                                                        <span class="badge bg-success"><i class="fa fa-check"></i> Completed</span>
                                                    </div>
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                                        <div>
                                                            <h5 class="mb-0">Assessment Name</h5>
                                                            <small><i class="ti-timer mr-1"></i>Dec 1, 2023</small>
                                                        </div>
                                                        <i class="fa fa-play-circle fa-lg"></i>
                                                    </div>
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start ">
                                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                                        <div>
                                                            <h5 class="mb-0">Assessment Name</h5>
                                                            <small><i class="ti-timer mr-1"></i>Dec 1, 2023</small>
                                                        </div>
                                                        <i class="fa fa-play-circle fa-lg"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-8 border">
                                        </div> -->
                                        <div class="col-md-12">
                                            <p  class="text-center no_record">Coming Soon...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT -->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script>
        var gradientArray = ['bg-yellow-gradient', 'bg-purple-gradient', 'bg-green-gradient', 'bg-red-gradient', 'bg-aqua-gradient',
            'bg-maroon-gradient', 'bg-teal-gradient', 'bg-blue-gradient'
        ];

        $(document).ready(function() {
            $('.card-img-top').each(function(index) {
                $(this).addClass(gradientArray[index % gradientArray.length]);
            })
            $('.stu-e-book-style').each(function(index) {
                $(this).addClass(gradientArray[index % gradientArray.length]);
            })
        });
    </script>
    <script>
        function uncheckOthers(checkbox) {
            var checkboxes = document.querySelectorAll('input[type=checkbox][name=video]');
            checkboxes.forEach(function(element) {
                if (element !== checkbox) {
                    element.checked = false;
                }
            });
        }
    </script>
    <script type="text/javascript">
        function semesterFilter(semester) {
            getSubjectsForSemester(semester);
            $.ajax({
                url: '/student/lms/e-books-filter',
                type: 'POST',
                dataType: 'text',
                data: {
                    "semester": semester,
                    'course_id': "<?= $course_id ?>"
                },
                success: function(result) {
                    if (result != 0) {
                        $('#data_list').html(result);
                    } else {
                        $('#data_list').html('<div class="col-md-12"><h3 style="text-align: center;">Data not available!</h3></div>');
                    }
                }
            })
        }

        function getSubjectsForSemester(semester) {
            $.ajax({
                url: '/student/lms/semester-filter',
                type: 'POST',
                dataType: 'text',
                data: {
                    "semester": semester,
                    'course_id': "<?= $course_id ?>"
                },
                success: function(result) {
                    if (result != 0) {
                        $('#subject_dropdown').html(result);
                    }
                }
            })
        }

        function subjectFilter(subject_id) {
            $.ajax({
                url: '/student/lms/e-books-filter',
                type: 'POST',
                dataType: 'text',
                data: {
                    "subject_id": subject_id,
                    'course_id': "<?= $course_id ?>"
                },
                success: function(result) {
                    if (result != 0) {
                        $('#data_list').html(result);
                    } else {
                        $('#data_list').html('<div class="col-md-12"><h3 style="text-align: center;">Data not available!</h3></div>');
                    }
                }
            })
        }
    </script>
    <script>
        $(document).ready(function() {
            $(".video_box").hide();
            $(".video_box").show();
            let video_url = $("#video_url_<?= $videoData[0]['id'] ?>").val();
            getDataFunc(id = null, "../../" + video_url)
        })

        function getDataFunc(id, video_url) {
            $(".video_box").show();
            $("#video_player").attr('src', "../../" + video_url);
            $(".active_" + id + " .list-group-item").addClass('active');
            var targetTab = $(this).attr('href');
            $(targetTab).addClass('active');
        }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>