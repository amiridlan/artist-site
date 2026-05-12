# AI Coding Agent Prompt — Content Production Kanban Board

**Role:** Senior Full-Stack Developer & UI/UX Expert

**Task:** Build a multi-format "Content Production Kanban Board" for an internal dashboard.

---

## Requirement Overview

Expand the existing Kanban feature to handle both quick social media posts and complex YouTube long-form videos. Implement robust filtering to prevent task "jumble" for a team of fewer than 6.

---

## 1. Data Structure & Card Types

### Task Metadata

- Add a `contentType` field with the following options:
  - `YouTube Long-form`
  - `Short-form/Social`
  - `Emergency/Trend`

### YouTube-Specific Fields

For `YouTube Long-form` cards, include sub-tasks for:

- `Scripting`
- `Filming`
- `Editing`

### Columns

| Column                  | Notes                                          |
| ----------------------- | ---------------------------------------------- |
| `Backlog / Ideas`       | All content types land here first              |
| `Drafting / Production` | YouTube takes longer; social posts flow faster |
| `Internal Review`       | All types go through review                    |
| `Scheduled`             | Approved content awaiting publish date         |
| `Published`             | Completed and live content                     |

---

## 2. UI Filtering & View Controls

### Global Toggles (Top-Bar Filter Menu)

- **Filter by Artist:** Dropdown to select one or more artists.
- **Filter by Content Type:** Toggle between `YouTube Only`, `Social Only`, or `Show All`.
- **"My Tasks" Toggle:** Quick filter to show only cards assigned to the currently logged-in user.

### Horizontal Swimlanes

- Group by **Artist** by default.
- Cards must be **color-coded** by `contentType`:
  - 🔴 Red → `YouTube Long-form`
  - 🔵 Blue → `Short-form/Social`
  - 🟠 Orange → `Emergency/Trend`

---

## 3. Workflow Logic

### WIP Limits

Set **separate limits per column and per content type**. Example:

- `Drafting / Production`:
  - Max **5** Social posts simultaneously
  - Max **2** YouTube videos simultaneously

### Drag-and-Drop

- Cards must be draggable between all columns regardless of `contentType`.
- On drop, update the card's `status` in the database via API call.

---

## Output Requirements

Provide the following deliverables:

1. **Vue 3 Components** — Kanban board with filtering logic and swimlane grouping.
2. **State Management** — Reactive toggles for filter controls (Pinia recommended).
3. **Backend Schema** — Updated database schema including the `contentType` field, YouTube sub-task fields, and WIP limit configuration.
4. **API Endpoints** — RESTful endpoints for:
   - Moving cards between columns (PATCH status update)
   - Fetching cards with filter params (artist, contentType, assignee)

---
