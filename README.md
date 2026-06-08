Here is the README content for your project. You can click the copy button in the top right corner of the code block and paste it directly into your `README.md` file.

```markdown
# T-REX Fitness Membership Management System

A comprehensive, web-based management system designed specifically for fitness clubs and gyms. Built with PHP and MySQL, this application streamlines the administration of members, trainers, memberships, and workout plans.

## Features

* **Admin Dashboard & Security:** Secure login system to ensure only authorized personnel can access or modify club records.
* **Member Management:**
  * Add, view, edit, and delete members.
  * Automatic tracking of membership durations (Monthly, Quarterly, Yearly) with dynamic expiry date calculation.
* **Trainer Management:**
  * Manage gym trainers, their contact information, and their specific fitness specializations.
* **Workout Plans:**
  * Assign and track customized daily workout routines for members.
  * Specify exercises, target sets, reps, and the responsible trainer.
* **Robust Search:** Quickly find members by searching across names, email addresses, phone numbers, or membership types.
* **Strict Data Validation:** 
  * Real-time JavaScript validation prevents duplicate emails and phone numbers before the form is submitted.
  * Enforces strict 10-digit phone number formatting and standard email structures.
  * Comprehensive server-side (PHP) validation acts as a secure fallback.
* **Attendance Checking:** Quickly verify if a member's subscription is currently `Active` or `Expired`.
* **SMS Notifications (Script):** Includes a backend script designed to integrate with SMS APIs to automatically notify members whose subscriptions have expired.

## Tech Stack

* **Frontend:** HTML5, Vanilla CSS, JavaScript (AJAX/Fetch API for real-time validation)
* **Backend:** PHP (Procedural/Prepared Statements)
* **Database:** MySQL

## Installation & Setup

1. **Prerequisites:** 
   * Ensure you have a local server environment installed like [XAMPP](https://www.apachefriends.org/), WAMP, or MAMP.
2. **Clone the Repository:**
   * Place the `trex` project folder into your web server's root directory (e.g., `C:\xampp\htdocs\trex`).
3. **Database Setup:**
   * Open phpMyAdmin or your MySQL command line.
   * Import the provided `fitness_club.sql` file. This will automatically create the `fitness_club` database, build the required tables, and insert initial sample data (including the default admin account).
4. **Configuration:**
   * If your MySQL database uses a password for the `root` user, update the database connection string in `config/db.php`.
5. **Run the Application:**
   * Start your Apache and MySQL servers.
   * Open your web browser and navigate to: `http://localhost/trex/public/login.php`

## Default Credentials
* **Username:** `admin`
* **Password:** `admin123`
*(Note: It is highly recommended to change these credentials in a production environment).*

## Project Structure

* `/assets` - Contains all CSS stylesheets and JavaScript files (validation scripts).
* `/config` - Database connection configuration (`db.php`).
* `/includes` - Reusable layout components (`header.php`, `footer.php`).
* `/public` - Main application logic, UI pages, and API endpoints (login, dashboard, add/edit forms, search, workouts).
* `fitness_club.sql` - Database schema and mock data dump.
```
