<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

const restaurantName = "El Sabor Auténtico";
const restaurantSlogan = "Descubre una experiencia culinaria inolvidable donde cada bocado cuenta una historia.";
</script>
<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 50;
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <Head :title="`Bienvenido a ${restaurantName}`" />
    <div class="page-wrapper">
        <!-- Hero Section / Sticky Navbar -->
        <header :class="['main-hero-header', { 'scrolled': isScrolled }]">
            <div class="container d-flex justify-content-between align-items-center">
                <Link :href="route('home')" class="logo-link">
                    <h1 class="restaurant-name-hero">{{ restaurantName }}</h1>
                </Link>
                <nav class="main-nav">
                    <template v-if="isAuthenticated">
                        <Link :href="route('dashboard')" class="nav-link btn btn-sm btn-outline-light me-2">Mi Cuenta</Link>
                        <Link :href="route('logout')" method="post" as="button" class="nav-link btn btn-sm btn-light">Cerrar Sesión</Link>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="nav-link btn btn-sm btn-outline-light me-2">Iniciar Sesión</Link>
                        <Link v-if="canRegister" :href="route('register')" class="nav-link btn btn-sm btn-light">Registrarse</Link>
                    </template>
                </nav>
            </div>
            <div class="hero-content-reveal" :class="{ 'visible': !isScrolled }">
                <div class="container text-center hero-text-content">
                    <p class="lead fs-4 mb-4">{{ restaurantSlogan }}</p>
                    <a href="#main-content-start" class="btn btn-primary btn-lg hero-cta-button" @click.prevent="scrollToContent">
                        Descubre Más
                        <i class="bi bi-arrow-down-circle ms-2"></i>
                    </a>
                </div>
            </div>
        </header>
        
        <main id="main-content-start" class="content-below-hero">
            <div class="container py-5">
                <!-- Sección de Características Clave -->
                <section id="features" class="py-5 text-center">
                    <h2 class="display-5 fw-bold mb-5">Tu Experiencia Única</h2>
                    <div class="row gx-lg-5 gy-4">
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="feature-icon mb-3">
                                    <i class="bi bi-calendar-heart fs-1 text-primary"></i>
                                </div>
                                <h3 class="h4 mb-2">Reservas Mágicas</h3>
                                <p>Elige tu mesa ideal en nuestro mapa interactivo. Fácil, rápido y con confirmación instantánea.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="feature-icon mb-3">
                                    <i class="bi bi-journal-richtext fs-1 text-primary"></i>
                                </div>
                                <h3 class="h4 mb-2">Menús Exquisitos</h3>
                                <p>Explora nuestra carta, con ingredientes frescos y platos que deleitarán tus sentidos.</p>
                                <a href="#menus" class="btn btn-sm btn-outline-primary mt-3">Ver Menús</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card">
                                <div class="feature-icon mb-3">
                                    <i class="bi bi-stars fs-1 text-primary"></i>
                                </div>
                                <h3 class="h4 mb-2">Eventos Memorables</h3>
                                <p>Desde noches temáticas hasta música en vivo, siempre hay algo especial esperándote.</p>
                                <a href="#events" class="btn btn-sm btn-outline-primary mt-3">Próximos Eventos</a>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Sección de Menús (Placeholder) -->
                <section id="menus" class="py-5 my-5 bg-white rounded shadow-lg">
                    <div class="container">
                        <h2 class="text-center display-5 fw-bold mb-5">Delicias de la Casa</h2>
                        <p class="text-center lead mb-4">Un adelanto de lo que te espera. Para detalles y precios, te invitamos a <Link :href="route('login')">iniciar sesión</Link>.</p>
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card menu-item-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Plato Estrella</h5>
                                        <p class="card-text">Breve descripción del plato que incite a probarlo.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card menu-item-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Especialidad del Chef</h5>
                                        <p class="card-text">Otro plato destacado con sus características principales.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card menu-item-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Postre Soñado</h5>
                                        <p class="card-text">El final perfecto para una comida inolvidable.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Sección de Eventos Especiales (Placeholder) -->
                <section id="events" class="py-5">
                    <div class="container">
                        <h2 class="text-center display-5 fw-bold mb-5">Agenda de Eventos</h2>
                        <p class="text-center lead mb-4">No te pierdas nuestros próximos acontecimientos. Algunos requieren reserva.</p>
                        <div class="text-center">
                            <p>Próximamente más información...</p>
                        </div>
                    </div>
                </section>
            </div>
        </main>
        
        <footer class="main-footer py-4 mt-auto bg-dark text-white">
            <div class="container text-center">
                <p class="mb-1">&copy; {{ new Date().getFullYear() }} {{ restaurantName }}. Todos los derechos reservados.</p>
                <p class="small mb-1">Plataforma de reservas por ReserTable</p>
                <p class="small text-white-50">Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})</p>
            </div>
        </footer>
    </div>
