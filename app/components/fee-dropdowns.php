<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingFeeDropDowns">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFeeDropDowns" aria-expanded="false" aria-controls="collapseFeeDropDowns">
          Fee Dropdowns
        </a>
    </div>
  </div>
  <div id="collapseFeeDropDowns" class="collapse" role="tabcard" aria-labelledby="headingFeeDropDowns">
    <div class="card-body">

      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn btn-primary" onclick="addComponents('fee-dropdowns', 'md', <?=$university_id?>)">Add</button>
        </div>
      </div>

      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableFeeDropDowns">
              <thead>
                <tr>
                  <th width="30%">Name</th>
                  <th>Admission Type</th>
                  <th>
                    <?php 
                      $modes = $conn->query("SELECT GROUP_CONCAT(Name SEPARATOR '/') as Mode FROM Modes WHERE University_ID = ".$university_id." GROUP BY University_ID");
                      $modes = $modes->fetch_assoc();
                      $modes = $modes['Mode'];
                    ?>
                    Fee Structure - <?=$modes?>
                  </th>
                  <th>Coupon</th>
                  <th>Late Fee</th>
                  <th data-orderable="false">Status</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  var table = $('#tableFeeDropDowns');
  var settings = {
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'ajax': {
      'url':'/app/components/fee-dropdowns/server',
      type: 'POST',
      "data":function(data) {
        data.university_id = '<?=$university_id?>';
      },
    },
    'columns': [  
      { data: "Name"},
      { data: "Admission_Type_ID"},
      { data: "Fee_Structure"},
      { data: "Coupon",
        visible: false
      },
      { data: "Late_Fee",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeLateFeeStatus( \''+row.ID+'\');" type="checkbox" '+checked+' id="fee-dropdown-late-status-switch-'+row.ID+'">\
            <label for="fee-dropdown-late-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      },
      { data: "Status",
        "render": function(data, type, row){
          var active = data==1 ? 'Active' : 'Inactive';
          var checked = data==1 ? 'checked' : '';
          return '<div class="form-check form-check-inline switch switch-lg success">\
            <input onclick="changeComponentStatus(\'Fee_Dropdowns\', \'FeeDropDowns\', \''+row.ID+'\');" type="checkbox" '+checked+' id="fee-dropdown-status-switch-'+row.ID+'">\
            <label for="fee-dropdown-status-switch-'+row.ID+'">'+active+'</label>\
          </div>';
        }
      }
    ],
    "sDom": "<t><'row'<p i>>",
    "destroy": true,
    "scrollCollapse": true,
    "oLanguage": {
        "sLengthMenu": "_MENU_ ",
        "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
    },
    "aaSorting": [],
    "iDisplayLength": 5
  };

  table.dataTable(settings);
</script>

<script>
  function changeLateFeeStatus(id){
    $.ajax({
      url:'/app/components/fee-dropdowns/late-fee',
      type:'POST',
      data:{id},
      dataType:'json',
      success: function(data) {
        if(data.status){
          notification('success', data.message);
          $('#tableFeeDropDowns').DataTable().ajax.reload(null, false);
        }else{
          notification('danger', data.message);
          $('#tableFeeDropDowns').DataTable().ajax.reload(null, false);
        }
      }
    })
  }
</script>
