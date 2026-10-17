<?php
require '../../includes/db-config.php';
session_start();
//if($_SESSION['university_id'] == 48){}
?>

<!-- Modal -->
 <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
  <style>
    .select2-container--default.select2-container--focus .select2-selection--multiple {
      border: unset !important;
      outline: 0;
    }
  </style>
<div class="modal-header clearfix text-left">
    <button aria-label="" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="pg-icon">close</i>
    </button>
    <h5 class="font-weight-bold text-blank">Import <span class="semi-bold">Result</span></h5>
</div>
<style>
    .modal-open .select2-container {
        z-index: auto;
    }
</style>
<form role="form" id="form-import-results" action="/app/results/excel-import-server" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
        <!-- University & Course -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group form-group-default required">
                    <input name="file" type="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group required">
                    <!--<label style="height:0px !important;"></label>-->
                    <div class="col-md-12 d-flex justify-content-end">
                    <div class="  cursor-pointer add_btn_form" style="width:max-content !important;" onclick="window.open('/app/samples/exam-result');">
                        <i class="ti ti-download"></i><u><span class=" ml-1">Download Sample</span></u>
                    </div></div>
                </div>
            </div>
        </div>
        
    </div>

    

    <div class="modal-footer clearfix justify-content-center">
        <div class="col-md-12 text-end m-t-10 sm-m-t-10">
            <!--<button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">-->
            <!--    <span>Save</span>-->
            <!--    <span class="hidden-block">-->
            <!--        <i class="pg-icon">tick</i>-->
            <!--    </span>-->
            <!--</button>-->
        <button aria-label="" type="button" data-dismiss="modal" class=" btn btn-secondary mr-2">
        <i class="ti ti-circle-dashed-x mr-2"></i> Close</button>
        <button aria-label="" type="submit" class="btn btn-primary ">
        <i class="ti ti-circle-check mr-2"></i> Save</button>
        </div>
    </div>
</form>


<script>
   
   $('#form-import-results').submit(function(e) {
		$('.modal').modal('hide');
		location.reload();
		//$('#results-table').DataTable().ajax.reload(null, false);
		notification('success', data.message);
	});


    
</script>