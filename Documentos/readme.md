# Diseño y Optimización de la Base de Datos para el Sistema de Gestión de Restaurantes ReserTable

## Análisis de la Estructura Implementada

En el desarrollo de este Trabajo de Fin de Grado, he diseñado una base de datos robusta que satisface los requerimientos fundamentales del sistema de gestión de restaurantes ReserTable. La arquitectura implementada contempla los siguientes componentes esenciales:

- Un sistema de gestión de usuarios con diferenciación entre empleados y administradores
- Registro y seguimiento de clientes junto con su historial de consumo
- Administración de códigos promocionales y descuentos
- Control de mesas y sistema de reservas
- Catálogo de productos clasificados en platos y bebidas
- Gestión de ingredientes e inventario
- Organización de eventos y promociones especiales
- Sistema de facturación integrado
- Registro de actividades para auditoría y seguimiento

## Aspectos Destacables del Diseño Implementado

Durante el desarrollo de la estructura de datos, he priorizado los siguientes aspectos que considero fundamentales para garantizar la integridad y eficiencia del sistema:

1. **Separación clara de entidades**: He establecido una distinción precisa entre usuarios, clientes, productos y demás entidades del sistema.
2. **Relaciones correctamente definidas**: Las relaciones entre tablas están implementadas mediante claves foráneas que garantizan la integridad referencial.
3. **Trazabilidad de cambios**: Todas las tablas incorporan campos de auditoría (`created_at` y `updated_at`) que permiten un seguimiento preciso de las modificaciones.
4. **Validación mediante enumeraciones**: He utilizado el tipo ENUM para restringir los valores posibles en campos específicos, asegurando la consistencia de los datos.

## Propuestas de Mejora y Optimización

### 1. Normalización Avanzada de la Estructura de Datos

Durante mi análisis, he identificado que el atributo `ingredient_name` se duplica en las tablas `product_ingredients` e `inventory`. Para resolver esta redundancia, he diseñado una tabla específica para ingredientes:

```sql
CREATE TABLE ingredients (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    unit_of_measure VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

Esta modificación requiere actualizar las tablas relacionadas para que referencien a esta nueva entidad, eliminando así la redundancia de datos.

### 2. Implementación del Sistema de Pedidos

He detectado la ausencia de una estructura para gestionar los pedidos de los clientes, componente esencial en un sistema de restauración. Mi propuesta incluye la creación de las siguientes tablas:

```sql
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
```

### 3. Optimización mediante Índices Estratégicos

Para mejorar el rendimiento en las consultas más frecuentes, he definido los siguientes índices:

```sql
CREATE INDEX idx_reservations_date ON reservations(reservation_date);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_discount_coupons_code ON discount_coupons(code);
CREATE INDEX idx_tables_status ON tables(status);
```

Estos índices acelerarán significativamente las búsquedas por fecha de reserva, categoría de producto, código de descuento y estado de las mesas, operaciones que he identificado como críticas para el rendimiento del sistema.

### 4. Refuerzo de la Integridad de Datos

He incorporado restricciones adicionales mediante cláusulas CHECK para garantizar la validez de los datos:

```sql
ALTER TABLE reservations ADD CONSTRAINT check_party_size 
    CHECK (party_size > 0);
    
ALTER TABLE products ADD CONSTRAINT check_price 
    CHECK (price >= 0);
    
ALTER TABLE discount_coupons ADD CONSTRAINT check_dates 
    CHECK (valid_from <= valid_to);
```

Estas restricciones previenen errores como números negativos de comensales, precios negativos o fechas de validez inconsistentes.

### 5. Categorización Flexible de Productos

En lugar de utilizar un ENUM para las categorías de productos, he diseñado una tabla específica que permite mayor flexibilidad y extensibilidad:

```sql
CREATE TABLE product_categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

Esta modificación facilita la incorporación de nuevas categorías sin necesidad de alterar la estructura de la tabla de productos.

### 6. Sistema de Gestión de Horarios

Para administrar eficientemente los horarios de apertura del restaurante, he diseñado la siguiente estructura:

```sql
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
```

Esta tabla permite definir horarios específicos para cada día de la semana y gestionar días de cierre.

### 7. Medidas de Seguridad y Protección de Datos

En mi análisis de seguridad, he identificado la necesidad de implementar:

- Encriptación para datos sensibles como información personal de clientes y credenciales
- Un sistema robusto de respaldo y recuperación para garantizar la continuidad del negocio

### 8. Preparación para el Crecimiento

Anticipando el crecimiento del sistema, he contemplado las siguientes estrategias:

- Particionamiento de tablas con alto volumen de datos como registros de actividad y reservas
- Desarrollo de procedimientos almacenados para optimizar operaciones complejas y frecuentes

## Conclusiones

La arquitectura de base de datos que he desarrollado proporciona una base sólida para el sistema ReserTable, cubriendo los requisitos funcionales identificados durante la fase de análisis. Las mejoras propuestas en este documento responden a principios fundamentales de diseño de bases de datos relacionales: normalización, integridad, rendimiento y escalabilidad.

La implementación de estas optimizaciones debe realizarse de manera progresiva, priorizando aquellas que ofrezcan mayor impacto en la funcionalidad y experiencia de usuario. Este enfoque incremental permitirá validar cada modificación y minimizar el impacto en el sistema en producción.

Este trabajo demuestra la aplicación práctica de los conocimientos adquiridos durante mi formación académica en el ámbito de las bases de datos y el desarrollo de sistemas de información.