# Security Decision Tree

When implementing a feature:

1. Does it modify data?
   → Use FormRequest + Policy + Service

2. Does it affect multiple tables?
   → Use DB::transaction

3. Does it involve file uploads?
   → Validate mime, size, storage path, authorization

4. Does it expose data?
   → Verify ownership and permissions

5. Does it trigger another module?
   → Use Event/Listener pattern

6. Is it security sensitive?
   → Add audit logging


# Security Architecture

## Authentication
- Guards
  - Primary guard is web session guard (config/auth.php, defaults.guard=web).
  - User provider points to App\Models\User.
  - API endpoint /api/user uses auth:sanctum.
- Authentication flow
  - Login uses LoginRequest -> authenticate().
  - On success:
    - user active-state check (inactive users are logged out)
    - session regenerate()
    - role-based redirect (admin/manager/driver/client)
    - login history recording (success/failed attempts)
  - Logout invalidates session and regenerates CSRF token.
- Session handling
  - SESSION_DRIVER defaults to database.
  - Cookies are secure by default (secure=true, http_only=true, same_site=lax).
  - Other-device session termination is supported via Auth::logoutOtherDevices().
- Email verification
  - User model implements MustVerifyEmail.
  - Role portals commonly enforce verified middleware.
  - Verification routes use signed + throttle middleware.
- Password reset
  - Standard Laravel reset flow is enabled (forgot-password, reset-password routes).
  - Reset token table password_reset_tokens, expiry/throttle are configured.

## Authorization
- Middleware
  - auth / auth:sanctum for identity.
  - role middleware for role gating by route group.
  - verified and throttle on sensitive/high-traffic routes.
- Roles
  - Role strings: admin, manager, driver, client.
  - RedirectIfAuthenticated and menus are role-driven.
- Permissions
  - Policy abilities plus explicit abort/abort_if guards for invariant checks.
- Policies
  - Gate::policy bindings in AuthServiceProvider are canonical.
  - Critical: effective Trip policy is App\Modules\Driver\Policies\TripPolicy.
- Ownership checks
  - Manager policies enforce ownership (created_by / approved_by lineage).
  - Driver trip operations enforce assignment ownership (trip.driver_id == authenticated driver).

## Input Validation
- FormRequest usage
  - Primary validation pattern is module FormRequest classes.
  - Controllers may still use inline validate() for small updates/uploads.
- Sanitization patterns
  - prepareForValidation() commonly trims and strip_tags string input.
  - Additional temporal/domain checks often use withValidator()/after().
- File upload validation
  - Repeated strict rules: image/file + mimes + max size.
  - Example allowed image types in requests: jpg, jpeg, png, webp (and svg for company logo endpoint).

## Database Security
- Mass assignment protection
  - Models use fillable arrays.
  - Sensitive attributes hidden for serialization (for example password, remember_token).
- Transactions
  - DB::transaction is used for critical multi-write workflows (trip/finance/spare flows).
- Query practices
  - Eloquent query builder with parameterized conditions (no raw user SQL composition pattern).
  - Repository pattern centralizes many read/write flows.
- Sensitive data handling
  - Password is hashed (cast + custom password attribute mapping to password_hash).
  - Exception handler dontFlash prevents password fields from being flashed.
  - Do not return secret fields in API payloads.

## UI Security
- CSRF protection
  - Blade meta csrf-token used in fetch requests.
  - Web forms rely on Laravel CSRF middleware.
- XSS prevention
  - Blade escaped output is default and should remain default.
  - Existing code sanitizes many inbound strings before persistence.
- Blade escaping rules
  - Use {{ }} by default.
  - Avoid unescaped output unless explicitly trusted and sanitized.

## API Security
- Authentication methods
  - auth:sanctum for API identity where needed.
  - Stateful API support enabled.
- Rate limiting
  - Route-level throttle used broadly.
  - Dedicated limiter trip-location: 240 requests/min keyed by user/ip.
- Request validation
  - API endpoints use FormRequest with strict numeric/range/date constraints.
  - Driver location endpoint includes assignment check + policy check + token device check.

## File Upload Security
- Allowed mime types
  - Common image mimes: jpg, jpeg, png, webp; logo endpoint also allows svg.
- Storage strategy
  - Files stored on public disk under scoped folders (avatars, company/logos, company/signatures).
  - Prior file removed before replacing to limit stale sensitive artifacts.
  - UUID-based filenames are used in admin profile uploads.
- Access control
  - Upload endpoints are role-protected and/or explicit role-checked in controller methods.
  - Upload requests are validated before storage.

## Logging and Monitoring
- Security-related logs
  - Login success/failure attempts are recorded in login_histories.
  - Sensitive operation failures log structured warnings/errors.
- Audit trail patterns
  - AuditLogService records user/action/table/record old/new values + IP + user agent.
  - Profile/security-sensitive changes are audit-logged (password change, avatar/company updates, session termination).

## AI Security Rules (Mandatory)
1. Never trust user input.
2. Always validate through FormRequest.
3. Always authorize actions through policies or middleware.
4. Never bypass ownership checks.
5. Never expose sensitive data in responses.
6. Escape output by default.
7. Use transactions for critical operations.
8. Follow least-privilege principles.
9. Reuse existing security patterns.
10. Flag security concerns before implementation.

## Enforcement Notes For Future Changes
- Default to auth + role middleware at route level, then policy in controller/service.
- Prefer FormRequest over inline validation; if inline validate() is used, keep scope minimal and migrate later.
- For API/device flows, keep both identity and context checks (owner assignment + token/device binding).
- Do not introduce new upload types or public file paths without explicit validation and access review.
- Preserve audit logging for security-relevant state changes.


# Pre-Implementation Security Checklist
Before generating code:

- [ ] FormRequest exists
- [ ] Policy exists
- [ ] Route middleware exists
- [ ] Ownership verified
- [ ] Input sanitized
- [ ] Output escaped
- [ ] Transaction required?
- [ ] Audit log required?
- [ ] File upload secured?
- [ ] Rate limit required?