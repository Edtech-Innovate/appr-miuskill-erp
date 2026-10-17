
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

 <li class="m-t-20 <?php print $breadcrumbs[1] == 'users' ? 'open active' : '' ?>">
        <a href="javascript:;"><span class="title">Users</span>
          <span class=" arrow <?php print $breadcrumbs[1] == 'users' ? 'open active' : '' ?>"></span></a>
        <span class="icon-thumbnail-main"><i class="uil uil-users-alt"></i></span></span>
        <ul class="sub-menu">
          <li class="">
            <a href="/users/vertical-heads">Vertical Heads</a>
            <span class="icon-thumbnail"><i class="pg-icon">VH</i></span>
          </li>
          <li class="">
            <a href="/users/academic-heads">Academic Heads</a>
            <span class="icon-thumbnail"><i class="pg-icon">AH</i></span>
          </li>
          <li class="">
            <a href="/users/operations">Operations</a>
            <span class="icon-thumbnail"><i class="pg-icon">Ot</i></span>
          </li>
          <li class="">
            <a href="/users/internal-team">Counsellor</a>
            <span class="icon-thumbnail"><i class="pg-icon">CO</i></span>
          </li>
          <li class="">
            <a href="/users/national-coordinators">Sub-Counsellor</a>
            <span class="icon-thumbnail"><i class="pg-icon">Sc</i></span>
          </li>
          <li class="">
            <a href="/users/regional-coordinator-master">Center Master</a>
            <span class="icon-thumbnail"><i class="pg-icon">RC</i></span>
          </li>
          <li class="">
            <a href="/users/regional-coordinators">Center</a>
            <span class="icon-thumbnail"><i class="pg-icon">Ce</i></span>
          </li>
          <li class="">
            <a href="/users/igcs">Sub-Center</a>
            <span class="icon-thumbnail"><i class="pg-icon">Sc</i></span>
          </li>
          <li class="">
            <a href="/users/accountants">Accountants</a>
            <span class="icon-thumbnail"><i class="pg-icon">AC</i></span>
          </li>
          <li class="">
            <a href="/users/academics-internal">Academics Internal</a>
            <span class="icon-thumbnail"><i class="pg-icon">AI</i></span>
          </li>
        </ul>
      </li>

    </ul>
    <div class="clearfix"></div>
  </div>
  <!-- END SIDEBAR MENU -->
