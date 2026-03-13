# Todo App Frontend (VueJS)

This is the frontend for the Todo application, built with Vue 3, Vite, and Tailwind CSS.

## Features

- **Main Table**: 
  - Inline editing for all fields.
  - Multi-column sorting (click headers).
  - Search by task name.
  - Filter by developer.
  - Column summaries with percentage progress bars.
- **Kanban Board**:
  - Drag and drop tasks between status columns.
  - Automatic status updates on drop.
- **Task Management**:
  - Modal to create new tasks.
  - Integration-ready architecture.

## Requirements

- Node.js 18+
- npm

## Installation

1. Navigate to the `frontend` directory:
   ```bash
   cd frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Run the development server:
   ```bash
   npm run dev
   ```

The application will be available at `http://localhost:5173`.

## Tech Stack

- **Framework**: Vue 3 (Composition API)
- **Styling**: Tailwind CSS 4
- **Icons**: Lucide Vue Next
- **Drag & Drop**: Vuedraggable
- **Date Handling**: date-fns
