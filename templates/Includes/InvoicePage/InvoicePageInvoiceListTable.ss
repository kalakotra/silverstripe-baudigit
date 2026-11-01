<div >
    <table id="invoicePageInvoiceListTable" class="dataTableMe table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>R.Nr.</th>
                <th>Kunde</th>
                <th>Datum</th>
                <th>Fäll.datum</th>
                <th>Brutto</th>
                <th>Status</th>
                <th data-orderable="false"></th>
            </tr>
        </thead>
        <tbody>
            <% loop readAllInputs %>
                <tr>
                    <td>$InvoiceNumber</td>
                    <td>
                        $ClientObject.getFullName
                        <br />
                        <small>$ClientObject.Client.Company</small>
                    </td>
                    <td data-order="$Created.Format('yyyyMMdd')">$Created.Format('dd.MM.yyyy')</td>
                    <td data-order="$DueDate.Format('yyyyMMdd')">$DueDate.Format('dd.MM.yyyy')</td>
                    <td class="text-end">$getTotalBruttoAsString</td>
                    <td class="text-end">
                        <% if Status == 0 %>
                            <span class="badge bg-secondary">$getGermanStatus</span>
                        <% else_if Status == 1 %>
                            <span class="badge bg-warning">$getGermanStatus</span>
                        <% else_if Status == 2 %>
                            <span class="badge bg-success">$getGermanStatus</span>
                        <% else_if Status == 3 %>
                            <span class="badge bg-danger">$getGermanStatus</span>
                        <% else_if Status == 4 %>
                            <span class="badge bg-danger">$getGermanStatus</span>
                        <% else %>
                            <span class="badge bg-warning">$getGermanStatus</span>
                        <% end_if %>
                    </td>
                    <td class="text-end">
                        <% if not checkEditingStatus %>
                            <a href="{$Top.Link}preview/$ID" target="_blank"><i class="bi bi-file-text bi-filetype-pdf fs-5 me-1"></i></a>
                        <% end_if %>
                        <% if Status == 1 %>
                            <a href="{$Top.Link}change-status/$ID/2" class="yousure text-success me-1" title="Als bezahlt markieren"><i class="bx bx-check-circle fs-5"></i></a>
                        <% end_if %>
                        <a href="{$Top.Link}edit/$ID"><i class="bx bx-edit-alt fs-5"></i></a>
                    </td>
                </tr>
            <% end_loop %>
        </tbody>
    </table>
</div>
