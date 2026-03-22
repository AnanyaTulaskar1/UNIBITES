# Unibites

PHP + MySQL web project for the Unibites food ordering system.

## Requirements

- XAMPP (Apache + MySQL)
- PHP (via XAMPP)
- Git

## Project Location

Keep the project in:

`C:\xampp\htdocs\Unibites`

## Database Setup

The app is currently configured in [config/db.php](/c:/xampp/htdocs/Unibites/config/db.php) to use:

- Host: `127.0.0.1`
- User: `root`
- Password: ``
- Database: `unibites`
- Port: `3307`

### Import database dump

1. Start Apache and MySQL from XAMPP Control Panel.
2. Create database `unibites` (if not already created).
3. Import [database/unibites.sql](/c:/xampp/htdocs/Unibites/database/unibites.sql) using phpMyAdmin.

## Run the Project

Open in browser:

`http://localhost/Unibites/`

## GitHub Repository

Remote URL:

`https://github.com/AnanyaTulaskar1/UNIBITES.git`

## Daily Git Workflow

From project root:

```powershell
git add .
git commit -m "Describe your change"
git push
```

If Git says "nothing to commit", no file changed since last commit.

## Update Database Dump

Run this whenever DB changes:

```powershell
cd C:\xampp\htdocs\Unibites
mkdir database -Force
& "C:\xampp\mysql\bin\mysqldump.exe" -u root -P 3307 unibites > database\unibites.sql
git add database/unibites.sql
git commit -m "Update database dump"
git push
```

## Clone on Another System

```powershell
git clone https://github.com/AnanyaTulaskar1/UNIBITES.git
```

Then place it in `htdocs`, import `database/unibites.sql`, start XAMPP, and open:

`http://localhost/Unibites/`
