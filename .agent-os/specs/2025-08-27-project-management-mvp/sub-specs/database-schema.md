# Database Schema

This is the database schema implementation for the spec detailed in @.agent-os/specs/2025-08-27-project-management-mvp/spec.md

## Changes

- New table: projects
  - id (pk), client_id (fk->clients), name, status [planned/active/on-hold/completed], start_date, end_date, budget_amount (decimal 15,2), notes (text), timestamps
- New table: project_tasks
  - id (pk), project_id (fk->projects), title, assignee (nullable string), due_date (nullable), percent_complete (tinyint 0-100), status [todo/doing/done], timestamps
- Modify table: invoices (add nullable project_id fk->projects)
- Modify table: expenses (add nullable project_id fk->projects)

## Specifications

- Migrations with foreign keys and indexes on project_id/client_id
- Use enum or string for statuses (consistent with app style), index status
- Add cascading deletes for project_tasks on project delete (restrict if invoices/expenses linked)

## Rationale

- Enables linking financial records to projects for P&L
- Provides minimal yet useful project progress tracking without overcomplication
- Indexed FKs for performant rollups

