<% with getNotifySessionData %>
    <% if $type %>
        <notification type="$type" icon="$icon" message="$message" />
    <% end_if %>
<% end_with %>