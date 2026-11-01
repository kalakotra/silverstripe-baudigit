/**
 * Baudigit i18n utility for React components
 *
 * Reads translations injected into the page by SilverStripe via:
 *   <script>
 *     window.baudigitI18n = <?= json_encode($this->i18nMessages()) ?>;
 *   </script>
 *
 * Falls back to the key itself when a translation is missing.
 *
 * Usage:
 *   import { t } from '@/utils/i18n';
 *   <button>{t('InvoicePage.SAVE', 'Speichern')}</button>
 */

const translations = window.baudigitI18n || {};

/**
 * Translate a key, optionally with variable substitution.
 * @param {string} key       Dot-notation key, e.g. 'InvoicePage.SAVE'
 * @param {string} fallback  Default string if key is missing
 * @param {object} vars      Optional variables: { name: 'World' } replaces {name}
 * @returns {string}
 */
export function t(key, fallback = '', vars = {}) {
  let str = translations[key] ?? fallback ?? key;

  Object.entries(vars).forEach(([k, v]) => {
    str = str.replace(`{${k}}`, v);
  });

  return str;
}
