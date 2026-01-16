## Postman Collection
This repository includes a Postman collection for easy API testing. Import the provided collection into Postman to quickly try all endpoints with example requests and parameters.

## Design Choices
- **JWT Authentication:** All protected endpoints require a JWT token, ensuring stateless, scalable, and secure authentication for API clients.
- **No User Table:** User management is intentionally omitted for simplicity and security; authentication is handled via issued tokens only.
- **Tag & Locale Extensibility:** Tags and locales are managed as first-class resources, allowing flexible categorization and language support.
- **Standardized Responses:** All API responses use a custom `api_response` helper for consistent structure, simplifying client integration and error handling.
- **Performance Considerations:** Factories and seeders are optimized for large datasets, and performance tests are included to ensure API scalability.

## Features
- JWT-secured API endpoints
- Manage translations, tags, and locales
- Extensible locale and tag system
- Consistent JSON responses via `api_response` helper
- MySQL support for development and testing
- Comprehensive unit, feature, and performance tests

## Prerequisites
- PHP >= 8.2
- Composer
- MySQL (or compatible database)
- Node.js & npm (for asset build, optional)

## Installation
1. **Clone the repository:**
	```bash
	git clone <your-repo-url>
	```
2. **Install PHP dependencies:**
	```bash
	composer install
	```
3. **Copy and configure your environment:**
	```bash
	cp .env.example .env
	# Edit .env to set DB credentials, APP_KEY, and JWT_SECRET
	```
	- Set `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` in `.env`.
	- Generate an app key:
	  ```bash
	  php artisan key:generate
	  ```
	- Set a secure `JWT_SECRET` in `.env` (Can use any random key).
4. **Run database migrations:**
	```bash
	php artisan migrate --seed
	```
	**Note:** If you encounter memory errors during seeding, use or any:
	```bash
	php -d memory_limit=2G artisan migrate --seed
	```
5. *(Optional)* **Install Node dependencies and build assets:**
	```bash
	npm install && npm run build
	```

## Running the API
Start the Laravel development server:
```bash
php artisan serve
```
The API will be available at `http://localhost:8000/api` by default.

## JWT Authentication
- Obtain a JWT token by POSTing to `/api/token`:
  ```bash
  curl -X POST http://localhost:8000/api/token
  ```
  The response will include a `token` field. Use this token in the `Authorization: Bearer <token>` header for all protected endpoints.

## Running Tests

Tests use a separate `.env.testing` file (MySQL by default). Make sure to:
- Generate a unique `APP_KEY` for testing:
	```bash
	php artisan key:generate --env=testing
	```
- Set a secure `JWT_SECRET` in `.env.testing` (you can use any random string or generate one):

To run all tests (standard):
```bash
php artisan test
```

For large data or performance tests (e.g., 100,000+ records), increase PHP memory and run phpunit directly:
```bash
php -d memory_limit=2G vendor/bin/phpunit
```
Ensure your test database is configured and accessible.

## API Endpoints (Summary)

- `POST   /api/token` — Get JWT token
	- Params (JSON):
		- `ttl` (optional, integer): Token time-to-live in seconds (default: 3600)

- `GET    /api/translations` — List translations
	- Query params (optional):
		- `locale_id`, `tag_id`, `key` (filtering)

- `POST   /api/translations` — Create translation
	- Body (JSON):
		- `key` (string, required)
		- `content` (string, required)
		- `locale_id` (integer, required)
		- `tag_id` (integer, optional)

- `GET    /api/translations/{id}` — Get translation

- `PUT    /api/translations/{id}` — Update translation
	- Body (JSON):
		- Any of: `key`, `content`, `locale_id`, `tag_id`

- `GET    /api/export/json` — Export all translations as JSON

- `GET    /api/tags` — List tags

- `POST   /api/tags` — Create tag
	- Body (JSON):
		- `name` (string, required)

- `GET    /api/locales` — List locales

- `POST   /api/locales` — Create locale
	- Body (JSON):
		- `code` (string, required)
		- `name` (string, required)

All endpoints (except `/api/token`) require a valid JWT in the `Authorization` header.