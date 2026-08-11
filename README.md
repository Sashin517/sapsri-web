# SAPSRI - Official Website

## Overview

The official website for **[NGO Name]**, built to showcase the organization's mission, programs, and impact, while giving the team an easy way to manage content — news, events, and media — through a custom admin panel. Built with plain PHP and MySQL for lightweight, low-maintenance hosting.

## Project Architecture

### 1. Public Site (`/`)
- `index.php` - Main entry point / homepage.
- `robots.txt` - Search engine crawling rules.
- `.htaccess` - URL rewriting and server-level configuration.

### 2. Admin Panel (`/admin`)
- Custom-built dashboard for managing site content (e.g., programs, news/blog posts, gallery, and other dynamic sections) without editing code directly.
- Handles authentication for authorized staff/editors only.

### 3. Assets (`/assets`)
- `css/` - Stylesheets.
- `js/` - Client-side scripts.
- `fonts/` - Custom web fonts.
- `icons/` - Icon assets used across the site.
- `media/` - Uploaded content, split into:
  - `docs/` - Downloadable documents (reports, brochures, etc.).
  - `img/` - Images used in pages, programs, and galleries.
  - `videos/` - Video content.

### 4. Data & Storage
- **Database (MySQL):** Stores site content, admin users, and any structured data (e.g., programs, news, contact submissions).
- `assets/media/` - Uploaded files are stored on the server directly (not version-controlled — see [Notes](#notes)).

---

## How to Run Locally

### Prerequisites
- PHP 7.4+ (or your target version)
- MySQL / MariaDB
- A local server environment (XAMPP, MAMP, Laragon, or PHP's built-in server)

### Step 1: Set Up the Database
1. Create a new MySQL database, e.g. `ngo_website`.
2. Import the provided schema/dump file (if available) using phpMyAdmin or the CLI:
   ```bash
   mysql -u root -p ngo_website < database/schema.sql
   ```

### Step 2: Configure the Application
1. Copy the example config file:
   ```bash
   cp config.example.php config.php
   ```
2. Open `config.php` and update your database credentials (host, database name, username, password).

### Step 3: Start the Local Server
Using PHP's built-in server from the project root:
```bash
php -S localhost:8000
```

### Step 4: Access the Application
- **Public site:** `http://localhost:8000`
- **Admin panel:** `http://localhost:8000/admin`

---

## Key Features

### Public-Facing Website
- Homepage highlighting the NGO's mission, vision, and impact.
- Program/cause pages describing ongoing initiatives.
- News/blog section for updates and announcements.
- Media gallery (images, videos, documents) for transparency and outreach.
- Contact/inquiry form for donors, volunteers, and partners.

### Admin Dashboard
- Secure login for staff/editors.
- Content management for pages, news, and media without touching code.
- Upload and organize files under `assets/media/`.

---

## Tech Stack Reference

- **Backend:** PHP (plain, no framework)
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript (custom, no frontend framework)
- **Hosting/Deployment:** FTP deployment via GitHub Actions to `sapsri.lk/sldevs/project-sedna`

---

## Deployment

This project deploys automatically on every push to `main` via GitHub Actions (`.github/workflows/deploy.yaml`), which syncs files to the live server over FTPS. Media files under `assets/media/` and build artifacts like `repomix-output.xml` are excluded from deployment sync and Git tracking — see `.gitignore` and the workflow's `exclude` list.

---

## Common Issues & Solutions

### Issue: "Database connection failed"
**Solution:** Double-check `config.php` for correct MySQL host, username, password, and database name. Confirm MySQL is running.

### Issue: Uploaded media not showing on the live site
**Solution:** Since `assets/media/` is excluded from Git, ensure media files are uploaded directly to the server (via FTP or the admin panel) rather than expecting them to deploy through the GitHub Actions pipeline.

### Issue: Admin panel login not working
**Solution:** Confirm the `admin` database table exists and has a valid user record. Check that sessions/cookies aren't blocked in your browser.

---

## Notes

- Replace `[NGO Name]` throughout this file with the organization's actual name.
- `assets/media/` files are intentionally excluded from version control (see `.gitignore`) since they're large binary/user-uploaded content — manage them directly on the server instead.