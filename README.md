# DevLink API

![Tests](https://github.com/aardnsyhs/devlink-api/actions/workflows/tests.yml/badge.svg)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue)
![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![License](https://img.shields.io/badge/license-MIT-green)

REST API for articles and code snippets built with Laravel 11.

## Tech Stack

- PHP 8.2+
- Laravel 11
- MySQL
- Laravel Sanctum (token auth)
- L5 Swagger (OpenAPI docs)
- Pest + PHPUnit

## Features

- Auth: register, login, logout
- Articles: public read + authenticated CRUD
- Snippets: public read + authenticated CRUD
- Policy-based ownership authorization
- Rate limiting (`auth` and `api`)
- OpenAPI docs with reusable schemas

## Project Structure

- `app/Http/Controllers/Api/V1`:
  API controllers
- `app/Services`:
  business logic layer
- `app/Repositories`:
  data access layer
- `app/Policies`:
  authorization rules
- `tests/Feature`:
  API/integration-style tests
- `tests/Unit`:
  unit tests for service/repository logic

## Quick Start

```bash
git clone https://github.com/aardnsyhs/devlink-api.git
cd devlink-api
composer install
cp .env.example .env
php artisan key:generate
```

Configure database in `.env`, then run:

```bash
php artisan migrate --seed
php artisan serve
```

API base URL (local):

```text
http://127.0.0.1:8000/api/v1
```

## API Documentation

Generate OpenAPI docs:

```bash
php artisan l5-swagger:generate
```

Swagger UI:

```text
http://127.0.0.1:8000/api/documentation
```

## Running Tests

Run all tests:

```bash
php artisan test
```

Run only feature tests:

```bash
php artisan test tests/Feature
```

Run only unit tests:

```bash
php artisan test tests/Unit
```

Run coverage (requires Xdebug/PCOV):

```bash
XDEBUG_MODE=coverage php artisan test --coverage --min=80
```

## CI Quality Gates

GitHub Actions workflow enforces:

- migration on clean test database
- OpenAPI generation check
- test suite execution
- coverage minimum threshold (`--min=80`)

## Notes

- Public listing endpoints default to `published` records.
- Public listing also supports `status` filtering (`draft`, `published`, `archived`) when explicitly provided.
- Ownership for update/delete is enforced by Laravel policies.
