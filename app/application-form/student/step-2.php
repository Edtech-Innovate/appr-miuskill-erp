<div id="rootwizard" class="m-t-50">
  <!-- Nav tabs -->
  <ul class="nav nav-tabs nav-tabs-linetriangle nav-tabs-separator nav-stack-sm" role="tablist" data-init-reponsive-tabs="dropdownfx">
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab1" data-target="#tab1" role="tab"><i class="uil uil-user-circle fs-14 tab-icon"></i> <span>Basic Details</span></a>
    </li>
    <li class="nav-item">
      <a class="active d-flex align-items-center" data-toggle="tab" href="#tab2" data-target="#tab2" role="tab"><i class="uil uil-location fs-14 tab-icon"></i> <span>Contact Details & Fee</span></a>
    </li>
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab3" data-target="#tab3" role="tab"><i class="uil uil-graduation-hat fs-14 tab-icon"></i> <span>Academics</span></a>
    </li>
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab4" data-target="#tab4" role="tab"><i class="uil uil-document fs-14 tab-icon"></i> <span>Documents</span></a>
    </li>
    <li class="nav-item">
      <a class="d-flex align-items-center" data-toggle="tab" href="#tab5" data-target="#tab5" role="tab"><i class="uil uil-file-check fs-14 tab-icon"></i> <span>Application Form</span></a>
    </li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane slide-left padding-20 active sm-no-padding" id="tab2">
      <form id="step_2" role="form" autocomplete="off" action="/app/application-form/student/update/step-2">
        <div class="row row-same-height">
          <div class="col-md-6 b-r b-dashed b-grey sm-b-b">
            <div class="padding-10 sm-padding-5">
              <h5>Social</h5>
              <div class="row clearfix">
                <div class="col-md-6">
                  <div class="form-group form-group-default required">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= $_SESSION['Email'] ?>" placeholder="ex: jhon@example.com">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group form-group-default">
                    <label>Alternate Email</label>
                    <input type="email" name="alternate_email" value="" class="form-control" placeholder="ex: jhondoe@example.com">
                  </div>
                </div>
              </div>

              <div class="row clearfix">
                <div class="col-md-6">
                  <div class="form-group form-group-default required">
                    <label>Mobile</label>
                    <input type="tel" name="contact" id="contact" onkeypress="return isNumberKey(event);" maxlength="10" value="<?= $_SESSION['Mobile'] ?>" class="form-control" placeholder="ex: 9977886655">
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group form-group-default">
                    <label>Alternate Mobile</label>
                    <input type="tel" name="alternate_contact" class="form-control" maxlength="10" value="" placeholder="ex: 9988776654">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="padding-10 sm-padding-5">
              <h5>Address</h5>
              <div class="row clearfix">
                <div class="col-md-8">
                  <div class="form-group form-group-default required">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" value="<?php print !empty($id) ? (!empty($address) ? $address['present_address'] : '') : '' ?>" placeholder="ex: 23 Street, California">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>Pincode</label>
                    <input type="tel" name="pincode" maxlength="6" class="form-control" placeholder="ex: 123456" value="<?php print !empty($address) ? (array_key_exists('present_pincode', $address) ? $address['present_pincode'] : '') : '' ?>" onkeypress="return isNumberKey(event)" onkeyup="getRegion(this.value);">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>City</label>
                    <select class="full-width" style="border: transparent;" name="city" id="city">

                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>District</label>
                    <select class="full-width" style="border: transparent;" name="district" id="district">

                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group form-group-default required">
                    <label>State</label>
                    <input type="text" name="state" class="form-control" placeholder="ex: California" id="state" readonly>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row row-same-height">
          <div class="col-md-12 b-t b-dashed b-grey sm-b-b">
            <h5 class="padding-10 sm-padding-5">Fee Payment:</h5>
            <div class="row padding-10 sm-padding-5">
              <div class="col-md-6">
                <div class="form-group form-group-default required">
                  <label>Payment Type</label>
                  <select class="full-width" style="border: transparent;" name="payment_type" id="payment_type" onchange="getFeeTable()">

                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group form-group-default required">
                  <label>Payable Amount</label>
                  <input type="text" disabled class="form-control" placeholder="Payable Amount" id="amount">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 padding-10 sm-padding-5" id="fee_table">

              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer m-t-20">
          <button aria-label="" class="btn btn-default btn-cons btn-animated from-left pull-right" type="button" onclick="goStepBack()">
            <span>Previous</span>
            <span class="hidden-block">
              <i class="uil uil-angle-left"></i>
            </span>
          </button>
          <button aria-label="" class="btn btn-primary btn-cons btn-animated from-left pull-right" onclick="initiatePayment()" type="button">
            <span>Pay</span>
            <span class="hidden-block">
              <i class="uil uil-angle-right"></i>
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>