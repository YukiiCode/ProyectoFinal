-- Script SQL simplificado para MySQL 8.0

-- Tabla de usuarios (empleados y administradores)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de clientes
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de códigos de descuento
CREATE TABLE discount_coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('global', 'personalized') NOT NULL,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    valid_from DATE NOT NULL,
    valid_to DATE NOT NULL,
    max_uses INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    client_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    CONSTRAINT check_dates CHECK (valid_from <= valid_to)
);

-- Tabla de mesas del restaurante
CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    table_number INT NOT NULL UNIQUE,
    capacity INT NOT NULL,
    status ENUM('available', 'reserved', 'occupied') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de reservas
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    table_id INT NOT NULL,
    reservation_date TIMESTAMP NOT NULL,
    party_size INT NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    discount_coupon_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
    FOREIGN KEY (discount_coupon_id) REFERENCES discount_coupons(id) ON DELETE SET NULL,
    CONSTRAINT check_party_size CHECK (party_size > 0)
);

-- Tabla de categorías de productos
CREATE TABLE product_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de productos (platos/bebidas)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT NOT NULL,
    available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE RESTRICT,
    CONSTRAINT check_price CHECK (price >= 0)
);

-- Tabla de alérgenos comunes
CREATE TABLE allergens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de ingredientes con información de alérgenos
CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    unit_of_measure VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de relación entre ingredientes y alérgenos
CREATE TABLE ingredient_allergens (
    ingredient_id INT NOT NULL,
    allergen_id INT NOT NULL,
    PRIMARY KEY (ingredient_id, allergen_id),
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE,
    FOREIGN KEY (allergen_id) REFERENCES allergens(id) ON DELETE CASCADE
);

-- Tabla de ingredientes de los productos
CREATE TABLE product_ingredients (
    product_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity_required DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (product_id, ingredient_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);

-- Tabla de inventario
CREATE TABLE inventory (
    ingredient_id INT PRIMARY KEY,
    quantity_in_stock DECIMAL(10, 2) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);

-- Tabla de eventos/promociones con descuentos integrados
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    discount_code VARCHAR(50) UNIQUE,
    discount_type ENUM('percentage', 'fixed'),
    discount_value DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT check_event_dates CHECK (start_date <= end_date)
);

-- Tabla de facturas
CREATE TABLE invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    reservation_id INT DEFAULT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
);

-- Tabla de logs del sistema (simplificada)
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabla de pedidos
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    reservation_id INT DEFAULT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
);

-- Tabla de items de pedidos
CREATE TABLE order_items (
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    notes TEXT,
    PRIMARY KEY (order_id, product_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Tabla de horarios del restaurante
CREATE TABLE restaurant_hours (
    day_of_week TINYINT PRIMARY KEY, -- 0=Domingo, 1=Lunes, etc.
    open_time TIME NOT NULL,
    close_time TIME NOT NULL,
    is_closed BOOLEAN DEFAULT FALSE
);

-- Índices estratégicos para optimizar consultas frecuentes
CREATE INDEX idx_reservations_date ON reservations(reservation_date);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_discount_coupons_code ON discount_coupons(code);
CREATE INDEX idx_tables_status ON tables(status);

-- Inserción de alérgenos comunes según la normativa europea
INSERT INTO allergens (name, description) VALUES
('Gluten', 'Cereales que contienen gluten: trigo, centeno, cebada, avena, espelta, kamut'),
('Crustáceos', 'Cangrejos, langostas, gambas, langostinos y otros crustáceos'),
('Huevos', 'Huevos y productos derivados'),
('Pescado', 'Pescado y productos a base de pescado'),
('Cacahuetes', 'Cacahuetes y productos a base de cacahuetes'),
('Soja', 'Soja y productos a base de soja'),
('Lácteos', 'Leche y sus derivados, incluida la lactosa'),
('Frutos secos', 'Almendras, avellanas, nueces, anacardos, pacanas, nueces de Brasil, pistachos, nueces de macadamia'),
('Apio', 'Apio y productos derivados'),
('Mostaza', 'Mostaza y productos derivados'),
('Sésamo', 'Granos de sésamo y productos derivados'),
('Sulfitos', 'Dióxido de azufre y sulfitos en concentraciones superiores a 10mg/kg'),
('Altramuces', 'Altramuces y productos a base de altramuces'),
('Moluscos', 'Moluscos y productos a base de moluscos');

