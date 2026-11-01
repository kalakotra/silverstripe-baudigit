<header name="Header">
    <div class="row">
        <div class="col-3">
            $SiteConfig.Logo
        </div>
        <div class="col-9 text-end">
            <h2>$SiteConfig.CompanyName</h2>
            <p>
                <% if $SiteConfig.CompanyAddressLine1 %>
                    $SiteConfig.CompanyAddressLine1<br>
                <% end_if %>
                <% if $SiteConfig.CompanyAddressLine2 %>
                    $SiteConfig.CompanyAddressLine2<br>
                <% end_if %>
                $SiteConfig.CompanyPostalCode $SiteConfig.CompanyCity<br>
                <% if $SiteConfig.CompanyContactPhone %>
                    $SiteConfig.CompanyContactPhone<br>
                <% end_if %>
                $SiteConfig.CompanyContactEmail
            </p>
        </div>
    </div>
</header>