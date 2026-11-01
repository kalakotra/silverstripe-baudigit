import React, { useState, useEffect } from 'react';
import { post } from '@/utils/api';
import { t } from '@/utils/i18n';

const EMPTY_FORM = {
  Title: '',
  Description: '',
  Quantity: '1',
  Unit: 'Stk',
  SellPriceNetto: '',
  BuyPriceNetto: '',
  Discount: '0',
  TaxRate: '19',
};

const UNITS = ['Stk', 'h', 'Std', 'km', 'Pkg', 'Monat', 'Pauschale', 'Liter', 'm', 'm²', 'Set'];

/**
 * Add / Edit article form for an Invoice.
 *
 * @param {number}   offerID   Parent invoice ID
 * @param {object}   initial     Pre-fill data when editing (null for new)
 * @param {function} onSaved     Called with the saved article data after success
 * @param {function} onCancel    Called when the user clicks Cancel
 */
export default function ArticleForm({ offerID, initial, onSaved, onCancel }) {
  const [fields, setFields] = useState(initial ? { ...EMPTY_FORM, ...initial } : EMPTY_FORM);
  const [loading, setLoading] = useState(false);
  const [error, setError]     = useState(null);

  // Re-populate when switching from add to edit or vice versa
  useEffect(() => {
    setFields(initial ? { ...EMPTY_FORM, ...initial } : EMPTY_FORM);
    setError(null);
  }, [initial]);

  const isEdit = Boolean(initial?.ID);

  function set(name, value) {
    setFields(prev => ({ ...prev, [name]: value }));
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      const url = isEdit
        ? `article/${initial.ID}`
        : `article/add`;

      const data = await post(url, { ...fields, OfferID: offerID });
      onSaved(data);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }

  function field(label, name, type = 'text', required = false) {
    return (
      <div className="col-md-6 mb-2">
        <label className="form-label small mb-1">{label}{required && ' *'}</label>
        <input
          type={type}
          className="form-control form-control-sm"
          value={fields[name]}
          onChange={e => set(name, e.target.value)}
          required={required}
        />
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit}>
      <h6 className="mb-3">
        {isEdit
          ? t('InvoicePage.ARTICLE_EDIT_TITLE', 'Artikel bearbeiten')
          : t('InvoicePage.ARTICLE_ADD_TITLE', 'Artikel hinzufügen')}
      </h6>

      {error && (
        <div className="alert alert-danger py-1 mb-2 small">{error}</div>
      )}

      <div className="row">
        {field(t('InvoicePage.ARTICLE_TITLE', 'Titel'), 'Title', 'text', true)}

        <div className="col-md-6 mb-2">
          <label className="form-label small mb-1">{t('InvoicePage.ARTICLE_UNIT', 'Einheit')}</label>
          <select
            className="form-select form-select-sm"
            value={fields.Unit}
            onChange={e => set('Unit', e.target.value)}
          >
            {UNITS.map(u => <option key={u} value={u}>{u}</option>)}
          </select>
        </div>

        {field(t('InvoicePage.ARTICLE_QUANTITY', 'Menge'), 'Quantity', 'number', true)}
        {field(t('InvoicePage.ARTICLE_SELL_PRICE', 'Verkaufspreis (netto)'), 'SellPriceNetto', 'number', true)}
        {field(t('InvoicePage.ARTICLE_BUY_PRICE', 'Einkaufspreis (netto)'), 'BuyPriceNetto', 'number')}
        {field(t('InvoicePage.ARTICLE_DISCOUNT', 'Rabatt (%)'), 'Discount', 'number')}
        {field(t('InvoicePage.ARTICLE_TAX', 'MwSt. (%)'), 'TaxRate', 'number', true)}

        <div className="col-12 mb-2">
          <label className="form-label small mb-1">{t('InvoicePage.ARTICLE_DESCRIPTION', 'Beschreibung')}</label>
          <textarea
            className="form-control form-control-sm"
            rows={2}
            value={fields.Description}
            onChange={e => set('Description', e.target.value)}
          />
        </div>
      </div>

      <div className="d-flex gap-2 mt-1">
        <button type="submit" className="btn btn-sm btn-success" disabled={loading}>
          {loading
            ? <><span className="spinner-border spinner-border-sm me-1" /> {t('InvoicePage.SAVING', 'Speichern…')}</>
            : t('InvoicePage.SAVE', 'Speichern')}
        </button>
        <button type="button" className="btn btn-sm btn-secondary" onClick={onCancel} disabled={loading}>
          {t('InvoicePage.CANCEL', 'Abbrechen')}
        </button>
      </div>
    </form>
  );
}
