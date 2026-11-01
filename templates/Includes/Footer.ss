<!--start footer-->
<footer class="page-footer">
    <p class="mb-0">
        &copy; $Now.format('Y') BauDigit - Alle Rechte vorbehalten.
        <% if $SiteConfig.CompanyContactWebsite %>
            | <a href="{$SiteConfig.CompanyContactWebsite}/impressum" target="_blank">Impressum</a> | <a href="{$SiteConfig.CompanyContactWebsite}/datenschutz" target="_blank">Datenschutz</a>
        <% end_if %>
    </p>
</footer>
<!--end footer-->