</template>

<script setup>
function scrollToContent() {
    const el = document.getElementById('main-content-start');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>

<style scoped>
.hero-content-reveal {
    opacity: 1;
    transition: opacity 0.7s;
}
.hero-content-reveal.visible {
    opacity: 1;
}
.hero-content-reveal:not(.visible) {
    opacity: 0;
}

/* Asegúrate de tener Bootstrap 5 y Bootstrap Icons CSS incluidos en tu proyecto */
/* npm install bootstrap-icons */
/* @import "bootstrap-icons/font/bootstrap-icons.css"; en tu app.css o app.js */

.page-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-hero-header {
  background: linear-gradient(135deg, rgba(0, 50, 150, 0.85), rgba(0, 100, 200, 0.95)), url('/images/hero-background.jpg') no-repeat center center; /* Reemplaza con una imagen tuya o usa solo el gradiente */
  background-size: cover;
  color: white;
  padding: 1.5rem 0; /* Padding base para la barra de navegación */
  position: sticky;
  top: 0;
  z-index: 1030;
  width: 100%;
  transition: all 0.4s ease-in-out;
  min-height: 80px; /* Altura mínima de la barra de navegación */
}

.main-hero-header:not(.scrolled) {
  min-height: 100vh; /* Hero a pantalla completa */
  display: flex;
  flex-direction: column;
  justify-content: center; /* Centra el contenido del hero verticalmente */
}

.main-hero-header .container {
  transition: all 0.4s ease-in-out;
}

.main-hero-header:not(.scrolled) .container:first-child { /* Contenedor del logo y nav */
    position: absolute;
    top: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 1140px; /* o tu max-width de container */
}

.restaurant-name-hero {
  font-size: 2rem; /* Tamaño inicial del nombre del restaurante */
  font-weight: bold;
  margin: 0;
  color: white;
  transition: font-size 0.4s ease-in-out;
}

.main-hero-header.scrolled .restaurant-name-hero {
  font-size: 1.5rem; /* Nombre del restaurante más pequeño en la barra fija */
}

.logo-link {
    text-decoration: none;
}

.nav-link {
  transition: opacity 0.3s ease;
}

.nav-link:hover {
    opacity: 0.8;
}

.hero-content-reveal {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s ease-out;
  visibility: hidden;
  text-align: center;
  width: 100%;
  position: absolute; /* Posicionamiento absoluto para centrarlo en el hero no scrolleado */
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 0 1rem; /* Padding para móviles */
}

.hero-content-reveal.visible {
  opacity: 1;
  transform: translateY(0);
  visibility: visible;
}

.hero-content-reveal.active {
  transition-delay: 0.3s;
}

.main-hero-header.scrolled .hero-content-reveal {
  opacity: 0;
  transform: translateY(30px);
  visibility: hidden;
  transition-delay: 0s;
}

.hero-text-content {
    margin-top: -80px; /* Ajuste para compensar la altura del nav y logo cuando el hero es full screen */
}

.hero-cta-button {
    font-weight: bold;
    padding: 0.75rem 2rem;
    border-radius: 50px; /* Botón más redondeado */
    transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
}

.hero-cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.content-below-hero {
  /* padding-top: 80px;  No es necesario si el header es sticky y el main fluye debajo */
  flex-grow: 1;
  background-color: #f4f7f6; /* Un color de fondo suave para el contenido */
}

.feature-card {
    background-color: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}

.menu-item-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.menu-item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.main-footer {
    /* Estilos del footer ya definidos */
}

/* Iconos de Bootstrap (asegúrate de importarlos) */
.bi {
  vertical-align: -0.125em; /* Alineación de iconos */
}

/* Asegúrate de tener Bootstrap 5 y Bootstrap Icons CSS incluidos en tu proyecto */
/* npm install bootstrap-icons */
/* @import "bootstrap-icons/font/bootstrap-icons.css"; en tu app.css o app.js */

.page-wrapper {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.main-hero-header {
  background: linear-gradient(135deg, rgba(0, 50, 150, 0.85), rgba(0, 100, 200, 0.95)), url('/images/hero-background.jpg') no-repeat center center; /* Reemplaza con una imagen tuya o usa solo el gradiente */
  background-size: cover;
  color: white;
  padding: 1.5rem 0; /* Padding base para la barra de navegación */
  position: sticky;
  top: 0;
  z-index: 1030;
  width: 100%;
  transition: all 0.4s ease-in-out;
  min-height: 80px; /* Altura mínima de la barra de navegación */
}

.main-hero-header:not(.scrolled) {
  min-height: 100vh; /* Hero a pantalla completa */
  display: flex;
  flex-direction: column;
  justify-content: center; /* Centra el contenido del hero verticalmente */
}

.main-hero-header .container {
  transition: all 0.4s ease-in-out;
}

.main-hero-header:not(.scrolled) .container:first-child { /* Contenedor del logo y nav */
    position: absolute;
    top: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
    width: 100%;
    max-width: 1140px; /* o tu max-width de container */
}

.restaurant-name-hero {
  font-size: 2rem; /* Tamaño inicial del nombre del restaurante */
  font-weight: bold;
  margin: 0;
  color: white;
  transition: font-size 0.4s ease-in-out;
}

.main-hero-header.scrolled .restaurant-name-hero {
  font-size: 1.5rem; /* Nombre del restaurante más pequeño en la barra fija */
}

.logo-link {
    text-decoration: none;
}

.nav-link {
  transition: opacity 0.3s ease;
}

.nav-link:hover {
    opacity: 0.8;
}

.hero-content-reveal {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s ease-out;
  visibility: hidden;
  text-align: center;
  width: 100%;
  position: absolute; /* Posicionamiento absoluto para centrarlo en el hero no scrolleado */
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding: 0 1rem; /* Padding para móviles */
}

.hero-content-reveal.visible {
  opacity: 1;
  transform: translateY(0);
  visibility: visible;
}

.hero-content-reveal.active {
  transition-delay: 0.3s;
}

.main-hero-header.scrolled .hero-content-reveal {
  opacity: 0;
  transform: translateY(30px);
  visibility: hidden;
  transition-delay: 0s;
}

.hero-text-content {
    margin-top: -80px; /* Ajuste para compensar la altura del nav y logo cuando el hero es full screen */
}

.hero-cta-button {
    font-weight: bold;
    padding: 0.75rem 2rem;
    border-radius: 50px; /* Botón más redondeado */
    transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
}

.hero-cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.content-below-hero {
  /* padding-top: 80px;  No es necesario si el header es sticky y el main fluye debajo */
  flex-grow: 1;
  background-color: #f4f7f6; /* Un color de fondo suave para el contenido */
}

.feature-card {
    background-color: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}

.menu-item-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.menu-item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.main-footer {
    /* Estilos del footer ya definidos */
}

/* Iconos de Bootstrap (asegúrate de importarlos) */
.bi {
  vertical-align: -0.125em; /* Alineación de iconos */
}
</style>