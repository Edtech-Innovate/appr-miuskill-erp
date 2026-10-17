</div>
<!-- end main-content -->

<!-- Footer -->
<footer id="footer" class="footer bg-black-222" data-bg-img="images/footer-bg.png">
  <div class="container pt-70 pb-40">
    <div class="row">
      <div class="col-sm-6 col-md-3">
        <div class="widget dark">
          <a hrf="/"><img class="mt-10 mb-15" alt=" Wilp Programm" src="images/main/logo-white.png"></a>
          <p class="font-16 text-white mb-10">Established in 2021, Sikkim Alpine University is strongly committed towards excellence in research and innovative practices in higher education. It is a State Private University established by the State Legislature of Government of Sikkim by Act 4 of 2006 & further amended with Act 14 of 2021.</p>

        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="widget dark">
          <h5 class="widget-title line-bottom">M.Tech Programmes</h5>
          <ul class="list angle-double-right list-border">
            <li><a href="mtech-computer-science.php">Computer Science and Engineering</a> </li>

            <li><a href="mtech-highway-engineering.php">Highway Engineering</a> </li>
            <li><a href="mtech-environmental-engineering.php">Environmental Engineering</a></li>
            <li><a href="mtech-structural-engineering.php">Structural Engineering</a></li>
            <li><a href="mtech-ece-engineering.php">Electronics and Communication Engineering</a></li>
            <li><a href="mtech-vlsi-engineering.php">VLSI</a></li>
            <li><a href="mtech-power-system-engineering.php">Power System</a></li>
            <li><a href="mtech-power-electronics-engineering.php">Power Electronics</a></li>
            <li><a href="mtech-mechanical-engineering.php">Mechanical Engineering</a></li>
            <li><a href="mtech-production-engineering.php">Production Engineering</a></li>
          </ul>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="widget dark">
          <h5 class="widget-title line-bottom">B.Tech Programmess</h5>
          <ul class="list angle-double-right list-border">
            <li><a href="btech-civil.php">Civil Engineering </a></li>
            <li><a href="btech-electronics-cummunication.php">Electronics & Communication Engineering </a></li>
            <li><a href="btech-electrical.php">Electrical Engineering </a></li>
            <li><a href="btech-mechanical.php">Mechanical Engineering</a></li>
            <li><a href="btech-computer-science.php">Computer Science and Engineering</a></li>
          </ul>

          <h5 class="widget-title line-bottom">Diploma</h5>
          <ul class="list angle-double-right list-border">
            <li><a href="diploma-civil.php">Diploma in Civil Engineering</a></li>
            <li><a href="diploma-electronics-cummunication.php">Diploma in Electronics & Communication Engineering</a></li>
            <li><a href="diploma-electrical.php">Diploma in Electrical Engineering</a></li>
            <li><a href="diploma-mechanical.php">Diploma in Mechanical Engineering</a></li>
          </ul>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="widget dark">
          <h5 class="widget-title line-bottom">Useful Links</h5>
          <ul class="list angle-double-right list-border">
            <li><a href="wilp-learning-experience.php">What is WILP?</a></li>
            <li><a href="online-and-remote-labs.php">Online and Remote Labs</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="javascript:void(0)">Student Login</a></li>
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="images/main/pdf/AICTE.pdf" target="_blank">AICTE**</a></li>
            <li><a href="fee-refund-policy.php">fee-refund-policy</a></li>
            <li><a href="faq.php">FAQs</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom bg-black-333">
    <div class="container pt-20 pb-20">
      <div class="row">
        <div class="col-md-6">
          <p class="font-11 text-black-777 m-0">Copyright &copy;2022 Sikkim Alpine University. All Rights Reserved</p>
        </div>
        <div class="col-md-6 text-right">
          <div class="widget no-border m-0">
            <ul class="list-inline sm-text-center mt-5 font-12">
              <li>
                <a href="fee-refund-policy.php">Refund Policy</a>
              </li>
              <li>|</li>
              <li>
                <a href="#">Terms & conditions</a>
              </li>
              <li>|</li>
              <li>
                <a href="#">Support</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>
<?php include 'parts/modal.php' ?>
<a class="scrollToTop" href="#"><i class="fa fa-angle-up"></i></a>
</div>
<!-- end wrapper -->

<!-- Footer Scripts -->
<script src="js/custom.js"></script>
<script src="js/lead.js?v=1"></script>

<script type="text/javascript" src="js/revolution-slider/js/extensions/revolution.extension.actions.min.js"></script>
<script type="text/javascript" src="js/revolution-slider/js/extensions/revolution.extension.carousel.min.js"></script>
<script type="text/javascript" src="js/revolution-slider/js/extensions/revolution.extension.kenburn.min.js"></script>
<script type="text/javascript" src="js/revolution-slider/js/extensions/revolution.extension.layeranimation.min.js"></script>
<script type="text/javascript" src="js/revolution-slider/js/extensions/revolution.extension.migration.min.js"></script>
<script type="text/javascript" src="js/revolution-slider/js/extensions/revolution.extension.navigation.min.js"></script>
<script type="text/javascript" src="js/revolution-slider/js/extensions/revolution.extension.parallax.min.js"></script>
<script type="text/javascript" src="js/revolution-slider/js/extensions/revolution.extension.slideanims.min.js"></script>
<script type="text/javascript" src="js/revolution-slider/js/extensions/revolution.extension.video.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
  function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
  }

  function isNotNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode
    return (charCode > 31 && (charCode < 48 || charCode > 57));
  }
</script>

<script>
  $(document).ready(function() {
    $('input.disablecopypaste').bind('copy paste', function(e) {
      e.preventDefault();
    });
  });
</script>