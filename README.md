# Cit-E Cycling Web Portal

A complete, working rebuild of the Cit-E Cycling admin portal: register interest,
admin login/logout, edit/delete participants, and search participants & clubs.
Styled with Bootstrap 5 + a custom "speed stripe" design system, fully responsive.

## Quick start (XAMPP / WAMP / MAMP)

1. Copy the whole `cycling` folder into your server's web root
   (e.g. `htdocs/cycling` for XAMPP).
2. Start Apache and MySQL from the XAMPP/WAMP control panel.
3. Open **phpMyAdmin**, create a database called `cycling`, and import
   `cycling.sql` into it (Import tab → choose file → Go). Do not edit the
   table structure — the app relies on it exactly as supplied.
4. Open `dbconnect.php` and check the four values match your local setup.
   The defaults already match a stock XAMPP install:
   ```php
   $servername = "localhost";
   $port       = "3306";
   $username   = "root";
   $password   = "";
   $database   = "cycling";
   ```
5. Visit `http://localhost/cycling/index.html` in your browser.

## Admin login

- Username: `admin`
- Password: `password123`

(These match the row already in the `user` table — nothing else needs
configuring.)

## What was built

- **Register interest** (`register_form.html` → `register.php`) — every
  field (first name, surname, email, terms checkbox) is required. The form
  is validated live in the browser, and re-validated on the server, so it
  cannot be submitted — or saved to the database — while any field is
  blank, even if JavaScript is disabled. Duplicate email addresses are
  rejected with a friendly message.
- **Login / logout** (`admin_login.html` → `login.php`, `logout.php`) —
  checks the submitted credentials against the `user` table and starts a
  PHP session. Every restricted page checks that session before rendering
  anything, and redirects back to the login page if it's missing.
- **Edit participant scores** (`edit_participant.php` /
  `edit_participant_form.php`) — only power output and distance can be
  changed; name fields are shown but disabled. Both values must be
  non-negative numbers.
- **Delete participants** (`delete.php`) — shows a confirmation page with
  the participant's details and requires the admin to type `DELETE` before
  the record is removed, to guard against accidental clicks.
- **Search** (`search_form.php` / `search_result.php`) — search
  participants by first name or surname (with the match highlighted), or
  search clubs by name to see the full roster plus total and average power
  output / distance for that club.
- **Validation** — every form is validated client-side (instant feedback)
  and server-side (so it can't be bypassed), with all database queries
  using PDO prepared statements to prevent SQL injection, and all output
  escaped with `htmlspecialchars()` to prevent XSS.
- **Styling** — Bootstrap 5 plus a custom design system (`assets/css/style.css`)
  with its own colour palette, type scale and components, fully responsive
  down to mobile.

## Extra features added

- A small admin dashboard on `admin_menu.php` with live counts
  (participants, clubs, sign-ups, average power output).
- A filter box on the participants list to quickly narrow by name or club.
- One-time flash messages (e.g. "participant updated", "logged out").
- A typed `DELETE` safety check before any participant is removed.
- Duplicate-registration protection on the interest form.
- Consistent, mobile-friendly navigation bar that adapts to whether an
  admin is logged in.

## Notes for assessment

- No filenames were changed and the database structure was not modified —
  `cycling.sql` is byte-for-byte the file you were given.
- A few new files were added (they don't replace anything): `logout.php`,
  `dbconnect.php` was filled in with working local defaults, plus
  `includes/` (shared header, footer, auth and DB-connection helpers) and
  `assets/` (CSS and JS). These keep the code DRY without touching any of
  the required page filenames or their linking.
