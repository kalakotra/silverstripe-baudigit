<div >
    <table id="projectListTable" class="dataTableMe table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Datum</th>
                <th>Titel</th>
                <th>Kunde</th>
                <th>Kundenobjekt</th>
                <th>Status</th>
                <th data-orderable="false"></th>
            </tr>
        </thead>
        <tbody>
            <% loop readAllInputs %>
                <tr>
                    <td data-order="$Created.Format('yyyyMMdd')">$Created.Format('dd.MM.yyyy')</td>
                    <td>$Title</td>
                    <td>$ClientObject.Client.Company</td>
                    <td>$ClientObject.getFullName</td>
                    <td>
                        <% if Status == 0 %>
                            <span class="badge bg-secondary">$getStatusLabel</span>
                        <% else_if Status == 1 %>
                            <span class="badge bg-primary">$getStatusLabel</span>
                        <% else_if Status == 2 %>
                            <span class="badge bg-success">$getStatusLabel</span>
                        <% else_if Status == 3 %>
                            <span class="badge bg-info">$getStatusLabel</span>
                        <% else_if Status == 4 %>
                            <span class="badge bg-danger">$getStatusLabel</span>
                        <% else %>
                            <span class="badge bg-warning">$getStatusLabel</span>
                        <% end_if %>
                    </td>
                    <td class="text-center"><a href="{$Top.Link}edit/$ID" class=""><i class="bx bx-edit-alt fs-5"></i></a></td>
                </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>
