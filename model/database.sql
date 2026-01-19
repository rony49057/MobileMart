-- Mobile Mart DB
-- Create database
CREATE DATABASE IF NOT EXISTS mobile_mart_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE mobile_mart_db;

-- USERS: phone is primary key (as you requested)
CREATE TABLE IF NOT EXISTS users (
  phone VARCHAR(20) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  gender VARCHAR(10) DEFAULT '',
  dob DATE NULL,
  address VARCHAR(255) DEFAULT '',
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('customer','admin','staff') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PRODUCTS
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  model VARCHAR(100) NOT NULL,
  brand VARCHAR(100) NOT NULL,
  ram VARCHAR(50) DEFAULT '',
  rom VARCHAR(50) DEFAULT '',
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  qty INT NOT NULL DEFAULT 0,
  image VARCHAR(120) NOT NULL DEFAULT 'default-phone.png',
  offer_percent INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- CART ITEMS (guest or logged-in)
CREATE TABLE IF NOT EXISTS cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(50) NOT NULL,
  user_phone VARCHAR(20) NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(session_id),
  INDEX(user_phone),
  FOREIGN KEY (user_phone) REFERENCES users(phone) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ORDERS
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_phone VARCHAR(20) NOT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  payment_method ENUM('cash','card') NOT NULL DEFAULT 'cash',
  status VARCHAR(40) NOT NULL DEFAULT 'Pending',
  assigned_staff_phone VARCHAR(20) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_phone),
  INDEX(assigned_staff_phone),
  FOREIGN KEY (user_phone) REFERENCES users(phone) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (assigned_staff_phone) REFERENCES users(phone) ON DELETE SET NULL ON UPDATE CASCADE
);

-- ORDER ITEMS
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  qty INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- STAFF SALARY
CREATE TABLE IF NOT EXISTS staff_salary (
  id INT AUTO_INCREMENT PRIMARY KEY,
  staff_phone VARCHAR(20) NOT NULL,
  amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  month VARCHAR(20) NOT NULL,
  note VARCHAR(255) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(staff_phone),
  FOREIGN KEY (staff_phone) REFERENCES users(phone) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Default admin account (phone: 01900000000, pass: admin123)
-- password_hash created by PHP password_hash. We'll insert a pre-made hash for admin123
INSERT INTO users (phone,name,gender,dob,address,password_hash,role)
VALUES
('01900000000','Admin','Other',NULL,'', '$2y$10$GSUXFx7vbpLfzdC6TqXID.2lIdb.Aprgb1gKfXvqbvBeLFJb63fGm', 'admin')
ON DUPLICATE KEY UPDATE role='admin';

-- Sample products
INSERT INTO products (model,brand,ram,rom,price,qty,image,offer_percent) VALUES
('Galaxy A15','Samsung','6GB','128GB', 18999, 8, 'default-phone.png', 5),
('Redmi 13C','Xiaomi','4GB','128GB', 14999, 15, 'default-phone.png', 0),
('Realme C53','Realme','6GB','128GB', 16999, 10, 'default-phone.png', 3)
ON DUPLICATE KEY UPDATE model=model;
