import React, { useState } from 'react';
import { del } from '@/utils/api';
import { t } from '@/utils/i18n';
import ArticleRow from './ArticleRow';
import ArticleForm from './ArticleForm';

/**
 * Full article management widget for an Invoice.
 * Renders the article table + inline add/edit form.
 *
 * Props:
 *   invoiceID  {number}  Parent invoice ID
 *   articles   {array}   Initial articles (from server JSON)
 *   editable   {boolean} Whether the invoice is in an editable state
 */
export default function ArticleTable({ invoiceID, articles: initial, editable }) {
  const [articles, setArticles] = useState(initial ?? []);
  const [editing, setEditing]   = useState(null);    // article being edited, or null (= show add form)
  const [showForm, setShowForm] = useState(false);

  // Calculate totals
  const totalNetto = articles.reduce((sum, a) => sum + Number(a.SellPriceNetto ?? 0) * Number(a.Quantity ?? 0), 0);
  const totalBrutto = articles.reduce((sum, a) => sum + Number(a.FinalPrice ?? 0), 0);

  function fmt(n) {
    return Number(n).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function handleSaved(article) {
    setArticles(prev => {
      const idx = prev.findIndex(a => a.ID === article.ID);
      if (idx >= 0) {
        const next = [...prev];
        next[idx] = article;
        return next;
      }
      return [...prev, article];
    });
    setShowForm(false);
    setEditing(null);
  }

  async function handleDelete(id) {
    if (!window.confirm(t('InvoicePage.ARTICLE_DELETE_CONFIRM', 'Artikel wirklich löschen?'))) return;
    try {
      await del(`article/delete/${id}`);
      setArticles(prev => prev.filter(a => a.ID !== id));
    } catch (err) {
      alert(err.message);
    }
  }

  function handleEdit(article) {
    setEditing(article);
    setShowForm(true);
    document.getElementById('baudigit-article-form')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function handleCancel() {
    setEditing(null);
    setShowForm(false);
  }

  return (
    <div>
      {/* ── Article table ─────────────────────────────────────── */}
      <div className="table-responsive">
        <table className="table table-sm table-hover mb-2">
          <thead className="table-light">
            <tr>
              <th>{t('InvoicePage.ARTICLE_TITLE', 'Artikel')}</th>
              <th>{t('InvoicePage.ARTICLE_DESCRIPTION', 'Beschreibung')}</th>
              <th className="text-end">{t('InvoicePage.ARTICLE_QUANTITY', 'Menge')}</th>
              <th>{t('InvoicePage.ARTICLE_UNIT', 'Einheit')}</th>
              <th className="text-end">{t('InvoicePage.ARTICLE_SELL_PRICE', 'Preis netto')}</th>
              <th className="text-end">{t('InvoicePage.ARTICLE_TAX', 'MwSt.')}</th>
              <th className="text-end">{t('InvoicePage.ARTICLE_FINAL', 'Gesamt brutto')}</th>
              {editable && <th />}
            </tr>
          </thead>
          <tbody>
            {articles.length === 0 ? (
              <tr>
                <td colSpan={editable ? 8 : 7} className="text-muted text-center py-3">
                  {t('InvoicePage.NO_ARTICLES', 'Keine Artikel vorhanden.')}
                </td>
              </tr>
            ) : (
              articles.map(a => (
                <ArticleRow
                  key={a.ID}
                  article={a}
                  editable={editable}
                  onEdit={handleEdit}
                  onDelete={handleDelete}
                />
              ))
            )}
          </tbody>
          {articles.length > 0 && (
            <tfoot className="table-light fw-semibold">
              <tr>
                <td colSpan={editable ? 6 : 5} className="text-end">
                  {t('InvoicePage.TOTAL_NETTO', 'Summe netto')}:
                </td>
                <td className="text-end">{fmt(totalNetto)} €</td>
                {editable && <td />}
              </tr>
              <tr>
                <td colSpan={editable ? 6 : 5} className="text-end">
                  {t('InvoicePage.TOTAL_BRUTTO', 'Summe brutto')}:
                </td>
                <td className="text-end">{fmt(totalBrutto)} €</td>
                {editable && <td />}
              </tr>
            </tfoot>
          )}
        </table>
      </div>

      {/* ── Add button ────────────────────────────────────────── */}
      {editable && !showForm && (
        <button
          type="button"
          className="btn btn-sm btn-outline-success mb-3"
          onClick={() => { setEditing(null); setShowForm(true); }}
        >
          <i className="bx bx-plus me-1" />
          {t('InvoicePage.ARTICLE_ADD', 'Artikel hinzufügen')}
        </button>
      )}

      {/* ── Add / Edit form ───────────────────────────────────── */}
      {editable && showForm && (
        <div id="baudigit-article-form" className="card border-0 bg-light p-3 mb-2">
          <ArticleForm
            invoiceID={invoiceID}
            initial={editing}
            onSaved={handleSaved}
            onCancel={handleCancel}
          />
        </div>
      )}
    </div>
  );
}
