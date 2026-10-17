<?php

$id = $_POST['id'];
$courseId = $_POST['courseId'];

require '../../includes/db-config.php';
$paymentData = $conn->query("select * from university_payment where id=".$id)->fetch_assoc();
?>

<!--<div class="modal-header clearfix text-left m-0 p-0">-->
<!--    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->

<!--    <h5 class="fs-4 text-black fw-bold">Allot<span class="semi-bold"></span>University Fee</h5>-->
<!--  </div>-->
  <div class="modal-header clearfix text-left">
  <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
  </button>
  <h5>Allot<span class="semi-bold"></span>University Fee</h5>
</div>
  <form action="/university/payment/update" method="post" id="unversity_payment_form">
  <div class="modal-body">
    <div class="row">
        <input type="hidden" name="id" value="<?=$id?>">
        <input type="hidden" name="courseId" value="<?=$courseId?>">
        <input type="hidden" name="stu_id" value="<?=isset($paymentData['student_id'])? $paymentData['student_id']:"" ?>">
        <input type="hidden" name="payment_type" value="<?=isset($paymentData['payment_type'])? $paymentData['payment_type']:"" ?>">

        <div class="col-md-6">
            <div class="form-group form-group-default required">
            <label for="amount">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" value="<?=$paymentData['amount']?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-group-default required">
            <label for="date">Date of Transaction</label>
            <input type="date" name="date_of_transaction" id="date_of_transaction" class="form-control" value="<?=$paymentData['date_of_transaction']?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-group-default required">
            <label for="urn_no">URN/Reference Number</label>
            <input type="text" name="urn_no" id="urn_no" class="form-control" value="<?=$paymentData['urn_no']?>">
        </div>
        </div>
        <div class="col-md-6">
            <div class="form-group form-group-default required">
            <label for="mode_of_payment">Mode of transaction</label>
            <select name="mode_of_payment" id="mode_of_payment" class="form-control">
                <option value="Cash" <?=$paymentData['mode_of_payment']=='Cash'?"selected":""?>>Cash</option>
                <option value="Debit" <?=$paymentData['mode_of_payment']=='Debit'?"selected":""?>>Debit Card</option>
                <option value="Credit" <?=$paymentData['mode_of_payment']=='Credit'?"selected":""?>>Credit Card</option>
                <option value="UPI" <?=$paymentData['mode_of_payment']=='UPI'?"selected":""?>>UPI</option>
                <option value="DD" <?=$paymentData['mode_of_payment']=='DD'?"selected":""?>>DD</option>
                <option value="Bank Transfer" <?=$paymentData['mode_of_payment']=='Bank Transfer'?"selected":""?>>Bank Transfer</option>
            </select></div>
        </div>
    </div>
</div>
<!--<div class="pt-6">-->
<!--    <div class="row justify-content-end">-->
<!--      <div class="col-sm-9">-->
<!--        <button type="submit" class="btn btn-primary me-4">Add</button>-->
<!--        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-outline-secondary">Cancel</button>-->
<!--      </div>-->
<!--    </div>-->
    
<!--  </div>-->
   <div class="modal-footer clearfix text-end">
    <div class="col-md-6 d-flex flex-row m-t-10 sm-m-t-10">
      <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
        <span>Save</span>
        <span class="hidden-block">
          <i class="pg-icon">Add</i>
        </span>
      </button>
      <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-outline-secondary">Cancel</button>
    </div>
  </div>
  </form>

  <script>
    $('#unversity_payment_form').submit(function(e){
        e.preventDefault();
        var formdata = new FormData(this);
        $.ajax({
            url:$(this).attr('action'),
            type:$(this).attr('method'),
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success:function(res)
            {
                if(res.status=='success'){
                    $('.modal').modal('hide');
                    Swal.fire({
                        title: "Done",
                        text: res.message,
                        icon: "success"
                    });
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
              }else{
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text:  res.message,
                });
                $('.swal2-container').css('z-index','9999');
              }              
            }
        })
    })
  </script>