# Security assessment — Glow Recruitment

Reviewed: application code, routes, middleware, policies, file storage, authentication,
session handling, asset pipeline and the deployment defaults in this repository.

This document describes the platform **after** the hardening pass in this change set.
Each finding lists the severity it had before the fix, unless marked as remaining.

---

## 1. What was fixed

### Critical

| Issue | Resolution |
| --- | --- |
| Third-party scripts loaded from CDNs (`cdn.tailwindcss.com`, jsDelivr Alpine) with no CSP. A CDN compromise or tampered package would execute with full session access. | Tailwind is compiled through Vite; Alpine was replaced with a small first-party script. A strict `Content-Security-Policy` now blocks all third-party script execution. |
| Broken CV upload: the controller wrote `original_filename` / `size` while the table uses `file_name` / `file_size`. Uploads failed, and any future retry could surface SQL errors. | Controller, model and views now agree on the schema; a regression test uploads a file and asserts private storage. |
| Withdrawal flow called a non-existent `delete` ability on a policy without it, so every "Withdraw" action errored. | `ApplicationPolicy::withdraw()` is now used, with an ownership + status check. |
| Core screens linked from the navigation returned 500s (missing views, resource routes for non-existent controller methods). Error pages in production can leak stack traces and paths. | All portal pages exist and every sidebar link is covered by a rendering test. Resource routes were replaced with explicit routes. |

### High

