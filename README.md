# Unibites

UniBites is a PHP + MySQL web application for campus canteen ordering. It supports three roles (user, shop, admin), order tokens, shop dashboards, menu management, and a basic payment flow.

## Tech Stack

- PHP (procedural + sessions)
- MySQL/MariaDB
- XAMPP (Apache + MySQL)
- HTML/CSS/JS (inline per page)

## Project Structure

- `index.php`: entry point
- `login.php`, `signup.php`: auth pages
- `login_process.php`, `signup_process.php`: auth logic
- `forgot_password.php`, `reset_password.php`: password reset flow
- `config/db.php`: DB connection
- `config/schema.php`: auto-create / alter core tables
- `user/`: customer flows (browse, cart, pay, receipt)
- `shop/`: shop dashboard, orders, menu management
- `admin/`: admin dashboard, sales, users, shops
- `assets/`: images, CSS, static assets
- `database/unibites.sql`: DB dump

## Requirements

- XAMPP (Apache + MySQL)
- PHP (via XAMPP)
- Git

## Project Location

Keep the project in:

`C:\xampp\htdocs\Unibites`

## Database Setup

The app is configured in `config/db.php` to use:

- Host: `127.0.0.1`
- User: `root`
- Password: ``
- Database: `unibites`
- Port: `3307`

### Import database dump

1. Start Apache and MySQL from XAMPP Control Panel.
2. Create database `unibites` (if not already created).
3. Import `database/unibites.sql` using phpMyAdmin.

### Auto schema creation

Core tables are created or updated on-demand when pages run. The helper functions live in `config/schema.php` and are called in user/shop/admin pages.

## Run the Project

Open in browser:

`http://localhost/Unibites/`

## Roles and Access

The system uses role-based sessions:

- `user`: regular customer
- `shop`: canteen/shop staff
- `admin`: system admin

Role is stored in the `users` table and checked on every dashboard page.

## Core Logic and Flows

### Authentication

Files:
- `login.php`, `login_process.php`
- `signup.php`, `signup_process.php`
- `forgot_password.php`, `reset_password.php`
- `logout.php`

Logic:
- Login supports email for all roles, and shop ID (name) for shop login.
- Passwords are hashed with `password_hash`.
- Sessions store `user_id`, `name`, and `role`.
- Users can reset password via token stored in `user_tokens` (1 hour validity).

### User Flow (Browse → Cart → Pay → Token)

Files:
- `user/dashboard.php`
- `user/menu.php` (fixed seed menu)
- `user/new_items.php` (shop-added items)
- `user/cart.php`
- `user/place_order.php`
- `user/receipt.php`
- `user/mytoken.php`
- `user/reorder.php`

Logic highlights:
- A user can only order from one shop at a time (`cart_shop` session).
- `menu.php` shows seeded base menu for each shop.
- `new_items.php` shows items added by shops in the dashboard.
- Availability window is enforced using `available_from` / `available_to`.
- Cart stores line items in `$_SESSION['cart']` with quantity and price.
- On payment submit, `place_order.php` generates a daily token per shop and inserts the order.
- Token numbers reset daily per shop by checking `DATE(created_at) = CURDATE()`.
- Orders are marked `READY` if all items are `auto_ready`, otherwise `PLACED`.
- Receipts and order history are auto-refreshed periodically in the UI.

### Payment (Current Implementation)

Files:
- `user/cart.php`
- `user/place_order.php`

Logic:
- Payment is a simplified flow that records `payment_method`, `payment_status`, and `payment_ref`.
- Supported methods: `UPI`, `CARD`.
- **Important:** Real payment gateway integration is not added yet. No sensitive data should be stored in DB.

### Shop Dashboard

Files:
- `shop/dashboard.php`
- `shop/orders.php`
- `shop/menu_manage.php`

Logic:
- Shop mapping is derived from the logged-in shop name.
- Orders view supports filtering by status and auto-refresh.
- Shops can cancel orders unless already completed/cancelled.
- Menu management allows add/edit of items, availability window, and auto-ready flag.
- Shop can upload item images directly in dashboard.

### Admin Dashboard

Files:
- `admin/dashboard.php`
- `admin/sales.php`
- `admin/orders.php`
- `admin/users.php`
- `admin/shops.php`

Logic:
- Admin can view all orders and filter by status.
- Sales report supports daily or monthly range.
- Sales exports to CSV (products or shop-wise).
- Admin can list all users and shops.

## Menu Data and Seeding

Seeded data:
- Base menus are hard-coded in `user/menu.php`.
- When a shop is first opened, the app seeds `menu_items` from those arrays.
- Shop-added items are stored with `is_seeded = 0` and shown in `user/new_items.php`.

## Image Upload for Shop Items

Location:
- Shop Dashboard → Manage Menu (`shop/menu_manage.php`)

How it works:
- Uploads images to `assets/uploads/menu/`.
- Path is saved in the `menu_items.image` field.
- User menus render images using this stored path.

Rules:
- Allowed formats: JPG, PNG, WebP
- Max size: 2MB

## Database Schema (Key Tables)

`users`
- id, name, last_name, email, password, role, status, created_at

`orders`
- id, user_id, shop_key, shop_label, token_no, token_code
- items_json, item_count, total_amount, status
- payment_method, payment_status, receipt_no, payment_ref
- created_at

`menu_items`
- id, shop_key, name, price, image
- is_available, available_from, available_to
- quality_note, auto_ready, is_seeded
- created_at, updated_at

`user_tokens`
- id, user_id, token, expires_at, created_at

## How to Use (Developer)

1. Clone repo into `C:\xampp\htdocs\Unibites`.
2. Start Apache + MySQL in XAMPP.
3. Create DB `unibites`.
4. Import `database/unibites.sql`.
5. Open `http://localhost/Unibites/`.

Test roles:
- Create a normal user via signup.
- Shop/admin accounts are in the SQL dump.

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
