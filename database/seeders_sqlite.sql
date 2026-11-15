-- =====================================================================
-- EasyPrint SaaS Platform - SQLite Seeders
-- =====================================================================

-- 1. SUBSCRIPTION PLANS
INSERT INTO subscription_plans (id, name, price, duration_days, max_printers, max_monthly_prints, features, is_active) VALUES
('plan_basic_monthly', 'Monthly Basic', 99.00, 30, 1, 1500, '{"printers": 1, "autoprint": true, "analytics": "basic"}', 1),
('plan_pro_monthly', 'Pro Unlimited', 499.00, 30, 5, 0, '{"printers": 5, "autoprint": true, "analytics": "advanced"}', 1),
('plan_annual_unlimited', 'Annual Enterprise', 3999.00, 365, 10, 0, '{"printers": 10, "autoprint": true, "analytics": "enterprise"}', 1);

-- 2. DEMO AGENTS (Password: Password@123)
-- Hash generated with: password_hash('Password@123', PASSWORD_BCRYPT)
INSERT INTO agents (id, agent_type, name, email, password_hash, phone) VALUES
('agent_admin_001', 'PLATFORM_ADMIN', 'Platform Administrator', 'admin@easyprint.io', '$2y$10$mfksa7PAmL9SQalr1nuYlO.o3Y3ocjneXn31ckM8fiFdSTvGDB9hK', '+919876543210'),
('agent_shop_001', 'SHOP_OWNER', 'Ramesh Sharma', 'ramesh@pixaprint.in', '$2y$10$mfksa7PAmL9SQalr1nuYlO.o3Y3ocjneXn31ckM8fiFdSTvGDB9hK', '+919812345678');

-- 3. DEMO SHOP
INSERT INTO shops (id, owner_agent_id, name, slug, upi_id, upi_name, accept_cash, autoprint, is_active) VALUES
('shop_pixaprint_001', 'agent_shop_001', 'Pixaprint Xerox & Cyber Cafe', 'pixaprint', 'ramesh.xerox@okaxis', 'Pixaprint Solutions', 1, 1, 1);

-- 4. ACTIVE SUBSCRIPTION FOR DEMO SHOP (Valid for 30 days)
INSERT INTO shop_subscriptions (id, shop_id, plan_id, status, starts_at, ends_at, gateway_order_id, gateway_payment_id) VALUES
('sub_pixaprint_001', 'shop_pixaprint_001', 'plan_pro_monthly', 'ACTIVE', datetime('now'), datetime('now', '+365 days'), 'order_simulated_init', 'pay_simulated_init');

-- 5. DEFAULT RATE CARD MATRIX FOR DEMO SHOP
INSERT INTO resource_pricing_rules (shop_id, paper_size, color_mode, sides, price_per_page) VALUES
('shop_pixaprint_001', 'A4', 'BLACK_AND_WHITE', 'SINGLE', 2.00),
('shop_pixaprint_001', 'A4', 'BLACK_AND_WHITE', 'DOUBLE', 3.00),
('shop_pixaprint_001', 'A4', 'COLOR', 'SINGLE', 10.00),
('shop_pixaprint_001', 'A4', 'COLOR', 'DOUBLE', 18.00),
('shop_pixaprint_001', 'A3', 'BLACK_AND_WHITE', 'SINGLE', 5.00),
('shop_pixaprint_001', 'A3', 'BLACK_AND_WHITE', 'DOUBLE', 8.00),
('shop_pixaprint_001', 'A3', 'COLOR', 'SINGLE', 20.00),
('shop_pixaprint_001', 'A3', 'COLOR', 'DOUBLE', 35.00),
('shop_pixaprint_001', 'LEGAL', 'BLACK_AND_WHITE', 'SINGLE', 3.00),
('shop_pixaprint_001', 'LEGAL', 'BLACK_AND_WHITE', 'DOUBLE', 5.00);

-- 6. DEMO MACHINE AGENT (DESKTOP CONNECTOR) & PRINTER
INSERT INTO device_agents (id, shop_id, device_name, pairing_code, pairing_code_expires_at, auth_token_hash, is_online, last_ping_at) VALUES
('dev_agent_001', 'shop_pixaprint_001', 'Counter-Main-PC', '839210', datetime('now', '+7 days'), 'b109f3bbbc244eb82441917ed06d618b9008dd09b3befd1b5e07394c706a8bb980b1d7785e5976ec0329ae255aea', 1, datetime('now'));

INSERT INTO resource_printers (id, shop_id, device_agent_id, printer_name, system_spool_name, is_default, is_online, supports_color, supports_duplex, paper_sizes) VALUES
('printer_001', 'shop_pixaprint_001', 'dev_agent_001', 'HP LaserJet Pro MFP M126nw', 'HP_LaserJet_Pro_MFP_M126nw', 1, 1, 0, 0, '["A4", "LEGAL", "LETTER"]');

