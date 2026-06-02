# AGENTS.md

This file defines how all AI agents must operate in this repository.

These instructions are mandatory and take precedence over default assumptions.

---

# 1. Primary Objective

Your goal is to make correct, consistent, and minimal changes while preserving the existing architecture.

Do not redesign the application.

Do not introduce new patterns when existing patterns already solve the problem.

Always prefer understanding and extending existing implementations over creating new abstractions.

---

# 2. Mandatory Context Loading Order

Before reading source code outside of `ai-context`, load context in this order:

1. `ai-context/project-map.md`
2. `ai-context/feature-map.md`
3. `ai-context/patterns.md`
4. `ai-context/security.md`
5. `ai-context/conventions.md`
6. `ai-context/business-rules.md`
7. `ai-context/module-map.md` (only if additional module detail is required)

## Rules

- Never start implementation before loading context.
- Prefer targeted exploration over repository-wide scans.
- Read only the minimum amount of code required.
- Explore related modules only when integration requires it.

---

# 3. Required Task Planning Workflow

Before making changes:

1. Identify the owning feature from `feature-map.md`
2. Identify the owning module
3. Identify affected files
4. Identify validation requirements
5. Identify authorization requirements
6. Identify business rules
7. Identify security implications
8. Identify possible side effects
9. Create an implementation plan
10. Then modify code

Do not skip this process.

---

# 4. Development Workflow

For every task:

## Step 1 — Understand

- Determine feature ownership
- Determine module ownership
- Determine dependencies

## Step 2 — Reuse

Search for:

- Similar features
- Existing services
- Existing repositories
- Existing actions
- Existing DTOs
- Existing policies
- Existing components

Prefer extension over duplication.

## Step 3 — Implement

Apply the smallest possible change.

Respect:

- Existing architecture
- Existing naming conventions
- Existing folder structure
- Existing security controls

## Step 4 — Verify

Validate:

- Authorization
- Validation
- Business rules
- Side effects
- Existing workflows

## Step 5 — Maintain Context

Update context files if project knowledge has changed.

---

# 5. Architecture Constraints

The following architectural rules are mandatory.

## Module Structure

Preserve module-first architecture:

```text
app/Modules/{Module}
```

Do not move features across modules without explicit instruction.

## Layer Responsibilities

Preferred flow:

```text
Controller
    ↓
FormRequest
    ↓
Service / Action
    ↓
Repository
    ↓
Model
```

### Rules

- Controllers remain thin
- Business logic belongs in Services or Actions
- Repositories handle data access concerns
- Models represent domain entities
- Policies handle authorization

## Existing Architecture First

Before creating new:

- Service
- Repository
- Action
- DTO
- Policy
- Event
- Listener
- Job
- Component

Search for an existing implementation first.

Reuse before creating.

---

# 6. Security Requirements

Security is mandatory.

For all generated code:

1. Never trust user input
2. Validate all user input
3. Authorize all protected actions
4. Never bypass ownership checks
5. Never expose sensitive information
6. Escape Blade output by default
7. Use least-privilege access
8. Preserve auditability
9. Follow existing security patterns
10. Flag security concerns before implementation

## Validation

Prefer:

- FormRequest classes

Avoid:

- Large inline validation blocks

## Authorization

Prefer:

- Middleware
- Policies
- Ownership checks

Never rely solely on UI restrictions.

## Database Operations

Use transactions when:

- Multiple writes occur
- Financial records are affected
- Status transitions affect related entities

## File Uploads

Always validate:

- MIME type
- File size
- Storage location
- Access control

## API Security

When modifying APIs:

- Enforce authentication
- Enforce authorization
- Validate requests
- Apply rate limits where appropriate

## Audit Logging

Preserve existing audit patterns when modifying:

- Financial workflows
- User management
- Security settings
- Status transitions

---

# 7. Performance Rules

Avoid performance regressions.

Required practices:

- Prevent N+1 queries
- Use eager loading where appropriate
- Paginate large datasets
- Load only required relationships
- Reuse repository query patterns
- Queue expensive background work

Do not trade maintainability for micro-optimizations.

---

# 8. UI Consistency Rules

Maintain the existing UI system.

Always:

- Reuse existing layouts
- Reuse existing Blade components
- Reuse existing Alpine.js patterns
- Follow existing Tailwind conventions
- Follow existing dark-theme design patterns

Do not introduce a new design system.

Do not create duplicate UI components when reusable components exist.

---

# 9. Context Maintenance Rules

The files inside `ai-context` are operational memory for future AI agents.

Update them whenever:

- Feature ownership changes
- Module responsibilities change
- Business rules change
- Security controls change
- Architecture patterns change
- Navigation paths change

## Context files should contain

- Stable knowledge
- Navigation information
- Decision-making guidance

## Context files should NOT contain

- Full source code
- Large code snippets
- Duplicate documentation
- Temporary implementation details

Keep context concise and AI-readable.

---

# 10. Anti-Duplication Rule

Before creating:

- Service
- Repository
- Action
- DTO
- Policy
- Component
- View
- Event
- Listener
- Job

Search for an existing implementation first.

Prefer extending existing code over creating parallel implementations.

---

# 11. Decision Priority

When selecting an implementation approach, use this order:

1. Existing feature pattern
2. Existing module pattern
3. Project patterns (`patterns.md`)
4. Project conventions (`conventions.md`)
5. Laravel conventions
6. New pattern (last resort)

Any deviation must be justified.

---

# 12. Definition of Done

A task is complete only when:

- Requested functionality is implemented
- Validation is correct
- Authorization is correct
- Business rules are preserved
- Security requirements are preserved
- Performance impact is acceptable
- Existing architecture is respected
- Context files are updated if project knowledge changed

Completion is not defined by code generation alone.

The change must remain consistent with the project's architecture, security model, and established implementation patterns.