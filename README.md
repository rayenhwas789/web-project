# 🎮 NEXUS Esports — v2

A full-stack esports platform with a PHP/MySQL backend, admin dashboard, tournament bookings, merch shop, and user accounts.

---

## 🚀 Quick Start (XAMPP)

### 1. Install XAMPP
Download from [apachefriends.org](https://www.apachefriends.org/) and install it.

### 2. Copy the project
Place the entire `nexus-esports-v2-db` folder inside:
- **Windows:** `C:\xampp\htdocs\`
- **Mac:** `/Applications/XAMPP/htdocs/`

### 3. Start XAMPP
Open XAMPP Control Panel → Start **Apache** and **MySQL**.

### 4. Import the database
1. Open your browser → go to `http://localhost/phpmyadmin`
2. Click **Import** (top menu)
3. Choose `schema.sql` from the project folder
4. Click **Go**

That's it — the database is created and filled with sample data automatically.

### 5. Open the site
Go to: `http://localhost/nexus-esports-v2-db/`

---

## 🔑 Login Credentials

| Role  | Email / Username | Password  |
|-------|-----------------|-----------|
| Admin | `admin@nexus.gg` | `admin123` |
| User  | `demo@nexus.gg`  | `nexus123` |

Admin login is hidden — on the login page, scroll to the bottom and click **"Admin Access"**.

---

## 📁 Project Structure

```
nexus-esports-v2-db/
├── index.html          ← Homepage
├── login.html          ← Login + Register (+ hidden Admin access)
├── booking.html        ← Tournament registration
├── tournaments.html    ← Tournaments list
├── profile.html        ← User profile
├── highlights.html     ← Video highlights
├── admin.html          ← Admin dashboard
├── db.php              ← Database config (edit credentials here)
├── schema.sql          ← Full DB schema + seed data
├── styles.css          ← Global styles
├── api/
│   ├── admin_stats.php
│   ├── admin_users.php
│   ├── admin_products.php
│   ├── admin_bookings.php
│   ├── admin_orders.php
│   ├── login.php
│   └── register.php
├── assets/             ← Videos and images
└── imgs/               ← Player and team images
```

---

## 🛢️ Database Tables

| Table      | Contents                              |
|------------|---------------------------------------|
| `users`    | 15 seed users (1 admin + 14 players)  |
| `products` | 15 merch/gear items                   |
| `bookings` | 25 tournament registrations           |
| `orders`   | 40 shop orders across all statuses    |

---

## ⚙️ Configuration

Edit `db.php` if your MySQL credentials are different from the defaults:

```php
define('DB_USER', 'root');   // your MySQL username
define('DB_PASS', '');       // your MySQL password (empty by default in XAMPP)
```

---

## 📤 Deploying to GitHub

1. Create a new repository on [github.com](https://github.com)
2. Open a terminal/command prompt in the project folder
3. Run these commands:

```bash
git init
git add .
git commit -m "Initial commit — NEXUS Esports v2"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git
git push -u origin main
```

> ⚠️ **Note:** `db.php` is in `.gitignore` so your database credentials won't be pushed. When setting up on another machine, copy `db.php` manually.

---

## 🖥️ Moving to Another PC

1. Copy the whole project folder to the new PC's XAMPP `htdocs`
2. Import `schema.sql` in phpMyAdmin (same steps as above)
3. That's it — all the seed data (users, products, orders, bookings) will be there

---

*Built with HTML, CSS, JavaScript, PHP, and MySQL.*
