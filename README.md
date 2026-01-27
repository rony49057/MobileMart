==========================================================
-- Mobile Mart - FINAL Database (One File)
-- Creates FULL DB with all required tables + fixed columns
-- Compatible with code using:
-- products.qty, products.offer_percent, orders.assigned_staff_phone
-- users.password_hash
-- ==========================================================
 
DROP DATABASE IF EXISTS mobile_mart_db;
 
CREATE DATABASE mobile_mart_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;
 
USE mobile_mart_db;
 
-- ==========================================================
-- 1) USERS
-- phone = PRIMARY KEY
-- password_hash used (password_verify compatible)
-- ==========================================================
CREATE TABLE users (
  phone VARCHAR(15) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  gender VARCHAR(10) NULL,
  dob DATE NULL,
  address TEXT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','staff','customer') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
-- ==========================================================
-- 2) PRODUCTS
-- FIXED: qty + quantity both included (beginner-friendly)
-- FIXED: offer_percent + offer both included
-- ==========================================================
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  model VARCHAR(100) NOT NULL,
  brand VARCHAR(100) NOT NULL,
  ram VARCHAR(50) NULL,
  rom VARCHAR(50) NULL,
  price DECIMAL(10,2) NOT NULL,
 
  -- Stock fields (both kept to avoid code mismatch)
  quantity INT NOT NULL DEFAULT 0,
  qty INT NOT NULL DEFAULT 0,
 
  image VARCHAR(255) NULL,
 
  -- Offer fields (both kept to avoid code mismatch)
  offer INT NOT NULL DEFAULT 0,
  offer_percent INT NOT NULL DEFAULT 0,
 
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
CREATE INDEX idx_products_brand ON products(brand);
CREATE INDEX idx_products_model ON products(model);
 
-- ==========================================================
-- 3) CART ITEMS
-- Guest uses session_id; logged user uses user_phone
-- ==========================================================
CREATE TABLE cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(100) NULL,
  user_phone VARCHAR(15) NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
CREATE INDEX idx_cart_session ON cart_items(session_id);
CREATE INDEX idx_cart_user ON cart_items(user_phone);
CREATE INDEX idx_cart_product ON cart_items(product_id);
 
-- ==========================================================
-- 4) ORDERS
-- FIXED: staff_phone + assigned_staff_phone both included
-- ==========================================================
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_phone VARCHAR(15) NOT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  payment_method ENUM('cash','card') NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'Pending',
 
  -- both kept to avoid mismatch
  staff_phone VARCHAR(15) NULL,
  assigned_staff_phone VARCHAR(15) NULL,
 
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
CREATE INDEX idx_orders_user ON orders(user_phone);
CREATE INDEX idx_orders_staff ON orders(staff_phone);
CREATE INDEX idx_orders_assigned_staff ON orders(assigned_staff_phone);
CREATE INDEX idx_orders_status ON orders(status);
 
-- ==========================================================
-- 5) ORDER ITEMS
-- ==========================================================
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
 
-- ==========================================================
-- 6) STAFF SALARY
-- ==========================================================
CREATE TABLE staff_salary (
  id INT AUTO_INCREMENT PRIMARY KEY,
  staff_phone VARCHAR(15) NOT NULL,
  amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  month VARCHAR(20) NOT NULL,     -- e.g., 2026-01
  note VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 
CREATE INDEX idx_salary_staff ON staff_salary(staff_phone);
CREATE INDEX idx_salary_month ON staff_salary(month);
 
-- ==========================================================
-- SAMPLE USERS (OPTIONAL)
-- NOTE:
-- This hash is a placeholder. EASIEST: register from website
-- Password hash example (same for all below):
-- ==========================================================
INSERT INTO users (phone, name, gender, dob, address, password_hash, role) VALUES
('01900000000', 'Admin', 'Male', '1995-01-01', 'Head Office',
'$2y$10$e0NRoRrHk8gqFJm9iG2oY.4z9QyqkzqSx7z2nJ7mZB0g2cJ1mXv2K', 'admin'),
 
('01811111111', 'Staff One', 'Male', '1998-05-10', 'Dhaka',
'$2y$10$e0NRoRrHk8gqFJm9iG2oY.4z9QyqkzqSx7z2nJ7mZB0g2cJ1mXv2K', 'staff'),
 
('01722222222', 'Customer One', 'Female', '2000-08-15', 'Chittagong',
'$2y$10$e0NRoRrHk8gqFJm9iG2oY.4z9QyqkzqSx7z2nJ7mZB0g2cJ1mXv2K', 'customer');
 
-- ==========================================================
-- SAMPLE PRODUCTS (OPTIONAL)
-- Keep quantity and qty same for safety
-- Keep offer and offer_percent same for safety
-- ==========================================================
INSERT INTO products (model, brand, ram, rom, price, quantity, qty, image, offer, offer_percent) VALUES
('Galaxy A15', 'Samsung', '6GB', '128GB', 18000.00, 10, 10, 'samsung_a15.jpg', 5, 5),
('Redmi 12',   'Xiaomi',  '8GB', '128GB', 16500.00, 15, 15, 'redmi_12.jpg',   0, 0),
('iPhone 11',  'Apple',   '4GB', '64GB',  55000.00, 5,  5,  'iphone11.jpg',  10, 10);
 
-- ==========================================================
-- END
-- ==========================================================

