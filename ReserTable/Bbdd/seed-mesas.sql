-- Inserta mesas de prueba con posiciones y estados variados
INSERT INTO tables (table_number, capacity, status, position_x, position_y, created_at, updated_at)
VALUES
  (1, 2, 'available', 10, 20, NOW(), NOW()),   -- disponible
  (2, 4, 'occupied', 30, 40, NOW(), NOW()),    -- ocupada
  (3, 6, 'reserved', 60, 15, NOW(), NOW()),    -- reservada
  (4, 2, 'available', 80, 70, NOW(), NOW()),   -- disponible
  (5, 4, 'available', 50, 60, NOW(), NOW());   -- disponible

-- Si necesitas limpiar antes:
-- DELETE FROM tables;
