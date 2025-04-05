-- Tabla de usuarios (empleados y administradores)
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL, -- Rol del usuario
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de clientes
CREATE TABLE clients (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de historial de consumo de clientes
CREATE TABLE clients_history (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL,
    invoice_id INT NOT NULL, -- Referencia a la factura generada
    total_amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Tabla de códigos de descuento
CREATE TABLE discount_coupons (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('global', 'personalized') NOT NULL, -- Descuento global o personalizado
    discount_type ENUM('percentage', 'fixed') NOT NULL, -- Tipo de descuento: porcentaje o fijo
    value DECIMAL(10, 2) NOT NULL, -- Valor del descuento
    valid_from DATE NOT NULL,
    valid_to DATE NOT NULL,
    max_uses INT DEFAULT NULL, -- Máximo número de usos permitidos
    used_count INT DEFAULT 0, -- Contador de usos
    client_id INT DEFAULT NULL, -- Si es personalizado, se asigna a un cliente específico
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
);

-- Tabla de mesas del restaurante
CREATE TABLE tables (
    id SERIAL PRIMARY KEY,
    table_number INT NOT NULL UNIQUE,
    capacity INT NOT NULL, -- Capacidad máxima de personas
    status ENUM('available', 'reserved', 'occupied') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de reservas
CREATE TABLE reservations (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL,
    table_id INT NOT NULL,
    reservation_date TIMESTAMP NOT NULL,
    party_size INT NOT NULL, -- Número de personas
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    discount_coupon_id INT DEFAULT NULL, -- Código de descuento aplicado (opcional)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
    FOREIGN KEY (discount_coupon_id) REFERENCES discount_coupons(id) ON DELETE SET NULL
);

-- Tabla de categorías de productos
CREATE TABLE product_categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de productos (platos/bebidas)
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT NOT NULL,
    available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE RESTRICT
);

-- Tabla de ingredientes
CREATE TABLE ingredients (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    unit_of_measure VARCHAR(50) NOT NULL,
    allergen_info TEXT, -- Información sobre alérgenos que contiene el ingrediente
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de ingredientes de los productos
CREATE TABLE product_ingredients (
    id SERIAL PRIMARY KEY,
    product_id INT NOT NULL,
    ingredient_id INT NOT NULL,
    quantity_required DECIMAL(10, 2) NOT NULL, -- Cantidad requerida por unidad
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);

-- Tabla de inventario
CREATE TABLE inventory (
    id SERIAL PRIMARY KEY,
    ingredient_id INT NOT NULL,
    quantity_in_stock DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE
);

-- Tabla de eventos/promociones
CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de ofertas asociadas a eventos
CREATE TABLE event_offers (
    id SERIAL PRIMARY KEY,
    event_id INT NOT NULL,
    discount_coupon_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (discount_coupon_id) REFERENCES discount_coupons(id) ON DELETE CASCADE
);

-- Tabla de facturas
CREATE TABLE invoices (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL,
    reservation_id INT DEFAULT NULL, -- Puede estar asociada a una reserva
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
);

-- Tabla de logs del sistema
CREATE TABLE logs (
    id SERIAL PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabla de pedidos
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    client_id INT NOT NULL,
    reservation_id INT DEFAULT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
);

-- Tabla de items de pedidos
CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Tabla de horarios del restaurante
CREATE TABLE restaurant_hours (
    id SERIAL PRIMARY KEY,
    day_of_week TINYINT NOT NULL, -- 0=Domingo, 1=Lunes, etc.
    open_time TIME NOT NULL,
    close_time TIME NOT NULL,
    is_closed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT unique_day UNIQUE (day_of_week)
);

-- Índices estratégicos para optimizar consultas frecuentes
CREATE INDEX idx_reservations_date ON reservations(reservation_date);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_discount_coupons_code ON discount_coupons(code);
CREATE INDEX idx_tables_status ON tables(status);

-- Restricciones adicionales para garantizar la integridad de datos
ALTER TABLE reservations ADD CONSTRAINT check_party_size 
    CHECK (party_size > 0);
    
ALTER TABLE products ADD CONSTRAINT check_price 
    CHECK (price >= 0);
    
ALTER TABLE discount_coupons ADD CONSTRAINT check_dates 
    CHECK (valid_from <= valid_to);

