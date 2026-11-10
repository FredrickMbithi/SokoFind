# SokoFind — Kirinyaga University Lost & Found

A responsive PHP/MySQL campus lost-and-found application for the SPI 2301 semester project. Students can publish lost or found listings, claim a found item, review claims on their own listings, and view live community reports.

## Features

- Account registration and sign-in with password hashing, session rotation, and CSRF protection.
- Search and filter open listings by type and category.
- Create, read, update, and delete listings; owners can mark items returned or reopen them.
- Claim workflow: a claimant sends a verification message; the listing owner accepts or declines it.
- Three live reports: listings by category, monthly activity, and status summary with claim totals.
- Responsive pages, browser form validation, and server-side validation.
- PDO prepared statements, modular PHP includes, arrays/functions/control structures, cookies via PHP sessions.

## Requirements

- PHP 8.1 or later with `PDO`, `pdo_mysql`, `mbstring`, and sessions enabled.
- MySQL 5.7+ or MariaDB 10.4+.
- Apache or a compatible web server. Configure the web document root to `public/` where possible.

## Local setup with Docker (recommended)

This starts PHP 8.3 with `pdo_mysql` and `mbstring`, plus MySQL 8.4. It does not change the PHP installed on your computer. Docker Desktop/Engine and Compose are required.

1. From this folder, run `docker compose up -d --build`. The first run downloads the official PHP and MySQL images and initializes the database schema.
2. Open <http://localhost:8080>. Register users and exercise listings, claims, and reports.
3. Stop the containers with `docker compose down`. The database persists in the `sokofind_mysql` volume. Use `docker compose down -v` only when you intend to erase the local database.

The database credentials in `compose.yaml` are local development values only. Replace them for any hosted deployment; do not reuse them in production. The database port is not exposed to the host, and the web app binds only to localhost.

## Local setup

1. Create a MySQL database and import `sql/schema.sql` (the script creates `sokofind`).
2. Copy `app/config.local.example.php` to `app/config.local.php` and set the database host, database name, username, and password. This file is ignored by Git.
3. Serve the `public/` directory as the web root. For a quick local preview, from the project directory run:

   ```sh
   php -S 127.0.0.1:8000 -t public
   ```

4. Open `http://127.0.0.1:8000`, register two accounts, then create a listing and exercise the claim workflow.

The project uses Nairobi time by default. Set `APP_APP_TIMEZONE` in `config.local.php` if needed.

## Deployment guide (GitHub + free PHP/MySQL hosting)

The public source repository is [FredrickMbithi/SokoFind](https://github.com/FredrickMbithi/SokoFind). Add the live HTTPS URL after hosting is configured. Hosting providers change their free plans; select a service that currently offers PHP 8.1+, MySQL/MariaDB, HTTPS, and a persistent database.

1. The project source is in [FredrickMbithi/SokoFind](https://github.com/FredrickMbithi/SokoFind). Keep `app/config.local.php` and all credentials out of Git.
2. Create a PHP site and MySQL database on the chosen host. Record the provider's database host, database name, database user, and password privately.
3. Import `sql/schema.sql` into the hosted database using phpMyAdmin or the provider's database tool.
4. Deploy the repository files. Set the document root to the `public/` folder. If the host only provides `public_html`, upload the contents of `public/` into `public_html/` and place the `app/` folder beside `public_html/` (one level above the public web root). Never put the database config file under a public directory.
5. Copy `app/config.local.example.php` to `app/config.local.php` on the server and enter the host's DB values. Keep this file outside Git and outside the public web root.
6. Enable HTTPS in the hosting panel. Open the live site, register a user, publish a test listing, search it, submit a claim from another account, review the claim as the listing owner, and inspect all three reports.
7. Remove test data before final demonstration if desired. Confirm that `app/config.local.php` is not visible from a browser.

- GitHub repository: https://github.com/FredrickMbithi/SokoFind
- Live application: **[Add HTTPS URL after hosting]**

## Suggested submission walkthrough

1. Register as a student and sign in.
2. Browse, search, and filter the campus board.
3. Post one Found item and one Lost item; edit one and delete a disposable test item.
4. Sign in with a second account and submit a claim to the Found item.
5. Return to the owner account and accept or decline the claim; demonstrate the resulting status.
6. Open Reports and explain the category, monthly activity, and status summaries.
7. Sign out and show that posting and reports require authentication.

## Four report test cases

| ID | Scenario | Expected result |
|---|---|---|
| TC-01 | Register with a valid name, email, and 8+ character password | Account is created, session starts, and user reaches the board |
| TC-02 | Submit a listing with a missing title, invalid category, or future date | Server rejects it and displays a helpful validation message |
| TC-03 | Search for a known open item and filter by Lost/Found and category | Only matching open listings appear; query values remain safely escaped |
| TC-04 | A second user claims an open found item; owner accepts the claim | Claim is stored, owner can review it, and item status changes to Claimed |

## Project layout

- `public/` — browser-accessible PHP pages and static assets.
- `app/` — shared bootstrap, database connection, validation helpers, and reusable view fragment.
- `sql/schema.sql` — MySQL schema with foreign keys, indexes, and claim uniqueness.
- `docs/` — report and project design materials.
