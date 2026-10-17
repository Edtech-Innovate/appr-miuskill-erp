<?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI'])); ?>

<!-- BEGIN SIDEBPANEL-->
<nav class="page-sidebar" data-pages="sidebar">
  <!-- BEGIN SIDEBAR MENU HEADER-->
  <div class="sidebar-header">
    <?php if(isset($_SESSION['university_logo'])){ ?>
      <img src="<?= $_SESSION['university_logo'] ?>" alt="logo" class="brand" data-src="<?= $_SESSION['university_logo'] ?>" data-src-retina="<?= $_SESSION['university_logo'] ?>" height="50">
    <?php } ?>
  </div>
  <!-- END SIDEBAR MENU HEADER-->
  <?php
    if($_SESSION['Designation']=='University'){
        include('navigation/university.php');
    }
    if($_SESSION['Role']=='Sub-Center'){
      include('navigation/sub-center.php');
    }else if($_SESSION['Role']=='Center'){
      include('navigation/center.php');
    }else if($_SESSION['Role']=='Sub-Counsellor'){
      include('navigation/sub-counsellor.php');
    }else if($_SESSION['Role']=='Counsellor'){
      include('navigation/counsellor.php');
    }elseif($_SESSION['Role']=='University Head' || $_SESSION['Role']=='Operations'){
      include('navigation/head.php');
    }elseif($_SESSION['Role']=='Accountant'){
      include('navigation/accountant.php');
    }if($_SESSION['Role']=='Student'){
      include('navigation/student.php');
    }elseif($_SESSION['Role']=='Administrator'){
      include('navigation/admin.php');
    }elseif($_SESSION['Role']=='Academic Head'){
      include('navigation/academic-head.php');
    }elseif($_SESSION['Role']=='Academics Internal'){
      include('navigation/academics-internal.php');
    }elseif($_SESSION['Role']=='Inhouse-Counsellor'){
         include('navigation/inhouse-counsellor.php');
    }
  ?>
</nav>
<!-- END SIDEBAR -->
<!-- END SIDEBPANEL-->
