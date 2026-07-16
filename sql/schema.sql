CREATE DATABASE IF NOT EXISTS dairyfarm CHARACTER SET utf8mb4;
USE dairyfarm;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  address TEXT,
  role ENUM('user','admin') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(100) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  unit VARCHAR(30) DEFAULT 'unit',
  stock INT DEFAULT 0,
  image VARCHAR(255) DEFAULT 'default.jpg',
  status ENUM('active','inactive') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  shipping_name VARCHAR(150),
  shipping_phone VARCHAR(20),
  shipping_address TEXT,
  payment_method VARCHAR(50) DEFAULT 'Cash on Delivery',
  status ENUM('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(150),
  price DECIMAL(10,2),
  quantity INT,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150),
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin login: admin@dairyfarm.com / admin123
INSERT INTO users (name, email, phone, password, role) VALUES
('Admin', 'admin@dairyfarm.com', '9999999999', 'admin123', 'admin');

INSERT INTO products (name, category, description, price, unit, stock, image) VALUES
('Fresh Cow Milk', 'Milk', 'Pure, farm-fresh cow milk delivered daily, rich in calcium and protein.', 60.00, 'Litre', 100, 'milk-cow.jpg'),
('Buffalo Milk', 'Milk', 'Creamy, high-fat buffalo milk perfect for sweets and tea.', 70.00, 'Litre', 80, 'milk-buffalo.jpg'),
('Pure Ghee', 'Ghee', 'Traditional bilona-method clarified butter, aromatic and golden.', 650.00, '500g Jar', 40, 'ghee.jpg'),
('Fresh Paneer', 'Paneer', 'Soft, homemade cottage cheese made fresh every morning.', 320.00, 'Kg', 30, 'paneer.jpg'),
('Curd / Dahi', 'Curd', 'Thick, naturally set curd made from full-cream milk.', 80.00, 'Kg', 60, 'curd.jpg'),
('White Butter', 'Butter', 'Unsalted, hand-churned white butter with rich texture.', 420.00, '500g', 25, 'butter.jpg'),
('Flavoured Lassi', 'Beverages', 'Refreshing sweet lassi made with farm curd and real fruit.', 50.00, '500ml', 50, 'lassi.jpg'),
('Khoya / Mawa', 'Sweets Base', 'Slow-cooked thickened milk solids for festive sweets.', 380.00, 'Kg', 20, 'khoya.jpg');
