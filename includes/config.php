<?php  
  if(in_array($_SERVER['HTTP_HOST'],["b2b.vidyaplanet.in","partner.vidyaplanet.in"])){
    $app_title = "Admission Portal";
    $organization_name = "Vidya Planet";
    $default_center_code_suffix = "VP_";
    $logged_in_users = "";
    $dark_logo = "/assets/img/university/1739959794.png";
    $dark_logo_retina = "/assets/img/vp/logo_2x.png";
    $light_logo = "/assets/img/vp/logo_white.png";
    $light_logo_retina = "/assets/img/vp/logo_white_2x.png";
    $login_cover = "/assets/img/vp/cover.jpg";
  }elseif($_SERVER['HTTP_HOST']=="lmslogin.sikkimalpineuniversity.edu.in"){
    $app_title = "Sikkim Alpine University";
    $organization_name = "Sikkim Alpine University";
    $logged_in_users = " AND Code LIKE 'SAU/%'";
    $default_center_code_suffix = "VP_";
    $dark_logo = "/assets/img/university/1739959794.png";
    $dark_logo_retina = "/assets/img/sikkim/logo.png";
    $light_logo = "/assets/img/sikkim/logo_white.png";
    $light_logo_retina = "/assets/img/sikkim/logo_white.png";
    $login_cover = "/assets/img/sikkim/cover.jpg";
  }else{
    $app_title = "MIU";
    $organization_name = "MIU";
    $logged_in_users = "";
    $userNamePrefix = "MIU";
    $dark_logo = "/assets/img/university/1776226178.png";
    $dark_logo_retina = "/assets/img/university/1776226178.png";
    $light_logo = "/assets/img/university/1776226178.png";
    $light_logo_retina = "/assets/img/university/1776226178.png";
    // $login_cover = "/assets/img/university/1739959794.png";
    $favicon = "/assets/img/university/1776226178.png";
    $apple_touch_favicon = "/assets/img/university/1776226178.png";
  }
