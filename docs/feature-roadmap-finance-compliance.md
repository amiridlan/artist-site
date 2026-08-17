# Feature Roadmap: Finance, Compliance & Operations Pain Points

Planning artifact only — no implementation yet. Captures the 9 curated pain points, confirmed scope decisions, and a detailed technical plan for all 5 phases (Phase 1 is fully ready to implement; Phases 2-5 are drafted but flagged where a dedicated pre-implementation review or external sign-off is still needed), so the roadmap survives independently of any single conversation/session.

## Background

After shipping 4 features (compliance documents, contracts, reporting dashboard, fan notifications), the following 9 additional pain points were curated by the team and scoped into a 5-phase roadmap. Scope decisions below were confirmed directly by the team, not assumed.

## The 9 pain points

1. Revenue splitting (group vs. individual) done manually in spreadsheets — error-prone, trust risk.
2. Costs tied to events/projects not tracked systematically.
3. No traceability between a contract and the actual work/event it governs.
4. Recurring streaming/royalty income (Spotify, YouTube, RIM/PPM) doesn't fit a one-off revenue model — reconciliation is manual.
5. No central record for member details (sizing, health notes, visa/passport, emergency contacts).
6. No formal sign-off trail for contracts, rates, or final deliverables — disputes over "who approved this."
7. No way to track brand/partner deals before they're signed (leads, negotiation, follow-ups).
8. No visibility into earnings trends, commission totals, expiring contracts, content velocity.
9. Navigating PPM/MACP royalty collection, visas/permits for international gigs.

## Confirmed scope decisions

