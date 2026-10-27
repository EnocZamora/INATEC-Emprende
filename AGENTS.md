# AGENT.md — Technical Context & Architecture Rules

## 1. Project Overview
- **Name:** INATEC Emprende
- **Objective:** Institutional paperless digital platform designed as an equitable showcase for student technical projects and entrepreneurial initiatives.
- **Business Principles:**
  - **Equitable Showcase:** No paid algorithms, promoted posts, or artificially boosted projects. Catalog filtering strictly relies on relevance, submission date, career, and technical center.
  - **Direct Conversion:** Contact between visitors and students occurs directly via normalized WhatsApp links (`wa.me/{number}` with pre-filled context messages).
  - **Zero Cloud Costs:** 100% local-first architecture for the MVP stage, completely avoiding paid cloud dependencies.

---

## 2. Tech Stack & Environment
- **Framework:** Laravel 11 (PHP 8.4)
- **Database:** MySQL / MariaDB (Database Name: `inatec-emprende`) running on XAMPP.
- **Storage:** Local file system via `Storage::disk('public')` symlinked with `php artisan storage:link`.
- **Tooling:** OpenCode CLI with high-tier LLMs (Claude Opus 5 / DeepSeek) for code generation.

---

## 3. Roles & Access Control (RBAC)
1. **Guest (Visitor):** 
   - Public access. Can explore the catalog, filter by center/career/category, view project details, and open direct WhatsApp contact links.
2. **Student (`role = 'student'`)**: 
   - Authenticated user. Can manage their profile (`user_profiles`), create, update, and delete their own projects, toggle status (`draft`, `published`), and trigger physical deletion of local images. Restricted strictly by Policies (no cross-user edits).
3. **Admin (`role = 'admin'`)**: 
   - Elevated institutional privileges. Can moderate project statuses (`suspended`), manage structural catalogs (`centers` and `careers`), view the user directory, modify user roles, and delete user accounts.

---

## 4. Architecture Principles & Clean Code Standards
1. **Skinny Controllers:** Controllers must strictly coordinate requests, invoke business logic, and return responses.
2. **Dedicated FormRequests:** All input validation, sanitization, and phone number formatting must reside in dedicated `FormRequest` classes.
3. **Security & Authorization:**
   - Prevent **IDOR** (*Insecure Direct Object Reference*) vulnerabilities by enforcing Laravel `Policies` and `Gates` so students can only modify their own data and projects.
4. **Query Optimization:** Strictly avoid N+1 query issues by enforcing *Eager Loading* (`with()`) across all Eloquent queries.
5. **File Handling:** Uploaded images must be validated, uniquely named, and saved locally inside `storage/app/public/projects`. Deleting a project or gallery photo must physically remove the corresponding file from local storage.

---

## 5. Database Schema & Relationships
- **`centers`**: Technical centers (`id`, `name`, `code`, `location`, `timestamps`).
- **`careers`**: Technical careers linked to centers (`id`, `center_id`, `name`, `timestamps`).
- **`users`**: System-wide unified identity (`id`, `name`, `email`, `password`, `role` ['student', 'admin'], `timestamps`).
- **`user_profiles`**: Extended student details (`id`, `user_id`, `center_id`, `career_id`, `phone_whatsapp`, `bio`, `timestamps`).
- **`projects`**: Ventures/projects (`id`, `user_id`, `title`, `slug`, `description`, `category`, `cover_photo`, `whatsapp_number`, `status` ['draft', 'published', `suspended`], `timestamps`, `softDeletes`).
- **`project_gallery`**: Extra photo gallery (`id`, `project_id`, `image_path`, `timestamps`).

---

## 6. AI Execution Guidelines
- Respond directly with clean, type-hinted, production-ready code.
- Omit unnecessary explanations, conversational fluff, and generic Laravel theory.
- Strictly adhere to Laravel 11 and PHP 8.4 modern features (`fn`, strict typing, modern validation attributes).