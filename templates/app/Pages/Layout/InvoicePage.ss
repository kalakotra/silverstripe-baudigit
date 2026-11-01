<% if $getCurrentInvoice %>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-lg-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="dropdown">Options</button>
                        <button type="button" class="btn btn-sm btn-success split-bg-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"><span class="visually-hidden">Toggle Dropdown</span></button>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                            <a href="#" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#pdfPreviewCanvas"><i class="bx bx-search"></i>Vorschau</a>
                            <a href="{$Top.Link}preview/$getCurrentInvoice.ID" class="dropdown-item" target="_blank"><i class="bx bx-file-pdf"></i>PDF öffnen</a>
                            <a href="{$Top.Link}duplicate/$getCurrentInvoice.ID" class="dropdown-item"><i class="bx bx-copy"></i>Duplizieren</a>
                            <div class="dropdown-divider"></div>
                            <a href="{$Top.Link}send/$getCurrentInvoice.ID" class="dropdown-item yousure"><i class="bx bx-envelope"></i>Versenden</a>
                            <% if $getCurrentInvoice.Status == 1 %>
                                <a href="{$Top.Link}reminder/$getCurrentInvoice.ID" class="dropdown-item yousure"><i class="bx bx-bell"></i>Zahlungserinnerung</a>
                            <% end_if %>
                            <% if $getCurrentInvoice.canBeCancelled %>
                                <a href="{$Top.Link}cancel/$getCurrentInvoice.ID" class="dropdown-item yousure text-danger"><i class="fadeIn animated bx bx-shield-x"></i>Storno</a>
                            <% end_if %>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <% include Includes/InvoicePage/InvoiceStatusSwitch %>
                </div>
                <div class="col-12 col-lg-2 text-end">
                    <a href="{$Top.Link}" class="btn btn-sm btn-primary px-4">Zurück zur Übersicht</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs nav-primary" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link <% if not $checkActiveTab %>active<% end_if %>" data-bs-toggle="tab" href="#invoice_home" role="tab" aria-selected="<% if not $checkActiveTab %>true<% else %>false<% end_if %>">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class="bi bi-house-door me-1 fs-6"></i></div>
                            <div class="tab-title">Rechnungsdaten ($getCurrentInvoice.getGermanStatus)</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <% if $checkActiveTab == 'invoice_article' %>active<% end_if %>" data-bs-toggle="tab" href="#invoice_article" role="tab" aria-selected="<% if $checkActiveTab == 'invoice_article' %>true<% else %>false<% end_if %>">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class="bi bi-list-task me-1 fs-6"></i></div>
                            <div class="tab-title">Artikel</div>
                        </div>
                    </a>
                </li>
                <% if $getCurrentInvoice.Project %>
                    <li class="nav-item">
                        <a class="nav-link <% if $checkActiveTab == 'invoice_project' %>active<% end_if %>" data-bs-toggle="tab" href="#invoice_project" role="tab" aria-selected="<% if $checkActiveTab == 'invoice_project' %>true<% else %>false<% end_if %>">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="bi bi-list-task me-1 fs-6"></i></div>
                                <div class="tab-title">Projekt Daten</div>
                            </div>
                        </a>
                    </li>
                <% end_if %>
                <% if $getCurrentInvoice.EmailLogs %>
                    <li class="nav-item">
                        <a class="nav-link <% if $checkActiveTab == 'invoice_emaillog' %>active<% end_if %>" data-bs-toggle="tab" href="#invoice_emaillog" role="tab">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="bx bx-envelope me-1 fs-6"></i></div>
                                <div class="tab-title">E-Mail Log <span class="badge bg-secondary ms-1">$getCurrentInvoice.EmailLogs.Count</span></div>
                            </div>
                        </a>
                    </li>
                <% end_if %>
            </ul>
            <div class="tab-content py-3">
                <div class="tab-pane fade <% if not $checkActiveTab %>show active<% end_if %>" id="invoice_home" role="tabpanel">
                    $edit
                </div>
                <div class="tab-pane fade <% if $checkActiveTab == 'invoice_article' %>show active<% end_if %>" id="invoice_article" role="tabpanel">
                    <% if $getCurrentInvoice.checkEditingStatus %>
                        <% include Includes/InvoicePage/InvoicePageArticleForms %>
                        <hr />
                    <% end_if %>
                    <% include Includes/InvoicePage/InvoicePageArticleTable %>
                </div>
                <% if $getCurrentInvoice.Project %>
                    <div class="tab-pane fade <% if $checkActiveTab == 'invoice_project' %>show active<% end_if %>" id="invoice_project" role="tabpanel">
                        <% include Includes/InvoicePage/InvoicePageProjectData Project=$getCurrentInvoice.Project %>
                    </div>
                <% end_if %>
                <% if $getCurrentInvoice.EmailLogs %>
                    <div class="tab-pane fade <% if $checkActiveTab == 'invoice_emaillog' %>show active<% end_if %>" id="invoice_emaillog" role="tabpanel">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Datum</th>
                                    <th>Typ</th>
                                    <th>Empfänger</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <% loop $getCurrentInvoice.EmailLogs %>
                                <tr>
                                    <td>$SentAt.Format('dd.MM.yyyy HH:mm')</td>
                                    <td>$getTypeLabel</td>
                                    <td>$SentTo</td>
                                    <td>
                                        <% if $Success %>
                                            <span class="badge bg-success">Gesendet</span>
                                        <% else %>
                                            <span class="badge bg-danger">Fehler</span>
                                        <% end_if %>
                                    </td>
                                </tr>
                                <% end_loop %>
                            </tbody>
                        </table>
                    </div>
                <% end_if %>
            </div>
        </div>
    </div>
<%-- ── PDF Vorschau Offcanvas (responsive: rechts auf lg+, unten auf kleinen) ─ --%>
<div class="offcanvas offcanvas-end offcanvas-lg" tabindex="-1" id="pdfPreviewCanvas" aria-labelledby="pdfPreviewLabel"
     style="width: min(680px, 100vw);">
    <div class="offcanvas-header border-bottom">
        <h6 class="offcanvas-title" id="pdfPreviewLabel"><i class="bx bx-file-pdf me-1"></i>Vorschau — $getCurrentInvoice.InvoiceNumber</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Schließen"></button>
    </div>
    <div class="offcanvas-body p-0">
        <iframe id="pdfPreviewFrame"
                src=""
                data-src="{$Top.Link}preview/$getCurrentInvoice.ID"
                style="width:100%; height:100%; min-height:80vh; border:none;"
                title="Rechnungsvorschau">
        </iframe>
    </div>
</div>
<script>
(function () {
    var canvas  = document.getElementById('pdfPreviewCanvas');
    var frame   = document.getElementById('pdfPreviewFrame');
    if (!canvas || !frame) return;
    var loaded  = false;
    canvas.addEventListener('show.bs.offcanvas', function () {
        if (!loaded) {
            frame.src = frame.dataset.src;
            loaded = true;
        }
    });
}());
</script>

<% else_if $isAddAction %>
    <div class="card">
        <div class="card-header text-end">
            <a href="{$Top.Link}" class="btn btn-sm btn-primary px-4">Zurück zur Übersicht</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-body">
                $add
            </div>
        </div>
    </div>
<% else %>
    <div class="card">
        <div class="card-header text-end">
            <a type="button" class="btn btn-sm btn-success px-4" href="{$Top.Link}add">Rechnung erstellen</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <% include Includes/InvoicePage/InvoicePageInvoiceListTable %>
        </div>
    </div>
<% end_if %>
