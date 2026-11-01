<%-- AJAX Artikel-Formular — kein Page-Reload --%>
<div id="articleFormWrap">

    <%-- Datalist für Autocomplete --%>
    <datalist id="articleList">
        <option data-id="0" data-unit="0" data-nettoprice="0" value=""></option>
        <% loop getAllArticles %>
            <option data-id="$ID" data-unit="$Unit" data-nettoprice="$SellPriceNetto" data-title="$Title" value="$Title || $InternalID || $formatPrice($SellPriceNetto)"></option>
        <% end_loop %>
    </datalist>

    <form id="articleAjaxForm" class="row g-2 align-items-end mb-3" novalidate>
        <input type="hidden" name="InvoiceID"       id="af_InvoiceID"       value="$getCurrentInvoice.ID">
        <input type="hidden" name="ArticleRecordID" id="af_ArticleRecordID" value="">
        <input type="hidden" name="BuyPriceNetto"   id="af_BuyPriceNetto"   value="0">
        <input type="hidden" name="SourceArticleID" id="af_SourceArticleID" value="">

        <div class="col-12 col-md-4">
            <label class="form-label small mb-1">Titel</label>
            <input type="text" name="Title" id="af_Title" class="form-control form-control-sm"
                   list="articleList" autocomplete="off" required>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small mb-1">Menge</label>
            <input type="number" name="Quantity" id="af_Quantity" class="form-control form-control-sm"
                   value="1" min="0.01" step="0.01" required>
        </div>
        <div class="col-6 col-md-1">
            <label class="form-label small mb-1">Einheit</label>
            <select name="Unit" id="af_Unit" class="form-select form-select-sm">
                <option value="0">Stück</option>
                <option value="1">m</option>
                <option value="2">m²</option>
                <option value="3">m³</option>
                <option value="4">l</option>
                <option value="5">kg</option>
                <option value="6">Stunde</option>
                <option value="7">Sonstige</option>
            </select>
        </div>
        <div class="col-6 col-md-2">
            <label class="form-label small mb-1">Preis (netto)</label>
            <input type="number" name="SellPriceNetto" id="af_SellPriceNetto" class="form-control form-control-sm"
                   value="" step="0.01" required>
        </div>
        <div class="col-6 col-md-1">
            <label class="form-label small mb-1">Rabatt %</label>
            <input type="number" name="Discount" id="af_Discount" class="form-control form-control-sm"
                   value="0" min="0" max="100" step="0.01">
        </div>
        <div class="col-6 col-md-1">
            <label class="form-label small mb-1">MwSt. %</label>
            <input type="number" name="TaxRate" id="af_TaxRate" class="form-control form-control-sm"
                   value="$SiteConfig.InvoiceTax" readonly>
        </div>
        <div class="col-12 col-md-5">
            <label class="form-label small mb-1">Beschreibung</label>
            <input type="text" name="Description" id="af_Description" class="form-control form-control-sm">
        </div>
        <div class="col-12 col-md-2">
            <label class="form-label small mb-1">Betrag (brutto)</label>
            <input type="text" id="af_FinalPrice" class="form-control form-control-sm bg-light" readonly value="0,00 €">
        </div>
        <div class="col-12 col-md-auto d-flex gap-2">
            <button type="submit" id="af_SubmitBtn" class="btn btn-sm btn-success px-3">
                <i class="bx bx-plus"></i> <span id="af_SubmitLabel">Hinzufügen</span>
            </button>
            <button type="button" id="af_CancelEdit" class="btn btn-sm btn-outline-secondary d-none px-3">
                Abbrechen
            </button>
        </div>
    </form>

</div>

