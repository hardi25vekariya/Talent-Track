Talent Track — Static Job Portal (Updated)

Overview

This is a small static job portal demo (HTML/CSS) focused on student job applications and admin job postings. The site is intentionally static with data embedded in HTML files for easy editing.

What I changed

- Rewrote `index.html` and `index.css` with a modern, responsive layout (header, hero, job cards, footer).
- Updated `job_listings.html` and `job_details.html` with curated Indian company job listings and realistic job descriptions.
- Updated application-related pages: `apply_job.html`, `my_applications.html`, and `view_applications.html` with consistent styling and example data.
- Kept original files and structure; edits are conservative and focused on look-and-feel and content.

How to preview locally

Since this is a static site, open any `.html` file directly in your browser. Recommended steps on Windows:

1. Open File Explorer and navigate to the project folder.
2. Double-click `index.html` to open it in your default browser.

Or, serve the folder quickly using Python (if installed):

```powershell
# from inside the project folder
python -m http.server 8000; Start-Process http://localhost:8000
```

Where to edit job data

- `job_listings.html` contains the main job cards shown on the Jobs page.
- `job_details.html` contains a sample detailed posting; duplicate or create new detail pages per job if you want unique pages.
- `index.html` shows featured jobs — update the cards there to highlight roles.
- For admin flows: `post_job.html` is a simple static form; to make it dynamic requires a backend.

Next steps you might want

- Add a tiny JSON file (jobs.json) and a small client-side script to load jobs dynamically into the listing page.
- Wire up a minimal backend (PHP/Node) to persist posted jobs and applications.
- Improve accessibility (ARIA attributes), keyboard navigation, and add unit tests for JS behavior.

If you'd like, I can:
- Convert the job data to a JSON file and add client-side code to render listings dynamically.
- Make `post_job.html` and `apply_job.html` functional with a simple Node/PHP backend.

Happy to continue — tell me which next step you prefer.

---
Database setup (MySQL) — login_system

1) Import the SQL dump
- Open phpMyAdmin or use the MySQL CLI and import `login_system.sql` included in the project.

2) Configure `db.php`
- Edit `db.php` and set `DB_CONFIGURED` to true, then update `DB_HOST`, `DB_USER`, `DB_PASS` and `DB_NAME` if needed.

3) Create a user or register via the site
- Use `register.html` to create a new user; when DB is configured the registration will insert into the `users` table.

4) Verify login events
- After logging in with `login.html`, check the `users.last_login` column and the `login_events` table in phpMyAdmin to see audit records.

Notes
- The project will fall back to a CSV file (`registrations.csv`) if DB is not configured. Set `DB_CONFIGURED` to true to force DB usage.
- For production, enable HTTPS and strengthen cookie/session handling.
