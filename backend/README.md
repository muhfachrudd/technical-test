# Todo App Backend (Laravel)

This is the backend for the Todo application, built with Laravel 12.

## Requirements

- PHP 8.2 or higher
- Composer
- SQLite (default)

## Installation

1. Navigate to the `backend` directory:
   ```bash
   cd backend
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate the application key:
   ```bash
   php artisan key:generate
   ```

5. Run the migrations:
   ```bash
   php artisan migrate
   ```

6. Serve the application:
   ```bash
   php artisan serve
   ```

The API will be available at `http://localhost:8000`.

## API Endpoints

### 1. Create Todo
- **URL**: `POST /api/todos`
- **Body**:
  ```json
  {
      "title": "String (required)",
      "assignee": "String (optional)",
      "due_date": "Date YYYY-MM-DD (required)",
      "time_tracked": "Numeric (optional, default 0)",
      "status": "Enum: pending, open, in_progress, completed (optional, default pending)",
      "priority": "Enum: low, medium, high (required)"
  }
  ```

### 2. Export Excel
- **URL**: `GET /api/excel`
- **Query Parameters**:
  - `title`: partial match
  - `assignee`: comma separated (e.g., `John,Doe`)
  - `start_date`, `end_date`: date range (YYYY-MM-DD)
  - `min_time`, `max_time`: numeric range
  - `status`: comma separated
  - `priority`: comma separated

### 3. Chart Data
- **URL**: `GET /api/chart`
- **Query Parameters**:
  - `type`: `status`, `priority`, or `assignee`

## Postman Collection

A Postman collection is provided in the `backend/Postman_Collection.json` file. You can import it into Postman and change the `base_url` variable if necessary.
