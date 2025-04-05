-- Tabla de alérgenos comunes
CREATE TABLE allergens (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(255), -- Ruta al icono que representa el alérgeno
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de relación entre ingredientes y alérgenos
CREATE TABLE ingredient_allergens (
    id SERIAL PRIMARY KEY,
    ingredient_id INT NOT NULL,
    allergen_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id) ON DELETE CASCADE,
    FOREIGN KEY (allergen_id) REFERENCES allergens(id) ON DELETE CASCADE,
    UNIQUE(ingredient_id, allergen_id) -- Evita duplicados
);

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