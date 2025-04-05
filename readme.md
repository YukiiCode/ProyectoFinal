# ReserTable: Sistema de Gestión de Restaurantes

[![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.0-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org/)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

## Descripción

ReserTable es un sistema integral de gestión para restaurantes desarrollado como Trabajo de Fin de Grado. La plataforma ofrece una solución completa para la administración de reservas, gestión de inventario, procesamiento de pedidos y seguimiento de alérgenos, optimizando las operaciones diarias del restaurante.

## Características Principales

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

### Promociones y Facturación
- Sistema de códigos de descuento personalizados
- Gestión de eventos especiales
- Facturación integrada

### Horarios y Operaciones
- Configuración de horarios de apertura
- Registro de actividades para auditoría

## Tecnologías Utilizadas

### Backend
- **Laravel 12**: Framework PHP robusto y moderno
- **MySQL**: Sistema de gestión de bases de datos relacional
- **PHP 8.2**: Lenguaje de programación del lado del servidor

### Frontend
- **Blade**: Motor de plantillas de Laravel
- **Vue.js**: Framework progresivo para construir interfaces de usuario
- **JavaScript/Vite**: Para interactividad en el lado del cliente

## Arquitectura de Base de Datos

El sistema está respaldado por una arquitectura de base de datos normalizada y optimizada que incluye:

- Normalización avanzada para evitar redundancia de datos
- Índices estratégicos para consultas de alto rendimiento
- Restricciones de integridad mediante claves foráneas y validaciones
- Campos de auditoría para seguimiento de cambios

## Instalación

```bash
# Clonar el repositorio
git clone https://github.com/yourusername/ProyectoFinal.git
cd ProyectoFinal/ReserTable

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=reservtable
# DB_USERNAME=root
# DB_PASSWORD=

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Compilar assets
npm run build

# Iniciar servidor de desarrollo
php artisan serve
```

## Contribución

Si deseas contribuir a este proyecto, por favor:

1. Haz un fork del repositorio
2. Crea una rama para tu funcionalidad (`git checkout -b feature/amazing-feature`)
3. Realiza tus cambios y haz commit (`git commit -m 'Add some amazing feature'`)
4. Sube tus cambios (`git push origin feature/amazing-feature`)
5. Abre un Pull Request

## Licencia

Este proyecto está licenciado bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

## Autor

Desarrollado como Trabajo de Fin de Grado por Fernando Nieves.

---

© 2025 ReserTable. Todos los derechos reservados.
