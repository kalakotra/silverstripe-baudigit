<div class="table-responsive-">
    <table id="offerListTable" class="dataTableMe table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Start</th>
                <th>Ende</th>
                <th>Rechnungsintervall</th>
                <th>Kunde</th>
                <th>Gesamt (brutto)</th>
                <th>Status</th>
                <th data-orderable="false"></th>
            </tr>
        </thead>
        <tbody>
            <% loop readAllInputs %>
                <tr>
                    <td data-order="$StartDate.Format('yyyyMMdd')">
                        $StartDate.Format('dd.MM.yyyy')
                    </td>
                    <td data-order="$ValidUntil.Format('yyyyMMdd')">
                        $ValidUntil.Format('dd.MM.yyyy')
                    </td>
                    <td>$getGermanInterval</td>
                    <td>$ClientObject.getFullName</td>
                    <td class="text-end">$getTotalBruttoAsString</td>
                    <td class="text-end">
                        <span class="badge <% if $Status=1 %>bg-success<% else %>bg-secondary<% end_if %>">$getGermanStatus</span>
                    </td>
                    <td class="text-end"><a href="{$Top.Link}edit/$ID" class=""><i class="bx bx-edit-alt fs-5"></i></a></td>
                </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>
