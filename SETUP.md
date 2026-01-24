# QUICK SETUP GUIDE

## Step 1: Create Database Manually

Open your MySQL client (phpMyAdmin, MySQL Workbench, or command line) and run:

```sql
CREATE DATABASE commissionpayoutsystem;
```

## Step 2: Run Migrations

```bash
php artisan migrate
```

## Step 3: Seed Root User

```bash
php artisan db:seed --class=RootUserSeeder
```

## Step 4: Start Server

```bash
php artisan serve
```

## Step 5: Access Application

Open browser: http://localhost:8000

---

## Alternative: Use SQL File Directly

You can also import the complete schema:

```bash
mysql -u root -p commissionpayoutsystem < database/schema.sql
```

Or import via phpMyAdmin:
1. Create database `commissionpayoutsystem`
2. Import file: `database/schema.sql`
3. Run: `php artisan serve`
