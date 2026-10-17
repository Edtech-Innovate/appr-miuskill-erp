<div class="card card-default m-b-0">
  <div class="card-header " role="tab" id="headingEmails">
    <div class="card-title">
      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseEmails" aria-expanded="false" aria-controls="collapseEmails">
        Emails
      </a>
    </div>
  </div>
  <div id="collapseEmails" class="collapse" role="tabcard" aria-labelledby="headingEmails">
    <div class="card-body">
      <div class="row p-b-20">
        <div class="col-lg-12 text-end">
          <button type="button" class="btn btn-primary" onclick="addComponents('emails', 'full', '')">Add</button>
        </div>
      </div>
      <div class="row p-b-20">
        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover nowrap" id="tableEmails">
              <thead>
                <tr>
                  <th width="50%">Name</th>
                  <th width="50%">Subject</th>
                  <th data-orderable="false">Vertical</th>
                  <th data-orderable="false"></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
