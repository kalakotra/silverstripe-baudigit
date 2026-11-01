<% if $getCurrentMemberRecord %>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-lg-6"></div>
                <div class="col-12 col-lg-6 text-end">
                    <a href="{$Top.Link}" class="btn btn-sm btn-primary px-4">Zurück zur Übersicht</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-body">
                <div class="accordion" id="memberFormAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="memberFormHeading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#memberFormCollapse" aria-expanded="true" aria-controls="memberFormCollapse">
                                Benutzerdaten - $getCurrentMemberRecord.FirstName $getCurrentMemberRecord.Surname
                            </button>
                        </h2>
                        <div id="memberFormCollapse" class="accordion-collapse collapse show" aria-labelledby="memberFormHeading" data-bs-parent="#memberFormAccordion">
                            <div class="accordion-body">
                                $edit
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<% else_if $isAddAction %>
    <div class="card">
        <div class="card-header text-end">
            <a href="{$Top.Link}" class="btn btn-sm btn-primary px-4">Zurück zur Übersicht</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-body">
                $add
            </div>
        </div>
    </div>
<% else %>
    <div class="card">
        <div class="card-header text-end">
            <a type="button" class="btn btn-sm btn-success px-4" href="{$Top.Link}add">Benutzer erstellen</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <% include MemberPageWorkerListTable %>
        </div>
    </div>
<% end_if %>
