<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php'); ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/menu.php'); ?>

<!-- START PAGE-CONTAINER -->
<div class="page-container">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/topbar.php'); ?>

    <!-- START PAGE CONTENT WRAPPER -->
    <div class="page-content-wrapper">
        <div class="content">
            <div class="jumbotron">
                <div class="container-fluid">
                    <div class="inner">
                        <!-- START BREADCRUMB -->
                        <ol class="breadcrumb d-flex flex-wrap justify-content-between">
                            <?php
                            $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                            foreach ($breadcrumbs as $i => $crumb) {
                                if ($i == count($breadcrumbs) - 1) {
                                    // echo '<li class="breadcrumb-item active">' . ucwords(str_replace("-", " ", $crumb)) . '</li>';
                                    echo '<li class="breadcrumb-item active">Center Authorization Certificate</li>';
                                }
                            }
                            ?>
                            <div>
                                <button class="custom_add_button btn-primary  border-none rounded-pill" data-toggle="tooltip" title="Add Authorize Center"
                                    onclick="add('center_authorize','lg')">Add <i class="uil uil-plus-circle ml-2"></i></button>
                                <!--<button class="custom_add_button" onclick="bulk_pdf()" aria-label="" title="" data-toggle="tooltip" data-original-title="Download Authorize Center">-->
                                <!--    <i class="uil uil-down-arrow"></i>-->
                                <!--</button>-->
                                <button class="custom_add_button btn-primary  border-none rounded-pill" onclick="bulk_pdf('zip')" 
    aria-label="" title="" data-toggle="tooltip" data-original-title="Download ZIP (Separate PDFs)">
    <i class="uil uil-folder-download"></i>
</button>

