<% if $getCurrentRecurringInvoice.getGermanStatusOptionsDataList %>
    <div class="row justify-content-end g-2 align-items-center">
        <div class="col-auto text-end">
            <b>Status ändern: </b>
        </div>
        <% loop $getCurrentRecurringInvoice.getGermanStatusOptionsDataList %>
            <div class="col-auto">
                <a href="{$Top.Link}change-status/{$Up.getCurrentRecurringInvoice.ID}/$Key" class="yousure btn btn-sm btn-sm <% if isActive %> btn-success<% else %> btn-outline-secondary<% end_if %>">$Title</a>
            </div>
        <% end_loop %>
    </div>
<% end_if %>
