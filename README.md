# NEXUS Esports — v2

## What changed

### 1. Login Page (`login.html`)
- Login + Register on the **same page** (tab switcher)
- **Admin login hidden** behind a subtle "Admin Access" link at the bottom of the Sign In form — same URL, no separate page
- Admin credentials: `admin` / `admin123`
- User demo: `demo@nexus.gg` / `nexus123`

### 2. Sidebar
- Sections: **Navigation**, **Esports**, **Account**
- Section labels separate groups visually
- "Register" and "Login" merged into one **Login / Register** link
- Admin link removed from public sidebar (access via Login page)

### 3. Database (phpMyAdmin)
- `schema.sql` — import into phpMyAdmin to create all tables
- `db.php` — PDO connection config (edit credentials)

## phpMyAdmin Setup

1. Open phpMyAdmin (`http://localhost/phpmyadmin`)
2. Click **Import** → choose `schema.sql` → Go
3. Edit `db.php` with your MySQL username/password
4. Include `require_once 'db.php';` in any PHP page that needs DB access

## Tables
| Table | Purpose |
|-------|---------|
| `users` | Registered users + admin accounts |
| `products` | Merch / shop items |
| `bookings` | Tournament registrations |
| `orders` | Shop orders |

## Running locally
Place the project in your XAMPP/WAMP `htdocs` folder, import `schema.sql`, and open `http://localhost/nexus-esports-v2/`.
