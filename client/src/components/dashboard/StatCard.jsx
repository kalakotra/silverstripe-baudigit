import React from 'react';

/**
 * A single KPI stat card for the dashboard.
 *
 * @param {string} title   Card label
 * @param {string} value   Main value (already formatted as a string)
 * @param {string} icon    Bootstrap-icon class, e.g. 'bi bi-receipt'
 * @param {string} color   Bootstrap color class, e.g. 'text-primary'
 * @param {string} trend   Optional trend badge, e.g. '+12%'
 * @param {string} trendColor  'text-success' | 'text-danger'
 */
export default function StatCard({ title, value, icon, color = 'text-primary', trend, trendColor }) {
  return (
    <div className="col-12 col-sm-6 col-xl-3">
      <div className="card border-0 shadow-sm h-100">
        <div className="card-body d-flex align-items-center gap-3 py-3">
          <div className={`fs-1 ${color}`}>
            <i className={icon} />
          </div>
          <div>
            <div className="fs-5 fw-bold lh-1">{value}</div>
            <div className="small text-muted mt-1">{title}</div>
            {trend && (
              <div className={`small fw-semibold mt-1 ${trendColor ?? 'text-secondary'}`}>
                {trend}
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
