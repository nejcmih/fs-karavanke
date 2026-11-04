# AGENTS.md — AI Coding Agent Guidelines & Project Architecture

This document defines the architectural rules, deployment pipelines, security constraints, and coding standards for AI coding assistants (Cursor, GitHub Copilot, Claude Code, Windsurf, etc.) and developers working on this repository.

---

## 1. Project Overview & Tech Stack

- **Purpose:** Static pages and multi-user blog platform (supporting articles, user-uploaded photos, and embedded videos).
- **Hosting & Infrastructure:** Shared/Managed **cPanel** hosting environment.
- **Backend Runtime:** **PHP 8.2+** (native cPanel execution).
- **Frontend & Tooling:** **Node.js** is used **strictly at build time** (local development and GitHub Actions CI/CD) for compiling CSS/JS assets (e.g., Vite, Tailwind CSS, TypeScript/ESBuild).
- **Database:** **MySQL / MariaDB** managed via **phpMyAdmin** and accessed in PHP strictly via **PDO** with prepared statements.
- **Authentication & Authorization:** **Clerk** (handling Google/Apple OAuth 2.0 & OpenID Connect) synced with local PHP/MySQL role management.
- **CI/CD Pipeline:** **GitHub Actions** automated deployment to cPanel (`public_html`).

---

## 2. Directory Structure & Separation of Concerns

Keep the application modular, lightweight, and safe for cPanel deployment. Never expose configuration files, raw source assets, or vendor internals publicly if avoidable, or protect them via `.htaccess`.

---

## 3. Git, CI/CD & Media Storage Rules (CRITICAL)

1. **Automated Deployment Only:**
   - Never design features that rely on manual FTP/cPanel File Manager code edits. All code changes flow from Git -> GitHub Actions -> cPanel.
2. **Protect Live User Uploads (`public_html/uploads/`):**
   - User-uploaded photos live exclusively on the cPanel server inside `public_html/uploads/`.
   - `/public_html/uploads/*` MUST remain in `.gitignore`.
   - When writing or modifying `.github/workflows/deploy.yml` (whether via `rsync`, `lftp`, or `ftp-deploy-action`), **always explicitly exclude `uploads/`, `.env`, and `.well-known/`** so deployments never delete live user content or server secrets.
3. **Video Policy (No Local Video Hosting):**
   - Don't build file upload handlers that accept or store raw video files (`.mp4`, `.mov`, `.webm`) on the cPanel server or in Git.
   - Videos must be hosted externally (YouTube, Vimeo, Cloudflare Stream) and stored in MySQL strictly as validated embed URLs or video IDs.
4. **Image Upload Hardening:**
   - Validate MIME types using `finfo_file()` (never trust `$_FILES['type']` or file extensions alone).
   - Restrict allowed image formats to `.jpg`, `.jpeg`, `.png`, and `.webp` with a strict size limit (e.g., max 5 MB).
   - Rename uploaded files using cryptographically random UUIDs/hashes (e.g., `bin2hex(random_bytes(16)) . '.webp'`) to prevent path traversal and filename collisions.
   - Place an `.htaccess` file inside `public_html/uploads/` that disables PHP execution (`php_flag engine off` or `<FilesMatch "\.(php|phtml|phar)$"> Deny from all </FilesMatch>`).

---

## 4. Authentication & Role-Based Access Control (Clerk + MySQL)

1. **Authentication Flow:**
   - Clerk handles frontend sign-in (Google OAuth, Apple Sign-In, email).
   - On the PHP backend, verify Clerk's `__session` JWT token using Clerk's JWKS public keys (via `firebase/php-jwt` or Clerk Backend SDK) on every protected request. Never trust unverified frontend user IDs or cookies.
2. **Role Management (`admin`, `author`, `reader`):**
   - **Hybrid Sync Pattern:** Users are identified by their unique `clerk_id` (`sub` claim in the JWT).
   - Check roles either via Clerk `publicMetadata.role` embedded in the session token claims OR via the local MySQL `users` table (`role ENUM('reader', 'author', 'admin') DEFAULT 'reader'`).
   - Always maintain a synchronized `users` row in MySQL (`id`, `clerk_id`, `email`, `display_name`, `role`, `created_at`) so blog `posts` can enforce foreign key integrity (`author_id REFERENCES users(id)`).
