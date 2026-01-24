# 5-Level Affiliate Commission Payout System

## 📋 Overview
A Laravel-based affiliate commission system with 5-level hierarchy support. When a user makes a sale, commissions are automatically distributed to their upline (up to 5 levels) using database transactions.

---

## 🗄️ DATABASE SCHEMA

### SQL Schema (MySQL)

```sql
-- ============================================
-- TABLE: tbl_users (Self-referencing hierarchy)
-- ============================================
CREATE TABLE tbl_users (
    pk_bint_user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vchr_name VARCHAR(100) NOT NULL,
    vchr_email VARCHAR(100) NOT NULL UNIQUE,
    fk_bint_parent_id BIGINT UNSIGNED NULL,
    tim_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tim_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Self-referencing foreign key
    FOREIGN KEY (fk_bint_parent_id) 
        REFERENCES tbl_users(pk_bint_user_id) 
        ON DELETE CASCADE,
    
    INDEX idx_parent (fk_bint_parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: tbl_sales
-- ============================================
CREATE TABLE tbl_sales (
    pk_bint_sale_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fk_bint_user_id BIGINT UNSIGNED NOT NULL,
    dec_amount DECIMAL(10, 2) NOT NULL,
    tim_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (fk_bint_user_id) 
        REFERENCES tbl_users(pk_bint_user_id) 
        ON DELETE CASCADE,
    
    INDEX idx_user (fk_bint_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: tbl_commissions
-- ============================================
CREATE TABLE tbl_commissions (
    pk_bint_commission_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fk_bint_sale_id BIGINT UNSIGNED NOT NULL,
    fk_bint_user_id BIGINT UNSIGNED NOT NULL,
    int_level INT NOT NULL,
    dec_percentage DECIMAL(5, 2) NOT NULL,
    dec_amount DECIMAL(10, 2) NOT NULL,
    tim_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (fk_bint_sale_id) 
        REFERENCES tbl_sales(pk_bint_sale_id) 
        ON DELETE CASCADE,
    
    FOREIGN KEY (fk_bint_user_id) 
        REFERENCES tbl_users(pk_bint_user_id) 
        ON DELETE CASCADE,
    
    INDEX idx_sale_user (fk_bint_sale_id, fk_bint_user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insert Root User (Parent to all)
-- ============================================
INSERT INTO tbl_users (vchr_name, vchr_email, fk_bint_parent_id) 
VALUES ('Root Admin', 'root@system.com', NULL);
```

---

## 💰 COMMISSION STRUCTURE

| Level | Relationship | Commission Rate |
|-------|-------------|-----------------|
| 1 | Direct Parent | 10% |
| 2 | Grandparent | 5% |
| 3 | Great-Grandparent | 3% |
| 4 | Great-Great-Grandparent | 2% |
| 5 | Great-Great-Great-Grandparent | 1% |

**Total Commission Distributed:** 21% of sale amount

---

## 🚀 SETUP INSTRUCTIONS

### 1. Environment Configuration
```bash
# Copy .env.example to .env
cp .env.example .env

# Update database credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=commissionpayoutsystem
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Create Database
```sql
CREATE DATABASE commissionpayoutsystem;
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Seed Root User
```bash
php artisan db:seed --class=RootUserSeeder
```

### 7. Start Development Server
```bash
php artisan serve
```

### 8. Access Application
Open browser: `http://localhost:8000`

---

## 📁 PROJECT STRUCTURE

```
app/
├── Models/
│   ├── User.php              # User model with self-referencing relationships
│   ├── Sale.php              # Sale model
│   └── Commission.php        # Commission model
├── Services/
│   └── CommissionService.php # Core business logic with transactions
└── Http/Controllers/
    ├── UserController.php    # User creation & sale processing
    └── DashboardController.php # Dashboard display

database/
├── migrations/
│   ├── *_create_tbl_users_table.php
│   ├── *_create_tbl_sales_table.php
│   └── *_create_tbl_commissions_table.php
└── seeders/
    └── RootUserSeeder.php

resources/views/
├── layout.blade.php          # Base layout (Black & Blue theme)
├── dashboard.blade.php       # Commission dashboard
└── users/
    └── create.blade.php      # Add user form
```

---

## 🔧 CORE LOGIC EXPLANATION

### CommissionService.php - Transaction Flow

```php
DB::beginTransaction();  // Start transaction

try {
    // 1. Create sale record
    $sale = Sale::create([...]);
    
    // 2. Traverse upward through parent hierarchy
    $currentUser = User::find($userId);
    $level = 1;
    
    while ($currentUser && $currentUser->fk_bint_parent_id && $level <= 5) {
        $parentUser = User::find($currentUser->fk_bint_parent_id);
        
        // 3. Calculate commission for this level
        $percentage = COMMISSION_RATES[$level];
        $commissionAmount = ($saleAmount * $percentage) / 100;
        
        // 4. Store commission record
        Commission::create([...]);
        
        // 5. Move to next parent
        $currentUser = $parentUser;
        $level++;
    }
    
    DB::commit();  // Commit if all succeed
    
} catch (Exception $e) {
    DB::rollBack();  // Rollback on any error
}
```

---

## 🧪 TESTING THE SYSTEM

### Test Scenario: 5-Level Hierarchy

#### Step 1: Create User Hierarchy
1. Go to **Add User** page
2. Create users in this order:

