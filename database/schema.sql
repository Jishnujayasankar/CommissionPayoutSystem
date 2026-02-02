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
    INDEX idx_user_level (fk_bint_user_id, int_level)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Commission distribution records';

-- ============================================
-- TABLE: tbl_commission_levels
-- Dynamic commission rates configuration
-- ============================================
CREATE TABLE tbl_commission_levels (
    pk_bint_level_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Primary key - Level ID',
    int_level INT NOT NULL UNIQUE COMMENT 'Commission level (1-5)',
    dec_percentage DECIMAL(5, 2) NOT NULL COMMENT 'Commission percentage for this level',
    bool_active BOOLEAN DEFAULT TRUE COMMENT 'Whether this level is active',
    tim_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    tim_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Record update timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Dynamic commission level configuration';

-- ============================================
-- SEED DATA: Root User
-- ============================================
INSERT INTO tbl_users (vchr_name, vchr_email, fk_bint_parent_id) 
VALUES ('Root Admin', 'root@system.com', NULL);

-- ============================================
-- SEED DATA: Commission Levels
-- ============================================
INSERT INTO tbl_commission_levels (int_level, dec_percentage, bool_active) VALUES
(1, 10.00, TRUE),
(2, 5.00, TRUE),
(3, 3.00, TRUE),
(4, 2.00, TRUE),
(5, 1.00, TRUE);
