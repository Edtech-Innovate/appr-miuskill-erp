
  <!-- START SIDEBAR MENU -->
  <div class="sidebar-menu">
    <!-- BEGIN SIDEBAR MENU ITEMS-->
    <ul class="menu-items">

      <!-- Single Menu -->
      <li class="m-t-20 ">
        <a href="/dashboard" class="detailed">
          <span class="title">Dashboard</span>
          <span class="details">No New Updates</span>
        </a>
        <span class="icon-thumbnail-main"><i class="uil uil-home"></i></span>
      </li>

      <!-- Multi Menu -->
      <li class="<?php print $breadcrumbs[1] == 'academics' ? 'open active' : '' ?>">
        <a href="javascript:;"><span class="title">Academics</span>
          <span class=" arrow <?php print $breadcrumbs[1] == 'academics' ? 'open active' : '' ?>"></span></a>
        <span class="icon-thumbnail-main"><i class="uil uil-graduation-hat"></i></span></span>
        <ul class="sub-menu">
          <li class="">
            <a href="/academics/departments">Departments</a>
            <span class="icon-thumbnail"><i class="pg-icon">Dp</i></span>
          </li>
          <li class="">
            <a href="/academics/programs">Programs</a>
            <span class="icon-thumbnail"><i class="pg-icon">Co</i></span>
          </li>
          <li class="">
            <a href="/academics/specializations">Specializations</a>
            <span class="icon-thumbnail"><i class="pg-icon">SC</i></span>
          </li>
          <li class="">
            <a href="/academics/syllabus">Syllabus</a>
            <span class="icon-thumbnail"><i class="pg-icon">SL</i></span>
          </li>
        </ul>
      </li>

      <?php if (isset($_SESSION['has_lms']) && $_SESSION['has_lms'] == 1) { ?>
        <li class="<?php print $breadcrumbs[1] == 'lms-settings' ? 'open active' : '' ?>">
          <a href="javascript:;"><span class="title">LMS Settings</span>
            <span class=" arrow <?php print $breadcrumbs[1] == 'lms-settings' ? 'open active' : '' ?>"></span></a>
          <span class="icon-thumbnail-main"><i class="uil uil-book-open"></i></span></span>
          <ul class="sub-menu">
            <li class="">
              <a href="/lms-settings/subjects">Subjects</a>
              <span class="icon-thumbnail"><i class="pg-icon">Sb</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/datesheets">Date Sheets</a>
              <span class="icon-thumbnail"><i class="pg-icon">DS</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/assignments">Assignments</a>
              <span class="icon-thumbnail"><i class="pg-icon">As</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/practicals">Practicals</a>
              <span class="icon-thumbnail"><i class="pg-icon">Pr</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/notifications">Notifications</a>
              <span class="icon-thumbnail"><i class="pg-icon">Nt</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/mock-tests">Mock Test</a>
              <span class="icon-thumbnail"><i class="pg-icon">Mt</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/exams">Exam</a>
              <span class="icon-thumbnail"><i class="pg-icon">Ex</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/results">Results</a>
              <span class="icon-thumbnail"><i class="pg-icon">AC</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/queries-&-feedback">Queries & Feedback</a>
              <span class="icon-thumbnail"><i class="pg-icon">QF</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/e-books">E-Books</a>
              <span class="icon-thumbnail"><i class="pg-icon">EB</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/videos">Videos</a>
              <span class="icon-thumbnail"><i class="pg-icon">Vi</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/dispatch">Dispatch</a>
              <span class="icon-thumbnail"><i class="pg-icon">Dt</i></span>
            </li>
            <li class="">
              <a href="/lms-settings/contact-us">Contact Us</a>
              <span class="icon-thumbnail"><i class="pg-icon">Co</i></span>
            </li>
          </ul>
        </li>
      <?php } ?>

      <li class="m-t-20">
        <a href="#" class="detailed">
          <span class="title">HR & Payroll</span>
          <span class="details">Coming Soon</span>
        </a>
        <span class="icon-thumbnail-main"><i class="uil uil-briefcase-alt"></i></span>
      </li>

      <li class="m-t-0">
        <a href="#" class="detailed">
          <span class="title">Support</span>
          <span class="details">Coming Soon</span>
        </a>
        <span class="icon-thumbnail-main"><i class="uil uil-phone-alt"></i></span>
      </li>

    </ul>
    <div class="clearfix"></div>
  </div>
  <!-- END SIDEBAR MENU -->
