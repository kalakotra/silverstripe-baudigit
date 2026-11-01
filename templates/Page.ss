<!DOCTYPE html>

<html lang="$ContentLocale" data-bs-theme="semi-dark">
<head>
	<% base_tag %>
	<title><% if $MetaTitle %>$MetaTitle<% else %>$Title<% end_if %> &raquo; $SiteConfig.Title</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	$MetaTags(false)
	
	<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="BauDigit" />
	<link rel="manifest" href="/site.webmanifest" />

	<!-- loader-->
	<link href="/_resources/themes/baudigit/css/pace.min.css" rel="stylesheet">
	<script src="/_resources/themes/baudigit/javascript/pace.min.js"></script>

	<!--plugins-->
	<link href="/_resources/themes/baudigit/javascript/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="/_resources/themes/baudigit/javascript/plugins/metismenu/metisMenu.min.css">
	<link rel="stylesheet" type="text/css" href="/_resources/themes/baudigit/javascript/plugins/metismenu/mm-vertical.css">
	<link rel="stylesheet" type="text/css" href="/_resources/themes/baudigit/javascript/plugins/simplebar/css/simplebar.css">
	<link href="/_resources/themes/baudigit/javascript/plugins/fullcalendar/css/main.min.css" rel="stylesheet">
	<!--bootstrap css-->
	<link href="/_resources/themes/baudigit/css/bootstrap.min.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/javascript/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="/_resources/themes/baudigit/css/extra-icons.css">
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
	
  	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

	<link href="/_resources/themes/baudigit/javascript/plugins/Drag-And-Drop/dist/imageuploadify.min.css" rel="stylesheet">
	<link rel="stylesheet" href="/_resources/themes/baudigit/javascript/plugins/notifications/css/lobibox.min.css">

	<!--main css-->
	<link href="/_resources/themes/baudigit/css/bootstrap-extended.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/main.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/dark-theme.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/blue-theme.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/semi-dark.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/bordered-theme.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/responsive.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css" />
</head>
<body class="$ClassName.ShortName<% if not $Menu(2) %> no-sidebar<% end_if %>" <% if $i18nScriptDirection %>dir="$i18nScriptDirection"<% end_if %>>


<% include NotifyData %>

<% include Header %>
<% include SideBar %>
<div class="main-wrapper" role="main">
	<div class="main-content">
		$Layout
	</div>
</div>
<!--start overlay-->
<div class="overlay btn-toggle"></div>
<!--end overlay-->
<% include Footer %>

<!--bootstrap js-->
	<script src="/_resources/themes/baudigit/javascript/bootstrap.bundle.min.js"></script>

	<!--plugins-->
	<script src="/_resources/themes/baudigit/javascript/jquery.min.js"></script>
	<!--plugins-->
	<script src="/_resources/themes/baudigit/javascript/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="/_resources/themes/baudigit/javascript/plugins/metismenu/metisMenu.min.js"></script>
	<script src="/_resources/themes/baudigit/javascript/plugins/apexchart/apexcharts.min.js" type="dont/load"></script>
	<script src="/_resources/themes/baudigit/javascript/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="/_resources/themes/baudigit/javascript/plugins/peity/jquery.peity.min.js"></script>
	<script>
	$(".data-attributes span").peity("donut")
	</script>
	<script src="/_resources/themes/baudigit/javascript/plugins/fullcalendar/js/main.min.js"></script>
	<script src="/_resources/themes/baudigit/javascript/plugins/fullcalendar/js/de.global.js"></script>
	<script src="/_resources/themes/baudigit/javascript/dashboard2.js" type="dont/load"></script>

	<script src="/_resources/themes/baudigit/javascript/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="/_resources/themes/baudigit/javascript/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	
	<script src="/_resources/themes/baudigit/javascript/plugins/Drag-And-Drop/dist/imageuploadify.min.js"></script>

	<!-- signature -->
	<script src="/_resources/themes/baudigit/javascript/jSignature.min.js"></script>

	<!--notification js -->
	<script src="/_resources/themes/baudigit/javascript//plugins/notifications/js/lobibox.min.js"></script>
	<script src="/_resources/themes/baudigit/javascript//plugins/notifications/js/notifications.min.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js"></script>
	<script>
	    Fancybox.bind("[data-fancybox]", {
	        // Options will go here
	    });
	</script>
	<script src="/_resources/themes/baudigit/javascript/main.js"></script>
	
</body>
</html>
