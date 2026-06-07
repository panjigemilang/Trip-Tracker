# Trip Tracker (Plan Trip Tracker)

A Progressive Web Application (PWA) designed to track, plan, and manage travel itineraries seamlessly. Trip Tracker features dynamic trip planning, offline-first support, progress tracking, and activity photo uploads.

## Tech Stack

- **Frontend**: Svelte 5 (utilizing Runes), TypeScript, TailwindCSS v4, shadcn-svelte UI.
- **Backend**: Laravel 12 API, Laravel Sanctum for Authentication, PHP 8.x.
- **Database**: PostgreSQL (neon.tech Cloud SQL).
- **Caching & Queue**: Redis (Upstash Serverless Redis).
- **Deployment**:
  - **Frontend**: Cloudflare Pages (via GitHub Actions automatic deployment).
  - **Backend**: Koyeb (via GitHub Actions automatic deployment).

---

## Directory Structure

```text
Trip Track/
├── backend/            # Laravel 12 API backend
├── frontend/           # Svelte 5 / SvelteKit frontend web app
└── .github/workflows/  # CI/CD deployment pipelines
```

---

## Backend Setup (`/backend`)

The backend requires PHP 8.x, Composer, and PostgreSQL/Redis instances.

### 1. Installation
Go to the backend directory and install PHP dependencies:
```bash
cd backend
composer install
```

### 2. Environment Configuration
Copy the `.env.example` file to `.env`:
```bash
cp .env.example .env
```
Open `.env` and fill in the required environment variables:
- **Application Key**: Run `php artisan key:generate` to generate a secure application key.
- **Database**: We use PostgreSQL. When using Neon PostgreSQL, append `;options=endpoint=<your-endpoint>` to the host to allow connection:
  ```env
  DB_CONNECTION=pgsql
  DB_HOST="your-neon-host.neon.tech;options=endpoint=your-neon-endpoint"
  DB_PORT=5432
  DB_DATABASE=neondb
  DB_USERNAME=your-username
  DB_PASSWORD=your-password
  DB_SSLMODE=require
  ```
- **Redis (Cache & Queues)**: Configure Upstash or local Redis:
  ```env
  CACHE_DRIVER=redis
  QUEUE_CONNECTION=redis
  SESSION_DRIVER=redis
  
  REDIS_HOST=your-upstash-redis-host.upstash.io
  REDIS_PASSWORD=your-upstash-redis-password
  REDIS_PORT=6379
  ```

### 3. Database Migrations
Run the migrations to create the database schemas:
```bash
php artisan migrate
```

### 4. Run Locally
Start the local development server:
```bash
php artisan serve
```
By default, the server runs at `http://localhost:8000`.

---

## Frontend Setup (`/frontend`)

The frontend is built with SvelteKit using Svelte 5 Runes. It requires Node.js 18+.

### 1. Installation
Go to the frontend directory and install npm packages:
```bash
cd frontend
npm install
```

### 2. Environment Configuration
Create a `.env` file in the `frontend` folder:
```bash
# Create frontend/.env
PUBLIC_API_URL=http://localhost:8000/api
```

### 3. Run Locally
Start the development server:
```bash
npm run dev
```
Open `http://localhost:5173` in your web browser.

---

## Deployment & CI/CD

This project has pre-configured workflows under `.github/workflows` to automatically deploy changes pushed to the `main` branch.

- **Frontend CI/CD (`frontend.yml`)**: Builds the SvelteKit application and deploys it directly to Cloudflare Pages.
- **Backend CI/CD (`backend.yml`)**: Runs PHP lint/testing verification and redeploys the containerized application on Koyeb.
