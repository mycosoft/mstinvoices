# API Specification

This is the API specification for the spec detailed in @.agent-os/specs/2025-08-27-project-management-mvp/spec.md

## Endpoints

### GET /projects
**Purpose:** List projects
**Parameters:** q (search), status, client_id
**Response:** JSON array of projects with client and progress
**Errors:** 401, 500

### POST /projects
**Purpose:** Create project
**Parameters:** name, client_id, status, start_date, end_date, budget_amount
**Response:** JSON project
**Errors:** 422, 401, 500

### GET /projects/{project}
**Purpose:** Show project with tasks and financial summary
**Parameters:** id
**Response:** JSON project, tasks, totals
**Errors:** 404, 401, 500

### PUT /projects/{project}
**Purpose:** Update project
**Parameters:** fields as above
**Response:** JSON project
**Errors:** 422, 404, 401, 500

### DELETE /projects/{project}
**Purpose:** Delete project
**Errors:** 404, 401, 409 (linked invoices/expenses), 500

### POST /projects/{project}/tasks
**Purpose:** Create task
**Parameters:** title, assignee, due_date, percent_complete, status
**Response:** JSON task
**Errors:** 422, 404, 401, 500

### PUT /projects/{project}/tasks/{task}
**Purpose:** Update task
**Parameters:** fields as above
**Response:** JSON task
**Errors:** 422, 404, 401, 500

### DELETE /projects/{project}/tasks/{task}
**Purpose:** Delete task
**Errors:** 404, 401, 500

