# Hospital Management System (PHP)

Hospital Management System is a PHP + MySQL web application for managing hospital operations such as patient registration, appointment booking, and an admin/doctor dashboard for managing core hospital data.

## Features
- Public website pages (Home, About, Doctors, Departments, Press Desk, Gallery, Contact, Feedback)
- User accounts with login/registration, password reset, and activation flow
- Appointment booking and appointment history
- Admin dashboard for managing doctors, departments, patients, appointments, feedback, etc.
- Doctor dashboard for managing appointments/visits and profile

## Tech Stack
- PHP (mysqli, sessions)
- MySQL / MariaDB
- Apache (or Laragon/XAMPP) with `mod_rewrite` enabled
- Front-end assets included in the repository (CSS/JS)

The repository also includes `vendor/` (PHPMailer) so you do not need Composer to run the app.

## Prerequisites
- PHP 8.x (SQL dump was generated with PHP 8.1.10)
- MySQL 8.x (SQL dump was generated with MySQL 8.0.30)
- Apache/Laragon/XAMPP with PHP and MySQL enabled

## Database Setup
1. Create a MySQL database named `KK_PATEL_HOSPITAL2`
   - Note: the SQL dump file is `kk_patel_hospital2.sql` (lowercase name in the dump header).
2. Import the database dump:
   - Use phpMyAdmin -> your created database -> **Import** -> select `kk_patel_hospital2.sql`
3. Verify the DB connection in `Database/connection.php`
   - Default values in this project:
     - Host: `localhost`
     - User: `root`
     - Password: *(blank / empty)*
     - Database: `KK_PATEL_HOSPITAL2`

If your MySQL credentials differ, update `Database/connection.php` accordingly.

## Running the Project Locally
1. Copy/keep this folder inside your web server root (for example, in Laragon's `www` folder).
2. Make sure Apache has `mod_rewrite` enabled (the project uses `.htaccess` rewrite rules).
3. Open your browser:
   - `http://localhost/<your-project-folder>/`

The app uses `index.php` as the main entry point.

## Login URLs
- User login: `login.php` (after login, user dashboard is under `User/index.php`)
- Admin login: `admin/kkadminlogin.php` (admin dashboard: `admin/index-2.php`)
- Doctor login: `Doctor/doctorlogin.php` (doctor dashboard is under `Doctor/index-2.php`)

## Demo (Animated)
If you want real app screenshots/gifs, replace these SVG files with your own images (keep the same filenames or update the links below).

![Loading spinner](readme-assets/loading-spinner.svg)

![Bounce heart](readme-assets/bounce-heart.svg)

![Appointment pulse](readme-assets/appointment-pulse.svg)

## Repository Notes
- Uploaded images/assets are stored inside the `admin/assets/uploads`, `Doctor/uploads`, and `User/uploads` folders (depending on the role).
- The SQL dump (`kk_patel_hospital2.sql`) contains pre-filled tables and hashed password values.
  - For testing, create accounts using the application UI, or update/reset credentials via the database and/or password reset flow.

## Project Report
- See `WP_PROJECT_GROUP_8_FUNCTIONALITY.pdf` for additional project documentation (if present in this repository).

## License
Add your license information here (MIT/Apache-2.0/etc.) if applicable.

