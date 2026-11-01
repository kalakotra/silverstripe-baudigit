<div class="table-responsive-">
    <table id="offerListTable" class="dataTableMe table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Datum</th>
                <th>Nr.</th>
                <th>Kunde</th>
                <th>Gesamt (brutto)</th>
                <th>Status</th>
                <th data-orderable="false"></th>
            </tr>
        </thead>
        <tbody>
            <% loop readAllInputs %>
                <tr>
                    <td data-order="$Created.Format('yyyyMMdd')">
                        $Created.Format('dd.MM.yyyy')
                    </td>
                    <td>$OfferNumber</td>
                    <td>$ClientObject.getFullName</td>
                    <td class="text-end">$Top.formatPrice($getTotalBrutto)</td>
                    <td>
                        <% if Status == 0 %>
                            <span class="badge bg-secondary">$getGermanStatus</span>
                        <% else_if Status == 1 %>
                            <span class="badge bg-warning">$getGermanStatus</span>
                        <% else_if Status == 2 %>
                            <span class="badge bg-success">$getGermanStatus</span>
                        <% else_if Status == 3 %>
                            <span class="badge bg-info">$getGermanStatus</span>
                        <% else_if Status == 4 %>
                            <span class="badge bg-danger">$getGermanStatus</span>
                        <% else %>
                            <span class="badge bg-warning">$getGermanStatus</span>
                        <% end_if %>
                    </td>
                    <td class="text-center"><a href="{$Top.Link}edit/$ID" class=""><i class="bx bx-edit-alt fs-5"></i></a></td>
                </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>
