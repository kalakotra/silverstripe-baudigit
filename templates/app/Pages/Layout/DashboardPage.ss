<%-- Dashboard: Offene Rechnungen Overview --%>

<%-- ── KPI Kacheln ─────────────────────────────────────────────────────────── --%>
<div class="row g-3 mb-4">

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card rounded-4 h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="wh-52 d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-15 text-warning flex-shrink-0">
                    <i class="bx bx-time-five fs-4"></i>
                </div>
                <div>
                    <div class="fs-5 fw-semibold">$getOpenInvoicesCount</div>
                    <div class="text-muted small">Offene Rechnungen</div>
                    <div class="fw-semibold text-warning">$getOpenInvoicesTotal</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card rounded-4 h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="wh-52 d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-15 text-danger flex-shrink-0">
                    <i class="bx bx-alarm-exclamation fs-4"></i>
                </div>
                <div>
                    <div class="fs-5 fw-semibold">$getOverdueInvoicesCount</div>
                    <div class="text-muted small">Überfällig</div>
                    <div class="fw-semibold text-danger">$getOverdueInvoicesTotal</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card rounded-4 h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="wh-52 d-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-15 text-success flex-shrink-0">
                    <i class="bx bx-check-circle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Bezahlt diesen Monat</div>
                    <div class="fs-5 fw-semibold text-success">$getPaidThisMonthTotal</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card rounded-4 h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="wh-52 d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-15 text-primary flex-shrink-0">
                    <i class="bx bx-file fs-4"></i>
                </div>
                <div>
                    <div class="fs-5 fw-semibold">$getOpenOffersCount</div>
                    <div class="text-muted small">Offene Angebote</div>
                </div>
            </div>
        </div>
    </div>

</div>

<%-- ── Offene Rechnungen Tabelle ───────────────────────────────────────────── --%>
<div class="card rounded-4 border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-semibold">Offene Rechnungen <span class="badge bg-warning text-dark ms-1">$getOpenInvoicesCount</span></h6>
        <% with getFirstInvoicePage %>
            <a href="$Link" class="btn btn-sm btn-outline-primary">Alle anzeigen</a>
        <% end_with %>
    </div>
    <div class="card-body p-0">
        <% if $getTopOpenInvoices %>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">R.Nr.</th>
                        <th>Kunde</th>
                        <th>Fällig am</th>
                        <th class="text-end">Betrag</th>
                        <th class="text-end pe-3">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <% loop $getTopOpenInvoices %>
                    <tr <% if $isOverdue %>class="table-danger bg-opacity-25"<% end_if %>>
                        <td class="ps-3 fw-semibold">$InvoiceNumber</td>
                        <td>
                            $ClientObject.getFullName
                            <br><small class="text-muted">$ClientObject.Client.Company</small>
                        </td>
                        <td>
                            $DueDate.Format('dd.MM.yyyy')
                            <% if $isOverdue %>
                                <span class="badge bg-danger ms-1">überfällig</span>
                            <% end_if %>
                        </td>
                        <td class="text-end fw-semibold">$getTotalBruttoAsString</td>
                        <td class="text-end pe-3">
                            <% with $getFirstInvoicePage %>
                                <a href="{$Link}change-status/{$Up.ID}/2" class="yousure btn btn-sm btn-outline-success me-1" title="Als bezahlt markieren">
                                    <i class="bx bx-check"></i> Bezahlt
                                </a>
                                <a href="{$Link}edit/{$Up.ID}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                            <% end_with %>
                        </td>
                    </tr>
                    <% end_loop %>
                </tbody>
            </table>
        </div>
        <% else %>
        <div class="text-center text-muted py-5">
            <i class="bx bx-check-circle fs-1 text-success"></i>
            <p class="mt-2 mb-0">Keine offenen Rechnungen.</p>
        </div>
        <% end_if %>
    </div>
</div>
