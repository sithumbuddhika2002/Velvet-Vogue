-- Velvet Vogue Database Initialization Script
CREATE DATABASE IF NOT EXISTS velvet_vogue;
USE velvet_vogue;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    category ENUM('casual', 'formal', 'accessories') NOT NULL,
    gender ENUM('men', 'women', 'unisex') NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    sizes VARCHAR(100) NOT NULL,    -- Comma separated sizes e.g. "S,M,L,XL"
    colors VARCHAR(100) NOT NULL,   -- Comma separated colors e.g. "Black,Ivory,Sand"
    stock INT NOT NULL DEFAULT 20,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_name VARCHAR(100) NOT NULL,
    shipping_email VARCHAR(100) NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status ENUM('Pending', 'Processing', 'Shipped', 'Delivered') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 4. Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    size VARCHAR(20) NOT NULL,
    color VARCHAR(30) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- 5. Inquiries Table
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed Initial Users (Passwords: admin123 and customer123)
-- Admin: admin@velvetvogue.com / admin123
-- Customer: customer@velvetvogue.com / customer123
INSERT INTO users (name, email, password, role) VALUES 
('Velvet Admin', 'admin@velvetvogue.com', '$2y$10$Q9HIxIDFEpAjB/tz95F7i.FrLj5yLuSSuhSf9ZaQKjdDx0QIXUyS.', 'admin'),
('John Doe', 'customer@velvetvogue.com', '$2y$10$GiEek3JHWxbmNtOPc4QlSuGGSzz58DKEzoFlnNXituCZE.ovg52NG', 'customer')
ON DUPLICATE KEY UPDATE email=email;

-- Seed Sample Products
INSERT INTO products (name, description, price, category, gender, image_url, sizes, colors, stock) VALUES
('Classic Trench Coat', 'An iconic double-breasted trench coat in structured cotton twill. Features storm flaps, a waist-defining belt, and premium solid horn buttons. A timeless formal layer for cold evenings.', 189.00, 'formal', 'women', 'trench_coat.jpg', 'S,M,L,XL', 'Sand,Onyx,Olive', 15),
('Silk Slip Dress', 'Elegant evening dress crafted from pure mulberry silk. Features a cowl neckline, adjustable crossover back straps, and a fluid bias-cut silhouette that drapes beautifully.', 145.00, 'formal', 'women', 'silk_slip.jpg', 'XS,S,M,L', 'Champagne,Emerald,Onyx', 12),
('Tailored Wool Blazer', 'Modern double-breasted blazer in structured wool blend. Structured shoulders, peak lapels, and functional welt pockets. Pairs perfectly with matching trousers.', 220.00, 'formal', 'men', 'wool_blazer.jpg', 'M,L,XL,XXL', 'Navy,Charcoal,Black', 10),
('Linen Resort Shirt', 'Breathable organic linen shirt designed for casual comfort. Features a relaxed camp collar, short sleeves, and natural wood buttons. Pre-washed for extra softness.', 75.00, 'casual', 'men', 'linen_shirt.jpg', 'S,M,L,XL', 'Ivory,Sage,Navy,Terracotta', 25),
('Heavyweight Cotton Tee', 'Premium casual daily essential tee made from 280GSM organic cotton. Features a clean ribbed crew neck and relaxed drop-shoulder cut.', 45.00, 'casual', 'unisex', 'cotton_tee.jpg', 'S,M,L,XL', 'Onyx,Ivory,Sand,Charcoal', 30),
('Pleated Dress Trousers', 'Classic high-waisted dress trousers featuring double front pleats, pressed creases, and an adjustable side-tab waistband. The ultimate formal trouser.', 120.00, 'formal', 'men', 'pleated_trousers.jpg', 'S,M,L,XL', 'Charcoal,Navy,Black', 18),
('Leather Crossbody Bag', 'Minimalist daily crossbody bag crafted from vegetable-tanned grain leather. Features secure brass hardware, an adjustable strap, and card compartments.', 95.00, 'accessories', 'unisex', 'leather_bag.jpg', 'One Size', 'Tan,Black,Chestnut', 8),
('Cashmere Knit Scarf', 'Super soft ribbed scarf knitted from 100% fine Mongolian cashmere. Lightweight yet exceptionally warm with raw finished edges.', 85.00, 'accessories', 'unisex', 'cashmere_scarf.jpg', 'One Size', 'Oatmeal,Charcoal,Burgundy', 15),
('Minimalist Leather Watch', 'A clean, gold-rimmed dial watch featuring a premium black calfskin strap, Japanese quartz movement, and scratch-resistant sapphire glass.', 160.00, 'accessories', 'unisex', 'leather_watch.jpg', 'One Size', 'Gold-Black,Silver-Brown', 6),
('Satin Wide-Leg Pants', 'Flowy, high-waisted satin trousers with an elasticated waistband and side pockets. Perfect combination of elegant comfort for semi-formal settings.', 110.00, 'casual', 'women', 'satin_pants.jpg', 'S,M,L', 'Emerald,Champagne,Black', 14);
