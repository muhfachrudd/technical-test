# Fullstack Todo Application - Technical Test

A comprehensive Todo Application featuring a Kanban board, data tables, and Excel reporting. Built with **Laravel 12** for the backend and **Vue.js 3** for the frontend.

## 🚀 Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Vue.js 3, Vite, Tailwind CSS 4, Lucide Icons
- **Database:** SQLite (default)
- **Features:** 
  - Kanban Board with Drag & Drop
  - Data Table with Inline Editing
  - Multi-column Sorting & Filtering
  - Excel Export Report
  - Chart Data Analytics API

---

## 🛠️ Installation & Setup

### 1. Prerequisites
Ensure you have the following installed:
- [PHP >= 8.2](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Node.js & npm](https://nodejs.org/)

### 2. Backend Setup (Laravel)
```bash
cd backend

# Install dependencies
composer install

# Set up environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database
# Note: On Windows, use 'type nul > database/database.sqlite' or create manually
php artisan migrate

# Start the server
php artisan serve
```
*The backend should now be running at `http://localhost:8000`.*

### 3. Frontend Setup (Vue.js)
```bash
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev
```
*The frontend should now be running (usually at `http://localhost:5173`).*

---

## 📑 API Features & Postman

The project includes a Postman collection located at `backend/Postman_Collection.json`.

### Available Endpoints:
- `GET /api/todos` - Fetch all tasks (supports filtering by title, assignee, status, etc.)
- `POST /api/todos` - Create a new task
- `PUT /api/todos/{id}` - Update a task
- `DELETE /api/todos/{id}` - Delete a task
- `GET /api/excel` - Download task report as Excel
- `GET /api/chart?type=status` - Get status summary chart data
- `GET /api/chart?type=priority` - Get priority summary chart data
- `GET /api/chart?type=assignee` - Get developer performance chart data

---

## 📸 Screenshots & README Requirements

- Final project structure is separated into `frontend/` and `backend/`.
- All features (Table, Kanban, Export, Search, Person Filter) are fully integrated with the Laravel API.
- Designed to be **Pixel Perfect** menurut instruksi desain.

Developed by Muhammad Fachruddin for PT Dynamic Talenta Navigator.
