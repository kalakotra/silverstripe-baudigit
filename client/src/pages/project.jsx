/**
 * Project page entry point.
 *
 * Mounts task and document management widgets.
 *
 * Inject into template:
 *   <script>
 *     window.baudigitProject = {
 *       projectID: $getCurrentProject.ID,
 *       editable: $getCurrentProject.canAddTasksAndDocuments,
 *       tasks: $getCurrentProject.ProjectTasks.JSON()
 *     };
 *   </script>
 *   <div id="baudigit-project-tasks"></div>
 */

import React from 'react';
import { createRoot } from 'react-dom/client';
import TaskTable from '@/components/project/TaskTable';

const taskContainer = document.getElementById('baudigit-project-tasks');
if (taskContainer && window.baudigitProject) {
  const { projectID, editable, tasks } = window.baudigitProject;
  createRoot(taskContainer).render(
    <React.StrictMode>
      <TaskTable
        projectID={projectID}
        tasks={tasks}
        editable={Boolean(editable)}
      />
    </React.StrictMode>
  );
}
