# Spec Requirements Document

> Spec: project-management-mvp
> Created: 2025-08-27

## Overview

Introduce a basic Projects module that lets users create projects, track progress with milestones/tasks, and link invoices and expenses to show budget vs. actuals.

## User Stories

### Create and track a project
As an Operations Manager, I want to create a project with a client, dates, status, and budget so that I can monitor progress and financials.

Workflow: Create project → add milestones/tasks → link expenses/invoices → view summary.

### Link financials to projects
As a Project Lead, I want to link invoices and expenses to a project so that I can see budget utilization and profitability.

Workflow: From invoice/expense forms select project → project shows totals and variance.

## Spec Scope

1. **Projects CRUD** - name, client, status, start/end dates, budget amount
2. **Milestones/Tasks** - tasks with percent complete, assignee, due date
3. **Financial Linking** - link invoices and expenses to projects, show P&L
4. **Project Dashboard** - summary: progress %, budget vs actual, deadlines

## Out of Scope

- Time tracking and timers
- Gantt charts and advanced dependencies
- External integrations

## Expected Deliverable

1. UI in AdminLTE to manage projects, tasks, and view summaries
2. Database migrations and model relationships to support linking financials


