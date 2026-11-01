<!DOCTYPE html>

<html lang="$ContentLocale" data-bs-theme="semi-dark">
<head>
	<% base_tag %>
	<title><% if $MetaTitle %>$MetaTitle<% else %>$Title<% end_if %> &raquo; $SiteConfig.Title</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	$MetaTags(false)
	<link rel="shortcut icon" href="$resourceURL('themes/simple/images/favicon.ico')" />

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
	<link rel="stylesheet" href="/_resources/themes/baudigit/css/extra-icons.css">
	<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
	<!--main css-->
	<link href="/_resources/themes/baudigit/css/bootstrap-extended.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/main.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/dark-theme.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/blue-theme.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/semi-dark.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/bordered-theme.css" rel="stylesheet">
	<link href="/_resources/themes/baudigit/css/responsive.css" rel="stylesheet">
</head>
<body class="$ClassName.ShortName<% if not $Menu(2) %> no-sidebar<% end_if %>" <% if $i18nScriptDirection %>dir="$i18nScriptDirection"<% end_if %>>
<div class="main-wrapper" role="main">
	<div class="main-content">
		$Layout
	</div>
</div>
<!--start overlay-->
<div class="overlay btn-toggle"></div>
<!--end overlay-->
<!--bootstrap js-->
  <script src="/_resources/themes/baudigit/javascript/bootstrap.bundle.min.js"></script>

  <!--plugins-->
  <script src="/_resources/themes/baudigit/javascript/jquery.min.js"></script>

  <!-- signature -->
  <script src="/_resources/themes/baudigit/javascript/jSignature.min.js"></script>

  <script src="/_resources/themes/baudigit/javascript/main.js"></script>
</body>
</html>