| Issue | Resolution |
| --- | --- |
| No CSRF-safe, CSP-safe inline handlers and no cache protection for authenticated HTML. | All inline handlers removed; authenticated responses send `Cache-Control: no-store, private` plus `Pragma: no-cache`. |
| Email verification was implemented on the model (`MustVerifyEmail`) and enforced by `verified` middleware, but the Fortify email-verification feature was commented out. The `verification.notice` route did not exist, so unverified users hit an error. | Email verification is enabled with views for notice, reset password, confirmation and 2FA challenge. New registrations are `unverified` until they click the link. |
| Login endpoint had no throttling (only Fortify's unused limiter existed). | `throttle:login` uses the Fortify limiter (5/min per email+IP). Registration and sensitive actions are rate limited too. |
| Suspended accounts were only blocked on role-protected routes. An authenticated suspended session could still hit shared pages. | `EnsureAccountIsActive` runs in the web group: any suspended user is logged out, session invalidated and redirected with an error. |
| Weak password policy (Laravel default of 8 characters). | Global policy: minimum 12 characters, mixed case and numbers. Registration cannot select the `admin` role. |
| No audit trail, despite an `audit_logs` table and model. | Auth events, job moderation, suspensions, verification decisions, subscription activation, application status changes and CV downloads are recorded and visible to administrators. Administrators cannot suspend other administrators or themselves. |
| Private employer verification documents had no review workflow. | Uploads are stored on the private disk, reviewed by admins only, and downloaded through an authenticated admin route. |
| `POST /api/user` returned the full user model (phone, suspension data, role). | Returns only `id`, `name`, `email` and is throttled at 60/min. |

### Medium

| Issue | Resolution |
| --- | --- |
| Job postings accepted inverted salary ranges, past deadlines, huge text and arbitrary currency values. | Validation now enforces ranges, bounds and lengths; `salary_max >= salary_min`. |
| Editing an approved listing silently changed live content with no review. | Approved listings return to `pending` on edit and disappear from public search until re-approved. |
| Free subscription plans could not be activated, so verified employers were permanently blocked by `JobPolicy`. Paid plans could be activated by a crafted request if such a route existed. | `SubscriptionController@activate` only allows active, free plans matching the user's role. Paid plans fail closed. |
| Saved-jobs endpoint was a route closure with no visibility checks and no throttling. | A controller rejects non-public/expired jobs and throttles saves. |
| Mass assignment silently discarded unknown attributes. | `Model::preventSilentlyDiscardingAttributes()` in non-production; models now match their schema. |
| The seeder file was syntactically invalid, so demo accounts could not be created. | Seeders fixed and extended (users, categories, plans, demo jobs) with verified demo accounts. |

### Headers now sent

`Content-Security-Policy` (no third-party scripts, no `object-src`, `frame-ancestors 'self'`,
`base-uri 'self'`, `form-action 'self'`), `X-Content-Type-Options: nosniff`,
`X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`,
`Permissions-Policy`, `Cross-Origin-Opener-Policy: same-origin`,
`X-Permitted-Cross-Domain-Policies: none`, and HSTS in production.
`X-XSS-Protection` was removed because modern browsers treat it as a liability.

---

## 2. Remaining risks (recommended next)

1. **Payments are not implemented (critical before launch).** The Paystack controllers are stubs, so paid plans fail closed and no money can be taken — safe, but the subscription product is incomplete. When implementing: verify the `x-paystack-signature` HMAC (SHA-512 with the secret key) against the raw body, re-fetch the transaction server-side, ignore client-supplied amounts, process idempotently by reference, and make the webhook route CSRF-exempt but signature-verified.
2. **Two-factor authentication is supported by Fortify but has no enrolment UI**, and `CheckTwoFactor` is a no-op. Enrolment (QR + recovery codes) plus mandatory 2FA for administrators should be the next security milestone; the challenge view already exists.
3. **Production environment values.** Set `APP_DEBUG=false`, `APP_ENV=production`, `APP_URL=https://…`, `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, a real mail provider (verification/reset emails currently log locally), and trusted proxies if behind a load balancer. A leaked `APP_KEY` invalidates encryption, sessions and signed URLs.
4. **Database.** Local development uses SQLite. Production should use managed MySQL/PostgreSQL with a least-privileged application user, encrypted backups, point-in-time recovery and restricted network access.
5. **Upload handling.** MIME checks use extension guessing. Add content sniffing, malware scanning for CVs/verification documents, size quotas per account, and a retention policy. Files are private today and only streamed through authorised controllers — keep it that way; never serve them from `public/`.
6. **Audit integrity.** Database audit rows can be altered by a compromised app account. Ship logs to an append-only store and alert on privilege changes, repeated failed logins and moderation spikes.
7. **Re-authentication for destructive admin actions.** Consider `password.confirm` on suspension, verification approval and job rejection. The confirmation view/route now exist.
8. **Account security features.** Login/security notifications, session management ("sign out other devices"), breached-password checks (`Password::uncompromised()`), and optional passkeys (Fortify is installed).
9. **Edge protection.** Add a WAF/CDN, request size limits and rate limits at the proxy. Application throttling is not a substitute for DDoS protection.
10. **CI security gates.** Run tests, `composer audit`, `npm audit`, `vendor/bin/pint --test` and a static analyser (PHPStan/Larastan) on every pull request. Dependencies are pinned only by `composer.lock`; there is no frontend lockfile yet (`npm install` will generate one — commit it).
11. **Role model.** Roles are a single string column; Spatie Permission is installed but unused. If staff roles grow (moderator, support), migrate to permissions rather than adding more string comparisons.
12. **Unused scaffolding.** `app/UserRole.php`, `app/JobStatus.php`, `app/ApplicationStatus.php` duplicate the `App\Enums` classes. Delete them to avoid divergent role/status logic.

---

## 3. Controls verified by tests

`php artisan test` covers:

- Role isolation across job seeker, employer and admin portals.
- Suspended users are logged out and cannot sign in.
- Unverified users are redirected to the verification notice; new accounts start unverified.
- Login throttling after repeated failures; registration rejects weak passwords and the admin role.
- Cross-employer job/application access and cross-seeker application/CV access return 403.
- Private CV download is owner-only; employer CV access requires an application to their job.
- CV upload persists metadata and stores files privately.
- Free-plan activation, role mismatch rejection and paid-plan rejection.
- Job posting is gated by an active subscription; invalid salary ranges are rejected; edits return listings to moderation.
- Audit records for approvals, suspensions and subscriptions; admins cannot suspend admins.
- Security headers, CSP and no-store caching for authenticated pages.
- Every page linked from the sidebar renders for its role.

Run locally:

```bash
php artisan test
vendor/bin/pint --test
npm run build
```

---

## 4. Operational checklist before production

- [ ] `APP_DEBUG=false`, `APP_ENV=production`, HTTPS only, HSTS confirmed.
- [ ] `php artisan config:cache route:cache view:cache` in the deploy pipeline.
- [ ] Mail provider configured; verification and reset flows tested end to end.
- [ ] Database backed up and restore-tested; migrations run with `--force`.
- [ ] Queue worker running (verification emails, future webhooks) and monitored.
- [ ] Log retention and alerting configured; audit log shipped off-box.
- [ ] Backups and secrets excluded from the repository and build artifacts.
- [ ] Dependency audit and test suite required in CI.
