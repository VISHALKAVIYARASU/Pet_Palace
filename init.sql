-- CREATE DATABASE pet_palace;
-- USE pet_palace;

-- CREATE TABLE users (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   username VARCHAR(100) NOT NULL,
--   password VARCHAR(255) NOT NULL,
--   role ENUM('admin', 'user') DEFAULT 'user'
-- );

-- CREATE TABLE products (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   name VARCHAR(255) NOT NULL,
--   price DECIMAL(10,2) NOT NULL,
--   stock INT NOT NULL,
--   image VARCHAR(255)
-- );

-- -- Insert sample admin
-- INSERT INTO users (username, password, role)
-- VALUES ('admin', 'admin', 'admin'); -- Replace with hashed password

-- -- Insert sample products
-- INSERT INTO products (name, price, stock, image) VALUES
-- ('Dog Toy', 299.99, 10, 'toy.jpg'),
-- ('Cat Food', 499.00, 0, 'food.jpg');
