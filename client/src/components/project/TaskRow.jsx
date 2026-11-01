import React from 'react';
import { t } from '@/utils/i18n';

const STATUS_LABELS = {
  0: { label: t('ProjectPage.TASK_STATUS_NEW', 'Neu'),          cls: 'bg-secondary' },
  1: { label: t('ProjectPage.TASK_STATUS_OPEN', 'Offen'),       cls: 'bg-warning text-dark' },
  2: { label: t('ProjectPage.TASK_STATUS_DONE', 'Erledigt'),    cls: 'bg-success' },
};

/**
 * A single task row inside the project tasks table.
 */
export default function TaskRow({ task, onEdit, onDelete, editable }) {
  const statusInfo = STATUS_LABELS[task.Status] ?? { label: '?', cls: 'bg-light' };

  return (
    <tr>
      <td>{task.Title}</td>
      <td>
        <span className={`badge ${statusInfo.cls}`}>{statusInfo.label}</span>
      </td>
      <td>{task.EstimatedHours ?? '—'} h</td>
      <td>{task.LoggedHours ?? '—'} h</td>
      {editable && (
        <td className="text-end" style={{ whiteSpace: 'nowrap' }}>
          <button
            type="button"
            className="btn btn-sm btn-outline-secondary me-1"
            onClick={() => onEdit(task)}
            title={t('ProjectPage.TASK_EDIT', 'Bearbeiten')}
          >
            <i className="bx bx-pencil" />
          </button>
          <button
            type="button"
            className="btn btn-sm btn-outline-danger"
            onClick={() => onDelete(task.ID)}
            title={t('ProjectPage.TASK_DELETE', 'Löschen')}
          >
            <i className="bx bx-trash" />
          </button>
        </td>
      )}
    </tr>
  );
}
