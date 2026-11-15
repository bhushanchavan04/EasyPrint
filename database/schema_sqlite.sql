-- =====================================================================
-- EasyPrint SaaS Platform - SQLite Schema (Zero-Config Development)
-- =====================================================================

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

-- 1. AGENTS
CREATE TABLE agents (
    id TEXT PRIMARY KEY,
    agent_type TEXT NOT NULL DEFAULT 'SHOP_OWNER',
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    phone TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shops (
    id TEXT PRIMARY KEY,
    owner_agent_id TEXT NOT NULL,
    name TEXT NOT NULL,
    slug TEXT UNIQUE NOT NULL,
    upi_id TEXT NOT NULL,
    upi_name TEXT NOT NULL,
    logo_url TEXT NULL,
    accept_cash INTEGER DEFAULT 1,
    autoprint INTEGER DEFAULT 1,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_agent_id) REFERENCES agents(id) ON DELETE CASCADE
);

-- 2. SUBSCRIPTION & PAYWALL ENGINE
CREATE TABLE subscription_plans (
    id TEXT PRIMARY KEY,
    name TEXT NOT NULL,
    price REAL NOT NULL,
    duration_days INTEGER NOT NULL,
    max_printers INTEGER NOT NULL DEFAULT 1,
    max_monthly_prints INTEGER NOT NULL DEFAULT 1000,
    features TEXT NOT NULL,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shop_subscriptions (
    id TEXT PRIMARY KEY,
    shop_id TEXT NOT NULL,
    plan_id TEXT NOT NULL,
    status TEXT DEFAULT 'TRIAL',
    starts_at DATETIME NOT NULL,
    ends_at DATETIME NOT NULL,
    gateway_order_id TEXT NULL,
    gateway_payment_id TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
);

-- 3. MACHINE AGENTS (DESKTOP CONNECTOR)
CREATE TABLE device_agents (
    id TEXT PRIMARY KEY,
    shop_id TEXT NOT NULL,
    device_name TEXT NOT NULL,
    pairing_code TEXT NULL,
    pairing_code_expires_at DATETIME NULL,
    auth_token_hash TEXT NULL,
    is_online INTEGER DEFAULT 0,
    last_ping_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);

CREATE TABLE resource_printers (
    id TEXT PRIMARY KEY,
    shop_id TEXT NOT NULL,
    device_agent_id TEXT NOT NULL,
    printer_name TEXT NOT NULL,
    system_spool_name TEXT NOT NULL,
    is_default INTEGER DEFAULT 0,
    is_online INTEGER DEFAULT 1,
    supports_color INTEGER DEFAULT 0,
    supports_duplex INTEGER DEFAULT 0,
    paper_sizes TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
    FOREIGN KEY (device_agent_id) REFERENCES device_agents(id) ON DELETE CASCADE
);

-- 4. PRICING RESOURCE MATRIX
CREATE TABLE resource_pricing_rules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    shop_id TEXT NOT NULL,
    paper_size TEXT DEFAULT 'A4',
    color_mode TEXT NOT NULL,
    sides TEXT NOT NULL,
    price_per_page REAL NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);

-- 5. DIGITAL ASSETS & PRINT ORDERS
CREATE TABLE resource_digital_documents (
    id TEXT PRIMARY KEY,
    shop_id TEXT NOT NULL,
    original_name TEXT NOT NULL,
    storage_path TEXT NOT NULL,
    mime_type TEXT NOT NULL,
    size_bytes INTEGER NOT NULL,
    total_pages INTEGER NOT NULL,
    expires_at DATETIME NOT NULL,
    is_deleted INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);

CREATE TABLE event_orders (
    id TEXT PRIMARY KEY,
    shop_id TEXT NOT NULL,
    order_number TEXT UNIQUE NOT NULL,
    customer_identifier TEXT NULL,
    total_pages INTEGER NOT NULL,
    total_amount REAL NOT NULL,
    status TEXT DEFAULT 'AWAITING_PAYMENT',
    confirmed_at DATETIME NULL,
    completed_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
);

CREATE TABLE event_order_specifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id TEXT NOT NULL,
    document_id TEXT NOT NULL,
    copies INTEGER DEFAULT 1,
    color_mode TEXT DEFAULT 'BLACK_AND_WHITE',
    sides TEXT DEFAULT 'SINGLE',
    paper_size TEXT DEFAULT 'A4',
    page_range TEXT DEFAULT 'all',
    calculated_amount REAL NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES event_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (document_id) REFERENCES resource_digital_documents(id) ON DELETE CASCADE
);

CREATE TABLE event_payments (
    id TEXT PRIMARY KEY,
    order_id TEXT NOT NULL,
    payment_method TEXT NOT NULL,
    amount REAL NOT NULL,
    transaction_reference TEXT NULL,
    is_verified INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES event_orders(id) ON DELETE CASCADE
);

CREATE TABLE event_print_executions (
    id TEXT PRIMARY KEY,
    order_id TEXT NOT NULL,
    device_agent_id TEXT NULL,
    printer_id TEXT NULL,
    status TEXT DEFAULT 'PENDING',
    attempts INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES event_orders(id) ON DELETE CASCADE
);

CREATE INDEX idx_shops_slug ON shops(slug);
CREATE INDEX idx_sub_status ON shop_subscriptions(shop_id, status, ends_at);
CREATE INDEX idx_orders_shop ON event_orders(shop_id, status);
CREATE INDEX idx_orders_num ON event_orders(order_number);

