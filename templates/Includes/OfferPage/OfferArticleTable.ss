<table id="offerArticleTable" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Titel</th>
            <th>Menge</th>
            <th class="text-end">Preis (netto)</th>
            <th class="text-end">Rabatt</th>
            <th class="text-end">MwSt.</th>
            <th class="text-end">Gesamtpreis</th>
            <th data-orderable="false"></th>
        </tr>
    </thead>
    <tbody>
        <% loop getAllOfferArticles %>
            <tr>
                <td>$Title</td>
                <td>$Quantity $getUnitLabel</td>
                <td class="text-end">$formatPrice($SellPriceNetto)</td>
                <td class="text-end"> ({$Discount}%) $formatPrice($getDiscountAmount)</td>
                <td class="text-end"> ({$TaxRate}%) $formatPrice($getTaxAmount)</td>
                <td class="text-end">$formatPrice($FinalPrice)</td>
                <td class="text-center">
                    <a href="{$Top.Link}edit/{$OfferID}/article/$ID?activetab=offer_article" class=""><i class="bx bx-edit-alt fs-5"></i></a>
                    <a href="{$Top.Link}article/delete/$ID" class="yousure"><i class="bx bx-x-circle text-danger fs-5"></i></a>
                </td>
            </tr>
        <% end_loop %>
    </tbody>
    <% with getCurrentOffer %>
    <tfoot>
        <tr>
            <th colspan="5" class="text-end">Gesamt (netto):</th>
            <th class="text-end">$Top.formatPrice($getTotalNetto)</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="5" class="text-end">Gesamt MwSt.:</th>
            <th class="text-end">$Top.formatPrice($getTotalTax)</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="5" class="text-end">Gesamt (brutto):</th>
            <th class="text-end">$Top.formatPrice($getTotalBrutto)</th>
            <th></th>
        </tr>
    </tfoot>
    <% end_with %>
</table>
