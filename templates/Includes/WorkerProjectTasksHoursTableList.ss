<table id="workerProjectTasksHoursTable" class="dataTableMe table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Stunden</th>
            <th>Aufgabe</th>
        </tr>
    </thead>
    <tbody>
        <% loop getActuallMonthWorkerTasksHours %>
            <tr <% if NeedsConfirmation %>class="table-warning-"<% end_if %>>
                <td>
                    <strong>Stunden: $Hours</strong>
                </td>
                <td>
                    <% if Description %>
                        <small>$Description</small>
                        
                        <br />
                        <hr />
                    <% end_if %>
                    $ProjectTask.Title
                    <br />
                    <small>
                        $ProjectTask.Project.ClientObject.Name $ProjectTask.Project.ClientObject.Address {$ProjectTask.Project.ClientObject.PostalCode}, $ProjectTask.Project.ClientObject.City
                    </small>
                </td>
            </tr>
        <% end_loop %>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2" align="right"><strong>Gesamtstunden: $getActuallMonthWorkerTasksHoursSum</strong></td>
        </tr>
    </tfoot>
</table>