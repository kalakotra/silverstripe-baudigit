import React from 'react';
import { t } from '@/utils/i18n';

/**
 * A single article row in the invoice article table.
 * Shows article details + inline edit/delete buttons.
 *
 * @param {object}   article   InvoiceArticle data from the server
 * @param {function} onEdit    Called with (article) when Edit is clicked
 * @param {function} onDelete  Called with (article.ID) when Delete is clicked
 * @param {boolean}  editable  Whether edit/delete controls are shown
 */
export default function ArticleRow({ article, onEdit, onDelete, editable }) {
  const finalPrice = Number(article.FinalPrice ?? 0).toLocaleString('de-DE', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

  return (
    <tr>
      <td>{article.Title}</td>
      <td>{article.Description}</td>
      <td className="text-end">{article.Quantity}</td>
      <td>{article.Unit}</td>
      <td className="text-end">
        {Number(article.SellPriceNetto ?? 0).toLocaleString('de-DE', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2,
        })}
      </td>
      <td className="text-end">{article.TaxRate} %</td>
      <td className="text-end fw-semibold">{finalPrice} €</td>
      {editable && (
        <td className="text-end" style={{ whiteSpace: 'nowrap' }}>
          <button
            type="button"
            className="btn btn-sm btn-outline-secondary me-1"
            onClick={() => onEdit(article)}
            title={t('InvoicePage.ARTICLE_EDIT', 'Bearbeiten')}
          >
            <i className="bx bx-pencil" />
          </button>
          <button
            type="button"
            className="btn btn-sm btn-outline-danger"
            onClick={() => onDelete(article.ID)}
            title={t('InvoicePage.ARTICLE_DELETE', 'Löschen')}
          >
            <i className="bx bx-trash" />
          </button>
        </td>
      )}
    </tr>
  );
}
