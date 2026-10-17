<?php
require '../../includes/db-config.php';
session_start();
?>

<!-- Modal -->
<div class="modal-header clearfix text-left">
    <button aria-label="Close" type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="pg-icon">close</i>
    </button>
    <h5>Create Center Authorization Certificate</h5>
</div>

<form id="centerForm" role="form" method="POST" enctype="multipart/form-data">
    <div class="modal-body row">
        <!-- Center Name -->
        <div class="form-group col-md-12 col-sm-12">
            <label>Center Name</label>
            <input type="text" onkeypress="return isCharKey(event);" class="form-control" name="center_name" required>
        </div>

        <!-- Email -->
        <div class="form-group col-md-6 col-sm-12">
            <label>Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>

        <!-- Phone -->
        <div class="form-group col-md-6 col-sm-12">
            <label>Phone</label>
            <input type="tel" onkeypress="return isNumberKey(event);" class="form-control" minlength="10" maxlength="10" name="phone" required>
        </div>

        <!-- Address -->
        <div class="form-group col-md-12 col-sm-12">
            <label>Address</label>
            <textarea class="form-control" name="address" required></textarea>
        </div>

        <!-- Programs -->
        <div class="form-group col-md-6 col-sm-12">
            <label>Programs</label>
            <input type="text" class="form-control" name="programs"
                placeholder="Eg. Computer Basics, Web Development" required>
        </div>

        <!-- Type -->
        <div class="form-group col-md-6 col-sm-12">
            <label>Type</label>
            <select class="form-control" name="type" required>
                <option value="">-- Select Type --</option>
                <option value="47">Bvoc</option>
                <option value="48">Skill</option>
            </select>
        </div>

        <!-- Pincode -->
        <div class="form-group col-md-6 col-sm-12">
            <label>Pincode</label>
            <input type="text" class="form-control" onkeypress="return isNumberKey(event);" maxlength="6"
                id="pincode" name="pincode" required>
        </div>

        <!-- State -->
        <div class="form-group col-md-6 col-sm-12">
            <label>State</label>
            <input type="text" class="form-control" id="state" name="state" readonly required>
        </div>

        <!-- District -->
        <div class="form-group col-md-6 col-sm-12">
            <label>District</label>
            <select class="form-control" id="district" name="district" required>
                <option value="">-- Select District --</option>
            </select>
        </div>

        <!-- City -->
        <div class="form-group col-md-6 col-sm-12">
            <label>City</label>
            <select class="form-control" id="city" name="city" required>
                <option value="">-- Select City --</option>
            </select>
        </div>

        <!-- Date of Issue -->
        <div class="form-group col-md-6 col-sm-12">
            <label>Date of Issue</label>
            <input type="date" class="form-control" id="date_of_issue" name="date_of_issue" min="<?= date('Y-m-d'); ?>" required>
        </div>

        <!-- Payment Type -->
        <div class="form-group col-md-6 col-sm-12">
            <label>Payment Type</label>
            <select class="form-control" name="payment_type" required>
                <option value="">-- Select Type --</option>
                <option value="Online">Online</option>
                <option value="Offline">Offline</option>
            </select>
        </div>

        <!-- Amount -->
        <div class="form-group col-md-6 col-sm-12">
            <label>Amount</label>
            <input type="text" onkeypress="return isNumberKey(event);" class="form-control" name="amount" required>
        </div>
        <div class="form-group col-md-6 col-sm-12">
            <label>Amount</label>
             <select class="form-control" name="recieved_vertical" required>
                <option value="">-- Select Type --</option>
                <option value="Edtech">Edtech Innovate</option>
                <option value="Glocal">Glocal</option>
            </select>
        </div>

        <!-- Payment Proof -->
        <div class="form-group col-md-6 col-sm-12">
            <label>Payment Proof (Image/PDF)</label>
            <input type="file" class="form-control" name="payment_proof"
                accept="image/*,.pdf,.svg" required>
        </div>
    </div>

    <div class="modal-footer clearfix justify-content-end">
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const today = new Date().toISOString().split("T")[0];
        document.getElementById("date_of_issue").setAttribute("min", today);
    });

    // Allow only characters
    function isCharKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        return (
            (charCode >= 65 && charCode <= 90) ||
            (charCode >= 97 && charCode <= 122) ||
            charCode === 32 ||
            charCode === 8
        );
    }

    // Allow only numbers
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        return !(charCode > 31 && (charCode < 48 || charCode > 57));
    }

    // Get Region Data from Pincode
    function getRegion(pincode) {
        if (pincode.length === 6) {
            $.ajax({
                url: '/app/regions/cities?pincode=' + pincode,
                type: 'GET',
                success: function(data) {
                    $('#city').html(data);
                }
            });
            $.ajax({
                url: '/app/regions/districts?pincode=' + pincode,
                type: 'GET',
                success: function(data) {
                    $('#district').html(data);
                }
            });
            $.ajax({
                url: '/app/regions/state?pincode=' + pincode,
                type: 'GET',
                success: function(data) {
                    $('#state').val(data);
                }
            });
        }
    }

    // On typing Pincode
    $(document).on("keyup", "#pincode", function() {
        let pincode = $(this).val();
        if (pincode.length === 6) {
            getRegion(pincode);
        }
    });

    // Form Validation + Submit
    $(function() {
        $('#centerForm').validate({
            rules: {
                center_name: {
                    required: true,
                    minlength: 3
                },
                phone: {
                    required: true,
                    minlength: 10,
                    maxlength: 10
                },
                email: {
                    required: true,
                    email: true
                },
                address: {
                    required: true,
                    minlength: 5
                },
                programs: {
                    required: true
                },
                type: {
                    required: true
                },
                pincode: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 6
                },
                state: {
                    required: true
                },
                district: {
                    required: true
                },
                city: {
                    required: true
                },
                date_of_issue: {
                    required: true
                },
                payment_proof: {
                    required: true
                }
            },
            messages: {
                center_name: "Please enter center name",
                phone: "Please enter valid 10 digit mobile number",
                email: "Please enter valid email",
                address: "Please enter address",
                programs: "Please enter at least one program",
                type: "Please select type",
                pincode: "Please enter valid 6-digit pincode",
                state: "State is required",
                district: "Please select district",
                city: "Please select city",
                date_of_issue: "Please select date of issue",
                payment_proof: "Please upload payment proof"
            },
            highlight: function(element) {
                $(element).addClass('error').closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).removeClass('error').closest('.form-group').removeClass('has-error');
            },
            submitHandler: function(form) {
                let formData = new FormData(form); // ✅ collect file + other inputs
                $.ajax({
                    url: "/app/center_authorize/store.php",
                    type: "POST",
                    data: formData,
                    processData: false, // ✅ required for file
                    contentType: false, // ✅ required for file
                    dataType: "json",
                    success: function(res) {
                        if (res.status == 200) {
                            notification('success', res.message);
                            $("#centerForm")[0].reset();
                            $('#autorize_center-table').DataTable().ajax.reload();
                            $(".modal").modal('hide');
                        } else {
                            notification('danger', res.message);
                        }
                    }
                });
                return false;
            }
        });
    });
</script>