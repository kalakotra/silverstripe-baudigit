# silverstripe-baudigit

Baudigit — invoice, offer, project and client management module for **SilverStripe 6**.

## Features

| Feature | Class |
|---------|-------|
| Invoices + PDF (ZUGFeRD/XRechnung) | `InvoicePageController` |
| Offers + PDF | `OfferPageController` |
| Projects + profitability | `ProjectPageController` |
| Clients + locations | `ClientPageController` |
| Article catalogue | `ArticlePageController` |
| Recurring invoices (cron) | `RecurringInvoicePageController` |
| Members / workers | `MemberPageController` |
| Worker time-sheets | `WorkerTasksPageController` / `WorkerTasksHoursPageController` |
| Worker calendar | `WorkerCalendarPageController` |
| Dashboard | `DashboardPageController` |
| Settings | `SettingsPageController` |

## Requirements

- PHP 8.2+
- SilverStripe Framework + CMS 6.x
- mpdf/mpdf 8.x
- horstoeko/zugferd 1.x

## Installation

```bash
composer require kalakotra/silverstripe-baudigit
vendor/bin/sake dev/build flush=1
```

## Feature toggles

Disable any feature in your project's `_config/*.yml`:

```yaml
Kalakotra\Baudigit\Controllers\RecurringInvoicePageController:
  enabled: false
```

## URL routing

All sub-URL routing happens inside each `PageController` via `$url_handlers`.
The single global route for the cron endpoint is in `_config/routes.yml`.

## Frontend (Vite + React 18)

```bash
npm install
npm run dev    # watch mode
npm run build  # production build → client/dist/
```

Include built assets in your page template:

```html
<script type="module" src="$resourceURL('kalakotra/silverstripe-baudigit:client/dist/invoice.js')"></script>
```

Then inject data and mount the widget:

```html
<script>
  window.baudigitInvoice = {
    invoiceID: $getCurrentInvoice.ID,
    editable: $getCurrentInvoice.checkEditingStatus,
    articles: []  // populated via controller JSON endpoint
  };
</script>
<div id="baudigit-invoice-articles"></div>
```

## i18n

All user-facing strings use `_t()` in PHP and the `t()` utility in React.
Translation files live in `lang/`.  German (`de.yml`) ships with the module.

Add your own locale:

```bash
# lang/fr.yml
fr:
  Kalakotra\Baudigit\Controllers\InvoicePageController:
    INVOICE_SAVED: 'Facture enregistrée avec succès.'
```

## License

Proprietary — kalakotra
