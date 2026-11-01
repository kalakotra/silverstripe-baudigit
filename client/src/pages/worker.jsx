/**
 * Worker pages entry point.
 *
 * Provides the time-logging (hours booking) widget for workers.
 *
 * Inject into template:
 *   <script>
 *     window.baudigitWorker = {
 *       memberID: $CurrentMember.ID,
 *       tasks: $getWorkerTasks.JSON()
 *     };
 *   </script>
 *   <div id="baudigit-worker-tasks"></div>
 */

import React, { useState } from 'react';
import { createRoot } from 'react-dom/client';
import { post } from '@/utils/api';
import { t } from '@/utils/i18n';

function WorkerTaskList({ memberID, tasks: initial }) {
  const [tasks] = useState(initial ?? []);
  const [hours, setHours] = useState({});
  const [notes, setNotes] = useState({});
  const [saving, setSaving] = useState({});
  const [saved, setSaved]   = useState({});

  async function logHours(taskID) {
    const h = parseFloat(hours[taskID]);
    if (!h || h <= 0) return;

    setSaving(prev => ({ ...prev, [taskID]: true }));
    try {
      await post(`worker-tasks-hours/log`, {
        TaskID: taskID,
        MemberID: memberID,
        Hours: h,
        Note: notes[taskID] ?? '',
        Date: new Date().toISOString().split('T')[0],
      });
      setSaved(prev => ({ ...prev, [taskID]: true }));
      setHours(prev => ({ ...prev, [taskID]: '' }));
      setTimeout(() => setSaved(prev => ({ ...prev, [taskID]: false })), 3000);
    } catch (err) {
      alert(err.message);
    } finally {
      setSaving(prev => ({ ...prev, [taskID]: false }));
    }
  }

  if (!tasks.length) {
    return (
      <p className="text-muted">
        {t('WorkerPage.NO_TASKS', 'Keine Aufgaben zugewiesen.')}
      </p>
    );
  }

  return (
    <div className="table-responsive">
      <table className="table table-sm table-hover">
        <thead className="table-light">
          <tr>
            <th>{t('WorkerPage.TASK', 'Aufgabe')}</th>
            <th>{t('WorkerPage.PROJECT', 'Projekt')}</th>
            <th className="text-end" style={{ width: 120 }}>
              {t('WorkerPage.HOURS', 'Stunden')}
            </th>
            <th>{t('WorkerPage.NOTE', 'Notiz')}</th>
            <th />
          </tr>
        </thead>
        <tbody>
          {tasks.map(task => (
            <tr key={task.ID}>
              <td>{task.Title}</td>
              <td className="text-muted small">{task.ProjectTitle}</td>
              <td>
                <input
                  type="number"
                  className="form-control form-control-sm text-end"
                  min="0"
                  step="0.25"
                  value={hours[task.ID] ?? ''}
                  onChange={e => setHours(prev => ({ ...prev, [task.ID]: e.target.value }))}
                  style={{ width: 90 }}
                />
              </td>
              <td>
                <input
                  type="text"
                  className="form-control form-control-sm"
                  value={notes[task.ID] ?? ''}
                  onChange={e => setNotes(prev => ({ ...prev, [task.ID]: e.target.value }))}
                  placeholder={t('WorkerPage.NOTE_PLACEHOLDER', 'optional…')}
                />
              </td>
              <td className="text-end">
                {saved[task.ID] ? (
                  <span className="text-success small">
                    <i className="bi bi-check2-circle me-1" />
                    {t('WorkerPage.SAVED', 'Gespeichert')}
                  </span>
                ) : (
                  <button
                    type="button"
                    className="btn btn-sm btn-success"
                    disabled={saving[task.ID] || !hours[task.ID]}
                    onClick={() => logHours(task.ID)}
                  >
                    {saving[task.ID]
                      ? <span className="spinner-border spinner-border-sm" />
                      : <i className="bi bi-plus-circle" />
                    }
                  </button>
                )}
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

const container = document.getElementById('baudigit-worker-tasks');
if (container && window.baudigitWorker) {
  const { memberID, tasks } = window.baudigitWorker;
  createRoot(container).render(
    <React.StrictMode>
      <WorkerTaskList memberID={memberID} tasks={tasks} />
    </React.StrictMode>
  );
}
