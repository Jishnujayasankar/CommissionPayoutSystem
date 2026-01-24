-- ============================================
-- 5-LEVEL AFFILIATE COMMISSION SYSTEM
-- Database Schema with Naming Conventions
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS commissionpayoutsystem;
USE commissionpayoutsystem;

-- ============================================
-- TABLE: tbl_users
-- Self-referencing hierarchy table
-- ============================================
DROP TABLE IF EXISTS tbl_commissions;
DROP TABLE IF EXISTS tbl_sales;
DROP TABLE IF EXISTS tbl_users;

CREATE TABLE tbl_users (
    pk_bint_user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key - User ID',
    vchr_name VARCHAR(100) NOT NULL COMMENT 'User full name',
    vchr_email VARCHAR(100) NOT NULL UNIQUE COMMENT 'User email address',
    fk_bint_parent_id BIGINT UNSIGNED NULL COMMENT 'Foreign key - Parent user ID (self-referencing)',
    tim_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    tim_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record update timestamp',
    
    -- Self-referencing foreign key constraint
    CONSTRAINT fk_users_parent 
        FOREIGN KEY (fk_bint_parent_id) 
        REFERENCES tbl_users(pk_bint_user_id) 
        ON DELETE CASCADE,
    
    -- Index for faster parent lookups
    INDEX idx_parent_id (fk_bint_parent_id),
    INDEX idx_email (vchr_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Users table with self-referencing parent-child hierarchy';

-- ============================================
-- TABLE: tbl_sales
-- Records all sales made by users
-- ============================================
CREATE TABLE tbl_sales (
    pk_bint_sale_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key - Sale ID',
    fk_bint_user_id BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key - User who made the sale',
    dec_amount DECIMAL(10, 2) NOT NULL COMMENT 'Sale amount in decimal format',
    tim_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Sale timestamp',
    
    -- Foreign key to users table
    CONSTRAINT fk_sales_user 
        FOREIGN KEY (fk_bint_user_id) 
        REFERENCES tbl_users(pk_bint_user_id) 
        ON DELETE CASCADE,
    
    -- Index for faster user sales lookup
    INDEX idx_user_id (fk_bint_user_id),
    INDEX idx_created_at (tim_created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Sales records table';

-- ============================================
-- TABLE: tbl_commissions
-- Stores commission distribution for each sale
-- ============================================
CREATE TABLE tbl_commissions (
    pk_bint_commission_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key - Commission ID',
    fk_bint_sale_id BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key - Sale that generated commission',
    fk_bint_user_id BIGINT UNSIGNED NOT NULL COMMENT 'Foreign key - User receiving commission',
    int_level INT NOT NULL COMMENT 'Level in hierarchy (1-5)',
    dec_percentage DECIMAL(5, 2) NOT NULL COMMENT 'Commission percentage',
    dec_amount DECIMAL(10, 2) NOT NULL COMMENT 'Commission amount',
    tim_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Commission creation timestamp',
    
    -- Foreign key to sales table
    CONSTRAINT fk_commissions_sale 
        FOREIGN KEY (fk_bint_sale_id) 
        REFERENCES tbl_sales(pk_bint_sale_id) 
        ON DELETE CASCADE,
    
    -- Foreign key to users table
    CONSTRAINT fk_commissions_user 
        FOREIGN KEY (fk_bint_user_id) 
        REFERENCES tbl_users(pk_bint_user_id) 
        ON DELETE CASCADE,
    
    -- Composite index for faster queries
    INDEX idx_sale_user (fk_bint_sale_id, fk_bint_user_id),
    INDEX idx_user_level (fk_bint_user_id, int_level),
    
    -- Constraint: Level must be between 1 and 5
    CONSTRAINT chk_level CHECK (int_level BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Commission distribution records';

-- ============================================
-- SEED DATA: Root User
-- ============================================
INSERT INTO tbl_users (vchr_name, vchr_email, fk_bint_parent_id) 
VALUES ('Root Admin', 'root@system.com', NULL);

-- ============================================
-- SAMPLE DATA: Test Hierarchy (Optional)
-- ============================================
-- Uncomment below to create a test hierarchy

/*
-- Level 1: Direct child of Root
INSERT INTO tbl_users (vchr_name, vchr_email, fk_bint_parent_id) 
VALUES ('User A', 'usera@test.com', 1);

-- Level 2
INSERT INTO tbl_users (vchr_name, vchr_email, fk_bint_parent_id) 
VALUES ('User B', 'userb@test.com', 2);

-- Level 3
INSERT INTO tbl_users (vchr_name, vchr_email, fk_bint_parent_id) 
VALUES ('User C', 'userc@test.com', 3);

-- Level 4
INSERT INTO tbl_users (vchr_name, vchr_email, fk_bint_parent_id) 
VALUES ('User D', 'userd@test.com', 4);

-- Level 5
INSERT INTO tbl_users (vchr_name, vchr_email, fk_bint_parent_id) 
VALUES ('User E', 'usere@test.com', 5);

-- Level 6 (will not receive commission)
INSERT INTO tbl_users (vchr_name, vchr_email, fk_bint_parent_id) 
VALUES ('User F', 'userf@test.com', 6);

-- Test Sale: $1000 by User F
-- Expected commissions:
-- User E (Level 1): $100 (10%)
-- User D (Level 2): $50 (5%)
-- User C (Level 3): $30 (3%)
-- User B (Level 4): $20 (2%)
-- User A (Level 5): $10 (1%)
-- Root Admin: $0 (Beyond level 5)

START TRANSACTION;

INSERT INTO tbl_sales (fk_bint_user_id, dec_amount) 
VALUES (7, 1000.00);

SET @sale_id = LAST_INSERT_ID();

INSERT INTO tbl_commissions (fk_bint_sale_id, fk_bint_user_id, int_level, dec_percentage, dec_amount) VALUES
(@sale_id, 6, 1, 10.00, 100.00),
(@sale_id, 5, 2, 5.00, 50.00),
(@sale_id, 4, 3, 3.00, 30.00),
(@sale_id, 3, 4, 2.00, 20.00),
(@sale_id, 2, 5, 1.00, 10.00);

COMMIT;
*/

-- ============================================
-- USEFUL QUERIES
-- ============================================

-- Get total commission for each user
-- SELECT 
--     u.pk_bint_user_id,
--     u.vchr_name,
--     u.vchr_email,
--     COALESCE(SUM(c.dec_amount), 0) as total_commission
-- FROM tbl_users u
-- LEFT JOIN tbl_commissions c ON u.pk_bint_user_id = c.fk_bint_user_id
-- GROUP BY u.pk_bint_user_id, u.vchr_name, u.vchr_email
-- ORDER BY total_commission DESC;

-- Get commission breakdown by level for a specific user
-- SELECT 
--     int_level,
--     COUNT(*) as commission_count,
--     SUM(dec_amount) as total_amount
-- FROM tbl_commissions
-- WHERE fk_bint_user_id = 1
-- GROUP BY int_level
-- ORDER BY int_level;

-- Get all sales with commission details
-- SELECT 
--     s.pk_bint_sale_id,
--     seller.vchr_name as seller_name,
--     s.dec_amount as sale_amount,
--     s.tim_created_at,
--     COUNT(c.pk_bint_commission_id) as commission_count,
--     SUM(c.dec_amount) as total_commission_paid
-- FROM tbl_sales s
-- JOIN tbl_users seller ON s.fk_bint_user_id = seller.pk_bint_user_id
-- LEFT JOIN tbl_commissions c ON s.pk_bint_sale_id = c.fk_bint_sale_id
-- GROUP BY s.pk_bint_sale_id, seller.vchr_name, s.dec_amount, s.tim_created_at
-- ORDER BY s.tim_created_at DESC;

-- ============================================
-- VERIFICATION
-- ============================================
SELECT 'Database schema created successfully!' as status;
SELECT COUNT(*) as root_user_count FROM tbl_users WHERE fk_bint_parent_id IS NULL;
