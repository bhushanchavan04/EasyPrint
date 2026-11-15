-- =====================================================================
-- EasyPrint SaaS Platform - Production MySQL 8.0 Schema (REA Framework)
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS event_print_executions;
DROP TABLE IF EXISTS event_payments;
DROP TABLE IF EXISTS event_order_specifications;
DROP TABLE IF EXISTS event_orders;
DROP TABLE IF EXISTS resource_digital_documents;
DROP TABLE IF EXISTS resource_pricing_rules;
DROP TABLE IF EXISTS resource_printers;
DROP TABLE IF EXISTS device_agents;
DROP TABLE IF EXISTS shop_subscriptions;
DROP TABLE IF EXISTS subscription_plans;
DROP TABLE IF EXISTS shops;
DROP TABLE IF EXISTS agents;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. AGENTS
CREATE TABLE agents (
    id VARCHAR(36) PRIMARY KEY,
    agent_type ENUM('PLATFORM_ADMIN', 'SHOP_OWNER', 'SHOP_STAFF') NOT NULL DEFAULT 'SHOP_OWNER',
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shops (
    id VARCHAR(36) PRIMARY KEY,
    owner_agent_id VARCHAR(36) NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(60) UNIQUE NOT NULL,
    upi_id VARCHAR(100) NOT NULL, -- Shopkeeper's personal VPA (e.g. merchant@icici)
    upi_name VARCHAR(100) NOT NULL,
    logo_url VARCHAR(255) NULL,
    accept_cash BOOLEAN DEFAULT TRUE,
    autoprint BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_agent_id) REFERENCES agents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. SUBSCRIPTION & PAYWALL ENGINE
CREATE TABLE subscription_plans (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(50) NOT NULL, -- 'Starter Trial', 'Monthly Basic', 'Pro Unlimited', 'Annual'
    price DECIMAL(8,2) NOT NULL, -- e.g. 99.00, 499.00
    duration_days INT NOT NULL, -- 30, 365
    max_printers INT NOT NULL DEFAULT 1,
    max_monthly_prints INT NOT NULL DEFAULT 1000, -- 0 for unlimited
    features JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE shop_subscriptions (
    id VARCHAR(36) PRIMARY KEY,
    shop_id VARCHAR(36) NOT NULL,
    plan_id VARCHAR(36) NOT NULL,
    status ENUM('TRIAL', 'ACTIVE', 'PAST_DUE', 'EXPIRED') DEFAULT 'TRIAL',
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    gateway_order_id VARCHAR(100) NULL,
    gateway_payment_id VARCHAR(100) NULL, -- Razorpay/Cashfree payment ref
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. MACHINE AGENTS (DESKTOP CONNECTOR)
CREATE TABLE device_agents (
    id VARCHAR(36) PRIMARY KEY,
    shop_id VARCHAR(36) NOT NULL,
    device_name VARCHAR(100) NOT NULL,
    pairing_code VARCHAR(6) NULL, -- 6-digit one-time pairing pin (e.g. '839210')
    pairing_code_expires_at DATETIME NULL,
    auth_token_hash VARCHAR(255) NULL,
    is_online BOOLEAN DEFAULT FALSE,
    last_ping_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE resource_printers (
    id VARCHAR(36) PRIMARY KEY,
    shop_id VARCHAR(36) NOT NULL,
    device_agent_id VARCHAR(36) NOT NULL,
    printer_name VARCHAR(150) NOT NULL, -- 'HP LaserJet Pro MFP M126nw'
    system_spool_name VARCHAR(150) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    is_online BOOLEAN DEFAULT TRUE,
    supports_color BOOLEAN DEFAULT FALSE,
    supports_duplex BOOLEAN DEFAULT FALSE,
    paper_sizes JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (device_agent_id) REFERENCES device_agents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. PRICING RESOURCE MATRIX
CREATE TABLE resource_pricing_rules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_id VARCHAR(36) NOT NULL,
    paper_size ENUM('A4', 'A3', 'LEGAL', 'LETTER') DEFAULT 'A4',
    color_mode ENUM('BLACK_AND_WHITE', 'COLOR') NOT NULL,
    sides ENUM('SINGLE', 'DOUBLE') NOT NULL,
    price_per_page DECIMAL(8,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. DIGITAL ASSETS & PRINT ORDERS
CREATE TABLE resource_digital_documents (
    id VARCHAR(36) PRIMARY KEY,
    shop_id VARCHAR(36) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    storage_path VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size_bytes BIGINT NOT NULL,
    total_pages INT NOT NULL,
    expires_at DATETIME NOT NULL,
    is_deleted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE event_orders (
    id VARCHAR(36) PRIMARY KEY,
    shop_id VARCHAR(36) NOT NULL,
    order_number VARCHAR(30) UNIQUE NOT NULL, -- MP-YYYYMMDD-XXXX
    customer_identifier VARCHAR(100) NULL,
    total_pages INT NOT NULL,
    total_amount DECIMAL(8,2) NOT NULL,
    status ENUM('AWAITING_PAYMENT', 'PAYMENT_VERIFIED', 'PRINTING', 'COMPLETED', 'FAILED', 'CANCELLED') DEFAULT 'AWAITING_PAYMENT',
    confirmed_at DATETIME NULL,
    completed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE event_order_specifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(36) NOT NULL,
    document_id VARCHAR(36) NOT NULL,
    copies INT DEFAULT 1,
    color_mode ENUM('BLACK_AND_WHITE', 'COLOR') DEFAULT 'BLACK_AND_WHITE',
    sides ENUM('SINGLE', 'DOUBLE') DEFAULT 'SINGLE',
    paper_size VARCHAR(10) DEFAULT 'A4',
    page_range VARCHAR(50) DEFAULT 'all',
    calculated_amount DECIMAL(8,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES event_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (document_id) REFERENCES resource_digital_documents(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE event_payments (
    id VARCHAR(36) PRIMARY KEY,
    order_id VARCHAR(36) NOT NULL,
    payment_method ENUM('DIRECT_UPI', 'CASH_AT_COUNTER') NOT NULL,
    amount DECIMAL(8,2) NOT NULL,
    transaction_reference VARCHAR(100) NULL,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES event_orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE event_print_executions (
    id VARCHAR(36) PRIMARY KEY,
    order_id VARCHAR(36) NOT NULL,
    device_agent_id VARCHAR(36) NULL,
    printer_id VARCHAR(36) NULL,
    status ENUM('PENDING', 'DOWNLOADING', 'SUBMITTED_TO_QUEUE', 'COMPLETED', 'FAILED') DEFAULT 'PENDING',
    attempts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES event_orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Indexes for High Performance Querying
CREATE INDEX idx_shops_slug ON shops(slug);
CREATE INDEX idx_sub_status ON shop_subscriptions(shop_id, status, ends_at);
CREATE INDEX idx_orders_shop ON event_orders(shop_id, status);
CREATE INDEX idx_orders_num ON event_orders(order_number);
CREATE INDEX idx_exec_status ON event_print_executions(status);
CREATE INDEX idx_doc_expiry ON resource_digital_documents(expires_at, is_deleted);

