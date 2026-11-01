<% if $getCurrentRecurringInvoice %>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-lg-2">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="dropdown">Options</button>
                        <button type="button" class="btn btn-sm btn-success split-bg-success dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"><span class="visually-hidden">Toggle Dropdown</span></button>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                            <a href="{$Top.Link}preview/$getCurrentRecurringInvoice.ID" class="dropdown-item" target="_blank"><i class="bx bx-search"></i>Vorschau</a>
                            <a href="{$Top.Link}to-invoice/$getCurrentRecurringInvoice.ID" class="dropdown-item yousure"><i class="bx bx-list-check"></i>Rechnung erstellen</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8">
                    <% include Includes/RecurringInvoicePage/RecurringInvoiceStatusSwitch %>
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
                    <a class="nav-link <% if $checkActiveTab != 'offer_article' %>show active<% end_if %>" data-bs-toggle="tab" href="#offer_home" role="tab" aria-selected="<% if $checkActiveTab != 'offer_article' %>true<% else %>false<% end_if %>">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class="bi bi-house-door me-1 fs-6"></i></div>
                            <div class="tab-title">Stammdaten ($getCurrentRecurringInvoice.getGermanStatus)</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <% if $checkActiveTab == 'offer_article' %>active<% end_if %>" data-bs-toggle="tab" href="#offer_article" role="tab" aria-selected="<% if $checkActiveTab == 'offer_article' %>true<% else %>false<% end_if %>">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class="bi bi-list-task me-1 fs-6"></i></div>
                            <div class="tab-title">Artikel</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <% if $checkActiveTab == 'offer_generated_invoices' %>active<% end_if %>" data-bs-toggle="tab" href="#offer_generated_invoices" role="tab" aria-selected="<% if $checkActiveTab == 'offer_generated_invoices' %>true<% else %>false<% end_if %>">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class="bi bi-file-text me-1 fs-6"></i></div>
                            <div class="tab-title">Erstellte Rechnungen</div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="tab-content py-3">
                <div class="tab-pane fade <% if $checkActiveTab != 'offer_article' %>show active<% end_if %>" id="offer_home" role="tabpanel">
                    $edit
                </div>
                <div class="tab-pane fade <% if $checkActiveTab == 'offer_article' %>show active<% end_if %>" id="offer_article" role="tabpanel">
                    <% include Includes/RecurringInvoicePage/RecurringInvoiceArticleForms %>
                    <hr />
                    <% include Includes/RecurringInvoicePage/RecurringInvoiceArticleTable %>
                </div>
                <div class="tab-pane fade <% if $checkActiveTab == 'offer_generated_invoices' %>show active<% end_if %>" id="offer_generated_invoices" role="tabpanel">
                    <% include Includes/RecurringInvoicePage/RecurringInvoiceGeneratedInvoicesTable %>
                </div>
            </div>
        </div>
    </div>
    <datalist id="articleList">
        <option data-id="0" data-unit="0" data-nettoprice="0" value=""></option>
        <% loop getAllArticles %>
            <option data-id="$ID" data-unit="$Unit" data-nettoprice="$SellPriceNetto" data-title="$Title" value="$Title || $InternalID || $formatPrice($SellPriceNetto)"></option>
        <% end_loop %>
    </datalist>
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
            <a type="button" class="btn btn-sm btn-success px-4" href="{$Top.Link}add">Wiederkehrende Rechnung erstellen</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <% include Includes/RecurringInvoicePage/RecurringInvoiceTable %>
        </div>
    </div>
<% end_if %>
