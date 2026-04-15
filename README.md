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

## UI Design and Layout

Unibites uses a simple, clean design approach that keeps the interface consistent across every role and module. The application emphasizes readability and usability with a neutral color palette, clear spacing, and consistent controls.

The landing/dashboard view for users is designed to make food ordering fast and intuitive:

- Top navigation includes the Unibites logo and quick access to login and signup.
- Central shop/menu access lets users select a campus canteen and browse available items.
- The main screen highlights the project purpose with a clear statement of the ordering flow and user benefits.
- Prominent action buttons such as `Browse Menu`, `View Cart`, and `Order Status` guide users through the process.

Consistency is maintained across the app by using the same UI patterns for:

- forms and buttons
- alerts and feedback messages
- menu/item cards
- dashboard tables and status indicators

This design helps users move smoothly between the user, shop, and admin interfaces while keeping the overall experience simple and focused.

## UI Interface Features

### Login / Signup Pages
- User login: Allows campus users to access the ordering system.
- Shop login: Enables canteen staff to manage orders and menu items.
- Admin login: Grants administrators access to overall system controls.
- Signup form with validation: Ensures new users provide valid details before registration.

### User Dashboard
- Shop selection: Lets users choose a canteen/shop to order from.
- Menu browsing: Displays available food items with price and availability.
- Search/filter items: Helps users find menu items quickly.
- Featured/new items: Highlights recently added or popular products.

### Menu / Cart Interface
- Add/remove item buttons: Allows users to update cart contents easily.
- Quantity controls: Supports changing quantity for each selected item.
- Cart summary with total price: Shows order totals before checkout.
- Checkout button: Starts the order placement and payment process.

### Order Flow
- Payment form: Collects payment method and basic transaction details.
- Order confirmation page: Shows the final order summary and confirmation.
- Receipt display: Provides a printable or viewable receipt after payment.
- Token generation/status screen: Displays the order token number and current status.

### Shop Dashboard
- Order list with statuses: Shows all incoming orders and their progress.
- Menu management UI: Lets shop staff add, edit, or remove menu items.
- Add/edit item form: Supports updating item details and availability.
- Upload item images: Enables adding pictures for menu products.

### Admin Dashboard
- All orders overview: Displays overall order activity across shops.
- Sales reports: Shows sales data for tracking revenue.
- User/shop management: Provides admin controls for users and shop entries.
- Filters and export buttons: Lets admin filter records and export reports.

### Common UI Elements
- Header/navigation bar: Provides easy navigation across all pages.
- Footer with contact/project info: Displays project and contact details.
- Alerts/messages for success or errors: Informs users about form results and actions.
- Responsive layout for mobile: Ensures the app works on phones and tablets.

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

## 10. References for Unibites

The following websites and tools were used while developing the Unibites project.

### Websites Referenced

11. World Wide Web Consortium (W3C)

Website: https://www.w3.org

Description: Official documentation source for HTML and CSS standards.

12. W3Schools

Website: https://www.w3schools.com

Description: Used for learning syntax and examples of web technologies.

13. Stack Overflow

Website: https://stackoverflow.com

Description: Used for debugging errors and solving coding issues.

14. GitHub

Website: https://github.com

Description: Used for version control and project file management.

15. Google Developers

Website: https://developers.google.com

Description: Used for understanding web development best practices.

16. GeeksforGeeks

Website: https://www.geeksforgeeks.org

Description: Used for programming and algorithm explanations.

17. Microsoft Learn

Website: https://learn.microsoft.com

Description: Used for learning development tools and software concepts.

18. PHP Manual

Website: https://www.php.net/manual/en/

Description: Used for PHP function reference and syntax.

19. MySQL Reference Manual

Website: https://dev.mysql.com/doc/

Description: Used for query and database reference.

20. XAMPP Documentation

Website: https://www.apachefriends.org/docs/

Description: Used for local server installation and configuration.

### Development Tools Referenced

21. Visual Studio Code

Description: Used for writing and managing project code.

22. Google Chrome

Description: Used for testing and debugging the application.

23. Mozilla Firefox

Description: Used for browser compatibility testing.

24. Microsoft Edge

Description: Used for cross-platform validation.

### Research and Documentation Sources

25. Agile Alliance

Website: https://www.agilealliance.org

Description: Used for understanding agile development practices and project planning.

26. Scrum Guides

Website: https://scrumguides.org

Description: Used for research on agile methodologies and team workflows.

27. IEEE Software Engineering Standards

Description: Used for software engineering best practices and standards.

28. ISO Software Development Guidelines

Description: Used for standard development methodologies and quality guidelines.

29. Software Testing Help

Website: https://www.softwaretestinghelp.com

Description: Used for research on software testing strategies and validation.

30. Selenium Documentation

Description: Used for automated testing reference.

31. Git Documentation

Website: https://git-scm.com/docs

Description: Used for documentation on version control workflows.

32. Bootstrap Documentation

Website: https://getbootstrap.com

Description: Used for layout and responsive design patterns.

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
