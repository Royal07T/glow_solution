# Glow Recruitment Agency Solution

A modern, responsive, subscription-based recruitment marketplace serving domestic and professional employment.

## Features

- **Public Homepage** - Premium design with purple and gold brand identity
- **Multi-Role System** - Job Seekers, Employers, and Administrators
- **Job Management** - Post, search, and apply for jobs with moderation workflow
- **CV Management** - Secure private CV storage
- **Subscription System** - Configurable plans for job seekers and employers
- **Paystack Integration** - Secure payment processing
- **Admin Dashboard** - Complete platform management
- **Application Tracking** - Full application lifecycle management

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2
- **Frontend**: Blade Templates, Livewire, Tailwind CSS
- **Database**: SQLite/MySQL
- **Payments**: Paystack
- **Authentication**: Laravel Breeze

## Installation

1. Clone the repository
2. Install dependencies:
```bash
composer install
npm install
```
3. Copy environment file:
```bash
cp .env.example .env
```
4. Generate application key:
```bash
php artisan key:generate
```
5. Run migrations:
```bash
php artisan migrate
```
6. Create storage link:
```bash
php artisan storage:link
```
7. Build assets:
```bash
npm run build
```
8. Start the server:
```bash
php artisan serve
```

## Configuration

### Paystack
Add your Paystack keys to `.env`:
```
PAYSTACK_PUBLIC_KEY=your_public_key
PAYSTACK_SECRET_KEY=your_secret_key
PAYSTACK_MERCHANT_EMAIL=your_email
```

### File Storage
CVs are stored privately in `storage/app/private/cvs/`. Ensure proper permissions.

## Security

- Server-side validation on all inputs
- Role-based authorization with policies and middleware
- CSRF protection
- Private file storage for CVs
- Secure payment verification with webhook signature validation
- No client-side role/subscription checks

## Roles & Permissions

- **Job Seeker**: Browse jobs, apply, manage CVs, track applications
- **Employer**: Post jobs (requires verification), manage applicants, needs subscription
- **Admin**: Full platform control, user management, job moderation

## License

This project is proprietary software for Glow Recruitment Agency.
# glow_solution

# Developer

Timothy Ogheenero Jehwo