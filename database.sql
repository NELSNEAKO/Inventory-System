CREATE DATABASE IF NOT EXISTS inventory_demo;
USE inventory_demo;

CREATE TABLE  users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(50),
    role ENUM('admin', 'user') NOT NULL
);

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100),
    category VARCHAR(50),
    stock INT,
    buying_price DECIMAL(10,2),
    selling_price DECIMAL(10,2),
    image_path VARCHAR(255),
    added_by INT,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL
);

INSERT INTO users (username, password, role) VALUES ('admin', 'admin123', 'admin');
INSERT INTO users (username, password, role) VALUES ('user', 'user123', 'user');

INSERT INTO categories (category_name) VALUES
('Finished Goods'),
('Machinery'),
('Packing Materials'),
('Raw Materials'),
('Stationery Items');

