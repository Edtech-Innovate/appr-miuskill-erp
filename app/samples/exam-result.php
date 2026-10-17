<?php
  require ('../../extras/vendor/shuchkin/simplexlsxgen/src/SimpleXLSXGen.php');

  $header[] = array('Course', 'Sub-Course','Enrollment Number', 'Subject Code', 'Obtained External Marks', 'Obtained Internal Marks', 'Semester', 'Exam Month','Exam Year','Paper Code');
  $xlsx = SimpleXLSXGen::fromArray( $header )->downloadAs('Results Sample-'.time().'.xlsx');
