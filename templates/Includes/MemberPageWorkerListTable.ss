<div class="table-responsive-">
    <table id="example" class="dataTableMe table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Name</th>
                <th>E-Mail</th>
                <th>Benutzergruppe</th>
                <th>Erstellt am</th>
                <th>Aktiv</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <% loop readAllInputs %>
                <tr>
                    <td>$FirstName $Surname</td>
                    <td>$Email</td>
                    <td align="center">
                        <% if Groups.filter('ID', 1).count > 0 %>
                            <span class="badge bg-danger">Administrator</span>
                        <% else_if Groups.filter('ID', 3).count > 0 %>
                            <span class="badge bg-primary">Arbeiter</span>
                        <% else %>
                            <span class="badge bg-secondary">Keine Gruppe</span>
                        <% end_if %>
                    </td>
                    <td data-order="$Created.Format('yyyyMMdd')">$Created.Format('dd.MM.yyyy')</td>
                    <td align="center">
                        <% if Active %>
                            <span class="badge bg-success">Ja</span>
                        <% else %>
                            <span class="badge bg-danger">Nein</span>
                        <% end_if %>
                    </td>
                    <td class="text-center">
                        <a href="{$Top.Link}edit/$ID" class="" title="Bearbeiten">
                            <i class="bx bx-edit-alt fs-5"></i>
                        </a>
                    </td>
                </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>
