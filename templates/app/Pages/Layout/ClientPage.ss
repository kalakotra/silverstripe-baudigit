<% if $getCurrentClient %>
    <div class="card">
        <div class="card-header text-end">
            <a href="{$Top.Link}location/add/$getCurrentClient.ID" class="btn btn-sm btn-success px-4">Standort hinzufügen</a>
            <a href="{$Top.Link}" class="btn btn-sm btn-primary px-4">Zurück zur Kundenübersicht</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="form-body">
                <% if $getCurrentLocation %>
                    $editLocation
                <% else %>
                    <%-- Client edit form + location list --%>
                    $edit
                    <% if checkLocationList %>
                        <hr />
                        <table id="locationListTable" class="dataTableMe table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Adresse</th>
                                    <th>Ansprechpartner</th>
                                    <th>Beschreibung</th>
                                    <th data-orderable="false"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <% loop readAllLocationInputs %>
                                    <tr>
                                        <td>$Name</td>
                                        <td>$Address <br />$PostalCode $City</td>
                                        <td>$ContactPerson <br />$ContactTelephone <br />$ContactEmail</td>
                                        <td>$Description</td>
                                        <td class="text-center">
                                            <a href="{$Top.Link}location/edit/$ID" class=""><i class="bx bx-edit-alt fs-4"></i></a>
                                        </td>
                                    </tr>
                                <% end_loop %>
                            </tbody>
                        </table>
                    <% end_if %>
                <% end_if %>
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
            <a type="button" class="btn btn-sm btn-success px-4" href="{$Top.Link}add">Kunde erstellen</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="locationListTable" class="dataTableMe table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Firma</th>
                            <th>Kontakt Person</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Adresse</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <% loop readAllInputs %>
                            <tr>
                                <td>$Company</td>
                                <td>$Name</td>
                                <td>$Email</td>
                                <td>$Phone</td>
                                <td>$Address <br />$PostalCode $City</td>
                                <td class="text-center">
                                    <a href="{$Top.Link}edit/$ID" class=""><i class="bx bx-edit-alt fs-5"></i></a>
                                </td>
                            </tr>
                        <% end_loop %>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<% end_if %>
