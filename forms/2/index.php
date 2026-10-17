<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
      <?php
        require '../../includes/db-config.php';
        $student_id = $_GET['student_id'];
        $id = base64_decode($student_id);
        $id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
        $get_record = $conn->query("SELECT * FROM Students WHERE ID = $id");
        $st = mysqli_fetch_assoc($get_record);
        $get_course = $conn->query("SELECT * FROM Courses WHERE ID = '".$st['Course_ID']."'");
        $gc = mysqli_fetch_assoc($get_course);
        $get_sub_course = $conn->query("SELECT * FROM Sub_Courses WHERE ID = '".$st['Sub_Course_ID']."'");
        $gsc = mysqli_fetch_assoc($get_sub_course);
        $get_university = $conn->query("SELECT * FROM Universities WHERE ID = '".$gc['University_ID']."'");
        $gu = mysqli_fetch_assoc($get_university);
      ?>
      <!--
         <style media="print">
          @page {
           size: auto;
           margin: 5px;
                }
         	   
         </style> -->
      <style>	
         img {
         -webkit-print-color-adjust: exact;
         }
         .title-par{
         PADDING-RIGHT: 3px;
         PADDING-LEFT: 3px;
         FONT-SIZE: 14px;
         COLOR: #000000;
         LINE-HEIGHT: 17px;
         FONT-FAMILY: arial;
         text-align: left;
         text-decoration: none;
         padding-top:5px;
         }
         .board {
         FONT-WEIGHT: normal;
         border-collapse:collapse;
         color: #666666;
         border-right-style: solid;
         border-left-style: solid;
         border-right-color: #d1d1d1;
         border-left-color: #0066CC;
         border-right-width: 1px;
         border-left-width: 1px;
         text-decoration: none;
         padding-top: 2px;
         padding-right: 2px;
         padding-bottom: 2px;
         padding-left: 2px;
         text-align: justify;
         font-family: "kruti Dev 020";
         FONT-FAMILY: Arial, Helvetica, sans-serif;
         TEXT-DECORATION: none;
         border: 1px solid #d1d1d1;
         font-size: 14px;
         color: #333333;
         line-height: 20px;
         padding: 5px;
         }
         .imgpad {
         padding-top:5px;
         padding-right: 5px;
         padding-bottom: 5px;
         padding-left: 5px;
         line-height: 17px;
         text-decoration: none;
         padding-top: 5px;
         }
         .pahragraph {
         PADDING-RIGHT: 3px;
         PADDING-LEFT: 3px;
         FONT-SIZE: 12px;
         PADDING-BOTTOM: 3px;
         FONT-SIZE: 12px;
         COLOR: #000000;
         LINE-HEIGHT: 17px;
         FONT-FAMILY: arial;
         text-align: justify;
         padding-top: 5px;
         border: 1px solid #EDEDED;
         }
         .text {
         FONT-WEIGHT: normal;
         MARGIN-TOP: 10px;
         FONT-SIZE: 11px;
         COLOR: #000000;
         LINE-HEIGHT: 17px;
         FONT-FAMILY: arial;
         text-decoration: none;
         }
         .formbox {
         PADDING-BOTTOM: 5px;
         LINE-HEIGHT: 125%;
         PADDING-TOP: 5px;
         background-color: #FFFFFF;
         }
         .formfield {
         PADDING-RIGHT: 2px;
         PADDING-LEFT: 2px;
         PADDING-BOTTOM: 2px;
         COLOR: #CCCCCC;
         PADDING-TOP: 2px;
         BACKGROUND-COLOR: #FFFFFF;
         font-size: 12px;
         font-family: Arial, Helvetica, sans-serif;
         }
         .thead{
         FONT-WEIGHT: bold;
         FONT-SIZE: 14px;
         COLOR: #333333;
         LINE-HEIGHT: 17px;
         FONT-FAMILY: arial;
         text-align: center;
         margin: 10px;
         }
         .login{
         FONT-WEIGHT: normal;
         MARGIN-TOP: 10px;
         FONT-SIZE: 14px;
         COLOR: #336699;
         LINE-HEIGHT: 0px;
         FONT-FAMILY: arial;
         text-align: left;
         padding-left: 5px;
         text-decoration: none;
         }
         A.login:hover{
         text-decoration: underline;
         }
         .topmargin{
         margin-top:5px;
         margin-left:15px;
         background-color:#F2F2F2;
         width:970px;
         }
         .lang {
         FONT-SIZE: 11px;
         COLOR: #0000CC;
         FONT-FAMILY: arial, Verdana, Arial;
         text-decoration: none;
         }
         .hamaraup{
         margin:0 auto;
         background-color:#FFFFFF;
         height:80px;
         width:970px;
         }
         .pages {
         FONT-WEIGHT: bold; FONT-SIZE: 14px; COLOR: #044482; FONT-FAMILY: tahoma; TEXT-DECORATION: underline;
         }
         .HR{
         FONT-WEIGHT: normal;
         FONT-SIZE: 5px;
         COLOR: #999999;
         FONT-FAMILY: Arial, Helvetica, sans-serif;
         TEXT-DECORATION: none;
         padding: 1px;
         }
         .fields {
         PADDING-BOTTOM: 5px;
         LINE-HEIGHT: 100%;
         PADDING-TOP: 5px;
         background-color: #FFFFFF;
         }
         .more {
         FONT-SIZE: 11px;
         COLOR: #0066CC;
         FONT-FAMILY: Arial, Helvetica, sans-serif;
         TEXT-DECORATION: none;
         padding-top: 3px;
         padding-bottom: 3px;
         font-weight: bold;
         }
         A.more:hover {
         TEXT-DECORATION: underline
         }
         .borderline {
         FONT-WEIGHT: normal;
         border-collapse:collapse
         COLOR;
         color: #000000;
         font-family: Arial, Helvetica, sans-serif;
         text-decoration: none;
         font-size: 10px;
         FONT-FAMILY: Arial, Helvetica, sans-serif;
         TEXT-DECORATION: none;
         border: 1px solid #EAEAEA;
         font-size: 14px;
         color: #333333;
         line-height: 20px;
         padding: 4px;
         }
         .border {
         FONT-WEIGHT: normal;
         border-collapse:collapse;
         color: #666666;
         border-right-style: solid;
         border-left-style: solid;
         border-right-color: #0066CC;
         border-left-color: #0066CC;
         border-right-width: 1px;
         border-left-width: 1px;
         text-decoration: none;
         padding-top: 2px;
         padding-right: 2px;
         padding-bottom: 2px;
         padding-left: 2px;
         text-align: justify;
         font-family: "kruti Dev 020";
         FONT-FAMILY: Arial, Helvetica, sans-serif;
         TEXT-DECORATION: none;
         border: 1px solid #EAEAEA;
         font-size: 14px;
         color: #333333;
         line-height: 20px;
         }
         .tbborder {
         FONT-WEIGHT: normal;
         text-decoration: none;
         text-align: justify;
         font-family: "kruti Dev 020";
         FONT-FAMILY: Arial, Helvetica, sans-serif;
         TEXT-DECORATION: none;
         border: 1px solid #616060;
         font-size: 14px;
         color: #333333;
         line-height: 20px;
         padding: 2px;
         }
         .boldlink {
         PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: bold; FONT-SIZE: 14px; PADDING-BOTTOM: 0px; COLOR: #FFFFFF; PADDING-TOP: 0px; FONT-FAMILY: verdana; TEXT-DECORATION: none
         }
         .admintopnav {
         PADDING-RIGHT: 2px; line-height:23px; PADDING-LEFT: 2px; FONT-WEIGHT: bold; FONT-SIZE: 14px; PADDING-BOTTOM: 0px; COLOR: #FFFFFF; PADDING-TOP: 0px; FONT-FAMILY: arial; TEXT-DECORATION: none
         }
         A.admintopnav:hover {
         line-height:23px;FONT-WEIGHT: bold; FONT-SIZE: 14px; COLOR: #FFFFFF; FONT-FAMILY: arial; TEXT-DECORATION: none; background-color:#fff; color:blue; padding:4px;
         }
         .toplinks {
         PADDING-RIGHT: 5px; PADDING-LEFT: 5px; FONT-WEIGHT: bold; FONT-SIZE: 14px; PADDING-BOTTOM: 5px; COLOR: #FFFFFF; PADDING-TOP: 5px; FONT-FAMILY: verdana; TEXT-DECORATION: none
         }
         A.toplinks:hover {
         PADDING-RIGHT: 5px;
         PADDING-LEFT: 5px;
         FONT-WEIGHT: bold;
         FONT-SIZE: 14px;
         PADDING-BOTTOM: 5px;
         COLOR: #FFFFFF;
         PADDING-TOP: 5px;
         FONT-FAMILY: verdana;
         TEXT-DECORATION: underline;
         }
         A.boldlink:hover {
         COLOR: #000000; TEXT-DECORATION: underline
         }
         .footlinks {
         PADDING-RIGHT: 5px; PADDING-LEFT: 5px; FONT-WEIGHT: bold; FONT-SIZE: 10px; PADDING-BOTTOM: 5px; COLOR:#CC3300; PADDING-TOP: 5px; FONT-FAMILY: verdana; TEXT-DECORATION: none;
         }
         A.footlinks:hover {
         PADDING-RIGHT: 5px;
         PADDING-LEFT: 5px;
         FONT-WEIGHT: bold;
         FONT-SIZE: 14px;
         PADDING-BOTTOM: 5px;
         COLOR: #CB0C13;
         PADDING-TOP: 5px;
         FONT-FAMILY: verdana;
         TEXT-DECORATION: none;
         }
         A.sidelink:hover {
         TEXT-DECORATION: underline; COLOR: #BC2E31; 
         }
         .table-text {
         FONT-WEIGHT: normal;
         FONT-SIZE: 12px;
         COLOR: #FFFFFF;
         FONT-FAMILY: verdana;
         TEXT-DECORATION: none;
         padding: 5px;
         }
         .table {
         PADDING-RIGHT: 0px;
         PADDING-LEFT: 5px;
         FONT-WEIGHT: normal;
         FONT-SIZE: 12px;
         PADDING-BOTTOM: 0px;
         COLOR: #000000;
         PADDING-TOP: 0px;
         FONT-FAMILY: verdana;
         TEXT-DECORATION: none;
         border-right-width: 9px;
         border-left-width: 9px;
         border-right-style: solid;
         border-left-style: solid;
         border-right-color: #CC3333;
         border-left-color: #CC3333;
         }
         .marquee {
         FONT-WEIGHT: bold;
         FONT-SIZE: 16px;
         COLOR: #CCCCCC;
         FONT-FAMILY: verdana;
         TEXT-DECORATION: none;
         text-align: justify;
         padding-right: 4px;
         padding-left: 4px;
         padding-top: 4px;
         padding-bottom: 4px;
         }
         A.marquee:hover {
         FONT-WEIGHT: bold; FONT-SIZE: 12px; TEXT-DECORATION: underline
         }
         .sublink {
         FONT-WEIGHT: bold;
         FONT-SIZE: 12px;
         COLOR: #2C679F;
         FONT-FAMILY: verdana;
         text-decoration: none;
         padding:2px;
         margin-left:10px;
         }
         A.sublink:hover {
         TEXT-DECORATION: underline; 
         }
         .aageadhik {
         FONT-WEIGHT: normal;
         FONT-SIZE: 12px;
         COLOR: #2C679F;
         FONT-FAMILY: verdana;
         text-decoration: none;
         }
         .wel {
         FONT: 14px/20px arial;
         COLOR: #696a6a;
         padding-left:4px;
         }
         A.aageadhik:hover {
         TEXT-DECORATION: underline; 
         }
         .request{
         COLOR: #0000FF;
         TEXT-DECORATION: none;
         font-family: Verdana, Arial, Helvetica, sans-serif;
         font-size: 11px;
         text-align: center;
         padding-right: 8px;
         padding-bottom: 8px;
         font-weight: bold;
         }
         .feedback {
         PADDING-LEFT: 14px; FONT-WEIGHT: normal; FONT-SIZE: 11px; COLOR: #000000; FONT-FAMILY: tahoma; TEXT-DECORATION: none
         }
         .subdetail {
         PADDING-RIGHT: 5px; PADDING-LEFT: 5px; FONT-WEIGHT: normal; FONT-SIZE: 11px; PADDING-BOTTOM: 5px; COLOR: #747474; PADDING-TOP: 5px; FONT-FAMILY: tahoma; TEXT-DECORATION: none
         }
         .contentlink {
         FONT-WEIGHT: bold; FONT-SIZE: 10px; COLOR: #FFFFFF; FONT-FAMILY: verdana; TEXT-DECORATION: none
         }
         A.contentlink:hover {
         TEXT-DECORATION: underline
         }
         .visitedlink {
         FONT-WEIGHT: bold; FONT-SIZE: 12px; COLOR: #FFFFFF; FONT-FAMILY: verdana; TEXT-DECORATION: none
         }
         A.visitedlink:hover {
         COLOR: #FFFFFF; TEXT-DECORATION: underline
         }
         A.contentlink-bot:hover {
         COLOR: #ffffff;
         ;
         TEXT-DECORATION: underline;
         font-family: "kruti Dev 020";
         }
         .copyright {
         FONT-WEIGHT: normal;
         FONT-SIZE: 11px;
         PADDING-BOTTOM: 4px;
         COLOR: #666666;
         PADDING-TOP: 4px;
         FONT-FAMILY: verdana;
         TEXT-DECORATION: none;
         text-align: right;
         margin-right:5px;
         }
         .searching {
         FONT-WEIGHT: normal;
         FONT-SIZE: 11px;
         PADDING-BOTTOM: 4px;
         COLOR: #666666;
         PADDING-TOP: 4px;
         FONT-FAMILY: verdana;
         TEXT-DECORATION: none;
         text-align: right;
         margin-right:5px;
         }
         .txtbold {
         FONT-WEIGHT: bold; FONT-SIZE: 12px; COLOR: #333333; FONT-FAMILY: verdana; TEXT-DECORATION: none
         }
         .visited {
         FONT-WEIGHT: bold; FONT-SIZE: 13px; COLOR: #009933; FONT-FAMILY: Arial, Helvetica, sans-serif; TEXT-DECORATION: none;}
         .numbers {
         FONT-WEIGHT: normal;
         FONT-SIZE: 14px;
         COLOR: #ffffff;
         FONT-FAMILY: Arial, Helvetica, sans-serif;
         TEXT-DECORATION: none;
         background-color: #FFBABA;
         padding: 5px;
         border: 1px solid #CCCCCC;
         line-height:35px;
         }
         A.numbers:hover {
         FONT-WEIGHT: normal;
         FONT-SIZE: 14px;
         COLOR: #ffffff;
         FONT-FAMILY: Arial, Helvetica, sans-serif;
         TEXT-DECORATION: none;
         background-color: #C20F15;
         padding: 5px;
         border: 1px solid #CCCCCC;
         }
         A.txtbold:hover {
         COLOR: #ffffff;
         TEXT-DECORATION: none;
         background-color: #4688C8;
         }
         .txtline {
         FONT-WEIGHT: bold;
         FONT-SIZE: 12px;
         COLOR: #0033CC;
         FONT-FAMILY: Verdana, Arial, Helvetica, sans-serif;
         TEXT-DECORATION: underline;
         padding: 0px;
         }
         .email {
         FONT-SIZE: 12pt;
         COLOR: #FFFFFF;
         FONT-FAMILY: Verdana, Arial, Helvetica, arial;
         BACKGROUND-COLOR: #6b86a6;
         TEXT-DECORATION: none;
         font-weight: bold;
         margin-bottom:5px;
         line-height:30px;
         padding:3px;
         }
         .shershayri
         {
         font-family:'Conv_K013', Sans-Serif;
         text-align:left;
         font-size: 16px;
         color: #333333;
         text-decoration: none;
         border: 1px solid #CC3333;
         padding: 4px;
         }
         .DDlink
         {
         font-family:'Conv_K013', Sans-Serif;
         text-align:justify;
         font-size: 18px;
         font-weight: normal;
         text-decoration: none;
         }
         .state-marquee {
         text-align:center;
         font-size: 16px;
         color: #333333;
         text-decoration: none;
         font-style: normal;
         font-weight: normal;
         font-variant: normal;
         background-attachment: fixed;
         border: 1px solid #eaeaea;
         }
         .hindi-link
         {
         font-family:'Conv_K013', Sans-Serif;
         text-align:right;
         font-size: 12px;
         color: #FFFFFF;
         text-decoration: none;
         font-style: normal;
         font-weight: normal;
         font-variant: normal;
         background-attachment: fixed;
         line-height: 20px;
         padding: 5px;
         }
         A.hindi-link:hover
         {
         font-weight: bold;
         }
         .keyskill
         {
         font-family:arial;
         font-size: 14px;
         color: #000000;
         font-weight:bold;
         text-decoration:none;
         margin-top: 24px;
         margin-right: 0px;
         margin-bottom: 0px;
         margin-left: 15px;
         }
         .text-heading
         {
         font-family:'Conv_K013', Sans-Serif;
         text-align:justify;
         font-size: 24px;
         color: #000099;
         text-decoration: none;
         font-style: normal;
         font-weight: normal;
         font-variant: normal;
         background-attachment: fixed;
         padding: 8px;
         }
         .hindi-sublink
         {
         font-family:'Conv_K013', Sans-Serif;
         text-align:justify;
         font-size: 20px;
         color: #000099;
         text-decoration: underline;
         font-style: normal;
         font-weight: normal;
         font-variant: normal;
         background-attachment: fixed;
         padding: 8px;
         }
         .hindi-heading
         {
         font-family:'Conv_K013', Sans-Serif;
         text-align:center;
         font-size: 25px;
         color: #000099;
         text-decoration: none;
         font-style: normal;
         font-weight: normal;
         font-variant: normal;
         background-attachment: fixed;
         padding: 8px;
         }
         .bluehead {
         PADDING-RIGHT: 0px;
         PADDING-LEFT: 0px;
         FONT-WEIGHT: bold;
         FONT-SIZE: 16px;
         PADDING-BOTTOM: 8px;
         COLOR: #333333;
         PADDING-TOP: 8px;
         FONT-FAMILY: Arial, Helvetica, sans-serif;
         TEXT-DECORATION: none;
         line-height: 12px;
         }
         .post-text {
         FONT: 14px/20px arial;
         COLOR: #696a6a;
         }
         .post-text_b {
         FONT: 14px/20px arial;
         COLOR: #696a6a;
         font-weight:bold;
         }
         .post-text_large {
         FONT: 25px arial;
         COLOR: #696a6a;
         font-weight:bold;
         text-decoration:none;
         }
         .page-content-wrapper .page-content {
         margin-left: 235px;
         margin-top: 0;
         min-height: 600px;
         padding: 25px 20px 10px;
         }
         .page-content {
         margin-top: 0;
         padding: 0;
         background-color: #eaeef3;
         }
         .print-content{
         width: 794px;
         margin: 0px auto;    
         border: 3px solid #cec5c5;
         background: #ffffff;
         border: 2px solid black;border-radius: 5px;
         }
         .st td {
         font-family: Lucida Fax;
         font-size: 13px;
         color: #003f6f;
         }
         .st-padding{
         padding: 6px;
         }
      </style>
      <script type="text/javascript">
         function printContent(){ 			
         			var content=document.getElementById('print_invoice').innerHTML;			
         		var pwin=window.open('','print_content','width=500,height=860');			
         		pwin.document.write('<html><body onload="window.print()" style="width:550px;margin:20px auto;font-size:11px; font-family:arial;border:1px solid #ccc;padding:10px;">'+content+'</body></html>');
         		pwin.document.close();
         		setTimeout(function(){pwin.close();},1000);
         	}
      </script>	
   </head>
   <script>
      $(document).ready(function(){
          var uri = window.location.toString();
          if (uri.indexOf("?") > 0) {
              var clean_uri = uri.substring(0, uri.indexOf("?"));
              window.history.replaceState({}, document.title, clean_uri);
          }
      });
   </script>
   <body class="page-content">
      <div id="bodywraper">
         <!-----link ke neeche ka wrapper---->
         <link href="./printformb_files/fm.css" rel="stylesheet" type="text/css">
         <div id="rightnavigator">
            <div class="print-content" id="print_invoice">
               <table width="794px" class ="st"  style="background: url(upperstrip3.jpeg);
                  background-repeat: repeat-y;"
                  >
                  <tbody>
                     <tr>
                        <td align="center">
                           <table align="center" width="775px">
                              <tbody>
                                 <tr>
                                    <td align="center">
                                       <img src="5f9ecb3c8e5cf_dde_logo1.png" width="120px">
                                    </td>
                                    <td align="left">
                                       <div align="center">
                                          <div>
                                             <h3 style="margin: 0px">
                                                Directorate of Distance Education
                                             </h3>
                                             <h2 style="margin: 0px">
                                                Swami Vivekanand Subharti University
                                             </h2>
                                             <h4 style="margin: 0px">
                                                Meerut - 250005 (U.P.)
                                             </h4>
                                          </div>
                                          <br>
                                       </div>
                                       <div align="center" class="heading" style="padding-bottom: 10px">
                                          <b><u>ONLINE APPLICATION FORM FOR ADMISSION</u></b>
                                       </div>
                                    </td>
                                    <td align="right" style="width: 50px; padding-top:100px;">
                                       <img src="https://api.qrserver.com/v1/create-qr-code/?size=50x50&data=<?php echo $st['ID'];?>"/>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                     <tr>
                        <td align="right"><?php echo sprintf("%'.06d\n", $st['ID']) ?>&nbsp;</td>
                     </tr>
                  </tbody>
               </table>
               <table width="100%" class="st" style="border-top: 2px solid #003f6f">
                  <tbody>
                     <table width="100%" border="0" cellspacing="3" cellpadding="3">
                        <tbody>
                           <tr>
                              <td width="51" align="left" valign="middle" class="keyskill">OANo. </td>
                              <td width="138" align="center" valign="middle" class="tbborder">&nbsp;<?php echo $st['OA_Number']; ?></td>
                              <td width="111" align="left" style="padding-left:50px;" valign="middle" class="keyskill">Application No. </td>
                              <td width="138" align="center" valign="middle" class="tbborder">&nbsp;</td>
                              <td align="right" valign="middle" class="keyskill">Session&nbsp;</td>
                              <td width="138" align="center" valign="middle" class="tbborder">&nbsp;<?php $get_session = $conn->query("SELECT * FROM Admission_Sessions WHERE ID = '".$st['Admission_Session_ID']."'"); $sess = mysqli_fetch_assoc($get_session); echo $sess['Name']; ?></td>
                           </tr>
                        </tbody>
                     </table>
                     <table width="100%" border="0" cellspacing="1" cellpadding="1">
                        <tbody>
                           <tr>
                              <td width="141" align="left" valign="middle" class="keyskill">Study Center Code&nbsp; </td>
                              <td width="86" align="left" valign="middle"><span class="tbborder">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></td>
                              <td width="157" align="right" valign="middle" class="keyskill">&nbsp; </td>
                              <td width="125" align="right" valign="middle" >&nbsp;</td>
                              <td align="right">
                                 <div class="tbborder" style="height: 104px; width: 102px;">
                                    <?php if($st['ID']!=''){ ?><img align="left" hspace="4" vspace="4" width="95px" height="100px" src="<?php $photo = $conn->query("SELECT Location FROM Student_Documents WHERE Student_ID = ".$st['ID']." AND Type = 'Photo'"); $photo = mysqli_fetch_assoc($photo); echo $photo['Location']; ?>"><?php } ?>
                                 </div>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <table width="100%" border="0" cellspacing="1" cellpadding="1" class="st-padding">
                        <tbody>
                           <tr>
                              <td width="30%" class="keyskill" >ENROLLMENT NUMBER
                                 <br>
                                 <span class="text">(For Office Use Only)</span>
                              </td>
                              <td width="47.4%" style="background: url(/assets/sc-stenr.gif) repeat-x;"><span style="letter-spacing:20px;"><?php echo $st['Enrollment_No']; ?></span></td>
                              <td width="20%">&nbsp;</td>
                           </tr>
                           <tr>
                              <td width="30%" class="keyskill">PROGRAMME APPLIED FOR
                                 <br>
                                 <span class="text">(Including Subject/Specialization)</span>
                              </td>
                              <td colspan="2" class="tbborder"><span><?php echo $gsc['Short_Name'] ?> | (<?php $get_type = $conn->query("SELECT * FROM Admission_Types WHERE ID = '".$st['Admission_Type_ID']."'"); $at = mysqli_fetch_assoc($get_type); echo strtoupper($at['Name']).' | '.$st['Duration'].' SEM'  ?>)</span></td>
                           </tr>
                           <tr>
                              <td colspan="3" align="left" valign="middle" class="formbox">1. Name in CAPITAL LETTERS (In English) </td>
                           </tr>
                           <tr>
                              <td height="15" colspan="3" align="left" valign="middle" class="tbborder"> &nbsp;<?php echo strtoupper($st['First_Name']).' '.$st['Middle_Name'].' '.$st['Last_Name']; ?></td>
                           </tr>
                           <tr>
                              <td colspan="3" align="left" valign="middle" class="formbox">2. Father's Name : {all the Candidates Including married women will mention Name of Father} </td>
                           </tr>
                           <tr>
                              <td height="15" colspan="3" align="left" valign="middle" class="tbborder">&nbsp;<?php echo strtoupper($st['Father_Name']); ?></td>
                           </tr>
                           <tr>
                              <td colspan="3" align="left" valign="middle" class="formbox">3. Mother's Name : </td>
                           </tr>
                           <tr>
                              <td height="15" colspan="3" align="left" valign="middle" class="tbborder">&nbsp;<?php echo strtoupper($st['Mother_Name']); ?></td>
                           </tr>
                        </tbody>
                     </table>
                     <table width="100%" border="0" cellspacing="1" cellpadding="1" class="st-padding">
                        <tbody>
                           <tr>
                              <td align="left" valign="middle" class="formbox">4. Sex: (v Tick) </td>
                              <td class="formbox">Male</td>
                              <td class="formbox">Female</td>
                              <td class="formbox">&nbsp;</td>
                              <td width="120" class="formbox">5. Date of Birth : </td>
                              <td width="57" align="center" valign="middle" class="formbox">Date</td>
                              <td width="58" align="center" valign="middle" class="formbox">Month</td>
                              <td width="65" align="center" valign="middle" class="formbox">Year</td>
                           </tr>
                           <tr>
                              <td width="133">&nbsp;</td>
                              <td width="42" class="tbborder">&nbsp; 
                                 <?php if($st['Gender']=='Male'){ ?>
                                 <img align="center" hspace="4" vspace="4" width="17px" height="15px" src="check.png" alt="tick">
                              </td>
                              <?php } ?>
                              <td width="44" class="tbborder">&nbsp;
                                 <?php if($st['Gender']=='Female'){ ?>
                                 <img align="center" hspace="4" vspace="4" width="17px" height="15px" src="check.png" alt="tick">
                              </td>
                              <?php } ?>
                              </td>
                              <td width="56">&nbsp;</td>
                              <td>&nbsp;</td>
                              <td align="center" valign="middle" class="tbborder">
                                 <?php $dob = explode('-', $st['DOB']); ?>
                                 <center>
                                    <?php echo $dob[2]; ?>
                                 </center>
                              </td>
                              <td align="center" valign="middle" class="tbborder">
                                 <center>
                                    <?php echo $dob[1]; ?>
                                 </center>
                              </td>
                              <td align="center" valign="middle" class="tbborder">
                                 <center>
                                    <?php echo $dob[0]; ?>
                                 </center>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <table width="100%" border="0" cellspacing="0" cellpadding="0" class="st-padding">
                        <tbody>
                           <tr>
                              <td colspan="2">6. Address for Correspondence (do not repeat name) </td>
                           </tr>
                           <tr>
                             <?php $address = json_decode($st['Address'], true); ?>
                              <td height="50" colspan="10" align="left" valign="top"style="background:url(sc-stadr.gif) repeat">
                                 <span style="letter-spacing:1px; line-height:27px;">
                                 <?php echo strtoupper($address['present_address']); ?>
                                 </span>
                              </td>
                           </tr>
                           <tr>
                              <td height="30" align="right" valign="middle" style="padding-right:5px;"> Pin Code&nbsp;&nbsp;</td>
                              <?php $array = str_split($address['present_pincode']); ?>
                              <td height="30" width="30" align="left" valign="middle" class="tbborder">
                                 <center><?php echo $array[0]?></center>
                              </td>
                              <td height="30" width="30" align="left" valign="middle" class="tbborder">
                                 <center><?php echo $array[1]?></center>
                              </td>
                              <td height="30" width="30" align="left" valign="middle" class="tbborder">
                                 <center><?php echo $array[2]?></center>
                              </td>
                              <td height="30" width="30" align="left" valign="middle" class="tbborder">
                                 <center><?php echo $array[3]?></center>
                              </td>
                              <td height="30" width="30" align="left" valign="middle" class="tbborder">
                                 <center><?php echo $array[4]?></center>
                              </td>
                              <td height="30" width="30" align="left" valign="middle" class="tbborder">
                                 <center><?php echo $array[5]?></center>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <table width="100%" border="0" cellspacing="1" cellpadding="1" class="st-padding">
                        <tbody>
                           <tr>
                              <td width="233" align="left" valign="middle">Phone No. with STD Code </td>
                              <td width="233">Mobile No.</td>
                              <td width="250">E-mail</td>
                           </tr>
                           <tr>
                              <td height="15" align="left" valign="middle" class="tbborder">&nbsp;</td>
                              <td height="15" align="left" valign="middle" class="tbborder"><?php echo strtoupper($st['Contact']); ?></td>
                              <td height="15" align="left" valign="middle" class="tbborder"><?php echo strtoupper($st['Email']); ?></td>
                           </tr>
                        </tbody>
                     </table>
                     <div width="100%" class="st-padding">
                        <table width="100%" height="140" cellpadding="8" cellspacing="0" border="1">
                           <tr>
                              <td width="25%" align="left" valign="top" class="tbborder"><strong>Examination</strong></td>
                              <td width="20%" align="center" valign="bottom" class="tbborder"><strong>Subject</strong></td>
                              <td align="left" valign="top" class="tbborder"><strong>Year of Passing </strong></td>
                              <td align="left" valign="top" class="tbborder"><strong>University/Board </strong></td>
                              <td width="17%" align="center" valign="bottom" class="tbborder"><strong>Division/Grade</strong></td>
                           </tr>
                           <tr>
                              <td align="center" valign="middle" class="tbborder">10th</td>
                              <?php $high = $conn->query("SELECT * FROM Student_Academics WHERE Student_ID = ".$st['ID']." AND Type = 'High School'");
                                $high = mysqli_fetch_assoc($high);
                              ?>
                              <td align="center" valign="middle" class="tbborder">&nbsp;<?php echo strtoupper($high['Subject']); ?></td>
                              <td width="18%" align="left" valign="top" class="tbborder"><?php echo $high['Year']; ?></td>
                              <td width="30%" align="center" valign="top" class="tbborder"><?php echo strtoupper($high['Board/Institute']); ?></td>
                              <td align="center" valign="middle" class="tbborder"><?php echo strtoupper($high['Total_Marks']); ?></td>
                           </tr>
                           <tr>
                              <td align="center" valign="middle" class="tbborder">12th</td>
                              <?php $inter = $conn->query("SELECT * FROM Student_Academics WHERE Student_ID = ".$st['ID']." AND Type = 'Intermediate'");
                                $inter = mysqli_fetch_assoc($inter);
                              ?>
                              <td align="center" valign="middle" class="tbborder">&nbsp;<?php echo strtoupper($inter['Subject']); ?></td>
                              <td align="left" valign="top" class="tbborder"><?php echo strtoupper($inter['Year']); ?></td>
                              <td align="center" valign="top" class="tbborder"><?php echo strtoupper($inter['Board/Institute']); ?></td>
                              <td align="center" valign="middle" class="tbborder"><?php echo strtoupper($inter['Total_Marks']); ?></td>
                           </tr>
                           <tr>
                              <td align="center" valign="middle" class="tbborder">UG</td>
                              <?php $ug = $conn->query("SELECT * FROM Student_Academics WHERE Student_ID = ".$st['ID']." AND Type = 'UG'");
                                $ug = mysqli_fetch_assoc($ug);
                              ?>
                              <td align="center" valign="middle" class="tbborder">&nbsp;<?php echo strtoupper($ug['Subject']); ?></td>
                              <td align="left" valign="top" class="tbborder"><?php echo strtoupper($ug['Year']); ?></td>
                              <td align="center" valign="top" class="tbborder"><?php echo strtoupper($ug['Board/Institute']); ?></td>
                              <td align="center" valign="middle" class="tbborder"><?php echo strtoupper($ug['Total_Marks']); ?></td>
                           </tr>
                           <tr>
                              <td align="center" valign="middle" class="tbborder">OTH</td>
                              <?php $other = $conn->query("SELECT * FROM Student_Academics WHERE Student_ID = ".$st['ID']." AND Type = 'Other'");
                                $other = mysqli_fetch_assoc($other);
                              ?>
                              <td align="center" valign="middle" class="tbborder">&nbsp;<?php echo strtoupper($other['Subject']); ?></td>
                              <td align="left" valign="middle" class="tbborder"><?php echo strtoupper($other['Year']); ?></td>
                              <td align="center" valign="middle" class="tbborder"><?php echo strtoupper($other['Board/Institute']); ?></td>
                              <td align="center" valign="middle" class="tbborder"><?php echo strtoupper($other['Total_Marks']); ?></td>
                           </tr>
                           </tbody>
                        </table>
                     </div>
                     </tr>  
                  </tbody>
               </table>
               <br/>
            </div>
         </div>
      </div>
   </body>
</html>
