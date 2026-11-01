<div class="form-holder">
    <% if $getCurrentTask %>
        $editTaskForm
    <% else %>
        <% if $getCurrentProject.canAddTasksAndDocuments %>
            $addTaskForm
        <% else %>
            <%-- its Completed, Billed or Canceled --%>
            <p class="text-muted">
                Es können keine neuen Aufgaben hinzugefügt werden, da das Projekt den Status "Abgeschlossen", "Abgerechnet" oder "Abgebrochen" hat.
            </p>
        <% end_if %>
    <% end_if %>
</div>
