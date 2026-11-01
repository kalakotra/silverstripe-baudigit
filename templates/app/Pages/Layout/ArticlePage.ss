<% if $getCurrentArticle %>
    <div class="card">
        <div class="card-header text-end">
            <a href="{$Top.Link}" class="btn btn-sm btn-primary px-4">Zurück zur Übersicht</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-body">
                $edit
            </div>
        </div>
    </div>
<% else %>
    <div class="card">
        <div class="card-header text-end">
            <button type="button" class="btn btn-sm btn-success px-4" data-bs-toggle="modal" data-bs-target="#FormModal">Produkt erstellen</button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="productListTable" class="dataTableMe table table-striped table-bordered" style="width:100%">
                    <thead>$Controller.readAllInputs.Count
                        <tr>
                            <th>Interne ID </th>
                            <th>Titel</th>
                            <th>Einheit</th>
                            <th>VP netto</th>
                            <th>VP brutto</th>
                            <th>Beschreibung</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <% loop readAllInputs %>
                            <tr>
                                <td>$InternalID</td>
                                <td>$Title</td>
                                <td>$getUnitLabel</td>
                                <td>$SellPriceNetto</td>
                                <td>$SellPriceBrutto</td>
                                <td>$Description</td>
                                <td class="text-center"><a href="{$Top.Link}edit/$ID" class=""><i class="bx bx-edit-alt fs-5"></i></a></td>
                            </tr>
                        <% end_loop %>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="FormModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 py-2 bg-light">
                    <h5 class="modal-title">Neues Produkt</h5>
                    <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="modal">
                        <i class="material-icons-outlined">close</i>
                    </a>
                </div>
                <div class="modal-body">
                    <div class="form-body">
                        $add
                    </div>
                </div>
            </div>
        </div>
    </div>
<% end_if %>
