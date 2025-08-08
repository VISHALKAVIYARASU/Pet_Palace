-- CREATE DATABASE pet_palace;
-- USE pet_palace;

-- CREATE TABLE users (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   username VARCHAR(100) NOT NULL,
--   password VARCHAR(255) NOT NULL,
--   role ENUM('admin1', 'user', 'admin2', 'admin3') DEFAULT 'user'
-- );

-- CREATE TABLE products (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   name VARCHAR(255) NOT NULL,
--   price DECIMAL(10,2) NOT NULL,
--   stock INT NOT NULL,
--   image VARCHAR(255)
-- );


-- CREATE TABLE user_cart (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT NOT NULL,
--     product_id INT NOT NULL,
--     quantity INT DEFAULT 1,
--     UNIQUE KEY (user_id, product_id),
--     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
--     FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
-- );


-- CREATE TABLE transactions (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     user_id INT,
--     amount DECIMAL(10, 2),
--     payment_status VARCHAR(50),
--     payment_method VARCHAR(50),
--     created_at DATETIME DEFAULT CURRENT_TIMESTAMP
-- );


-- -- Insert sample admin
-- INSERT INTO users (username, password, role)
-- VALUES ('admin', 'admin', 'admin'); -- Replace with hashed password

-- -- Insert sample products
-- INSERT INTO products (name, price, stock, image) VALUES
-- ('Dog Toy', 299.99, 10, 'toy.jpg'),
-- ('Cat Food', 499.00, 0, 'food.jpg');
