/**
 * Invoice page entry point.
 *
 * Mounts the React ArticleTable widget into #baudigit-invoice-articles
 * when that element exists on the page.
 *
 * Data is read from window.baudigitInvoice (injected by the template):
 *
 *   <script>
 *     window.baudigitInvoice = {
 *       invoiceID: $getCurrentInvoice.ID,
 *       editable: $getCurrentInvoice.checkEditingStatus,
 *       articles: $getCurrentInvoice.InvoiceArticles.JSON()
 *     };
 *   </script>
 *   <div id="baudigit-invoice-articles"></div>
 */

import React from 'react';
import { createRoot } from 'react-dom/client';
import ArticleTable from '@/components/invoice/ArticleTable';

const container = document.getElementById('baudigit-invoice-articles');
if (container && window.baudigitInvoice) {
  const { invoiceID, editable, articles } = window.baudigitInvoice;
  createRoot(container).render(
    <React.StrictMode>
      <ArticleTable
        invoiceID={invoiceID}
        articles={articles}
        editable={Boolean(editable)}
      />
    </React.StrictMode>
  );
}
