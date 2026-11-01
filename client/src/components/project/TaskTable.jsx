import React, { useState } from 'react';
import { del, post } from '@/utils/api';
import { t } from '@/utils/i18n';
import TaskRow from './TaskRow';

/**
 * Full task management widget for a Project.
 */
export default function TaskTable({ projectID, tasks: initial, editable }) {
  const [tasks, setTasks] = useState(initial ?? []);
  const [form, setForm]   = useState({ Title: '', EstimatedHours: '', Description: '' });
  const [showForm, setShowForm] = useState(false);
  const [loading, setLoading]   = useState(false);
  const [error, setError]       = useState(null);

  async function handleSave(e) {
    e.preventDefault();
    setLoading(true);
    setError(null);
    try {
      const task = await post('task/add', { ...form, ProjectID: projectID });
      setTasks(prev => [...prev, task]);
      setForm({ Title: '', EstimatedHours: '', Description: '' });
      setShowForm(false);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }

  async function handleDelete(id) {
    if (!window.confirm(t('ProjectPage.TASK_DELETE_CONFIRM', 'Aufgabe wirklich löschen?'))) return;
    try {
      await del(`task/delete/${id}`);
      setTasks(prev => prev.filter(t => t.ID !== id));
    } catch (err) {
      alert(err.message);
    }
  }

  return (
    <div>
      <div className="table-responsive">
        <table className="table table-sm table-hover mb-2">
          <thead className="table-light">
            <tr>
              <th>{t('ProjectPage.TASK_TITLE', 'Aufgabe')}</th>
              <th>{t('ProjectPage.TASK_STATUS', 'Status')}</th>
              <th className="text-end">{t('ProjectPage.TASK_ESTIMATED', 'Geschätzt')}</th>
              <th className="text-end">{t('ProjectPage.TASK_LOGGED', 'Gebucht')}</th>
              {editable && <th />}
            </tr>
          </thead>
          <tbody>
            {tasks.length === 0 ? (
              <tr>
                <td colSpan={editable ? 5 : 4} className="text-muted text-center py-3">
                  {t('ProjectPage.NO_TASKS', 'Keine Aufgaben vorhanden.')}
                </td>
              </tr>
            ) : (
              tasks.map(task => (
                <TaskRow
                  key={task.ID}
                  task={task}
                  editable={editable}
                  onEdit={() => {}}
                  onDelete={handleDelete}
                />
              ))
            )}
          </tbody>
        </table>
      </div>

      {editable && !showForm && (
        <button
          type="button"
          className="btn btn-sm btn-outline-success mb-2"
          onClick={() => setShowForm(true)}
        >
          <i className="bx bx-plus me-1" />
          {t('ProjectPage.TASK_ADD', 'Aufgabe hinzufügen')}
        </button>
      )}

      {editable && showForm && (
        <form onSubmit={handleSave} className="card border-0 bg-light p-3 mb-2">
          {error && <div className="alert alert-danger py-1 mb-2 small">{error}</div>}
          <div className="row g-2">
            <div className="col-md-6">
              <input
                type="text"
                className="form-control form-control-sm"
                placeholder={t('ProjectPage.TASK_TITLE', 'Aufgabentitel *')}
                value={form.Title}
                onChange={e => setForm(p => ({ ...p, Title: e.target.value }))}
                required
              />
            </div>
            <div className="col-md-3">
              <input
                type="number"
                className="form-control form-control-sm"
                placeholder={t('ProjectPage.TASK_ESTIMATED', 'Geschätzte Stunden')}
                value={form.EstimatedHours}
                onChange={e => setForm(p => ({ ...p, EstimatedHours: e.target.value }))}
                min="0" step="0.5"
              />
            </div>
            <div className="col-12">
              <textarea
                className="form-control form-control-sm"
                rows={2}
                placeholder={t('ProjectPage.TASK_DESCRIPTION', 'Beschreibung')}
                value={form.Description}
                onChange={e => setForm(p => ({ ...p, Description: e.target.value }))}
              />
            </div>
          </div>
          <div className="d-flex gap-2 mt-2">
            <button type="submit" className="btn btn-sm btn-success" disabled={loading}>
              {loading ? <span className="spinner-border spinner-border-sm" /> : t('ProjectPage.SAVE', 'Speichern')}
            </button>
            <button type="button" className="btn btn-sm btn-secondary" onClick={() => setShowForm(false)}>
              {t('ProjectPage.CANCEL', 'Abbrechen')}
            </button>
          </div>
        </form>
      )}
    </div>
  );
}
