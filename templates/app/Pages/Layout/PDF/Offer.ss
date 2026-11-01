<!doctype html>
<html>
<head>
    <% base_tag %>
    $MetaTags(0)
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>$Title - $SiteConfig.Title </title>
    <link href="/_resources/themes/baudigit/css/bootstrap.min.css" rel="stylesheet">
    <link href="/_resources/themes/baudigit/css/bootstrap-extended.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/main.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/pdf.css" rel="stylesheet">
    <style>
        
    </style>
</head>
<body>
    <% with $Offer %>
        <div class="row pb-4">
            <div class="col-6">
                <br />
                <% with ClientObject %>
                    $Client.getFullInfo
                    <br />
                    <br />
                    $getNameForPDF
                <% end_with %>
            </div>
            <div class="offset-1 col-5 text-end">
                <div class="row">
                    <div class="col-12">
                        <h2>Angebot</h2>
                    </div>
                    <div class="col-12">
                        <table width="100%">
                            <tr>
                                <td>
                                    Angebotsnummer:
                                </td>
                                <td style="padding-left: 20px;" class="text-end">
                                    $OfferNumber
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Erstellt am:
                                </td>
                                <td style="padding-left: 20px;" class="text-end">
                                    $Created.Format('dd.MM.YYYY')
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Gültig bis:
                                </td>
                                <td style="padding-left: 20px;" class="text-end">
                                    $ValidUntil.Format('dd.MM.YYYY')
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <% if IntroText %>
            <div class="row pt-2">
                <div class="col-12">
                    <p>$IntroText</p>
                </div>
            </div>
        <% end_if %>

        <div class="row">
            <div class="col-12 py-2">

                <table id="offerArticleTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th width="40%">Titel</th>
                            <th class="text-end">Menge</th>
                            <th class="text-end">Preis</th>
                            <th class="text-end">Rabatt</th>
                            <th class="text-end">Gesamtpreis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <% loop OfferArticles %>
                            <tr class="borderme-top">
                                <td class="border-primary">$Title</td>
                                <td class="text-end"><small>$getUnitLabel</small> $Quantity</td>
                                <td class="text-end">$formatPrice($SellPriceNetto)</td>
                                <td class="text-end"> {$Discount}%</td>
                                <td class="text-end">$formatPrice($FinalPrice)</td>
                            </tr>
                            <% if Description %>
                                <tr class="">
                                    <td colspan="5"><small>$Description</small></td>
                                </tr>
                            <% end_if %>
                        <% end_loop %>
                    </tbody>
                    <tfoot>
                        <tr class="borderme">
                            <th colspan="4" class="text-end">Gesamt (netto):</th>
                            <th class="text-end">$Top.formatPrice($getTotalNetto)</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Gesamt MwSt.:</th>
                            <th class="text-end">$Top.formatPrice($getTotalTax)</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-end">Gesamt (brutto):</th>
                            <th class="text-end">$Top.formatPrice($getTotalBrutto)</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
                    

        <% if OutroText %>
            <div class="row pb-2">
                <div class="col-12">
                    <p>$OutroText</p>
                </div>
            </div>
        <% end_if %>
    <% end_with %>
</body>
</html>