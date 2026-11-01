/**
 * Dashboard page entry point.
 *
 * Renders KPI stat cards from data injected by the template.
 *
 * Inject into template:
 *   <script>
 *     window.baudigitDashboard = {
 *       stats: {
 *         openInvoices:    $getOpenInvoicesCount,
 *         overdueInvoices: $getOverdueInvoicesCount,
 *         openOffers:      $getOpenOffersCount,
 *         activeProjects:  $getActiveProjectsCount,
 *         revenueThisMonth: "$getRevenueThisMonth",
 *         revenueLastMonth: "$getRevenueLastMonth"
 *       }
 *     };
 *   </script>
 *   <div id="baudigit-dashboard-stats"></div>
 */

import React from 'react';
import { createRoot } from 'react-dom/client';
import StatCard from '@/components/dashboard/StatCard';
import { t } from '@/utils/i18n';

function Dashboard({ stats }) {
  const cards = [
    {
      title: t('DashboardPage.OPEN_INVOICES', 'Offene Rechnungen'),
      value: stats.openInvoices ?? '—',
      icon: 'bi bi-receipt',
      color: 'text-primary',
    },
    {
      title: t('DashboardPage.OVERDUE_INVOICES', 'Überfällige Rechnungen'),
      value: stats.overdueInvoices ?? '—',
      icon: 'bi bi-exclamation-triangle',
      color: 'text-danger',
    },
    {
      title: t('DashboardPage.OPEN_OFFERS', 'Offene Angebote'),
      value: stats.openOffers ?? '—',
      icon: 'bi bi-file-earmark-text',
      color: 'text-warning',
    },
    {
      title: t('DashboardPage.ACTIVE_PROJECTS', 'Aktive Projekte'),
      value: stats.activeProjects ?? '—',
      icon: 'bi bi-kanban',
      color: 'text-success',
    },
    {
      title: t('DashboardPage.REVENUE_THIS_MONTH', 'Umsatz diesen Monat'),
      value: stats.revenueThisMonth ?? '—',
      icon: 'bi bi-currency-euro',
      color: 'text-info',
    },
    {
      title: t('DashboardPage.REVENUE_LAST_MONTH', 'Umsatz letzten Monat'),
      value: stats.revenueLastMonth ?? '—',
      icon: 'bi bi-graph-up',
      color: 'text-secondary',
    },
  ];

  return (
    <div className="row g-3">
      {cards.map((card, i) => (
        <StatCard key={i} {...card} />
      ))}
    </div>
  );
}

const container = document.getElementById('baudigit-dashboard-stats');
if (container && window.baudigitDashboard) {
  createRoot(container).render(
    <React.StrictMode>
      <Dashboard stats={window.baudigitDashboard.stats} />
    </React.StrictMode>
  );
}
