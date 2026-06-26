# MEI Backend - AGENTS.md

## Technology Stack

### Backend Framework
- **Laravel 13.x** - PHP web framework with built-in features (routing, ORM, middleware, authentication)
- **PHP 8.3+** - Runtime language
- **Laravel Sail** - Containerized local development environment

### Databases & Caching
- **MySQL 8.0** - Primary relational database
- **Redis** - In-memory cache and session storage

### Authentication & Security
- **Laravel Sanctum** - API token authentication (for mobile/SPA)
- **JWT Auth (tymon/jwt-auth 2.1)** - JWT-based authentication
- **Laravel Permissions (Spatie)** - Role and permission management

### Core Libraries & Tools
- **Doctrine DBAL** - Database abstraction layer for migrations
- **Guzzle HTTP** - HTTP client for external API calls
- **Spatie Laravel Data** - Data transfer objects and validation

### PDF & Document Generation
- **DOMPDF** - HTML to PDF conversion
- **LibMergePDF** - PDF merging utilities
- **PHPOffice/PHPSpreadsheet** - Excel/CSV handling
- **Browsershot** - Screenshot and PDF from HTML

### Barcode & File Processing
- **ZIP Extension** - Archive compression support

### API Documentation
- **L5 Swagger (darkaonline/l5-swagger 8.6.3)** - Swagger/OpenAPI documentation generator

### Testing
- **PHPUnit 11.x** - Test framework
- **Mockery** - Mocking library for unit tests
- **Collision** - Error display and debugging
- **Laravel Pint** - Code style linting

### Frontend/Build Tools
- **Vite 5.0** - JavaScript build tool and dev server
- **Axios** - HTTP client for frontend
- **Puppeteer** - Headless browser automation
- **Chromium** - Browser engine

## Architecture

### Directory Structure

```
├── app/                          # Application code
│   ├── Data/                     # Spatie Laravel Data DTOs (validation + transfer)
│   ├── Enums/                    # PHP backed enums for fixed value sets
│   ├── Http/
│   │   ├── Controllers/          # Route handlers
│   │   ├── Middleware/           # Request/response middleware
│   │   └── Resources/            # Resources to output data
│   ├── Models/                   # Eloquent ORM models
│   ├── Repositories/             # Data access abstraction layer
│   ├── Services/                 # Business logic
│   ├── Exceptions/               # Custom exceptions
│   ├── Values/                   # Value objects and domain values
│   └── helpers.php               # Helper functions
├── bootstrap/                    # Application bootstrap files
├── config/                       # Configuration files (database, mail, etc.)
├── database/
│   ├── migrations/               # Schema migrations
│   ├── factories/                # Model factories for testing
│   └── seeders/                  # Database seeders
├── routes/                       # Route definitions
│   └── api.php
├── resources/                    # Views and templates
├── tests/                        # Test suite
│   ├── Unit/                     # Unit tests
│   ├── Feature/                  # Feature/integration tests
│   ├── Traits/                   # Shared test utilities
│   └── TestCase.php              # Base test class
├── storage/                      # Generated files, logs, cache
├── docker/                       # Docker configuration
│   ├── 8.3/                      # Default PHP 8.3 Dockerfile
│   └── 8.3_mac/                  # macOS-optimized PHP 8.3 Dockerfile
└── public/                       # Web-accessible files
```

### Layered Architecture

1. **Route Layer** (routes/) - Defines API endpoints and HTTP verbs
2. **Controller Layer** (app/Http/Controllers/) - Handles HTTP concerns (request/response, DTO injection), delegates domain logic to Services
3. **Service Layer** (app/Services/) - Contains business logic and data processing
4. **Model Layer** (app/Models/) - Eloquent ORM models with database relationships
5. **Database Layer** - MySQL with migrations and seeders

### Key Configuration Files

- `.env` - Environment variables (copy from `.env.example`)
- `phpunit.xml` - PHPUnit test configuration
- `config/database.php` - Database connections (includes `mysql_testing` for tests)
- `docker-compose.yml` - Docker service definitions
- `docker-compose-mac.yml` - macOS-specific Docker service definitions

## Testing with Docker

### Test Infrastructure

**PHPUnit Configuration** (`phpunit.xml`):
- Unit tests: `tests/Unit`
- Feature tests: `tests/Feature`
- Test database connection: `mysql_testing`
- Test environment: `APP_ENV=testing`

**Docker Services** (from docker-compose.yml):
- `laravel.test` - PHP 8.3 application container
- `mysql` - MySQL 8.0 database
- `redis` - Redis cache
- `mailpit` - Local SMTP capture service
- Auto-created testing database for test runs

### Run All Tests

```bash
make test
```

This command:
1. Clears Laravel config cache
2. Runs all tests in `tests/` directory
3. Executes in Docker container via Laravel Sail

### Run Tests with Coverage Report

```bash
make test-coverage
```

This command:
1. Generates HTML coverage report in `coverage/` directory
2. Generates text coverage summary in `coverage.txt`
3. Reports on code coverage for `app/` directory

### Run Specific Tests

