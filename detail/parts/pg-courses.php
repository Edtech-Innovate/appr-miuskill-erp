<!-- Testimonial section start -->
<?php require('admin/includes/conn.php'); ?>

<?php

$courseData = [];
$courseQuery = $conn->query("SELECT courses.* FROM courses JOIN categories ON categories.ID = courses.Category_id WHERE courses.Status = 1
      AND categories.Categories_name = 'Postgraduate (PG)' ORDER BY courses.ID DESC ");
while ($course = $courseQuery->fetch_assoc()) {
    $courseData[] = $course;


}

// echo "<pre>";
// print_r($courseData);
// echo "</pre>";
// exit;

?>


<section class="testimonial-2 section-padding-top pb-70" style="background: #F4F9FF;">
    <div class="container">
        <div class="row ">
            <div class="col-12">
                <div class="section-heading mb-5 pb-3 mb-xl-0 ">
                    <span class="subheading">Courses</span>
                    <h2 class="font-lg mb-10"> PG Courses</h2>
                </div>
            </div>

            <!-- <div class="col-12">
                <div class="testimonials-slides-2 owl-carousel owl-theme">


                    <div class="testimonial-item">
                        <div class="course-grid tooltip-style border bg-white">
                            <div class="course-header">
                                <div class="course-thumb">
                                    <img src="assets/images/course/img_06.jpg" alt="course image" class="img-fluid">
                                </div>
                            </div>

                            <div class="course-content">
                                <h3 class="course-title mb-20">
                                    Data Competitive Strategy law & Organization
                                </h3>
                                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolores, molestiae... </p>
                            </div>

                            <div class="course-hover-content">
                                <h3 class="course-title mb-20 mt-30">
                                    Data Competitive Strategy law & Organization
                                </h3>

                                <p class="mb-20">
                                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Libero, culpa. At
                                    voluptatem autem ipsam deleniti
                                </p>
                                <a href="#" class="btn btn-grey btn-sm rounded">Join Now <i
                                        class="fal fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-item">
                        <div class="course-grid tooltip-style border bg-white">
                            <div class="course-header">
                                <div class="course-thumb">
                                    <img src="assets/images/course/img_06.jpg" alt="course image" class="img-fluid">
                                </div>
                            </div>

                            <div class="course-content">
                                <h3 class="course-title mb-20">
                                    Data Competitive Strategy law & Organization
                                </h3>
                                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolores, molestiae... </p>
                            </div>

                            <div class="course-hover-content">
                                <h3 class="course-title mb-20 mt-30">
                                    Data Competitive Strategy law & Organization
                                </h3>

                                <p class="mb-20">
                                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Libero, culpa. At
                                    voluptatem autem ipsam deleniti
                                </p>
                                <a href="#" class="btn btn-grey btn-sm rounded">Join Now <i
                                        class="fal fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-item">
                        <div class="course-grid tooltip-style border bg-white">
                            <div class="course-header">
                                <div class="course-thumb">
                                    <img src="assets/images/course/img_06.jpg" alt="course image" class="img-fluid">
                                </div>
                            </div>

                            <div class="course-content">
                                <h3 class="course-title mb-20">
                                    Data Competitive Strategy law & Organization
                                </h3>
                                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolores, molestiae... </p>
                            </div>

                            <div class="course-hover-content">
                                <h3 class="course-title mb-20 mt-30">
                                    Data Competitive Strategy law & Organization
                                </h3>

                                <p class="mb-20">
                                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Libero, culpa. At
                                    voluptatem autem ipsam deleniti
                                </p>
                                <a href="#" class="btn btn-grey btn-sm rounded">Join Now <i
                                        class="fal fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-item">
                        <div class="course-grid tooltip-style border bg-white">
                            <div class="course-header">
                                <div class="course-thumb">
                                    <img src="assets/images/course/img_06.jpg" alt="course image" class="img-fluid">
                                </div>
                            </div>

                            <div class="course-content">
                                <h3 class="course-title mb-20">
                                    Data Competitive Strategy law & Organization
                                </h3>
                                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolores, molestiae... </p>
                            </div>

                            <div class="course-hover-content">
                                <h3 class="course-title mb-20 mt-30">
                                    Data Competitive Strategy law & Organization
                                </h3>

                                <p class="mb-20">
                                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Libero, culpa. At
                                    voluptatem autem ipsam deleniti
                                </p>
                                <a href="#" class="btn btn-grey btn-sm rounded">Join Now <i
                                        class="fal fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->


            <div class="col-12">
                <div class="courses-names2 d-flex flex-wrap">

                    <?php foreach ($courseData as $courses): ?>
                    <div class="btn-container me-2 mb-2">
                        <button class="btn btn-primary rounded"><?= ($courses['Course_name']) ?></a>


                    </div>

                    <!-- <div>
                        <p>

                            <?= (strlen($courses['Content']) > 80 ? substr($courses['Content'], 0, 80) . '...' : $courses['Content']) ?>
                        </p>
                    </div> -->
                    <?php endforeach; ?>
                    <!-- <div class="btn-container me-2 mb-2">
                        <button class="btn btn-primary rounded">M.Tech - CSE</button>
                    </div> -->
                    <!-- <div class="btn-container me-2 mb-2">
                        <button class="btn btn-primary rounded">M.Tech - AIML</button>
                    </div> -->
                    <!-- <div class="btn-container me-2 mb-2">
                        <button class="btn btn-primary rounded">M.Tech - IT</button>
                    </div> -->
                    <!-- <div class="btn-container me-2 mb-2">
                        <button class="btn btn-primary rounded">M.Tech - Cyber Security</button>
                    </div> -->
                    <!-- <div class="btn-container me-2 mb-2">
                        <button class="btn btn-primary rounded">MCA </button>
                    </div> -->
                    <!-- <div class="btn-container me-2 mb-2">
                        <button class="btn btn-primary rounded">M.Com</button>
                    </div> -->

                    <!-- <div class="btn-container me-2 mb-2">
                        <button class="btn btn-primary rounded">MBA</button>
                    </div> -->
                    <!-- <div class="btn-container me-2 mb-2">
                        <button class="btn btn-primary rounded">MA</button>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Testimonial section End -->