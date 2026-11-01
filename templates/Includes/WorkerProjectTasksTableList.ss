<table id="workerProjectTasksTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Titel/Beschreibung</th>
        </tr>
    </thead>
    <tbody>
        <% loop getAssignedTasks %>
            <tr <% if NeedsConfirmation %>class="table-warning-"<% end_if %>>
                <td>
                    $Title
                    <br />
                    <small>$Description</small>
                    <br />
                    <div class="row">
                        <div class="col-6 mt-2">
                            <a href="https://www.google.com/maps/place/{$Project.ClientObject.Address}+{$Project.ClientObject.PostalCode},+{$Project.ClientObject.City}" target="_blank" class="btn btn-primary px-4">
                                <i class="bi bi-geo-alt me-1 fs-6  text-white"></i>
                            </a>
                        </div>
                        <% if canSignHours %>
                            <div class="col-6 text-end mt-2">
                                <a href="$Top.Link?TaskID=$ID" class="btn btn-success px-4">
                                    <i class="bi bi-clock-history me-1 fs-6  text-white"></i>
                                </a>
                            </div>
                        <% end_if %>
                    </div>
                </td>
            </tr>
        <% end_loop %>
    </tbody>
</table>