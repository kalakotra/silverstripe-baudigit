<div id='calendarMembers' <% if actProject.StartDate %>data-start="$actProject.StartDate.Format('yyyy-MM-dd')" <% end_if %> <% if actProject.EndDate %>data-end="$actProject.EndDate.Format('yyyy-MM-dd')" <% end_if %>>
    <% loop getAssignedTasks %>
        <marker data-url="https://www.google.com/maps/place/{$Project.ClientObject.Address}+{$Project.ClientObject.PostalCode},+{$Project.ClientObject.City}" data-name="$Project.ClientObject.Name $Project.ClientObject.Address {$Project.ClientObject.PostalCode}, $Project.ClientObject.City - $Title" data-popover="text" data-color--="$Member.MemberColor" data-start="$StartDate.Format('yyyy-MM-dd')" data-end="$EndDate.Format('yyyy-MM-dd')"></marker>
    <% end_loop %>
</div>