| Area | Decision |
|---|---|
| Revenue splitting (#1) | **Record-only.** System stores agreed % / fixed splits per member/group and displays/exports them. No payout calculation, no money moves through the system. |
| Accounting tool | **Standalone** — no Xero/AutoCount/SQL Account integration; none currently in use. |
| Sensitive data access (#5) | Super-Admin-only by default, with a **Super-Admin-configurable toggle** to grant specific departments read access to passport/visa fields on demand — a dynamic settings table + toggle UI, not a static seeded permission. |
| Health notes (#5) | **Structured fields** (allergies, conditions, medications), not free text. |
| Sign-off trail (#6) | **Single Super-Admin approver** — no dual-approval chain. |
| Brand deal pipeline (#7) | **Full pipeline** (lead→negotiating→signed→lost), justified by 10+ deals/year. |
| PPM/MACP (#9a) | **Both apply** — some members co-write, so composer royalties (MACP) track alongside performer royalties (RPM). Status/reconciliation only, never auto-calculated amounts. |
| Visa/permit tracker (#9b) | **Build it** — justified by 4+ international gigs/year. |
| Retention window for #5 | **Still open** — needs actual HR/legal sign-off before Phase 3 goes live. Do not build/ship purge logic without this confirmation. |

## Per-pain-point technical mapping

| # | Pain point | Concept | New vs. layered | Risk flag |
|---|---|---|---|---|
| 1 | Revenue splitting | `revenue_events` + `revenue_splits` tables. Records agreed % or fixed splits per member/group; derives display/export amounts only. Optional FK to `schedule_events`/`contracts`. | Net new | **Highest financial-trust risk.** Splits must be immutable once finalized (versioned, never UPDATE'd). |
| 2 | Cost tracking | `cost_entries`, polymorphic (mirrors `documents`/`conflict_logs`) onto `ScheduleEvent`/`KanbanCard`/`Contract`. Ledger-lite: category, amount, submitter, approver — not double-entry bookkeeping. | Net new | Submitter ≠ approver must be enforced at build time. |
| 3 | Contract-to-work traceability | `contract_schedule_event` pivot (composite key, mirrors `member_schedule_event`). | Tiny/layered | None. |
| 4 | Recurring royalty income | `royalty_income_entries`: source, period, expected vs. received, reconciliation status. Manual reconciliation log only — no Spotify/YouTube/PPM API integration. | Net new | Low — reconciling money already received. |
| 5 | Central sensitive member record | New `member_sensitive_records` table, 1:1 with Member. Encrypted casts mandatory for passport number and health notes. Structured health fields. Dynamic department-access toggle. | Net new | **Most legally sensitive item.** Needs PDPA consent record, read-access audit log, retention policy (open). |
| 6 | Sign-off trail | Polymorphic `approvals` table (mirrors `conflict_logs`) attachable to `Contract` and `KanbanCard`. `approval_status`/`approved_by`/`approved_at` on `Contract`; `deliverable_approved_at`/`deliverable_approved_by` on `KanbanCard`. | Layered onto Contract/KanbanCard | Internal corroboration only — the signed PDF remains the legal instrument, not e-signature. New `approve-deliverables` permission, Super-Admin-only. |
| 7 | Brand/partner deal pipeline | `deals` table (lead→negotiating→signed→lost), links to `contracts.id` on signing. | Net new, small | Not a full CRM — no email sync/automation. |
| 8 | Reporting (earnings, commissions, expiring contracts, content velocity) | New `ReportController` queries. Expiring-contracts + content-velocity are cheap (existing data); earnings/commission totals depend on #1/#2. | Layered onto ReportController | Gate by `view-revenue`/`view-costs`, not blanket `view-reports`. |
| 9 | PPM/MACP + visa/permit navigation | (a) status-only `royalty_registrations`/`royalty_claims` tables — no amount calculation ever. (b) visa/permit checklist status field on `ScheduleEvent`. | Net new, both | Legal distinction: MACP (composer rights) vs. RPM (performer rights) — both apply per confirmed decision. |

## Phased roadmap

| Phase | Contents | Size | Gate |
|---|---|---|---|
| **0** | Confirm scope questions (done — see decisions above); kick off HR/legal engagement on #5's retention policy (longest lead time on the roadmap) | — | Done except retention window |
| **1** | #3 traceability, #6 sign-off trail, #7 deal pipeline, cheap #8 report additions (expiring contracts, content velocity) | Small | None — fully detailed below, ready to implement |
| **2** | #1 revenue splits, #2 cost tracking | Large | Immutable splits and dual permission-file updates (`RolePermissionSeeder.php` + `HandleInertiaRequests.php`'s `auth.can`) are non-negotiable |
| **3** | #5 sensitive records | Large | Schema can build in parallel with Phase 2; **go-live blocked until HR/legal signs off on retention window + consent wording** |
| **4** | #4 royalty reconciliation + remaining #8 report sections | Medium | Reports gated per-permission |
| **5** | #9 visa/permit tracker | Small | Confirmed in-scope given 4+ intl gigs/year |

---

## Phase 1: Contract Traceability, Sign-Off Trail, Deal Pipeline, Report Additions

**Status: ready to implement.** Fully detailed, no open questions.

Extends existing conventions only: the `conflict_logs`/`documents` polymorphic pattern, the `member_schedule_event`-style composite-key pivot pattern, inline `$request->validate()` (no FormRequest classes), raw permission-string `$this->authorize()` calls, and the Contracts/Reports page styling already shipped.

### A. Contract ↔ Work Traceability (#3)

**Migration** `contract_schedule_event` pivot — mirrors `member_schedule_event` exactly (composite primary key, no `id()`, no timestamps):
```php
Schema::create('contract_schedule_event', function (Blueprint $table) {
    $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
    $table->foreignId('schedule_event_id')->constrained()->cascadeOnDelete();
    $table->primary(['contract_id', 'schedule_event_id']);
});
```

**Model changes**:
- `app/Models/Contract.php`: add `scheduleEvents(): BelongsToMany` → `belongsToMany(ScheduleEvent::class, 'contract_schedule_event')`.
- `app/Models/ScheduleEvent.php`: add `contracts(): BelongsToMany` → `belongsToMany(Contract::class, 'contract_schedule_event')`.

**Controller** `app/Http/Controllers/Admin/ContractController.php`:
- `edit(Contract $contract)`: also pass `memberScheduleEvents` (that member's `ScheduleEvent`s via `Member::scheduleEvents()`) and `linkedEventIds` (`$contract->scheduleEvents->pluck('id')`).
- `update()`: accept `schedule_event_ids` (`nullable|array`, `schedule_event_ids.*` `exists:schedule_events,id`), `$contract->scheduleEvents()->sync($data['schedule_event_ids'] ?? [])`.

**Frontend** `resources/js/Pages/Admin/Contracts/Edit.vue`: "Governed Events" checkbox list (that member's events, title + date), bound to `form.schedule_event_ids`. `Members/Show.vue`: show linked-event count per contract in the existing Contracts section.

### B. Sign-Off Trail (#6)

Polymorphic pattern (like `conflict_logs`/`documents`) covers both contracts/rates AND final deliverables via one mechanism, matching pain point #6's own wording.

**Migration 1** `approvals` table:
```php
Schema::create('approvals', function (Blueprint $table) {
    $table->id();
    $table->string('approvable_type');
    $table->unsignedBigInteger('approvable_id');
    $table->enum('action', ['submitted', 'approved', 'rejected']);
    $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->index(['approvable_type', 'approvable_id'], 'approvals_approvable_idx');
});
```

**Migration 2** add to `contracts`: `approval_status` enum(`pending`,`approved`,`rejected`) default `pending`, `approved_by` (FK users, nullable), `approved_at` (nullable timestamp).

**Migration 3** add to `kanban_cards`: `deliverable_approved_at` (nullable timestamp), `deliverable_approved_by` (FK users, nullable).

**Model** `app/Models/Approval.php`: `approvable(): MorphTo`, `performedBy(): BelongsTo(User::class, 'performed_by')`.

**Model changes**: `Contract.php` gets `approvals(): MorphMany`, `approval_status`/`approved_by`/`approved_at` fillable/casts. `KanbanCard.php` gets `approvals(): MorphMany`, `deliverable_approved_at`/`deliverable_approved_by` fillable/cast.

**Controller changes**:
- `ContractController`: `approve(Contract $contract)` and `reject(Request $request, Contract $contract)`, both gated `approve-deliverables` (new permission, Super-Admin-only — stricter than `manage-contracts`). `approve()` sets status/approver/timestamp + logs `Approval`. `reject()` requires `notes` (reason), sets status, logs `Approval`.
- `KanbanCardController`: `approveDeliverable(KanbanCard $kanbanCard)`, gated `approve-deliverables`, only allowed when `stage === 'completed'` (else `abort(422)`), sets deliverable-approval fields + logs `Approval`.

**Routes**:
```php
Route::post('contracts/{contract}/approve', [ContractController::class, 'approve'])->name('contracts.approve');
Route::post('contracts/{contract}/reject', [ContractController::class, 'reject'])->name('contracts.reject');
Route::post('kanban/{kanbanCard}/approve-deliverable', [KanbanCardController::class, 'approveDeliverable'])->name('kanban.approve-deliverable');
```

**Frontend**: `Contracts/Edit.vue` — approval-status badge, Approve/Reject buttons if `auth.can['approve-deliverables']` (reject opens inline textarea for required reason), approval history list below (same list-row style as Documents/Contracts sections). `Kanban/Index.vue` — on `completed`-stage cards without `deliverable_approved_at`, show "Approve Deliverable" button if permitted; once approved, show a green "✓ Approved" badge. Needs `usePage()` added to this file for `auth.can` access (matches `Members/Show.vue`/`Calendar/Index.vue` pattern).

### C. Brand/Partner Deal Pipeline (#7)

**Migration** `deals` table:
```php
Schema::create('deals', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('partner_name');
    $table->enum('stage', ['lead', 'negotiating', 'signed', 'lost'])->default('lead');
    $table->decimal('value_estimate', 10, 2)->nullable();
    $table->text('description')->nullable();
    $table->foreignId('contract_id')->nullable()->constrained('contracts')->nullOnDelete();
    $table->date('follow_up_date')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
    $table->index('stage');
    $table->index('follow_up_date');
});
```

**Model** `app/Models/Deal.php`: fillable (`title, partner_name, stage, value_estimate, description, contract_id, follow_up_date, created_by`), casts (`value_estimate => decimal:2, follow_up_date => date`), `contract(): BelongsTo`, `createdBy(): BelongsTo`, `scopeInStage($query, $stage)`.

**Controller** `app/Http/Controllers/Admin/DealController.php` — mirrors `ContractController`: `index` (stage filter, paginated, overdue follow-up highlighted), `create`/`store`, `edit`/`update`, `destroy`, plus `moveStage(Deal $deal)` (`PATCH`, validates `stage` only, for quick transitions). All gated `manage-deals`.

**Routes**:
```php
Route::get('deals', [DealController::class, 'index'])->name('deals.index');
Route::get('deals/create', [DealController::class, 'create'])->name('deals.create');
Route::post('deals', [DealController::class, 'store'])->name('deals.store');
Route::get('deals/{deal}/edit', [DealController::class, 'edit'])->name('deals.edit');
Route::put('deals/{deal}', [DealController::class, 'update'])->name('deals.update');
Route::patch('deals/{deal}/stage', [DealController::class, 'moveStage'])->name('deals.move-stage');
Route::delete('deals/{deal}', [DealController::class, 'destroy'])->name('deals.destroy');
```

**Frontend** `resources/js/Pages/Admin/Deals/{Index,Create,Edit}.vue` — copy `Contracts/{Index,Create,Edit}.vue` structure (stage badge + filter + follow-up-overdue amber highlight in Index; `DateInput` for follow-up date, plain inputs elsewhere in Create/Edit). Stage badge in Index doubles as a quick-move control (inline `<select @change>` → `router.patch` to `deals.move-stage`).

### D. Report Additions (#8, cheap items only)

**`ReportController.php`** — two new private methods:
- `expiringContracts()`: `Contract::expiringWithin(config('contracts.renewal_lookahead_days'))->with('member')->orderBy('end_date')->limit(10)->get()` → `[member, end_date, daysLeft]`. Reuses the existing `scopeExpiringWithin` on `Contract`.
- `contentVelocity()`: `ScheduleEvent::whereIn('type', ['content_filming','social_media_post'])->confirmed()->where('start_datetime','>=', now()->subMonths(6))` grouped by `TO_CHAR(start_datetime,'YYYY-MM')`, same gap-filling pattern as `conflictTrend()`/`revenueTrend()`.

**Frontend** `Reports/Index.vue`: "Expiring Contracts" card (list, name + days-left, amber if ≤14 days) and "Content Velocity" card (small `Bar` chart, trailing 6 months, reuses already-registered `BarElement`).

### RBAC additions (applies to all of A-D)

`RolePermissionSeeder.php`:
```php
Permission::create(['name' => 'approve-deliverables']); // Super Admin only
Permission::create(['name' => 'manage-deals']);
```
Super Admin: both (via `Permission::all()`). Marketing Department: add `manage-deals`. Events Department: add `manage-deals`. Artist: neither.

`HandleInertiaRequests.php` `auth.can`: add `'approve-deliverables'`, `'manage-deals'`.

`AdminLayout.vue`: new "Business Development" nav section (after "Compliance"), "Deals" `NavItem` gated `v-if="canManageDeals"`.

### Build sequence

A (traceability) — no dependencies. B (sign-off) — independent of A's data, can build in parallel. C (deals) — fully independent. D (reports) — reads A/B/C's underlying data but is the smallest piece; build last.

### Verification checklist

- `php -l` every new/modified PHP file.
- Run each new migration individually against the dev Postgres DB, confirm routes via `php artisan route:list --name=admin`.
- Seed new permissions/role grants via `php artisan tinker` (not a full seeder re-run — collides with already-seeded rows).
- Sanity-check new report queries directly in `tinker` against real data.
- `npm run build` (backend) to confirm all new/changed Vue files compile.
- `composer test` — expect the same one pre-existing unrelated failure, nothing new.
- Manual RBAC check: Artist sees no Deals nav item, gets 403 on `approve-deliverables` actions; Marketing/Events see Deals but not Contract Approve/Reject buttons.

---

## Phase 2: Revenue Splitting (#1) + Cost Tracking (#2)

**Status: architecture drafted, needs a dedicated pre-implementation review before build** — this phase carries the roadmap's highest financial-trust risk (#1) and is the first phase to introduce money-adjacent records outside the existing Billplz/fanclub billing scope. The draft below is a starting point for that review, not a green light.

### #1 Revenue Splitting

**`revenue_events`** — one row per money-in event (a concert payout, a merch drop, a brand deal payment):
```php
Schema::create('revenue_events', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->enum('source', ['concert', 'merch', 'streaming', 'brand_deal', 'other']);
    $table->date('event_date');
    $table->decimal('gross_amount', 10, 2);
    $table->string('currency', 3)->default('MYR');
    $table->foreignId('schedule_event_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
    $table->timestamp('voided_at')->nullable(); // corrections happen by voiding + re-entering, not editing
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```

**`revenue_splits`** — one row per recipient per revenue event:
```php
Schema::create('revenue_splits', function (Blueprint $table) {
    $table->id();
    $table->foreignId('revenue_event_id')->constrained()->cascadeOnDelete();
    $table->enum('recipient_type', ['member', 'group_pool', 'agency']);
    $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete(); // set when recipient_type = member
    $table->decimal('percentage', 5, 2)->nullable();
    $table->decimal('fixed_amount', 10, 2)->nullable();
    $table->decimal('calculated_amount', 10, 2)->nullable(); // snapshotted at finalize time, not recomputed later
    $table->enum('status', ['draft', 'finalized'])->default('draft');
    $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('finalized_at')->nullable();
    $table->timestamps();
});
```

**Immutability design** (the core risk-mitigation for #1): once a `revenue_splits` row's `status` flips to `finalized`, the controller must reject any `update()`/`destroy()` on it. A mistake is corrected by voiding the parent `revenue_events` row (`voided_at`) and creating a fresh one — never by editing a finalized split. This trades a small amount of UX friction for an audit trail that can't be quietly altered after the fact, which is the whole point of solving pain point #1 ("trust risk").

**Open design question for the pre-implementation review**: should the system hard-validate that percentages sum to ≤100% per revenue event? Rows can mix `percentage` and `fixed_amount` recipients (e.g. "agency takes a flat RM500, remainder split by percentage among members"), which makes a simple sum-to-100 rule not quite right — needs a concrete worked example from real payout scenarios before the validation logic is finalized.

### #2 Cost Tracking

**`cost_entries`** — polymorphic, mirrors the `documents`/`conflict_logs` pattern, attachable to `ScheduleEvent`, `KanbanCard`, or `Contract` (nullable for costs not tied to a specific work item):
```php
Schema::create('cost_entries', function (Blueprint $table) {
    $table->id();
    $table->nullableMorphs('costable'); // costable_type/costable_id, nullable for general costs
    $table->enum('category', ['venue', 'travel', 'production', 'marketing', 'other']);
    $table->decimal('amount', 10, 2);
    $table->date('incurred_date');
    $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();
    $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
    $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->foreignId('receipt_document_id')->nullable()->constrained('documents')->nullOnDelete();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```
Receipts reuse the existing `Document`/`DocumentStorageService` from Phase 0's work (a receipt is just a `Document` with `type='other'`, linked via `receipt_document_id`) rather than building a second upload pipeline.

**Submitter ≠ approver, enforced at controller level**: the `approve()` action must reject if `$request->user()->id === $costEntry->submitted_by`, regardless of permission — a staff member cannot rubber-stamp their own cost claim even if they hold the approval permission.

### RBAC (draft)
New permissions: `manage-revenue-splits` (Super Admin only — matches #1's trust-sensitivity), `submit-costs` (Marketing Dept, Events Dept, Super Admin — they're the ones incurring costs), `approve-costs` (Super Admin only), `view-financial-reports` (Super Admin only for v1; may extend to department heads once the reporting split in #8 is designed).

### Explicitly out of scope for Phase 2
Multi-currency conversion, tax computation, automatic payout/bank transfer, double-entry bookkeeping or a trial balance, and any export integration to an external accounting tool (per the confirmed "stay standalone" decision). This phase produces records for humans to act on, not a financial system that moves money.

### Dependencies
None on other phases. Can start independently, but should NOT start without the pre-implementation review resolving the percentage-validation question above.

---

## Phase 3: Central Sensitive Member Record (#5)

**Status: architecture drafted, BLOCKED on HR/legal sign-off before go-live.** Schema and backend can be built and tested against synthetic data in parallel with other phases, but must not be exposed with real member data until the retention-window question is resolved.

**`member_sensitive_records`** — 1:1 with `Member`:
```php
Schema::create('member_sensitive_records', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->unique()->constrained()->cascadeOnDelete();
    $table->json('sizing')->nullable(); // shirt/shoe/etc., structured but flexible
    $table->text('health_allergies')->nullable();   // encrypted cast
    $table->text('health_conditions')->nullable();  // encrypted cast
    $table->text('health_medications')->nullable(); // encrypted cast
    $table->string('passport_number')->nullable();  // encrypted cast
    $table->date('passport_expiry')->nullable();
    $table->string('visa_status')->nullable();
    $table->string('emergency_contact_name')->nullable();
    $table->string('emergency_contact_phone')->nullable();
    $table->string('emergency_contact_relationship')->nullable();
    $table->timestamp('consent_recorded_at')->nullable(); // PDPA consent checkpoint
    $table->timestamps();
});
```
`health_allergies`/`health_conditions`/`health_medications`/`passport_number` use Laravel's `encrypted` model cast (application-level encryption keyed off `APP_KEY`) — chosen over structured allergy/condition lookup tables per the "structured fields, not free text" decision being about usability (quick emergency lookup), not about needing relational querying across members' conditions.

**Dynamic access-grant table** (the confirmed "Super-Admin-configurable toggle" decision — a deliberate departure from this codebase's normal static-permission pattern):
```php
Schema::create('sensitive_data_access_grants', function (Blueprint $table) {
    $table->id();
    $table->string('role_name'); // matches spatie Role::name, e.g. 'Events Department'
    $table->enum('field_group', ['passport_visa', 'health', 'sizing_emergency']);
    $table->boolean('enabled')->default(false);
    $table->foreignId('toggled_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('toggled_at')->nullable();
    $table->timestamps();
    $table->unique(['role_name', 'field_group']);
});
```
Read-access check (in a policy or the controller): Super Admin always allowed; any other role must have a matching `sensitive_data_access_grants` row with `enabled=true` for the field group being requested. Super Admin manages this via a small settings page (toggle switches per department per field group) rather than the seeder.

**Read-access audit log** (append-only, mirrors `conflict_logs`):
```php
Schema::create('sensitive_data_access_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_sensitive_record_id')->constrained()->cascadeOnDelete();
    $table->foreignId('viewed_by')->constrained('users')->cascadeOnDelete();
    $table->string('field_group');
    $table->timestamp('viewed_at');
});
```
Written every time a non-Super-Admin user views a gated field group — this is what actually answers "who looked at this passport number and when," which is the real point of building this table rather than just relying on the access-grant toggle.

### Blocking items before go-live
1. **Retention window after member departure** — proposed default 12 months, not confirmed. Purge logic must not be built/scheduled until this is signed off.
2. **PDPA consent wording** — `consent_recorded_at` needs an actual consent flow/copy reviewed by whoever handles compliance, not an engineering placeholder.

### Dependencies
None technically — can build in parallel with Phase 2. Sequenced after Phase 2 in the roadmap mainly because Phase 2's immutability pattern (draft/finalized) is a useful reference for this phase's own audit-trail design, not because of a hard dependency.

---

## Phase 4: Royalty Reconciliation (#4) + Remaining Reports (#8)

**Status: architecture drafted.** Lower risk than Phases 2/3 — this is a reconciliation log for money already received, not money changing hands or new sensitive PII.

**`royalty_income_entries`**:
```php
Schema::create('royalty_income_entries', function (Blueprint $table) {
    $table->id();
    $table->enum('source', ['spotify', 'youtube', 'ppm', 'macp', 'rim', 'other']);
    $table->date('period_start');
    $table->date('period_end');
    $table->decimal('expected_amount', 10, 2)->nullable();
    $table->decimal('received_amount', 10, 2)->nullable();
    $table->date('received_date')->nullable();
    $table->enum('reconciliation_status', ['pending', 'matched', 'discrepancy'])->default('pending');
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();
});
```
Manual entry only, by design: staff transcribe what a royalty statement (Spotify for Artists, YouTube Analytics, PPM/MACP member portals) says, then mark `matched` once the bank deposit confirms it, or `discrepancy` if it doesn't. **No API integration to any of these platforms is planned** — PPM/MACP in particular are unlikely to expose usable public APIs, so manual entry is treated as the realistic long-term state for this data, not a v1 shortcut to be automated later.

**Remaining `ReportController` additions** (the #8 items that depend on Phase 2's data existing):
- Earnings trend: `revenue_splits` (status=finalized) + `royalty_income_entries` (matched), summed per month.
- Commission totals: `revenue_splits` filtered to `recipient_type IN ('group_pool','agency')`, summed per period.
- Both gated behind `view-financial-reports` (defined in Phase 2), not the general `view-reports` permission — a non-finance department head seeing schedule/conflict reports should not automatically see revenue splits.

### Dependencies
Royalty reconciliation (#4) itself has no dependency and could move earlier in the roadmap if desired. The report additions specifically require Phase 2's `revenue_splits` table to exist — that's the only reason this is sequenced after Phase 2/3 rather than alongside Phase 1.

---

## Phase 5: PPM/MACP Status Tracking + Visa/Permit Tracker (#9)

**Status: architecture drafted, confirmed in-scope** (4+ international gigs/year, members hold both composer and performer credits).

**Visa/permit tracker** — per-member, per-event, since different members touring together may need different visas depending on nationality:
```php
Schema::create('travel_requirements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('schedule_event_id')->constrained()->cascadeOnDelete();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->enum('requirement_type', ['visa', 'work_permit', 'vaccination', 'other']);
    $table->enum('status', ['not_started', 'in_progress', 'submitted', 'approved', 'denied'])->default('not_started');
    $table->date('due_date')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```
Surfaced as a checklist on the relevant `ScheduleEvent`'s edit page for international engagements, plus a "Travel Requirements" section on the Reports page showing anything overdue/not-started within N days of the event.

**Royalty society registration status** (#9a — separate from Phase 4's income reconciliation; this tracks *whether a member is registered*, not money):
```php
Schema::create('royalty_registrations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained()->cascadeOnDelete();
    $table->enum('society', ['macp', 'rpm']);
    $table->enum('registration_status', ['not_registered', 'pending', 'active'])->default('not_registered');
    $table->date('registered_at')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
});
```
Both MACP (composer/songwriter rights — relevant to members who co-write) and RPM (performer rights — relevant to all members) get tracked per the confirmed decision that both apply. **This table never calculates or stores royalty amounts** — it exists purely so staff can see at a glance which members are actually registered with which society, since an unregistered member can't collect royalties they're legally owed regardless of what the system's income reconciliation (Phase 4) shows.

### Explicitly out of scope
Any actual filing, submission, or interaction with PPM/MACP/immigration systems — this phase tracks status for internal visibility only. The filing/compliance work itself remains a legal/administrative process outside the system, per the original scoping distinction between "track status in the system" and "the actual filing/compliance work."

### Dependencies
None — fully independent of other phases. Could be built any time after Phase 1.
