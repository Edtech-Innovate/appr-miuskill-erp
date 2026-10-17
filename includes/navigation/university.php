
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

      <li class="<?php print $breadcrumbs[1]=='admissions' ? 'open active' : '' ?>">
        <a href="javascript:;"><span class="title">Admissions</span>
        <span class=" arrow <?php print $breadcrumbs[1]=='admissions' ? 'open active' : '' ?>"></span></a>
        <span class="icon-thumbnail-main"><i class="uil uil-book-reader"></i></span></span>
        <ul class="sub-menu">
          
          <li class="">
            <a href="/admissions/applications">Applications</a>
            <span class="icon-thumbnail"><i class="pg-icon">AP</i></span>
          </li>
          <?php if($_SESSION['Designation'] = "University"){ ?>
          <li class="">
            <a href="/admissions/upload-marksheet">Marksheets</a>
            <span class="icon-thumbnail"><i class="pg-icon">M</i></span>
          </li>
          <?php } ?>
        </ul>
      </li>


    </ul>
    <div class="clearfix"></div>
  </div>
  <!-- END SIDEBAR MENU -->
