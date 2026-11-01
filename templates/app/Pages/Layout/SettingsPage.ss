<div class="card">
    <div class="form-body">
        <div class="card-body">
            <ul class="nav nav-tabs nav-primary" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link <% if not $checkActiveTab %>active<% end_if %>" data-bs-toggle="tab" href="#settings_home" role="tab" aria-selected="<% if $checkActiveTab %>false<% else %>true<% end_if %>">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon">
                                <i class="bi bi-house-door me-1 fs-6"></i>
                            </div>
                            <div class="tab-title">Firma</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <% if $checkActiveTab='settings_invoices' %>active<% end_if %>" data-bs-toggle="tab" href="#settings_invoices" role="tab" aria-selected="<% if $checkActiveTab='settings_invoices' %>true<% else %>false<% end_if %>">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class="bi bi-list-task me-1 fs-6"></i>
                            </div>
                            <div class="tab-title">Rechnungen</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <% if $checkActiveTab='settings_texts' %>active<% end_if %>" data-bs-toggle="tab" href="#settings_texts" role="tab" aria-selected="<% if $checkActiveTab='settings_texts' %>true<% else %>false<% end_if %>">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bi bi-file-earmark-text me-1 fs-6'></i>
                            </div>
                            <div class="tab-title">Texte</div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="tab-content py-3">
                <div class="tab-pane fade <% if not $checkActiveTab %>show active<% end_if %>" id="settings_home" role="tabpanel">
                    <div class="form-body">$companySettingsForm</div>
                </div>
                <div class="tab-pane fade <% if $checkActiveTab='settings_invoices' %>show active<% end_if %>" id="settings_invoices" role="tabpanel">
                    <div class="form-body">$invoiceSettingsForm</div>
                </div>
                <div class="tab-pane fade <% if $checkActiveTab='settings_texts' %>show active<% end_if %>" id="settings_texts" role="tabpanel">
                    <div class="form-body">$textSettingsForm</div>
                </div>
            </div>
        </div>
    </div>
</div>