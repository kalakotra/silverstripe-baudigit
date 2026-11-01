<table id="invoiceArticleTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Titel</th>
            <th>Menge</th>
            <th class="text-end">Preis (netto)</th>
            <th class="text-end">Rabatt</th>
            <th class="text-end">MwSt.</th>
            <th class="text-end">Gesamtpreis</th>
            <% if $getCurrentInvoice.checkEditingStatus %>
                <th data-orderable="false"></th>
            <% end_if %>
        </tr>
    </thead>
    <tbody>
        <% loop getAllInvoiceArticles %>
            <tr data-article-id="$ID">
                <td>$Title<% if $Description %><br><small class="text-muted">$Description</small><% end_if %></td>
                <td>$Quantity $getUnitLabel</td>
                <td class="text-end">$formatPrice($SellPriceNetto)</td>
                <td class="text-end">({$Discount}%) $formatPrice($getDiscountAmount)</td>
                <td class="text-end">({$TaxRate}%) $formatPrice($getTaxAmount)</td>
                <td class="text-end">$formatPrice($FinalPrice)</td>
                <% if $Invoice.checkEditingStatus %>
                    <td class="text-center">
                        <a href="#" class="js-edit-article me-1"
                            data-id="$ID"
                            data-title="$Title.ATT"
                            data-quantity="$Quantity"
                            data-unit="$Unit"
                            data-sellpricenetto="$SellPriceNetto"
                            data-discount="$Discount"
                            data-taxrate="$TaxRate"
                            data-description="$Description.ATT">
                            <i class="bx bx-edit-alt fs-5"></i>
                        </a>
                        <a href="#" class="js-delete-article text-danger" data-id="$ID">
                            <i class="bx bx-x-circle fs-5"></i>
                        </a>
                    </td>
                <% end_if %>
            </tr>
        <% end_loop %>
    </tbody>
    <% with getCurrentInvoice %>
        <tfoot>
            <tr>
                <th colspan="5" class="text-end">Gesamt (netto):</th>
                <th class="text-end" id="total-netto">$Top.formatPrice($getTotalNetto)</th>
                <% if checkEditingStatus %>
                    <th></th>
                <% end_if %>
            </tr>
            <tr>
                <th colspan="5" class="text-end">Gesamt MwSt.:</th>
                <th class="text-end" id="total-tax">$Top.formatPrice($getTotalTax)</th>
                <% if checkEditingStatus %>
                    <th></th>
                <% end_if %>
            </tr>
            <tr>
                <th colspan="5" class="text-end">Gesamt (brutto):</th>
                <th class="text-end" id="total-brutto">$Top.formatPrice($getTotalBrutto)</th>
                <% if checkEditingStatus %>
                    <th></th>
                <% end_if %>
            </tr>
        </tfoot>
    <% end_with %>
</table>
