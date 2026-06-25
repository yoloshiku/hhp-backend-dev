<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# HHP Backend

Backend API for the Human Health Project, built with Laravel.
Provides authentication, email verification, user management, and contact functionality.

## Requirements

*   PHP >= 8.1
*   Composer
*   Laravel 12
*   MySQL
*   Git (optional, for version control)

## Setup Instructions

### Clone the repository
```
git clone https://github.com/marlon-9407/hhp-backend.git
``` 

```
cd hhp-backend
``` 

*   Install dependencies
```
composer install
``` 

*   Create a MySQL database for the application, for example:
```
hhp_db
``` 

*   Create a MySQL database for the tests for example:
```
hhp_testing_db
``` 

### Environment setup

*   Copy .env file
```
cp .env.example .env
``` 

### Set up environment variables
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
``` 

*   Set up environment variables in phpunit.xml for testing, for example:
```
<!-- Database -->
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_DATABASE" value="hhp_testing_db"/>
``` 

### Mail configuration

*   (Important) Set up mail variables, you must configure the MAIL_ADMIN_ADDRESS, This is the email address where contact form submissions will be sent. and also add MAIL_ADMIN_NEWSLETTER_SUBSCRIBER_ADDRESS, This is the email address where newsletter subscribers form submissions will be sent (info@humanhealthproject.org) for example:
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@humanhealthproject.com"
MAIL_FROM_NAME="Human Health Project"
MAIL_ADMIN_ADDRESS="info@humanhealthproject.org"
MAIL_ADMIN_NEWSLETTER_SUBSCRIBER_ADDRESS="info@humanhealthproject.org"
``` 

### Sanctum configuration

```
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:5173,127.0.0.1:5173
SESSION_DOMAIN=localhost
``` 

### Frontend URL (for email verification)

*   (Important) Set up FRONTEND_URL variable, This is the frontend URL where users will be redirected after clicking the email verification link. The frontend is responsible for handling and processing the verification URL. for example:
```
FRONTEND_URL=http://localhost:5173
``` 

*   Also make sure APP_URL is set correctly:
```
APP_URL=http://localhost
``` 

### Generate key & migrate

*   Generate application key
```
php artisan key:generate
``` 

*   Run database migrations
```
php artisan migrate
``` 

### Run the app

*   Serve the application
```
php artisan serve
``` 

### Run tests
```
php artisan test
``` 

## API Endpoints

### Authentication
- POST /api/auth/register
- POST /api/auth/login
- POST /api/auth/logout

### Email Verification
- POST /api/email/verification-notification
- GET  /api/email/verify/{id}/{hash}

### User
- PUT /api/user/profile

### Contact
- POST /api/contact

### Newsletter Subscriber
- POST /api/newsletter-subscriber

## Folder Structure (Key Parts)
```
app/Http/Controllers – Application controllers

app/Services – Business logic service classes

app/Repositories – Data access layer (Repositories handle database operations)

app/Models – Eloquent models

resources/views – Blade templates (used for emails)

routes/api.php – Api routes

tests/Unit – Unit tests for services and repositories

tests/Feature – Integration tests for controllers and repositories
``` 

## Architecture

This project follows a layered architecture:

- Controllers → Handle HTTP requests
- Services → Contain business logic
- Repositories → Handle database operations
- Models → Eloquent ORM models

## Testing

- Feature tests: test API endpoints
- Unit tests: test services and repositories