<script>
(function ($) {
    'use strict';

    var BASE_URL  = '$Top.Link';
    var editingID = null;

    // ── Live Betrag preview ────────────────────────────────────────────────────
    function recalcPreview() {
        var qty      = parseFloat($('#af_Quantity').val()) || 0;
        var price    = parseFloat($('#af_SellPriceNetto').val()) || 0;
        var discount = parseFloat($('#af_Discount').val()) || 0;
        var tax      = parseFloat($('#af_TaxRate').val()) || 19;
        var net      = qty * price;
        var after    = net - net * (discount / 100);
        var brutto   = after + after * (tax / 100);
        $('#af_FinalPrice').val(formatEur(brutto));
    }

    $('#af_Quantity, #af_SellPriceNetto, #af_Discount, #af_TaxRate').on('input', recalcPreview);

    // ── Autocomplete fill ──────────────────────────────────────────────────────
    $('#af_Title').on('input', function () {
        var val = $(this).val();
        var opt = $('#articleList option').filter(function () { return $(this).val() === val; });
        if (opt.length) {
            $('#af_SellPriceNetto').val(parseFloat(opt.data('nettoprice')) || 0);
            $('#af_Unit').val(opt.data('unit') || 0);
            $('#af_SourceArticleID').val(opt.data('id') || '');
            recalcPreview();
        }
    });

    // ── Submit ─────────────────────────────────────────────────────────────────
    $('#articleAjaxForm').on('submit', function (e) {
        e.preventDefault();
        if (!$('#af_Title').val().trim() || !$('#af_SellPriceNetto').val()) {
            showToast('warning', 'Bitte Titel und Preis ausfüllen.');
            return;
        }

        var url = editingID
            ? BASE_URL + 'article/' + editingID
            : BASE_URL + 'article/add';

        // Borrow SecurityID from any SS form on the page
        var secID = $('input[name="SecurityID"]').first().val() || '';

        $('#af_SubmitBtn').prop('disabled', true);

        $.ajax({
            url:     url,
            type:    'POST',
            data:    $(this).serialize() + '&SecurityID=' + encodeURIComponent(secID),
            dataType:'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (!res.ok) { showToast('danger', res.error || 'Fehler'); return; }
                var isEdit = !!editingID;
                if (isEdit) { updateRow(res.article); } else { appendRow(res.article); }
                updateTotals(res.totals);
                resetForm();
                showToast('success', isEdit ? 'Artikel aktualisiert.' : 'Artikel hinzugefügt.');
            },
            error:    function () { showToast('danger', 'Serverfehler.'); },
            complete: function () { $('#af_SubmitBtn').prop('disabled', false); }
        });
    });

    // ── Cancel edit ────────────────────────────────────────────────────────────
    $('#af_CancelEdit').on('click', function () { resetForm(); });

    // ── Delete (delegated) ─────────────────────────────────────────────────────
    $(document).on('click', '.js-delete-article', function (e) {
        e.preventDefault();
        if (!confirm('Artikel wirklich löschen?')) return;
        var id  = $(this).data('id');
        var row = $(this).closest('tr');
        $.ajax({
            url:     BASE_URL + 'article/delete/' + id,
            type:    'GET',
            dataType:'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                if (!res.ok) { showToast('danger', res.error || 'Fehler'); return; }
                row.fadeOut(200, function () { $(this).remove(); });
                updateTotals(res.totals);
                showToast('success', 'Artikel gelöscht.');
            },
            error: function () { showToast('danger', 'Serverfehler.'); }
        });
    });

    // ── Edit click (delegated) ─────────────────────────────────────────────────
    $(document).on('click', '.js-edit-article', function (e) {
        e.preventDefault();
        editingID = $(this).data('id');
        $('#af_ArticleRecordID').val(editingID);
        $('#af_Title').val($(this).data('title'));
        $('#af_Quantity').val($(this).data('quantity'));
        $('#af_Unit').val($(this).data('unit'));
        $('#af_SellPriceNetto').val($(this).data('sellpricenetto'));
        $('#af_Discount').val($(this).data('discount'));
        $('#af_TaxRate').val($(this).data('taxrate'));
        $('#af_Description').val($(this).data('description'));
        recalcPreview();
        $('#af_SubmitLabel').text('Speichern');
        $('#af_CancelEdit').removeClass('d-none');
        $('html,body').animate({ scrollTop: $('#articleFormWrap').offset().top - 80 }, 250);
    });

    // ── Helpers ────────────────────────────────────────────────────────────────
    function appendRow(a) {
        $('#invoiceArticleTable tbody').append(buildRow(a));
    }

    function updateRow(a) {
        var existing = $('#invoiceArticleTable tbody tr[data-article-id="' + a.id + '"]');
        if (existing.length) { existing.replaceWith(buildRow(a)); } else { appendRow(a); }
    }

    function buildRow(a) {
        return '<tr data-article-id="' + a.id + '">' +
            '<td>' + esc(a.title) + (a.description ? '<br><small class="text-muted">' + esc(a.description) + '</small>' : '') + '</td>' +
            '<td>' + fmt(a.quantity) + ' ' + esc(a.unitLabel) + '</td>' +
            '<td class="text-end">' + eur(a.sellPriceNetto) + '</td>' +
            '<td class="text-end">(' + fmt(a.discount) + '%) ' + eur(a.discountAmount) + '</td>' +
            '<td class="text-end">(' + fmt(a.taxRate) + '%) ' + eur(a.taxAmount) + '</td>' +
            '<td class="text-end">' + eur(a.finalPrice) + '</td>' +
            '<td class="text-center">' +
                '<a href="#" class="js-edit-article me-1"' +
                ' data-id="' + a.id + '" data-title="' + esc(a.title) + '"' +
                ' data-quantity="' + a.quantity + '" data-unit="' + a.unit + '"' +
                ' data-sellpricenetto="' + a.sellPriceNetto + '" data-discount="' + a.discount + '"' +
                ' data-taxrate="' + a.taxRate + '" data-description="' + esc(a.description || '') + '">' +
                '<i class="bx bx-edit-alt fs-5"></i></a>' +
                '<a href="#" class="js-delete-article text-danger" data-id="' + a.id + '">' +
                '<i class="bx bx-x-circle fs-5"></i></a>' +
            '</td></tr>';
    }

    function updateTotals(t) {
        if (!t) return;
        $('#total-netto').text(eur(t.netto));
        $('#total-tax').text(eur(t.tax));
        $('#total-brutto').text(eur(t.brutto));
    }

    function resetForm() {
        document.getElementById('articleAjaxForm').reset();
        $('#af_ArticleRecordID').val('');
        $('#af_FinalPrice').val('0,00 €');
        $('#af_SubmitLabel').text('Hinzufügen');
        $('#af_CancelEdit').addClass('d-none');
        editingID = null;
    }

    function eur(n)  { return (+n).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €'; }
    function fmt(n)  { return (+n).toLocaleString('de-DE', { minimumFractionDigits: 0, maximumFractionDigits: 2 }); }
    function esc(s)  { return $('<div>').text(s || '').html(); }

    function showToast(type, msg) {
        if (typeof window.showNotify === 'function') { window.showNotify(type, msg); return; }
        // Fallback: Bootstrap alert na vrhu forme
        var cls = type === 'success' ? 'alert-success' : type === 'warning' ? 'alert-warning' : 'alert-danger';
        var el  = $('<div class="alert ' + cls + ' alert-dismissible py-2 mt-2 mb-0">' + esc(msg) +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
        $('#articleFormWrap').append(el);
        setTimeout(function () { el.fadeOut(400, function () { $(this).remove(); }); }, 3500);
    }

}(jQuery));
</script>
