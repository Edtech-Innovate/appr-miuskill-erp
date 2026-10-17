<div class="modal fade" id="modalform" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center" id="myModalLabel">Apply Now</h4>
      </div>
      <div class="modal-body">
        <!-- Appilication Form Start-->
        <form id="pop_form" name="reservation_form" class="reservation-form mt-20" method="post">
          <div class="row">
            <div class="col-sm-12">
              <div class="form-group mb-20">
                <input placeholder="Enter Name" type="text" id="name" name="name" onkeypress="return isNotNumberKey(event);" required="" class="form-control" aria-required="true">
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group mb-20">
                <input placeholder="Email" type="email" id="email" name="email" onkeyup="checkLeadEmail(this.value)" class="form-control" required="" aria-required="true">
                <dt id="7c48f9fe-05cf-4b94-a418-634b01fac705" style="color: red;"></dt>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group mb-20">
                <input placeholder="Phone" type="tel" maxlength="10" minlength="10" onkeypress="return isNumberKey(event);" onkeyup="checkLeadMobile(this.value)" id="phone" name="mobile" class="form-control disablecopypaste" required="" aria-required="true">
                <dt id="f99e084d-afc3-4401-a8f6-b17a982d3381" style="color: red;"></dt>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group mb-20">
                <div class="styled-select">
                  <select id="lead_courses" name="course" class="form-control" onchange="getLeadSubCourses('lead_sub_course', this.value)" required="" aria-required="true">
                    <option value="" selected disabled>Choose Course</option>

                  </select>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group mb-20">
                <div class="styled-select">
                  <select id="lead_sub_course" name="sub_course" class="form-control course" required="" aria-required="true">
                    <option value="" selected disabled> Choose Specialization</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
              <div class="form-group mb-0 mt-10">
                <button type="submit" class="btn btn-flat btn-theme-colored text-uppercase mt-10 mb-sm-30 border-left-theme-color-2-4px" data-loading-text="Please wait...">Submit</button>
              </div>
            </div>
          </div>
        </form>
        <!-- Application Form End-->
      </div>
    </div>
  </div>
</div>