```
Root Admin (ID: 1)
└── User A (ID: 2, Parent: Root Admin)
    └── User B (ID: 3, Parent: User A)
        └── User C (ID: 4, Parent: User B)
            └── User D (ID: 5, Parent: User C)
                └── User E (ID: 6, Parent: User D)
                    └── User F (ID: 7, Parent: User E)
```

#### Step 2: Record a Sale
1. Create **User F** with Parent = **User E**
2. Enter Sale Amount: **$1000.00**
3. Click "Create User & Process Sale"

#### Step 3: Verify Commission Distribution

Expected commissions for $1000 sale by User F:

| User | Level | Rate | Commission |
|------|-------|------|------------|
| User E | 1 | 10% | $100.00 |
| User D | 2 | 5% | $50.00 |
| User C | 3 | 3% | $30.00 |
| User B | 4 | 2% | $20.00 |
| User A | 5 | 1% | $10.00 |
| Root Admin | 6 | 0% | $0.00 (Beyond level 5) |

**Total Distributed:** $210.00

#### Step 4: Check Dashboard
1. Go to **Dashboard**
2. Verify each user's total commission matches expected values

---

## 🔍 VALIDATION & SECURITY

### Input Validation
- ✅ User name: Required, max 100 characters
- ✅ Email: Required, valid email format, unique
- ✅ Parent ID: Required, must exist in database
- ✅ Sale amount: Optional, numeric, minimum 0

### Security Features
- ✅ Laravel CSRF protection on all forms
- ✅ Eloquent ORM prevents SQL injection
- ✅ Database transactions ensure data integrity
- ✅ Foreign key constraints maintain referential integrity
- ✅ Prepared statements via PDO

---

## 🎨 UI FEATURES

- **Color Scheme:** Black & Blue gradient theme
- **Responsive Design:** Works on all screen sizes
- **No Authentication:** Direct access (as per requirements)
- **Two Main Pages:**
  1. **Dashboard:** View all users and total commissions
  2. **Add User:** Create users and record sales

---

## 📊 DATABASE QUERIES

### Get Total Commission for a User
```sql
SELECT SUM(dec_amount) as total_commission
FROM tbl_commissions
WHERE fk_bint_user_id = ?;
```

### Get Commission Breakdown by Level
```sql
SELECT int_level, COUNT(*) as count, SUM(dec_amount) as total
FROM tbl_commissions
WHERE fk_bint_user_id = ?
GROUP BY int_level
ORDER BY int_level;
```

### Get User Hierarchy Path
```sql
WITH RECURSIVE hierarchy AS (
    SELECT pk_bint_user_id, vchr_name, fk_bint_parent_id, 0 as level
    FROM tbl_users
    WHERE pk_bint_user_id = ?
    
    UNION ALL
    
    SELECT u.pk_bint_user_id, u.vchr_name, u.fk_bint_parent_id, h.level + 1
    FROM tbl_users u
    INNER JOIN hierarchy h ON u.pk_bint_user_id = h.fk_bint_parent_id
)
SELECT * FROM hierarchy;
```

---

## 🐛 TROUBLESHOOTING

### Issue: Migration fails
**Solution:** Ensure MySQL is running and database exists
```bash
mysql -u root -p
CREATE DATABASE commissionpayoutsystem;
```

### Issue: Foreign key constraint error
**Solution:** Run migrations in correct order (users → sales → commissions)
```bash
php artisan migrate:fresh
php artisan db:seed --class=RootUserSeeder
```

### Issue: Root user not found
**Solution:** Run the seeder
```bash
php artisan db:seed --class=RootUserSeeder
```

---

## 📝 NAMING CONVENTIONS FOLLOWED

| Element | Prefix | Example |
|---------|--------|---------|
| Table | tbl_ | tbl_users |
| Primary Key | pk_bint_ | pk_bint_user_id |
| Foreign Key | fk_bint_ | fk_bint_parent_id |
| Varchar Column | vchr_ | vchr_name |
| Integer Column | int_ | int_level |
| Decimal Column | dec_ | dec_amount |
| Timestamp Column | tim_ | tim_created_at |

---

## ✅ REQUIREMENTS CHECKLIST

- ✅ Self-referencing users table with parent_id
- ✅ Unlimited depth hierarchy support
- ✅ Commission distribution up to 5 levels only
- ✅ Correct commission rates (10%, 5%, 3%, 2%, 1%)
- ✅ Laravel framework with Eloquent ORM
- ✅ PDO with prepared statements (via Eloquent)
- ✅ Database transactions (BEGIN, COMMIT, ROLLBACK)
- ✅ Input validation and security
- ✅ Black & Blue UI theme
- ✅ No login/logout pages
- ✅ Root user seeded in database
- ✅ Add user page with sale amount input
- ✅ Dashboard showing total commissions
- ✅ Clean, interview-quality code
- ✅ Inline comments explaining logic
- ✅ Proper naming conventions

---

## 🎯 KEY FEATURES

1. **Atomic Operations:** Sale + commissions created in single transaction
2. **Automatic Distribution:** Commissions calculated and stored automatically
3. **Level Limiting:** Only 5 levels receive commission (6+ get nothing)
4. **Referential Integrity:** Cascading deletes maintain data consistency
5. **Real-time Dashboard:** View all commissions instantly
6. **Simple UI:** No authentication required, direct access

---

## 📞 SUPPORT

For issues or questions, check:
- Laravel Documentation: https://laravel.com/docs
- MySQL Documentation: https://dev.mysql.com/doc/

---

**Built with Laravel 10.x | PHP 8.x | MySQL 8.x**