3. **Authorization Guards:**
   - **`reader`:** Can view public static pages and published blog posts.
   - **`author`:** Can create posts, upload post images, and edit/delete **only their own** posts (`WHERE post.id = :id AND post.author_id = :current_user_id`).
   - **`admin`:** Can manage all posts, approve/publish drafts, and promote/demote user roles.
   - Enforce role checks on the **PHP server side** before rendering admin views or executing state-changing `POST`/`PUT`/`DELETE` queries.

---

## 5. Security & Safe Coding Standards

### 5.1 Secrets Management
- **NEVER** hardcode API keys, database credentials, Clerk secret keys, Google Client Secrets, or Apple `.p8` private keys in source files.
- Load all sensitive configuration via `.env` using `vlucas/phpdotenv` or native server environment variables.
- Ensure `.env`, `*.p8`, and `*.key` are permanently listed in `.gitignore`.

### 5.2 Database & SQL Safety
- Use **PDO** with `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` and `PDO::ATTR_EMULATE_PREPARES => false`.
- **100% Prepared Statements:** Every SQL query containing variables must use bound parameters (`:param` or `?`). String concatenation or variable interpolation inside SQL strings is strictly forbidden.
- Whenever modifying database structure, add or update the corresponding migration script inside `/sql/` so phpMyAdmin schemas stay reproducible.

### 5.3 XSS, CSRF & Input Validation
- **Output Escaping (XSS Prevention):** Escape all dynamic data rendered in HTML views using `htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.
- **Rich Text Content:** If blog posts allow rich HTML or Markdown, parse Markdown server-side and sanitize HTML output through an allowlist sanitizer (e.g., `ezyang/htmlpurifier`) before rendering or storing—stripping `<script>`, `onload`, `onerror`, and `<iframe>` tags (except explicitly validated YouTube/Vimeo embed wrappers).
- **CSRF Protection:** All state-changing requests (`POST`, `PUT`, `DELETE`) that rely on cookies/sessions must validate a cryptographic CSRF token or verify strict `Origin`/`Referer` headers alongside SameSite cookies.

---

## 6. Efficient Coding & Performance Guidelines for AI Agents

1. **Zero Over-Engineering:**
   - Favor simple, modular, readable PHP classes and functions over heavy enterprise abstractions.
   - Keep external Composer and NPM dependencies to an absolute minimum to ensure fast GitHub Actions builds and compatibility with shared cPanel hosting limits.
2. **Strict Types & Modern PHP:**
   - Begin every PHP file with `declare(strict_types=1);`.
   - Use explicit parameter types, return type declarations, and nullable types (`?string`, `int`, `array`).
   - Handle errors gracefully: log detailed exceptions server-side (`error_log()`) and return clean, generic error messages to the user (never expose stack traces or SQL errors in production).
3. **Database Efficiency:**
   - Avoid `SELECT *`; explicitly list required columns.
   - Prevent N+1 query problems by using `JOIN` queries when fetching blog posts with author metadata.
   - Always paginate blog post listings (`LIMIT :limit OFFSET :offset`) and ensure `slug`, `author_id`, `clerk_id`, and `created_at` columns are properly indexed in MySQL.
4. **Frontend Asset Discipline:**
   - Write modular components and keep JavaScript minimal (progressive enhancement).
   - Ensure images rendered in blog templates include `loading="lazy"`, explicit `width`/`height` attributes to prevent layout shift (CLS), and descriptive `alt` attributes.

---

## 7. Agent Workflow Checklist (Before Generating or Modifying Code)

Whenever asked to implement a feature or fix a bug, the AI agent must verify:
- [ ] **No Node.js Runtime Dependency:** Does this run natively on PHP/cPanel after the static assets are built?
- [ ] **Uploads Protected:** If touching deployment scripts or file handling, is `public_html/uploads/` protected from deletion and direct script execution?
- [ ] **SQL Injection Check:** Are all database interactions using PDO prepared statements?
- [ ] **Auth & Role Check:** Does the endpoint verify the Clerk JWT and confirm the user's `role` (`admin` vs `author`) before modifying data?
- [ ] **XSS Check:** Are all user-supplied strings and database outputs properly escaped in the PHP view template?