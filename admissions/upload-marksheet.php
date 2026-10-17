<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>

<div class="page-container">
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>

  <div class="page-content-wrapper">
    <div class="content">
      <div class="jumbotron" data-pages="parallax">
        <div class="container-fluid sm-p-l-0 sm-p-r-0">
          <div class="inner">
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
              <?php
              $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
              for ($i = 1; $i <= count($breadcrumbs); $i++) {
                if (count($breadcrumbs) == $i) :
                  $active = "active";
                  $crumb = explode("?", $breadcrumbs[$i]);
                  echo '<li class="breadcrumb-item ' . $active . '">' . htmlspecialchars($crumb[0]) . '</li>';
                endif;
              }
              ?>
              <div>
                <?php if ($_SESSION['Role'] != "University Head") { ?>
                  <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Department" onclick="add('upload-marksheet', 'md')">
                    <i class="uil uil-plus-circle"></i>
                  </button>
                <?php } ?>
              </div>
            </ol>
          </div>
        </div>
      </div>

      <div class="container-fluid">
        <div class="card card-transparent">
          <div class="card-header">
            <div class="pull-right">
              <div class="col-xs-12">
                <input type="text" id="departments-search-table" class="form-control pull-right" placeholder="Search">
              </div>
            </div>
            <div class="clearfix"></div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover nowrap" id="upload_marksheet-table" style="width:100%;">
                <thead>
                  <tr>
                    <th>S No</th>
                    <th>Name</th>
                    <th>File</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>

  <script type="text/javascript">
    $(function() {
      var role = '<?= addslashes($_SESSION['Role']) ?>';
      var show = (role === 'Administrator');

      var table = $('#upload_marksheet-table').DataTable({
        processing: true,
        serverSide: true,
        serverMethod: 'post',
        ajax: {
          url: '/app/upload-marksheet/server.php', // adjust path if needed
        },
        columns: [
          {
            data: null,
            orderable: false,
            searchable: false,
            render: function(data, type, row, meta) {
              return meta.row + meta.settings._iDisplayStart + 1;
            }
          },
          { data: "file_name" },
          {
            data: "file_path",
            visible: show,
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
              if (type === 'display') {
                  
                return '<a href="../' +(data) + '" target="_blank" class="btn btn-sm btn-outline-primary">View</a>';
              }
              return data;
            }
          },
          {
           data: "ID",
           visible: show,
            orderable: false,
           searchable: false,
          render: function(data, type, row) {
        
         return '<div class="button-list text-start">' +
          '<a href="../' + row.file_path + '" download class="btn btn-sm btn-outline-success me-1 border-0"><i class="uil uil-download-alt"></i></a>' +
         '<i class="uil uil-trash icon-xs cursor-pointer text-danger" onclick="destroy(\'upload-marksheet\', \'' + data + '\')"></i>' +
         '</div>';
  
          }
          }
        ],
        order: [], // disable initial ordering
        lengthMenu: [10, 25, 50],
        pageLength: 25,
        language: {
          lengthMenu: "_MENU_ ",
          info: "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
        },
        sDom: "<t><'row'<'col-sm-6'i><'col-sm-6'p>>",
        scrollCollapse: true,
        destroy: true,
      });

      $('#departments-search-table').on('keyup', function() {
        table.search(this.value).draw();
      });
    });
  </script>

  <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>
