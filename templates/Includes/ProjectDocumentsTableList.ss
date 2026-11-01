<ul class="nav nav-tabs nav-primary" role="tablist">
    <% loop Top.getProjectDocumentCategories %>
        <li class="nav-item" role="presentation">
            <a class="nav-link <% if isFirst %>active<% end_if %>" data-bs-toggle="tab" href="#project_document_$ID" role="tab" aria-selected="<% if isFirst %>active<% else %>false<% end_if %>">
                <div class="d-flex align-items-center">
                    <div class="tab-title">$Title ($Top.actProject.Documents.filter('Category', $ID).count())</div>
                </div>
            </a>
        </li>
    <% end_loop %>
</ul>
<div class="tab-content py-3">
    <% loop Top.getProjectDocumentCategories %>
        <div class="tab-pane fade <% if isFirst %> show active<% end_if %>" id="project_document_$ID" role="tabpanel">
            <div class="row">
                <% loop $Top.actProject.Documents.filter('Category', $ID) %>
                    <div class="col-6 col-md-4 col-lg-3 document-item mb-2">
                        <a href="$Link" target="_blank" data-fancybox>
                            <i class="bi bi-file-text bi-filetype-$Extension me-1 fs-1"></i>
                            <br />
                            $Title
                        </a>
                    </div>
                <% end_loop %>
            </div>
        </div>
    <% end_loop %>
</div>
