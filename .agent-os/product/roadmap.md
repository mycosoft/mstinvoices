# Product Roadmap

## Phase 0: Already Completed

- [x] Invoice management with statuses and PDF export
- [x] Quotation management with conversion to invoice and email
- [x] Client and item catalogs
- [x] Expenses tracking with categories and approvals
- [x] Reports and dashboards (revenue, cash flow, quick reports)
- [x] Authentication and AdminLTE UI integration

## Phase 1: Project Management (MVP)

**Goal:** Introduce projects with basic progress tracking and financial linkage
**Success Criteria:** Create projects, track status and budget vs. actuals, link invoices/expenses

### Features
- [ ] Projects CRUD (name, client, status, start/end, budget) `M`
- [ ] Milestones and tasks (percent complete, assignees, due dates) `M`
- [ ] Link invoices and expenses to projects; project P&L summary `M`
- [ ] Project dashboard: progress, budget utilization, upcoming deadlines `S`
- [ ] Permissions: project-level access within existing auth `S`

### Dependencies
- Settings: enable project module
- Database migrations for projects, milestones, tasks, links

## Phase 2: Financials & Workflow

**Goal:** Improve billing automation and tracking
**Success Criteria:** Faster quote-to-cash and clearer receivables

### Features
- [ ] Partial payments and payment schedules `M`
- [ ] Overdue reminders and dunning emails `S`
- [ ] Payment methods configurability in settings (bank/MoMo) `S`
- [ ] Export to Excel/CSV for projects and financials `S`

### Dependencies
- Background jobs for reminders

## Phase 3: Scale & Polish

**Goal:** Usability, performance, and extensibility
**Success Criteria:** Faster UI, reusable invoice styles, cleaner codebase

### Features
- [ ] Extract invoice CSS to assets; multiple templates `S`
- [ ] Eager loading and N+1 guards in controllers `S`
- [ ] Role-based access control (Admin/Staff) `M`
- [ ] API endpoints for invoices/projects (future mobile) `M`

### Dependencies
- RBAC library or policies; API authentication

