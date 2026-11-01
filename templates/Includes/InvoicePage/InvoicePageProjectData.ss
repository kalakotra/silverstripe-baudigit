<% if Project %>
    <% with Project %>
        <div class="row">
            <div class="col-12 col-lg-6">
                <h5>Projekt Informationen</h5>
                <div class="row mb-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        Projektname ($getStatusLabel):
                    </div>
                    <div class="col-12 col-md-6 col-lg-8">
                        $Title
                    </div>
                    <% if $StartDate %>
                        <div class="col-12 col-md-6 col-lg-4">
                            Startdatum:
                        </div>
                        <div class="col-12 col-md-6 col-lg-8">
                            $StartDate.Format('dd.MM.yyyy')
                        </div>
                    <% end_if %>
                    <% if $EndDate %>
                        <div class="col-12 col-md-6 col-lg-4">
                            Enddatum:
                        </div>
                        <div class="col-12 col-md-6 col-lg-8">
                            $EndDate.Format('dd.MM.yyyy')
                        </div>
                    <% end_if %>
                    <div class="col-12">
                        Projektbeschreibung:
                    </div>
                    <div class="col-12">
                        $Description
                    </div>
                </div>
            </div>
            <div class="col-12 ">
                <div >
                    <hr />
                    <h5>Projekt Aufgaben</h5>
                    <table id="invoice_project_{$ID}_Task" class="dataTableMe table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Aufgaben</th>
                            </tr>
                        </thead>
                        <tbody>
                            <% loop ProjectTasks %>
                                <tr>
                                    <td>
                                        $Title 
                                        <span class="$getHoursStatus">$EstimatedHours / $getSumHours</span>
                                        <% if Status == 0 %>
                                            <span class="badge bg-danger">$getStatusLabel</span>
                                        <% else_if Status == 1 %>
                                            <span class="badge bg-warning">$getStatusLabel</span>
                                        <% else_if Status == 2 %>
                                            <span class="badge bg-success">$getStatusLabel</span>
                                        <% else %>
                                            <span class="badge bg-primary">$getStatusLabel</span>
                                        <% end_if %>
                                        <% if Description %>
                                            <p><small>$Description</small></p>
                                        <% end_if %>
                                        <div class="accordion" id="accordionProjectTasks_{$ID}">
                                            <% loop getProjectTasksGroupedByMember.GroupedBy('LoggedByID') %>
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header " id="accordionProjectTasks_heading_{$Up.ID}_$LoggedByID">
                                                        <button class="accordion-button collapsed p-1" type="button" data-bs-toggle="collapse" data-bs-target="#accordionProjectTasks_collapse_{$Up.ID}_$LoggedByID" aria-expanded="false" aria-controls="accordionProjectTasks_collapse_{$Up.ID}_$LoggedByID">
                                                            <small>$Top.getUserByID($LoggedByID).Title</small>
                                                        </button>
                                                    </h2>
                                                    <div id="accordionProjectTasks_collapse_{$Up.ID}_$LoggedByID" class="accordion-collapse collapse" aria-labelledby="accordionProjectTasks_heading_{$Up.ID}_$LoggedByID" data-bs-parent="#accordionProjectTasks_{$Up.ID}">
                                                        <div class="accordion-body">
                                                            <% loop Children %>
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        $Hours Stunde(n) / $Created.Format('dd.MM.yyyy H:mm')
                                                                        <% if Description %>
                                                                            <p><small>$Description</small></p>
                                                                        <% end_if %>
                                                                        <% if $TaskImages %>
                                                                            <div class="row">
                                                                                <% loop $TaskImages %>
                                                                                    <div class="col-auto py-3">
                                                                                        <a href="$Link" data-fancybox="gallery" data-caption="{$Up.Created.Format('dd.MM.yyyy H:mm')} - $Up.LoggedBy.FirstName $Up.LoggedBy.LastName">
                                                                                            $Fill(150,150)
                                                                                        </a>
                                                                                    </div>
                                                                                <% end_loop %>
                                                                            </div>
                                                                            <hr />
                                                                        <% end_if %>
                                                                    </div>
                                                                </div>
                                                            <% end_loop %>
                                                        </div>
                                                    </div>
                                                </div>
                                            <% end_loop %>
                                        </div>
                                    </td>
                                </tr>
                            <% end_loop %>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <% end_with %>
<% end_if %>