<!--<button class="custom_add_button" onclick="bulk_pdf('merge')" -->
<!--    aria-label="" title="" data-toggle="tooltip" data-original-title="Download Merged PDF (Color)">-->
<!--    <i class="uil uil-file-plus"></i>-->
<!--</button>-->
                                
                                <button class="custom_add_button btn-primary  border-none rounded-pill" onclick="upload_autorize_center()" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload Authorize Center">
                                    <i class="uil uil-upload"></i>
                                </button>
                            </div>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- START TABLE SECTION -->
            <div class="container-fluid">
                <div class="card card-transparent">
                    <div class="card-header">
                        <div class="d-flex justify-between row" style="justify-content:space-between;">
                            <div class="col-lg-3 col-sm-12">
                                <select class="full-width" style="width:40px" data-init-plugin="select2" id="batch_pdf"
                                    onchange="addFilter(this.value, 'batch_pdf')" data-placeholder="Choose Batch">
                                    <option value="">Choose Batch</option>
                                    <?php $batchs = $conn->query("SELECT distinct batch FROM center_authorize  order by batch ASC");
                                    while ($batch = $batchs->fetch_assoc()) {
                                        echo '<option value="' . $batch['batch'] . '">' . $batch['batch'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <!--<div class="col-lg-3 col-sm-12">-->
                            <!--    <select class="form-control w-100" style="width:40px" id="batch_pdf"-->
                            <!--        onchange="addFilterprograms_type(this.value, 'programs_type')" data-placeholder="Choose Batch">-->
                            <!--        <option value="">Select Programs</option>-->
                            <!--        <option value="47">Bvoc</option>-->
                            <!--        <option value="48">Skill</option>-->
                            <!--    </select>-->
                            <!--</div>-->
                            <div class="col-lg-2 col-sm-12">
                                <input type="text" id="autorize_center-search-table" class="form-control" placeholder="Search">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover nowrap w-100 table-responsive" id="autorize_center-table">
                            <thead>
                                <tr>
                                    <th>Center Name</th>
                                    <th>Type</th>
                                    <th>Date of Issue</th>
                                    <th>Recieved Vertical</th>
                                    <th>Address</th>
                                    <th>Batch</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                    <th>Payment Proof</th>
                                    <th>Status</th>
                                    <th>Receiving Date</th>
                                    <th>Dispatch Date</th>
                                    <th>Center Document </th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END TABLE SECTION -->
        </div>
    </div>
 <!--<i class="uil uil-file-download icon-xs cursor-pointer text-danger"-->
 <!--                             data-toggle="tooltip"-->
 <!--                             title="Download PDF"-->
 <!--                             onclick="colordownload_center_pdf('${data}','color')"></i>-->
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php'); ?>
    <script>
        // let selectedBatch = "";

        // function addFilter(batch, by) {
        //     selectedBatch = batch;
        //     $('#autorize_center-table').DataTable().ajax.reload();
        // }
        // let selectProgramsType = "";

        // function addFilterprograms_type(programtype, by) {
        //     let selectedPrograms = programtype;
        //     console.log(selectedPrograms); //  Correct variable name

        //     // If you need to send it to DataTable server-side
        //     $('#autorize_center-table').DataTable().ajax.reload();
        // }

        // function bulk_pdf() {
        //     if (selectedBatch && selectedBatch !== "0") {

        //         window.open('/app/center_authorize/parnter_pdf.php?batch=' + selectedBatch, '&programs_type'+selectedPrograms+'_blank');
        //     } else {

        //         window.open('/app/center_authorize/parnter_pdf.php?batch=all', '_blank');
        //     }
        // }
        let selectedBatch = "";
        let selectedPrograms = ""; // make it global

        function addFilter(batch, by) {
            selectedBatch = batch;
            $('#autorize_center-table').DataTable().ajax.reload();
        }

        function addFilterprograms_type(programtype, by) {
            selectedPrograms = programtype; // assign to global variable
            console.log(selectedPrograms);

            $('#autorize_center-table').DataTable().ajax.reload();
        }

        // function bulk_pdf() {
        //     let url = "/app/center_authorize/parnter_pdf.php?";

        //     // Add batch filter
        //     if (selectedBatch && selectedBatch !== "0") {
        //         url += "batch=" + encodeURIComponent(selectedBatch);
        //     } else {
        //         url += "batch=all";
        //     }

        //     // Add program type filter (if selected)
        //     if (selectedPrograms && selectedPrograms !== "") {
        //         url += "&programs_type=" + encodeURIComponent(selectedPrograms);
        //     }

        //     // open in new tab
        //     window.open(url, "_blank");
        // }
function bulk_pdf(mode = "zip") {
    let url = "/app/center_authorize/parnter_pdf.php?";

    // Add batch filter
    if (selectedBatch && selectedBatch !== "0") {
        url += "batch=" + encodeURIComponent(selectedBatch);
    } else {
        url += "batch=all";
    }

    // Add program type filter
    if (selectedPrograms && selectedPrograms !== "") {
        url += "&programs_type=" + encodeURIComponent(selectedPrograms);
    }

    // Always color when merging
    if (mode === "merge") {
        url += "&colortype=color&output=merge";
    } else {
        url += "&output=zip";
    }

    // open in new tab
    window.open(url, "_blank");
}

        function upload_autorize_center() {
            $.ajax({
                url: '/app/center_authorize/bulkupload_center_authorize',
                type: 'GET',
                success: function(data) {
                    $('#lg-modal-content').html(data);
                    $('#lgmodal').modal('show');
                }
            })
        }
        $(document).ready(function() {
            var table = $('#autorize_center-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/app/center_authorize/server.php',
                    type: 'POST',
                    data: function(d) {
                        d.batch_id = selectedBatch;
                    }
                },
                columns: [{
                        data: "center_name",
                        width: "25%"
                    },
                    {
                        data: "type_id",
                        width: "15%",
                        render: function(data, type, row) {
                            if (data == 47) return "Bvoc";
                            if (data == 48) return "Skill";
                            return data;
                        }
                    },
                    {
                        data: "date_of_issue",
                        width: "15%"
                    },
                      {
                        data: "recieved_vertical",
                        width: "15%"
                    },
                    {
                        data: "address",
                        width: "25%"
                    },
                    {
                        data: "batch",
                        width: "25%"
                    },
                    {
                        data: "payment_type",
                        width: "25%"
                    },
                    {
                        data: "amount",
                        width: "25%"
                    },
                    {
                        data: "payment_proof",
                        width: "25%",
                        render: function(data, type, row) {
                            // console.log(data);
                            // return false;
                            let id = row.id;
                            if (row.payment_proof && row.payment_proof.trim() !== "") {
                                return ` <i class="uil uil-eye icon-xs cursor-pointer text-primary"
                               data-toggle="tooltip"
                               title="Preview"
                               onclick="center_document('${id}', 'payment_proof')"></i>`;
                            } else {
                                return `<p>N/A</p>`;
                            }
                        }
                    },
                    {
                        data: "status",
                        width: "20%",
                        render: function(data, type, row) {
                            let badgeClass = "badge-secondary";
                            if (data === "pending") badgeClass = "badge-warning";
                            if (data === "received") badgeClass = "badge-info";
                            if (data === "dispatch") badgeClass = "badge-success";

                            return `<span class="badge ${badgeClass}">${data.toUpperCase()}</span>`;
                        }
                    },
                    {
                        data: "receiving_date",
                        width: "25%",
                        render: function(data, type, row) {

                            let id = row.id;
                            if (data && data.trim() !== "") {
                                // If date already exists → show the date only
                                return data;
                            } else {
                                // If date is empty → show edit button
                                return `
                <i class="uil uil-edit icon-xs cursor-pointer custom_edit_button"
                   onclick="datemodal('receiving_date', '${data}', 'md','${id}')"></i>
            `;
                            }
                        }
                    },
                    //         {
                    //             data: "dispatch_date",
                    //             width: "25%",
                    //             render: function(data, type, row) {
                    //                 let id = row.id;
                    //                 if (data && data.trim() !== "") {
                    //                     return data;
                    //                 } else {
                    //                     return `
                    //     <i class="uil uil-edit icon-xs cursor-pointer custom_edit_button"
                    //        onclick="datemodal('dispatch_date', '${data}', 'md','${id}')"></i>
                    // `;
                    //                 }
                    //             }
                    //         },
                    {
                        data: "dispatch_date",
                        width: "25%",
                        render: function(data, type, row) {
                            let id = row.id;

                            // Only show dispatch if receiving_date is filled
                            // console.log(row.receiving_date);
                            if (row.receiving_date && row.receiving_date.trim() !== "") {
                                if (data && data.trim() !== "") {
                                    return data; // already filled
                                } else {
                                    return `
                    <i class="uil uil-edit icon-xs cursor-pointer custom_edit_button"
                       onclick="datemodal('dispatch_date', '${data}', 'md','${id}')"></i>
                `;
                                }
                            } else {
                                return `<span class="text-muted"></span>`; // not allowed yet
                            }
                        }
                    }, {
                        data: "center_doc",
                        width: "25%",
                        render: function(data, type, row) {
                            // console.log(data);
                            // return false;
                            if (row.center_doc && row.center_doc.trim() !== "") {
                                let id = row.id;
                                return `<i class="uil uil-eye icon-xs cursor-pointer text-primary"
                       data-toggle="tooltip"
                       title="Preview"
                       onclick="center_document('${id}', 'center_doc')"></i>`;
                            } else {
                                return `<p>N/A</p>`;
                            }
                        }
                    },
                    {
                        data: "id",
                        width: "20%",
                        className: "text-center",
                        render: function(data, type, row) {
                            let id = row.id;
                            return `<div class="button-list d-flex justify-content-center gap-2">   
                                <i class="uil uil-upload icon-xs cursor-pointer"
                                onclick="datemodal('center_doc', '${data}', 'md','${id}')"></i>   
                            <i class="uil uil-eye icon-xs cursor-pointer text-primary"
                               data-toggle="tooltip"
                               title="Preview"
                               onclick="preview_center('${data}')"></i>
                            <i class="uil uil-file-download icon-xs cursor-pointer text-black"
                              data-toggle="tooltip"
                              title="Download PDF"
                              onclick="download_center_pdf('${data}')"></i>
                               `;
                        }
                    }
                ],
                dom: "<t><'row'<p i>>",
                language: {
                    lengthMenu: "_MENU_ ",
                    info: "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },
                pageLength: 25
            });

            $('#autorize_center-search-table').on('input', function() {
                table.search(this.value).draw();
            });

            $('[data-toggle="tooltip"]').tooltip();
        });

        function center_document(id, doc_type) {
            $.ajax({
                url: '/app/center_authorize/center_document.php',
                type: 'GET',
                data: {
                    id: id,
                    type: doc_type
                },
                success: function(data) {
                    $('#lg-modal-content').html(data);
                    $('#lgmodal').modal('show');
                }
            });
        }

        function preview_center(id) {
            $.ajax({
                url: '/app/center_authorize/preview.php?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#lg-modal-content').html(data);
                    $('#lgmodal').modal('show');
                }
            });
        }

        function datemodal(field, value, size = 'md', id) {
            $.ajax({
                url: '/app/center_authorize/date_modal.php',
                type: 'GET',
                data: {
                    field: field,
                    value: value,
                    id: id
                },
                success: function(data) {
                    console.log(data);
                    $('#' + size + '-modal-content').html(data);
                    $('#' + size + 'modal').modal('show');
                },
                error: function(xhr) {
                    console.error("Error loading date modal:", xhr.responseText);
                    notification('danger', 'Failed to load date modal.');
                }
            });
        }

        function download_center_pdf(id) {
            window.open('/app/center_authorize/parnter_pdf.php?id=' + id, '_blank');
        }

        function colordownload_center_pdf(id, type) {
            console.log(type);
            window.open('/app/center_authorize/parnter_pdf.php?id=' + id + '&colortype=' + type, '_blank');
        }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php'); ?>