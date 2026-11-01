/**
 * Offer page entry point — mirrors the invoice article widget for Offers.
 *
 * Inject into template:
 *   <script>
 *     window.baudigitOffer = {
 *       offerID: $getCurrentOffer.ID,
 *       editable: $getCurrentOffer.checkEditingStatus,
 *       articles: $getCurrentOffer.OfferArticles.JSON()
 *     };
 *   </script>
 *   <div id="baudigit-offer-articles"></div>
 */

import React from 'react';
import { createRoot } from 'react-dom/client';
import OfferArticleTable from '@/components/offer/ArticleTable';

const container = document.getElementById('baudigit-offer-articles');
if (container && window.baudigitOffer) {
  const { offerID, editable, articles } = window.baudigitOffer;
  createRoot(container).render(
    <React.StrictMode>
      <OfferArticleTable
        offerID={offerID}
        articles={articles}
        editable={Boolean(editable)}
      />
    </React.StrictMode>
  );
}
