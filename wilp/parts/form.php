 <!-- Appilication Form Start-->
 <form id="reservation_form" name="reservation_form" class="reservation-form mt-20" method="post">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group mb-20">
	                    <input type="hidden" name="page" value="wilp.sikkimalpineuniversity.edu.in">
                      <input placeholder="Enter Name" type="text" id="name" name="name" onkeypress="return isNotNumberKey(event);" class="form-control" aria-required="true" required="required"/>
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group mb-20">
                      <input placeholder="Email" type="email" id="email" name="email" class="form-control" required="" aria-required="true">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group mb-20">
                      <input placeholder="Phone" type="tel" maxlength="10" minlength="10" onkeypress="return isNumberKey(event);" id="phone" name="mobile" class="form-control disablecopypaste" required="" aria-required="true">
                    </div>
                  </div>
                  <div class="col-sm-12">
                    <div class="form-group mb-20">
                      <div class="styled-select">
                        <select id="department"  name="department" class="form-control department" required="" aria-required="true">
                          <option value="" selected disabled>Choose Course</option>
                          <option value="Diploma">Diploma</option>
                          <option value="B.Tech">B.Tech</option>
                          <option value="M.Tech">M.Tech</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group mb-20">
                      <div class="styled-select">
                        <select id="course" name="course" class="form-control course" required="" aria-required="true">
                          <option value="" selected disabled> Choose Specialization</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-12">
                    <div class="form-group mb-20">
                      <div class="styled-select">
                        <select id="emp_status" name="emp_status" class="form-control" required="" aria-required="true">
                          <option value="">Select Employment Status</option>
                          <option value="Government Job"> Government Job</option>
                          <option value="Private Job"> Private Job</option>
                          <option value="Other"> Other</option>
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


