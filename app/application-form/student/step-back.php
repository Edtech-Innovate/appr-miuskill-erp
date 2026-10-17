<?php
if (isset($_POST)) {
  session_start();
  $_SESSION['Step'] = $_SESSION['Step'] - 1;
}