```bash
# Run single test file
vendor/bin/sail artisan test tests/Unit/SomeTest.php

# Run tests matching pattern
vendor/bin/sail artisan test --filter=testSomething

# Run only Feature tests
vendor/bin/sail artisan test --testsuite=Feature

# Run only Unit tests
vendor/bin/sail artisan test --testsuite=Unit
```

### Docker-Based Test Execution

**Database Setup for Tests:**
- MySQL initializes separate `testing` database via `docker/mysql/create-testing-database.sh`
- Database connection uses `mysql_testing` defined in `config/database.php`
- Each test run uses fresh database state

**Test Database Refresh (if needed):**

```bash
# Refresh testing database while inside container
vendor/bin/sail artisan migrate:fresh --database=mysql_testing
```

**Access Test Database from Container:**

```bash
# Connect to MySQL container
docker compose exec mysql bash

# Or from host (if exposed)
mysql -h 127.0.0.1 -P 3306 -u root -p testing
```

### Database Migrations in Tests

Tests use configuration from `phpunit.xml`:
```xml
<env name="DB_CONNECTION" value="mysql_testing"/>
```

Migrations are run automatically on test startup. If you add new migrations:

```bash
# Create migration
vendor/bin/sail artisan make:migration create_table_name

# Migrations apply to both dev and test databases
```

### Test File Structure

**Unit Test Example Location:**
```
tests/Unit/SomeClassTest.php
```

**Feature Test Example Location:**
```
tests/Feature/SomeControllerTest.php
```

**Base Classes:**
- `tests/TestCase.php` - Extends Laravel TestCase, provides test utilities
- `tests/CreatesApplication.php` - Application bootstrapping for tests
- `tests/Traits/` - Shared test helper traits

### Docker Commands for Testing

```bash
# Start Docker environment
make run

# macOS-specific Docker environment
make run_mac

# Get shell inside web container (for running custom commands)
make shell

# View container logs
make logs

# Stop containers
make stop

# Stop macOS Docker environment
make stop_mac
```

### Typical Test Workflow

1. **Start Docker Environment:**
   ```bash
   make run
   ```

2. **Write/Modify Tests:**
   - Create test files in `tests/Unit/` or `tests/Feature/`
   - Extend `Tests\TestCase` as base class

3. **Run Tests:**
   ```bash
   make test
   ```

4. **Check Coverage:**
   ```bash
   make test-coverage
   ```
   - Open `coverage/index.html` in browser

5. **Stop Environment:**
   ```bash
   make stop
   ```

### Important Environment Variables for Tests

Set in `phpunit.xml`:
- `APP_ENV=testing`
- `DB_CONNECTION=mysql_testing`
- `CACHE_DRIVER=array`
- `QUEUE_CONNECTION=sync`
- `SESSION_DRIVER=array`
- `MAIL_MAILER=array`

These ensure tests run in isolation without external dependencies.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/framework (LARAVEL) - v13
- laravel/pennant (PENNANT) - v1
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.
- Check for PSR-12 (https://www.php-fig.org/psr/psr-12/), PSR-2 (https://www.php-fig.org/psr/psr-2/) and PSR-1 (https://www.php-fig.org/psr/psr-1/)

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `vendor/bin/sail npm run build`, `vendor/bin/sail npm run dev`, or `vendor/bin/sail composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Artisan

- Run Artisan commands directly via the command line (e.g., `vendor/bin/sail artisan route:list`). Use `vendor/bin/sail artisan list` to discover available commands and `vendor/bin/sail artisan [command] --help` to check parameters.
- Inspect routes with `vendor/bin/sail artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `vendor/bin/sail artisan config:show app.name`, `vendor/bin/sail artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `vendor/bin/sail artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `vendor/bin/sail artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail npm run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `vendor/bin/sail artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `vendor/bin/sail artisan list` and check their parameters with `vendor/bin/sail artisan [command] --help`.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `vendor/bin/sail artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `vendor/bin/sail npm run build` or ask the user to run `vendor/bin/sail npm run dev` or `vendor/bin/sail composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- This project upgraded from Laravel 10 without migrating to the new streamlined Laravel file structure.
- This is perfectly fine and recommended by Laravel. Follow the existing structure from Laravel 10. We do not need to migrate to the new Laravel structure unless the user explicitly requests it.

## Laravel 10 Structure

- Middleware typically lives in `app/Http/Middleware/` and service providers in `app/Providers/`.
- There is no `bootstrap/app.php` application configuration in a Laravel 10 structure:
    - Middleware registration happens in `app/Http/Kernel.php`
    - Exception handling is in `app/Exceptions/Handler.php`
    - Console commands and schedule register in `app/Console/Kernel.php`
    - Rate limits likely exist in `RouteServiceProvider` or `app/Http/Kernel.php`

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/sail bin pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test --format agent`, simply run `vendor/bin/sail bin pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `vendor/bin/sail artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `vendor/bin/sail artisan test --compact`.
- To run all tests in a file: `vendor/bin/sail artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `vendor/bin/sail artisan test --compact --filter=testName` (recommended after making a change to a related file).

</laravel-boost-guidelines>
