# Technical Specification

This is the technical specification for the spec detailed in @.agent-os/specs/2025-08-27-project-management-mvp/spec.md

## Technical Requirements

- Projects: Model, migration, controller, views (index/create/edit/show)
- Tasks: Model, migration, controller (nested under projects), fields: title, assignee(optional), due_date, percent_complete, status
- Link invoices/expenses to projects: add optional project_id FK to invoices and expenses
- Project Dashboard: aggregate progress (avg percent_complete), budget vs actual (sum invoices - sum expenses)
- Policies: Gate access to projects for authenticated users
- Eager loading: with(['client','tasks']) on project pages
- UI/UX: AdminLTE forms and tables; breadcrumbs consistent with app

## External Dependencies (Conditional)

- None required; use existing Laravel + AdminLTE stack

