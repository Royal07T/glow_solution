# Glow Recruitment Agency Solution

Glow Recruitment is a subscription-based recruitment marketplace for domestic and professional employment in Nigeria. It connects job seekers with verified employers and gives administrators the tools to moderate listings, verify employers, manage users, and oversee applications.

The application is built as a server-rendered Laravel monolith with Blade templates, Tailwind CSS, Alpine.js, and Paystack payments.

---

## Table of Contents

- [Highlights](#highlights)
- [Roles and Capabilities](#roles-and-capabilities)
- [Core Workflows](#core-workflows)
- [Payments and Subscriptions](#payments-and-subscriptions)
- [Location Data](#location-data)
- [Notifications and Audit Trail](#notifications-and-audit-trail)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Installation](#installation)
- [Database and Seeding](#database-and-seeding)
- [Environment Variables](#environment-variables)
- [Paystack Setup](#paystack-setup)
- [File Storage](#file-storage)
- [Security and Authorization](#security-and-authorization)
- [Testing and Code Quality](#testing-and-code-quality)
- [Development Commands](#development-commands)
- [Deployment Checklist](#deployment-checklist)
- [Operational Notes](#operational-notes)
- [License](#license)

---

## Highlights

- Public marketing homepage with featured jobs, categories, company contact details, map, WhatsApp link, and responsive navigation.
- Three account types: **job seeker**, **employer**, and **administrator**.
- Job discovery with keyword, category, job type, employment type, and location filters.
- Employer job posting with administrator moderation before publication.
- Administrator job posting that publishes immediately under the agency profile.
- Applicant review for employers and administrators, including CV download and application status updates.
- Private CV storage with policy-based access control.
- Free and paid subscription plans with configurable entitlements.
- Paystack redirect checkout, server-side payment verification, signed webhooks, and idempotent subscription activation.
- Nigerian state and LGA data for profile and job location selects.
- In-app database notifications, audit logs, account security settings, two-factor authentication, and passkey support.
- Security headers, Content Security Policy, role middleware, form requests, policies, and rate limiting on sensitive actions.

---

## Roles and Capabilities

### Job Seeker

- Register and verify an email address.
- Maintain a job seeker profile with location, experience, education, availability, and other details.
- Upload and manage CVs/resumes. Files are stored privately.
- Browse approved, published, non-expired jobs.
- Search and filter jobs by keyword, category, type, employment type, and location.
- Save and unsave jobs.
- Apply with an optional CV and cover letter.
- Prevent duplicate applications to the same job.
- Track applications by status and view application details.
- Withdraw eligible applications.
- View interview-stage applications.
- Receive in-app notifications when application statuses change.
- Choose a free or paid job seeker plan.

### Employer

- Register a household or business employer account.
- Maintain a company/household profile and submit verification documents.
- Access job posting only after email verification, employer verification, and an active subscription.
- Create, edit, and delete job listings.
- Submit jobs for administrator moderation.
- Manage applicants for their own jobs.
- View candidate profiles, cover letters, and CVs attached to applications.
- Update application status and add notes to candidates.
- Receive in-app notifications for new applications and withdrawals.
- Manage subscription and billing.

Edits to an approved employer job reset it to **pending** and clear its publication timestamp so it is moderated again.

### Administrator

- Access a platform dashboard with user, job, application, verification, revenue, and audit summaries.
- Create, update, suspend, and unsuspend user accounts.
- Review employer verification requests and approve or reject them.
- Moderate employer-submitted jobs: approve or reject with notes.
- Post agency jobs directly. These are published immediately under an automatically created **Glow Recruitment Agency** employer profile owned by the administrator.
- View applicants on any job listing, including admin-posted jobs.
- Review candidate details, download attached CVs, and update application status.
- Manage subscription plans, pricing, entitlements, and active status.
- Inspect audit logs for sensitive platform activity.
- Access a dedicated applicants inbox with job and status filters.

---

## Core Workflows

### Job Lifecycle

1. An employer creates a job. The job enters the `pending` state.
2. An administrator reviews it and either approves or rejects it.
3. Approval sets `approved`, records `published_at`, and makes the job publicly visible.
4. Rejection stores the moderator's notes so the employer can correct and resubmit.
5. Job listings can move to `closed` or `expired`; expired or unapproved jobs are not publicly viewable.
6. Administrator-created agency jobs are approved and published immediately.

### Application Lifecycle

Supported application statuses:

- `submitted`
- `under_review`
- `shortlisted`
- `interview`
- `accepted`
- `rejected`
- `withdrawn`

Job seekers can withdraw only eligible applications. Employers and administrators can move applications through the review pipeline. Status changes notify the candidate in-app.

### Employer Verification

Employers submit verification information and documents. Administrators review the submission and approve or reject the employer. Approved employers can proceed to job posting once they also have an active subscription.

### Admin Applicant Review

Administrators can access applicants in two ways:

1. Open an admin job listing and review the paginated applicant section.
2. Use **Admin → Recruitment → Applicants** to filter all applications by job and status.

The admin application page supports viewing candidate details, cover letters, attached CVs, and updating application status.

---

## Payments and Subscriptions

Subscription plans target either job seekers or employers and define entitlements such as active job listings or application limits.

- Free plans are activated directly.
- Paid plans use Paystack hosted checkout.
- The application creates a pending `Payment` before redirecting.
- Paystack amounts are sent in the currency subunit (kobo for NGN).
- The callback verifies the transaction server-side before activating the subscription.
- The webhook verifies the `x-paystack-signature` header using HMAC SHA512 and the configured secret key.
- Payment completion is idempotent and protected with a database transaction and row lock.
- Amount and currency are checked before a payment is marked successful.
- Successful payments activate the user's subscription and create an audit log entry.
- Payment history is shown on the billing page.

**Important:** the Paystack merchant account must have at least one active payment channel enabled in the Paystack dashboard. Otherwise transaction initialization fails with `No active channel to process transaction. Please contact merchant`.

---

## Location Data

The application includes Nigerian location data for dependent state and LGA/city selects.

- **37 states** (including the Federal Capital Territory).
- **737 LGAs**.
- Data is stored in the `states` and `lgas` tables and loaded by `StateLgaSeeder`.
- The public endpoint `GET /locations/lgas?state={id}` powers the dependent city select.
- State and city values are stored as strings on job seeker profiles, employer profiles, and jobs.
- Legacy free-text values remain available as fallback options when editing existing records.

Run the location seeder with:

```bash
php artisan db:seed --class=StateLgaSeeder
```

The seeder is idempotent and safe to run again.

---

## Notifications and Audit Trail

Notifications are stored in the database and displayed through the notification bell and notifications page.

Examples include:

- New application received.
- Application withdrawn.
- Application status updated.
- Job approved or rejected.
- Subscription activated.
- Payment successful.

Administrators can inspect audit log entries for sensitive actions such as job moderation, user changes, verification decisions, subscription activation, and payment success.

---

## Tech Stack

| Area | Technology |
| --- | --- |
| Backend | Laravel 13, PHP 8.3+ |
| Authentication | Laravel Fortify, custom login/registration controllers |
| Authorization | Policies, gates, custom role middleware |
| Frontend | Blade, Tailwind CSS 4, Alpine.js 3 |
| Build tool | Vite 8 |
| Database | MySQL (recommended for local/runtime) or SQLite |
| Payments | Paystack |
| Permissions | Spatie Laravel Permission |
| Testing | PHPUnit 12 |
| Formatting | Laravel Pint |

---

## Project Structure

```text
app/
  Enums/                 Job, application, user-role enums
  Http/Controllers/      Public, job seeker, employer, admin controllers
  Http/Middleware/       Role, verification, subscription, security middleware
  Models/                Eloquent models
  Notifications/         Database notifications
  Policies/              Application, CV, and job authorization
  Services/              Paystack integration
database/
  migrations/            Schema
  seeders/               Location, categories, plans, and optional demo data
resources/
  css/                   Tailwind entry point
  js/                    Frontend interactions
  views/                 Blade layouts and pages
routes/
  web.php                Public and authenticated application routes
tests/
  Feature/               Role, workflow, payment, security, and page tests
```

---

## Requirements

- PHP **8.3+**
- Composer
- Node.js **20+** and npm
- MySQL 8+ or SQLite
- A Paystack account for paid subscriptions

Check your installed versions:

```bash
php -v
composer -V
node -v
npm -v
```

---

## Installation

```bash
git clone https://github.com/Royal07T/glow_solution.git
cd glow_solution

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure the database in `.env`, then run:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run build
```

Start the application:

```bash
php artisan serve
```

For frontend development with Vite hot reload, run this in a second terminal:

```bash
npm run dev
```

---

## Database and Seeding

### MySQL

Create a database and update `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=glow_recruitment
DB_USERNAME=root
DB_PASSWORD=
```

Then run:

```bash
php artisan migrate
```

### SQLite

Create the database file:

```bash
touch database/database.sqlite
```

Update `.env`:

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

Then run:

```bash
php artisan migrate
```

### Seeders

`DatabaseSeeder` currently loads the Nigerian state and LGA data:

```bash
php artisan db:seed
# or
php artisan migrate --seed
```

For a complete local environment, seed categories and subscription plans:

```bash
php artisan db:seed --class=JobCategorySeeder
php artisan db:seed --class=SubscriptionPlanSeeder
```

Optional demo users and jobs:

```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=DemoDataSeeder
```

Run `UserSeeder` before `DemoDataSeeder`. Demo accounts use the password `password` and must never be used in production.

---

## Environment Variables

Key application values:

| Variable | Purpose |
| --- | --- |
| `APP_NAME` | Application name |
| `APP_ENV` | Usually `local` or `production` |
| `APP_DEBUG` | Must be `false` in production |
| `APP_URL` | Public application URL; important for Paystack callbacks |
| `DB_CONNECTION` | `mysql` or `sqlite` |
| `DB_DATABASE` | Database name or SQLite file path |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |
| `SESSION_DRIVER` | Session storage, database by default |
| `QUEUE_CONNECTION` | Queue backend |
| `FILESYSTEM_DISK` | Default filesystem disk |
| `PAYSTACK_PUBLIC_KEY` | Paystack public key |
| `PAYSTACK_SECRET_KEY` | Paystack secret key; server-side only |
| `PAYSTACK_PAYMENT_URL` | Paystack API base URL |
| `PAYSTACK_MERCHANT_EMAIL` | Merchant/fallback contact email |
| `COMPANY_ADDRESS` | Company address shown on the public site |
| `COMPANY_PHONE` | Company phone number |
| `COMPANY_MAP` | Map coordinates |
| `COMPANY_MAP_LINK` | Google Maps link |

After changing environment values in a cached deployment:

```bash
php artisan config:clear
php artisan config:cache
```

---

## Paystack Setup

1. Create or open your Paystack account.
2. Enable at least one payment channel for the environment you are testing. Card is recommended for local checkout testing.
3. Copy the test or live API keys into `.env`.
4. Set `APP_URL` to the public HTTPS application URL.
5. Add the webhook URL in the Paystack dashboard:

```text
https://your-domain.com/payments/paystack/webhook
```

6. Ensure the webhook is configured for the same mode as the keys in use (test keys with the test-mode webhook, live keys with the live webhook).
7. Rebuild configuration after changing keys:

```bash
php artisan config:clear
```

The webhook route is excluded from CSRF protection because Paystack sends a server-to-server POST request. Signature verification and amount/currency checks still apply.

For local HTTPS testing, expose the app with a tunnel such as ngrok and set `APP_URL` to the tunnel URL. The application trusts forwarded proxy headers so generated asset and callback URLs use HTTPS.

---

## File Storage

CVs and other private uploads are stored under the private disk:

```text
storage/app/private/cvs/
```

Access is controlled by `CvPolicy`:

- The CV owner can download it.
- An administrator can download it.
- An employer can download it only when it is attached to an application for one of their jobs.

The generic authorized download route is:

```text
GET /cvs/{cv}/download
```

Ensure the web server can write to `storage/` and `bootstrap/cache/`.

---

## Security and Authorization

- Server-side validation for job, profile, application, payment, and account input.
- Role-based route middleware for job seekers, employers, and administrators.
- Laravel policies for jobs, applications, and CVs.
- Email verification for authenticated application areas.
- Employer verification and active subscription checks for employer job management.
- Private CV storage rather than public uploads.
- CSRF protection across web forms, with an explicit exception only for the Paystack webhook.
- Paystack webhook HMAC SHA512 signature validation.
- Payment amount and currency verification before subscription activation.
- Idempotent payment completion and database locks to prevent duplicate activation.
- Content Security Policy and standard security headers.
- `no-store` cache headers for authenticated responses.
- Rate limiting on sensitive authentication actions.
- Two-factor authentication and passkeys through Fortify.
- Account suspension support.

Secrets belong only in `.env`. Never commit `.env`, Paystack secret keys, or private uploads.

---

## Testing and Code Quality

The test suite uses SQLite in memory and covers authentication, roles, jobs, applications, payments, security, locations, notifications, and admin workflows.

Run all tests:

```bash
php artisan test
```

Run a specific area:

```bash
php artisan test tests/Feature/Payments
php artisan test tests/Feature/Admin
```

Format changed PHP files:

```bash
vendor/bin/pint --dirty
```

Build production assets:

```bash
npm run build
```

Current suite status at the time of writing: **97 tests, 395 assertions passing**.

---

## Development Commands

| Command | Purpose |
| --- | --- |
| `php artisan serve` | Start the Laravel development server |
| `npm run dev` | Start Vite in watch mode |
| `npm run build` | Build production assets |
| `php artisan migrate` | Run migrations |
| `php artisan db:seed` | Run the default seeder |
| `php artisan test` | Run the test suite |
| `vendor/bin/pint --dirty` | Format changed PHP files |
| `php artisan route:list` | Inspect registered routes |
| `php artisan config:clear` | Clear cached configuration |

---

## Deployment Checklist

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Set the final HTTPS `APP_URL`.
- Configure MySQL and run `php artisan migrate --force`.
- Run required production seeders, especially subscription plans and job categories.
- Run `php artisan storage:link` if public storage links are needed.
- Ensure `storage/` and `bootstrap/cache/` are writable.
- Install and build frontend dependencies with `npm ci && npm run build`.
- Cache configuration, routes, views, and events with `php artisan optimize`.
- Configure the Paystack live keys and live webhook URL.
- Enable the required Paystack payment channels.
- Use HTTPS and ensure the reverse proxy forwards the original scheme and host.
- Configure a real mail driver if email delivery is required.
- Run `php artisan test` before deployment.

---

## Operational Notes

- Administrator-posted jobs are owned by an automatically created `Glow Recruitment Agency` employer profile. Notifications for moderation of admin-owned jobs are suppressed for the administrator.
- Employer job edits require moderation again.
- Paystack test mode can authenticate successfully while still failing checkout if the merchant account has no active channel. Enable a channel in the Paystack dashboard before testing payments.
- Public job pages hide unapproved, unpublished, expired, or deadline-passed listings.
- The application currently sends notifications through the database channel; email notifications are not automatically sent for workflow events.
- The `DemoDataSeeder` is optional and intended for local demonstration only.

---

## License

This project is proprietary software for Glow Recruitment Agency.

## Developer

Timothy Ogheenero Jehwo
