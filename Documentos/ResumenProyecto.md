# Resumen del Proyecto ReserTable

## Descripción General

ReserTable es un sistema integral de gestión para restaurantes desarrollado como Trabajo de Fin de Grado. La plataforma ofrece una solución completa para la administración de reservas, gestión de inventario, procesamiento de pedidos y seguimiento de alérgenos, optimizando las operaciones diarias del restaurante.

## Objetivos del Proyecto

El objetivo principal es desarrollar una aplicación web para la gestión y administración de un restaurante, permitiendo a los clientes reservar mesas de forma interactiva y obtener beneficios como códigos de descuento y un historial de consumo. Además, facilitará la gestión interna del establecimiento, permitiendo al administrador organizar la disposición de las mesas y asignar descuentos de manera personalizada.

## Tecnologías Utilizadas

### Backend
- **Laravel 12**: Framework PHP robusto y moderno
- **MySQL 8.0**: Sistema de gestión de bases de datos relacional
- **PHP 8.2**: Lenguaje de programación del lado del servidor

### Frontend
- **Blade**: Motor de plantillas de Laravel
- **Vue.js 3.0**: Framework progresivo para construir interfaces de usuario
- **JavaScript/Vite**: Para interactividad en el lado del cliente

### Infraestructura
- **Docker**: Contenedores para el entorno de desarrollo
- **MySQL 8.0**: Base de datos en contenedor
- **PHPMyAdmin**: Interfaz de administración de la base de datos

## Módulos del Sistema

### Módulo Cliente
- Registro e inicio de sesión
- Reserva de mesas con mapa interactivo
- Historial de reservas y consumo
- Generación y uso de códigos de descuento

### Módulo Administrador
- Gestión de mesas y disponibilidad
- Creación y asignación de códigos de descuento (globales o personalizados)
- Visualización y administración de reservas
- Gestión del menú y platos disponibles

## Características Principales Implementadas

### Gestión de Usuarios y Clientes
- Sistema de autenticación con roles diferenciados (administradores y empleados)
- Registro y seguimiento de clientes
- Historial de consumo de clientes

### Reservas y Mesas
- Sistema avanzado de reservas con gestión de estados
- Control de disponibilidad de mesas en tiempo real
- Asignación inteligente según capacidad y disponibilidad

### Catálogo y Pedidos
- Gestión completa de productos (platos y bebidas)
- Categorización flexible de productos
- Sistema de pedidos con seguimiento de estado

### Inventario y Alérgenos
- Control de inventario de ingredientes
- Seguimiento de alérgenos según normativa europea
- Alertas de stock bajo

## Arquitectura de Base de Datos

El sistema está respaldado por una arquitectura de base de datos normalizada y optimizada que incluye:

- Normalización avanzada para evitar redundancia de datos
- Índices estratégicos para consultas de alto rendimiento
- Restricciones de integridad mediante claves foráneas y validaciones
- Campos de auditoría para seguimiento de cambios

### Entidades Principales

1. **USERS**: Gestión de usuarios del sistema (administradores y empleados)
2. **CLIENTS**: Información de los clientes del restaurante
3. **TABLES**: Mesas disponibles en el restaurante
4. **RESERVATIONS**: Reservas realizadas por los clientes
5. **PRODUCTS**: Catálogo de productos (platos y bebidas)
6. **PRODUCT_CATEGORIES**: Categorías de productos
7. **INGREDIENTS**: Ingredientes utilizados en los productos
8. **ALLERGENS**: Alérgenos según normativa europea
9. **ORDERS**: Pedidos realizados por los clientes
10. **DISCOUNT_COUPONS**: Códigos de descuento para clientes

## Estado Actual del Desarrollo

Según el roadmap del proyecto, actualmente se han completado:

- ✅ Análisis de requisitos del sistema
- ✅ Diseño de la arquitectura de la base de datos
- ✅ Implementación del esquema de base de datos
- ✅ Configuración del entorno de desarrollo con Docker
- ✅ Creación del diagrama Entidad-Relación

En proceso:

- ⏳ Definición de la arquitectura del sistema
- ⏳ Diseño de wireframes para interfaces principales
- ⏳ Implementación del sistema de autenticación y gestión de usuarios
- ⏳ Desarrollo del módulo de reservas y gestión de mesas

## Mejoras Implementadas

1. **Optimización del Esquema SQL**: Se ha simplificado y optimizado el esquema de la base de datos para mejorar el rendimiento y la mantenibilidad.

2. **Diagrama ER Interactivo**: Se ha creado un diagrama Entidad-Relación interactivo utilizando Mermaid.js que permite visualizar la estructura de la base de datos y exportarla como imagen PNG.

3. **Contenedorización**: Se ha implementado Docker para facilitar el desarrollo y despliegue del proyecto, incluyendo contenedores para MySQL y PHPMyAdmin.

4. **Seguimiento de Alérgenos**: Se ha implementado un sistema completo para el seguimiento de alérgenos según la normativa europea, facilitando la gestión de información para clientes con alergias alimentarias.

## Próximos Pasos

Según el roadmap del proyecto, los próximos pasos incluyen:

1. Desarrollo del backend para el sistema de autenticación y gestión de usuarios
2. Implementación del módulo de reservas y gestión de mesas
3. Desarrollo del catálogo de productos y sistema de pedidos
4. Creación de las interfaces de usuario para los módulos principales

## Conclusión

El proyecto ReserTable avanza según lo planificado, con un enfoque en la creación de una base sólida para el sistema. La arquitectura de base de datos está bien diseñada y optimizada, y se han implementado mejoras significativas como la contenedorización y el diagrama ER interactivo. Los próximos pasos se centrarán en el desarrollo de los módulos principales del sistema y la creación de interfaces de usuario intuitivas y funcionales.