<h5>Projektaufgaben</h5>
<table id="projectTaskTable" class="dataTableMe table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Beschreibung</th>
            <th>Zugewiesen an</th>
            <th>Dauer</th>
            <th>Status</th>
            <th data-orderable="false"></th>
        </tr>
    </thead>
    <tbody>
        <% loop getAllProjectTasks %>
            <tr <% if NeedsConfirmation %>class="table-warning-"<% end_if %>>
                <td>
                    $Title
                    <br />
                    <small>$Description</small>
                </td>
                <td>
                    <% loop AssignedTo %>
                        $FirstName $Surname<br />
                    <% end_loop %>
                </td>
                <td>
                    Von: $StartDate.Format('dd.MM.yyyy')
                    <br />
                    Bis: $EndDate.Format('dd.MM.yyyy')
                </td>
                <td>
                    <% if Status == 0 %>
                        <span class="badge bg-danger">$getStatusLabel</span>
                    <% else_if Status == 1 %>
                        <span class="badge bg-warning">$getStatusLabel</span>
                    <% else_if Status == 2 %>
                        <span class="badge bg-success">$getStatusLabel</span>
                    <% else %>
                        <span class="badge bg-primary">$getStatusLabel</span>
                    <% end_if %>
                </td>
                <td class="text-center">
                    <a href="{$Top.Link}edit/{$ProjectID}/task/$ID?activetab=project_tasks" class="" title="Bearbeiten">
                        <i class="bx bx-edit-alt fs-5"></i>
                    </a>
                    <a href="{$Top.Link}task/delete/$ID" class="yousure" title="Löschen">
                        <i class="bx bx-x-circle text-danger fs-4"></i>
                    </a>
                </td>
            </tr>
        <% end_loop %>
    </tbody>
</table>
