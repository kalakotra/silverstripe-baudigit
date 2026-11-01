<!--start sidebar-->
    <aside class="sidebar-wrapper" data-simplebar="true">
      <div class="sidebar-header">
        <div class="logo-icon">
          <img src="$ThemedResourceURL(/images/icon.png)" class="logo-img" alt="">
        </div>
        <div class="logo-name flex-grow-1">
          <h5 class="mb-0">BauDigit</h5>
        </div>
        <div class="sidebar-close">
          <span class="material-icons-outlined">close</span>
        </div>
      </div>
      <div class="sidebar-nav">
          <!--navigation-->
          <ul class="metismenu" id="sidenav">
            <%--
            <li class="menu-label">UI Elements</li>
            --%>
            <% loop $Menu(1) %>
              <% if MenuSeparator %>
                <li class="menu-label">$MenuSeparator</li>
              <% end_if %>
              <li class="<% if $LinkingMode !='link' %>mm-active<% end_if %>">
                <% if Children && $LinkingMode!='link' %>
                  <a href="javascript:;" >
                    <div class="parent-icon">
                      <% if MenuIcon %>
                        <i class="$MenuIcon"></i>
                      <% end_if %>
                    </div>
                    <div class="menu-title">
                      $MenuTitle.XML
                    </div>
                  </a>
                  <ul>
                    <% loop Children %>
                      <li class="<% if $LinkingMode !='link' %>mm-active<% end_if %>">
                        <a href="$Link"><i class="material-icons-outlined">arrow_right</i>$MenuTitle.XML</a>
                      </li>
                    <% end_loop %>
                  </ul>
                <% else %>
                  <a href="$Link">
                    <div class="parent-icon">
                      <% if MenuIcon %>
                        <i class="$MenuIcon"></i>
                      <% end_if %>
                    </div>
                    <div class="menu-title">
                      $MenuTitle.XML
                    </div>
                  </a>
                <% end_if %>
              </li>
            <% end_loop %>
           </ul>
          <!--end navigation-->
      </div>
    </aside>
  <!--end sidebar-->