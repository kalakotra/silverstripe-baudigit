<!doctype html>
<html>
<head>
    <% base_tag %>
    $MetaTags(0)
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>$Title - $SiteConfig.Title </title>
    <link href="/_resources/themes/baudigit/css/main.css" rel="stylesheet">
</head>
<body>
    <div>
    </div>
    
<% with $Offer %>
    <div class="main">
        <header>
            <div class="logo">
            </div>
        </header>
        <div class="header-bg">
            
        </div>

        <p class="sender">
            Adiwidjaja Teamworks GmbH / Königstraße 16 / 22926 Ahrensburg<% if $IsEnglish %> / Germany<% end_if %>
        </p>
        <div class="invoice-head">
            <div class="address">
                    $RenderAddress1() <br>
                    $RenderPostcode() <br>
                    $RenderCountry()
            </div>
            <div class="invoice-data">
                <p>
                    <strong>Ahrensburg, <% if $IsEnglish %>$Date.Format("dd/MM/yyyy")<% else %>$Date.Format("dd.MM.yyyy")<% end_if %></strong>
                </p>
                <p><% if $IsEnglish %>Invoice number<% else %>Rechnungsnummer<% end_if %>: $InvoiceNumber</p>
                $ExtraText
            </div>
        </div>
        <div class="content">
            <h1><% if $IsEnglish %>Invoice<% else %>Rechnung<% end_if %> „$Headline“</h1>
            $Description
            <div class="price-table">
                <table>
                    <tr>
                        <th></th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Quantity<% else %>Menge<% end_if %>
                            <% if $Unit %> ($Unit)<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Rate<% else %>Satz<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Amount<% else %>Betrag<% end_if %>
                        </th>
                    </tr>
                    <% loop $Items %>
                        <tr>
                            <td>$Title</td>
                            <td class="td--right">$Amount</td>
                            <td class="td--right">$Rate</td>
                            <td class="td--right">$Sum €</td>
                        </tr>
                    <% end_loop %>
                    <tr>
                        <td><strong><% if $IsEnglish %>Net total<% else %>Nettosumme<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatAmount</strong></td>
                    </tr>
                    <tr>
                        <td><% if $IsEnglish %>VAT<% else %>zzgl. Mwst.<% end_if %></td>
                        <td class="td--right">$VAT %</td>
                        <td></td>
                        <td class="td--right">$FormatVATAmount</td>
                    </tr>
                    <tr class="tr--sum">
                        <td><strong><% if $IsEnglish %>Total<% else %>Summe<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatGrossAmount</strong></td>
                    </tr>
                </table>
            </div>
            <% if $Conditions %>
                $Conditions
            <% else %>
                <% if $IsEnglish %>
                    <p>
                        Thank you for your order. Please transfer the indicated
                        amount after receipt of invoice within 10 days without deductions to the
                        specified account. Thank you very much.
                    </p>
                <% else %>
                    <p>Vielen Dank für Ihren Auftrag. Bitte überweisen Sie uns den angegebenen
                        Betrag nach Rechnungserhalt innerhalb von 10 Tagen ohne Abzüge auf das
                        angegebene Konto. Vielen Dank.</p>
                <% end_if %>
            <% end_if %>
        </div><div class="content">
            <h1><% if $IsEnglish %>Invoice<% else %>Rechnung<% end_if %> „$Headline“</h1>
            $Description
            <div class="price-table">
                <table>
                    <tr>
                        <th></th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Quantity<% else %>Menge<% end_if %>
                            <% if $Unit %> ($Unit)<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Rate<% else %>Satz<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Amount<% else %>Betrag<% end_if %>
                        </th>
                    </tr>
                    <% loop $Items %>
                        <tr>
                            <td>$Title</td>
                            <td class="td--right">$Amount</td>
                            <td class="td--right">$Rate</td>
                            <td class="td--right">$Sum €</td>
                        </tr>
                    <% end_loop %>
                    <tr>
                        <td><strong><% if $IsEnglish %>Net total<% else %>Nettosumme<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatAmount</strong></td>
                    </tr>
                    <tr>
                        <td><% if $IsEnglish %>VAT<% else %>zzgl. Mwst.<% end_if %></td>
                        <td class="td--right">$VAT %</td>
                        <td></td>
                        <td class="td--right">$FormatVATAmount</td>
                    </tr>
                    <tr class="tr--sum">
                        <td><strong><% if $IsEnglish %>Total<% else %>Summe<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatGrossAmount</strong></td>
                    </tr>
                </table>
            </div>
            <% if $Conditions %>
                $Conditions
            <% else %>
                <% if $IsEnglish %>
                    <p>
                        Thank you for your order. Please transfer the indicated
                        amount after receipt of invoice within 10 days without deductions to the
                        specified account. Thank you very much.
                    </p>
                <% else %>
                    <p>Vielen Dank für Ihren Auftrag. Bitte überweisen Sie uns den angegebenen
                        Betrag nach Rechnungserhalt innerhalb von 10 Tagen ohne Abzüge auf das
                        angegebene Konto. Vielen Dank.</p>
                <% end_if %>
            <% end_if %>
        </div><div class="content">
            <h1><% if $IsEnglish %>Invoice<% else %>Rechnung<% end_if %> „$Headline“</h1>
            $Description
            <div class="price-table">
                <table>
                    <tr>
                        <th></th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Quantity<% else %>Menge<% end_if %>
                            <% if $Unit %> ($Unit)<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Rate<% else %>Satz<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Amount<% else %>Betrag<% end_if %>
                        </th>
                    </tr>
                    <% loop $Items %>
                        <tr>
                            <td>$Title</td>
                            <td class="td--right">$Amount</td>
                            <td class="td--right">$Rate</td>
                            <td class="td--right">$Sum €</td>
                        </tr>
                    <% end_loop %>
                    <tr>
                        <td><strong><% if $IsEnglish %>Net total<% else %>Nettosumme<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatAmount</strong></td>
                    </tr>
                    <tr>
                        <td><% if $IsEnglish %>VAT<% else %>zzgl. Mwst.<% end_if %></td>
                        <td class="td--right">$VAT %</td>
                        <td></td>
                        <td class="td--right">$FormatVATAmount</td>
                    </tr>
                    <tr class="tr--sum">
                        <td><strong><% if $IsEnglish %>Total<% else %>Summe<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatGrossAmount</strong></td>
                    </tr>
                </table>
            </div>
            <% if $Conditions %>
                $Conditions
            <% else %>
                <% if $IsEnglish %>
                    <p>
                        Thank you for your order. Please transfer the indicated
                        amount after receipt of invoice within 10 days without deductions to the
                        specified account. Thank you very much.
                    </p>
                <% else %>
                    <p>Vielen Dank für Ihren Auftrag. Bitte überweisen Sie uns den angegebenen
                        Betrag nach Rechnungserhalt innerhalb von 10 Tagen ohne Abzüge auf das
                        angegebene Konto. Vielen Dank.</p>
                <% end_if %>
            <% end_if %>
        </div><div class="content">
            <h1><% if $IsEnglish %>Invoice<% else %>Rechnung<% end_if %> „$Headline“</h1>
            $Description
            <div class="price-table">
                <table>
                    <tr>
                        <th></th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Quantity<% else %>Menge<% end_if %>
                            <% if $Unit %> ($Unit)<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Rate<% else %>Satz<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Amount<% else %>Betrag<% end_if %>
                        </th>
                    </tr>
                    <% loop $Items %>
                        <tr>
                            <td>$Title</td>
                            <td class="td--right">$Amount</td>
                            <td class="td--right">$Rate</td>
                            <td class="td--right">$Sum €</td>
                        </tr>
                    <% end_loop %>
                    <tr>
                        <td><strong><% if $IsEnglish %>Net total<% else %>Nettosumme<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatAmount</strong></td>
                    </tr>
                    <tr>
                        <td><% if $IsEnglish %>VAT<% else %>zzgl. Mwst.<% end_if %></td>
                        <td class="td--right">$VAT %</td>
                        <td></td>
                        <td class="td--right">$FormatVATAmount</td>
                    </tr>
                    <tr class="tr--sum">
                        <td><strong><% if $IsEnglish %>Total<% else %>Summe<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatGrossAmount</strong></td>
                    </tr>
                </table>
            </div>
            <% if $Conditions %>
                $Conditions
            <% else %>
                <% if $IsEnglish %>
                    <p>
                        Thank you for your order. Please transfer the indicated
                        amount after receipt of invoice within 10 days without deductions to the
                        specified account. Thank you very much.
                    </p>
                <% else %>
                    <p>Vielen Dank für Ihren Auftrag. Bitte überweisen Sie uns den angegebenen
                        Betrag nach Rechnungserhalt innerhalb von 10 Tagen ohne Abzüge auf das
                        angegebene Konto. Vielen Dank.</p>
                <% end_if %>
            <% end_if %>
        </div><div class="content">
            <h1><% if $IsEnglish %>Invoice<% else %>Rechnung<% end_if %> „$Headline“</h1>
            $Description
            <div class="price-table">
                <table>
                    <tr>
                        <th></th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Quantity<% else %>Menge<% end_if %>
                            <% if $Unit %> ($Unit)<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Rate<% else %>Satz<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Amount<% else %>Betrag<% end_if %>
                        </th>
                    </tr>
                    <% loop $Items %>
                        <tr>
                            <td>$Title</td>
                            <td class="td--right">$Amount</td>
                            <td class="td--right">$Rate</td>
                            <td class="td--right">$Sum €</td>
                        </tr>
                    <% end_loop %>
                    <tr>
                        <td><strong><% if $IsEnglish %>Net total<% else %>Nettosumme<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatAmount</strong></td>
                    </tr>
                    <tr>
                        <td><% if $IsEnglish %>VAT<% else %>zzgl. Mwst.<% end_if %></td>
                        <td class="td--right">$VAT %</td>
                        <td></td>
                        <td class="td--right">$FormatVATAmount</td>
                    </tr>
                    <tr class="tr--sum">
                        <td><strong><% if $IsEnglish %>Total<% else %>Summe<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatGrossAmount</strong></td>
                    </tr>
                </table>
            </div>
            <% if $Conditions %>
                $Conditions
            <% else %>
                <% if $IsEnglish %>
                    <p>
                        Thank you for your order. Please transfer the indicated
                        amount after receipt of invoice within 10 days without deductions to the
                        specified account. Thank you very much.
                    </p>
                <% else %>
                    <p>Vielen Dank für Ihren Auftrag. Bitte überweisen Sie uns den angegebenen
                        Betrag nach Rechnungserhalt innerhalb von 10 Tagen ohne Abzüge auf das
                        angegebene Konto. Vielen Dank.</p>
                <% end_if %>
            <% end_if %>
        </div><div class="content">
            <h1><% if $IsEnglish %>Invoice<% else %>Rechnung<% end_if %> „$Headline“</h1>
            $Description
            <div class="price-table">
                <table>
                    <tr>
                        <th></th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Quantity<% else %>Menge<% end_if %>
                            <% if $Unit %> ($Unit)<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Rate<% else %>Satz<% end_if %>
                        </th>
                        <th class="td--right td-nobr">
                            <% if $IsEnglish %>Amount<% else %>Betrag<% end_if %>
                        </th>
                    </tr>
                    <% loop $Items %>
                        <tr>
                            <td>$Title</td>
                            <td class="td--right">$Amount</td>
                            <td class="td--right">$Rate</td>
                            <td class="td--right">$Sum €</td>
                        </tr>
                    <% end_loop %>
                    <tr>
                        <td><strong><% if $IsEnglish %>Net total<% else %>Nettosumme<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatAmount</strong></td>
                    </tr>
                    <tr>
                        <td><% if $IsEnglish %>VAT<% else %>zzgl. Mwst.<% end_if %></td>
                        <td class="td--right">$VAT %</td>
                        <td></td>
                        <td class="td--right">$FormatVATAmount</td>
                    </tr>
                    <tr class="tr--sum">
                        <td><strong><% if $IsEnglish %>Total<% else %>Summe<% end_if %></strong></td>
                        <td></td>
                        <td></td>
                        <td class="td--right"><strong>$FormatGrossAmount</strong></td>
                    </tr>
                </table>
            </div>
            <% if $Conditions %>
                $Conditions
            <% else %>
                <% if $IsEnglish %>
                    <p>
                        Thank you for your order. Please transfer the indicated
                        amount after receipt of invoice within 10 days without deductions to the
                        specified account. Thank you very much.
                    </p>
                <% else %>
                    <p>Vielen Dank für Ihren Auftrag. Bitte überweisen Sie uns den angegebenen
                        Betrag nach Rechnungserhalt innerhalb von 10 Tagen ohne Abzüge auf das
                        angegebene Konto. Vielen Dank.</p>
                <% end_if %>
            <% end_if %>
        </div>

    </div>
    <footer class="footer">
        <div class="footer-bg">
        </div>
        <% if $IsEnglish %>
            <div class="footer_block">
                <p>
                    Adiwidjaja Teamworks GmbH<br/>
                    Königstraße 16 / 22926 Ahrensburg<br/>
                    (0 41 02) 208 456 / mail@adiwidjaja.com<br/>
                    Business manager: Andy Adiwidjaja
                </p>
            </div>
            <div class="footer_block">
                <p>
                    Registered office of the company: Ahrensburg<br/>
                    Amtsgericht Lübeck<br/>
                    Commercial register number: HRB4821-AH<br/>
                    VAT-Id: DE232822254
                </p>
            </div>
            <div class="footer_block">
                <p>
                    IBAN DE80 200 700 24 0 3004546 00<br/>
                    BIC DEUTDEDBHAM<br/>
                    Deutsche Bank Hamburg
                </p>
            </div>
        <% else %>
            <div class="footer_block">
                <p>
                    Adiwidjaja Teamworks GmbH<br/>
                    Königstraße 16 / 22926 Ahrensburg<br/>
                    (0 41 02) 208 456 / mail@adiwidjaja.com<br/>
                    Geschäftsführer: Andy Adiwidjaja
                </p>
            </div>
            <div class="footer_block">
                <p>
                    Sitz der Gesellschaft: Ahrensburg<br/>
                    Amtsgericht Lübeck<br/>
                    Handelsregisternummer: HRB4821-AH<br/>
                    Ust-Id: DE232822254
                </p>
            </div>
            <div class="footer_block">
                <p>
                    IBAN DE80 200 700 24 0 3004546 00<br/>
                    BIC DEUTDEDBHAM<br/>
                    Deutsche Bank Hamburg
                </p>
            </div>
        <% end_if %>
    </footer>
<% end_with %>
</body>
</html>
