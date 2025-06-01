<script setup>
import { usePage } from '@inertiajs/vue3';
const laravelVersion = usePage().props.laravelVersion ?? '';
const phpVersion = usePage().props.phpVersion ?? '';
</script>

<template>
    <footer class="bg-dark dark:bg-gray-900 text-white dark:text-gray-100 py-4 mt-auto border-top dark:border-gray-700 transition-colors duration-300">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="text-white dark:text-gray-100 mb-2">
                        <i class="pi pi-utensils me-2"></i>
                        El Sabor Auténtico
                    </h5>
                    <p class="small text-white-50 dark:text-gray-400 mb-0">
                        Experiencias culinarias únicas desde 1995
                    </p>
                </div>
                
                <div class="col-md-4 mb-3 mb-md-0">
                    <h6 class="text-white dark:text-gray-100 mb-2">Contacto</h6>
                    <p class="small text-white-50 dark:text-gray-400 mb-1">
                        <i class="pi pi-phone me-1"></i>
                        +34 912 345 678
                    </p>
                    <p class="small text-white-50 dark:text-gray-400 mb-1">
                        <i class="pi pi-envelope me-1"></i>
                        info@elsaborautentico.com
                    </p>
                    <p class="small text-white-50 dark:text-gray-400 mb-0">
                        <i class="pi pi-map-marker me-1"></i>
                        Calle Mayor 123, Madrid
                    </p>
                </div>
                
                <div class="col-md-4">
                    <h6 class="text-white dark:text-gray-100 mb-2">Síguenos</h6>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="text-white-50 dark:text-gray-400 hover:text-white dark:hover:text-gray-200 transition-colors" aria-label="Facebook">
                            <i class="pi pi-facebook fs-5"></i>
                        </a>
                        <a href="#" class="text-white-50 dark:text-gray-400 hover:text-white dark:hover:text-gray-200 transition-colors" aria-label="Instagram">
                            <i class="pi pi-instagram fs-5"></i>
                        </a>
                        <a href="#" class="text-white-50 dark:text-gray-400 hover:text-white dark:hover:text-gray-200 transition-colors" aria-label="Twitter">
                            <i class="pi pi-twitter fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <hr class="my-4 border-white-25 dark:border-gray-600">
            
            <div class="row">
                <div class="col-md-6 text-start">
                    <p class="mb-1 text-white-50 dark:text-gray-400">
                        &copy; {{ new Date().getFullYear() }} El Sabor Auténtico. Todos los derechos reservados.
                    </p>
                    <p class="small mb-0 text-white-50 dark:text-gray-400">
                        Plataforma de reservas por ReserTable
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="small text-white-50 dark:text-gray-400 mb-0" v-if="laravelVersion && phpVersion">
                        <i class="pi pi-code me-1"></i>
                        Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                    </p>
                </div>
            </div>
            
            <slot name="extra" />
        </div>
    </footer>
</template>

<style scoped>
footer {
    backdrop-filter: blur(10px);
}

.border-white-25 {
    border-color: rgba(255, 255, 255, 0.25) !important;
}

.dark .border-white-25 {
    border-color: rgba(75, 85, 99, 0.5) !important;
}

/* Efectos hover mejorados */
footer a:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

footer .social-links a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    transition: all 0.3s ease;
}

footer .social-links a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px) scale(1.1);
}

.dark footer .social-links a:hover {
    background-color: rgba(75, 85, 99, 0.3);
}
</style>
