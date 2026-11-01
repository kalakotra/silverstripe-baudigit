/**
 * Baudigit API utility
 *
 * Provides fetch wrappers that automatically include the SilverStripe
 * SecurityID (CSRF token) in POST/PUT/DELETE requests.
 *
 * Usage:
 *   import { post, get } from '@/utils/api';
 *   const data = await post('/invoice-page/article/add', { InvoiceID: 5, ... });
 */

/**
 * Read the SilverStripe SecurityID from a hidden form field on the page.
 * SilverStripe renders SecurityID into every Form — we pull it from the DOM.
 */
function getSecurityID() {
  const field = document.querySelector('input[name="SecurityID"]');
  return field ? field.value : '';
}

/**
 * Read the page's base URL from the <base> tag or window.location.
 */
function getBaseURL() {
  const base = document.querySelector('base');
  return base ? base.href : window.location.origin + '/';
}

/**
 * Perform a JSON POST request with CSRF token.
 * @param {string} url  Relative URL (will be resolved against base)
 * @param {object} body Payload object (will be JSON-serialised)
 * @returns {Promise<object>}
 */
export async function post(url, body) {
  const res = await fetch(getBaseURL() + url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      'X-SecurityID': getSecurityID(),
    },
    body: JSON.stringify({ ...body, SecurityID: getSecurityID() }),
  });

  if (!res.ok) {
    const err = await res.text();
    throw new Error(err || `HTTP ${res.status}`);
  }

  return res.json();
}

/**
 * Perform a JSON GET request.
 * @param {string} url  Relative URL
 * @returns {Promise<object>}
 */
export async function get(url) {
  const res = await fetch(getBaseURL() + url, {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
    },
  });

  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

/**
 * Perform a DELETE request.
 * @param {string} url
 * @returns {Promise<object>}
 */
export async function del(url) {
  const res = await fetch(getBaseURL() + url, {
    method: 'POST',   // SilverStripe usually uses POST for destructive actions
    headers: {
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ SecurityID: getSecurityID() }),
  });

  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}
