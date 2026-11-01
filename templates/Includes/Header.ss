<!--start header-->
  <header class="top-header">
    <nav class="navbar navbar-expand align-items-center gap-4 justify-content-between">
      <div class="btn-toggle">
        <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
      </div>
      <div class="search-bar flex-grow-1">
        <div class="position-relative">
          <div class="search-popup p-3">
            <div class="card rounded-4 overflow-hidden">
              <div class="card-header d-lg-none">
                
              </div>
              <div class="card-body search-content">
                
              </div>
              <div class="card-footer text-center bg-transparent">
                
              </div>
            </div>
          </div>
        </div>
      </div>
      <ul class="navbar-nav gap-1 nav-right-links align-items-center">
        <li class="nav-item d-lg-none mobile-search-btn">
          <a class="nav-link" href="javascript:;"><i class="material-icons-outlined">search</i></a>
        </li>
        <li class="nav-item dropdown">
          
        </li>

        <li class="nav-item dropdown position-static  d-md-flex d-none">
          
        </li>
        <li class="nav-item dropdown">
          
        </li>
        <li class="nav-item dropdown">
          <div class="dropdown-menu dropdown-notify dropdown-menu-end shadow">
            <div class="notify-list">
            </div>
          </div>
        </li>
        <li class="nav-item d-md-flex d-none">
          
        </li>
        <li class="nav-item dropdown">
          
        </li>
      </ul>
      <% if SiteConfig.Logo %>
        <div class="appLogo">$SiteConfig.Logo.ScaleMaxWidth(120)</div>
      <% else %>
        <h1 class="h4 mb-0">$SiteConfig.Title</h1>
      <% end_if %>
    </nav>
  </header>
  <!--end top header-->