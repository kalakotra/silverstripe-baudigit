<% with getCurrentRecurringInvoice %>
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
                <% loop Invoices %>
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
                        <td class="text-end">$getGermanStatus</td>
                        <td class="text-end">
                            <a href="{$getFirstInvoicePage.Link}preview/$ID" target="_blank"><i class="bi bi-file-text bi-filetype-pdf fs-5 me-1"></i></a>
                        </td>
                    </tr>
                <% end_loop %>
            </tbody>
        </table>
    </div>
<% end_with %>
