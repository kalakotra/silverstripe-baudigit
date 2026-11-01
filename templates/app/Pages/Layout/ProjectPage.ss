<% if $getCurrentProject %>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-lg-2">
                    <% if getCurrentProject.Status==2 %>
                        <a href="{$Top.getMyClass(Kalakotra\Baudigit\Pages\InvoicePage).First().Link}add?p=$getCurrentProject.ID" class="btn btn-sm btn-success px-4">In Rechnung umwandeln</a>
                    <% end_if %>
                </div>
                <div class="col-12 col-lg-8">
                    <% include Includes/ProjectPage/ProjectStatusSwitch %>
                </div>
                <div class="col-12 col-lg-2 text-end">
                    <a href="{$Top.Link}" class="btn btn-sm btn-primary px-4">Zurück zur Übersicht</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="form-body">
            <div class="card-body">
                <ul class="nav nav-tabs nav-primary" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <% if not $checkActiveTab %>active<% end_if %>" data-bs-toggle="tab" href="#project_home" role="tab" aria-selected="<% if $checkActiveTab %>false<% else %>true<% end_if %>">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="bi bi-house-door me-1 fs-6"></i></div>
                                <div class="tab-title">Projektdaten ($getCurrentProject.getStatusLabel)</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <% if $checkActiveTab='project_tasks' %>active<% end_if %>" data-bs-toggle="tab" href="#project_tasks" role="tab" aria-selected="<% if $checkActiveTab='project_tasks' %>true<% else %>false<% end_if %>">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="bi bi-list-task me-1 fs-6"></i></div>
                                <div class="tab-title">Aufgaben</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <% if $checkActiveTab='project_documents' %>active<% end_if %>" data-bs-toggle="tab" href="#project_documents" role="tab" aria-selected="<% if $checkActiveTab='project_documents' %>true<% else %>false<% end_if %>">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bi bi-file-earmark-text me-1 fs-6'></i></div>
                                <div class="tab-title">Dokumente</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link <% if $checkActiveTab='project_profit' %>active<% end_if %>" data-bs-toggle="tab" href="#project_profit" role="tab" aria-selected="<% if $checkActiveTab='project_profit' %>true<% else %>false<% end_if %>">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class="bi bi-graph-up-arrow me-1 fs-6"></i></div>
                                <div class="tab-title">Profitabilität</div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="tab-content py-3">
                    <div class="tab-pane fade <% if not $checkActiveTab %>show active<% end_if %>" id="project_home" role="tabpanel">
                        $edit
                    </div>
                    <div class="tab-pane fade <% if $checkActiveTab='project_tasks' %>show active<% end_if %>" id="project_tasks" role="tabpanel">
                        <div class="row">
                            <div class="col-12 col-lg-8 mb-2">
                                <% include ProjectTasksTableList %>
                            </div>
                            <div class="col-12 col-lg-4 mb-2">
                                <% include ProjectTasksForms %>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade <% if $checkActiveTab='project_documents' %>show active<% end_if %>" id="project_documents" role="tabpanel">
                        <div class="px-3">
                            <div class="row">
                                <div class="col-12 col-lg-8 mb-2">
                                    <% include ProjectDocumentsTableList %>
                                </div>
                                <div class="col-12 col-lg-4 mb-2">
                                    <% include ProjectDocumentsForms %>
                                </div>
                            </div>
                        </div>
                    </div>
                    <%-- ── Profitabilität Tab ──────────────────────────────────────── --%>
                    <div class="tab-pane fade <% if $checkActiveTab='project_profit' %>show active<% end_if %>" id="project_profit" role="tabpanel">
                        <% with getCurrentProject %>
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="card border-0 bg-light h-100 text-center py-3">
                                    <div class="fs-4 fw-bold text-primary">$getTotalInvoiced.Nice €</div>
                                    <div class="small text-muted mt-1">Fakturiert (brutto)</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 bg-light h-100 text-center py-3">
                                    <div class="fs-4 fw-bold">$getTotalHoursLogged h</div>
                                    <div class="small text-muted mt-1">Stunden gebucht</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card border-0 bg-light h-100 text-center py-3">
                                    <div class="fs-4 fw-bold text-danger">$getTotalLaborCost.Nice €</div>
                                    <div class="small text-muted mt-1">Lohnkosten ($HourlyRate €/h)</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <% if $getProfitAmount >= 0 %>
                                    <div class="card border-0 bg-success bg-opacity-10 h-100 text-center py-3">
                                        <div class="fs-4 fw-bold text-success">$getProfitAmount.Nice €</div>
                                        <div class="small text-muted mt-1">Gewinn ($getProfitMarginPercent %)</div>
                                    </div>
                                <% else %>
                                    <div class="card border-0 bg-danger bg-opacity-10 h-100 text-center py-3">
                                        <div class="fs-4 fw-bold text-danger">$getProfitAmount.Nice €</div>
                                        <div class="small text-muted mt-1">Verlust ($getProfitMarginPercent %)</div>
                                    </div>
                                <% end_if %>
                            </div>
                        </div>

                        <% if not $HourlyRate %>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Kein Stundensatz hinterlegt — Lohnkosten können nicht berechnet werden.
                                Bitte den <strong>Stundensatz (€/h)</strong> in den Projektdaten eintragen.
                            </div>
                        <% end_if %>

                        <%-- Rechnungen-Tabelle --%>
                        <h6 class="mb-2">Verknüpfte Rechnungen</h6>
                        <% if $Invoices %>
                        <table class="table table-sm table-hover mb-4">
                            <thead class="table-light">
                                <tr><th>Nr.</th><th>Status</th><th class="text-end">Betrag (brutto)</th></tr>
                            </thead>
                            <tbody>
                                <% loop $Invoices %>
                                <tr>
                                    <td><a href="$getFirstInvoicePage.Link">$InvoiceNumber</a></td>
                                    <td>$getGermanStatus</td>
                                    <td class="text-end">$getTotalBrutto €</td>
                                </tr>
                                <% end_loop %>
                            </tbody>
                        </table>
                        <% else %>
                            <p class="text-muted small">Keine Rechnungen verknüpft.</p>
                        <% end_if %>

                        <%-- Stunden pro Aufgabe --%>
                        <h6 class="mb-2">Stunden je Aufgabe</h6>
                        <% if $ProjectTasks %>
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr><th>Aufgabe</th><th class="text-end">Geschätzt</th><th class="text-end">Gebucht</th></tr>
                            </thead>
                            <tbody>
                                <% loop $ProjectTasks %>
                                <tr>
                                    <td>$Title</td>
                                    <td class="text-end">$EstimatedHours h</td>
                                    <td class="text-end">$TaskHours.Sum('Hours') h</td>
                                </tr>
                                <% end_loop %>
                            </tbody>
                        </table>
                        <% else %>
                            <p class="text-muted small">Keine Aufgaben vorhanden.</p>
                        <% end_if %>
                        <% end_with %>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
            <a type="button" class="btn btn-sm btn-success px-4" href="{$Top.Link}add">Projekt erstellen</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <% include Includes/ProjectPage/ProjectListTable %>
        </div>
    </div>
<% end_if %>
