<template>
    <AdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark">{{ t('admin.settings.title') }}</h1>
                    <p class="text-muted mb-0">{{ t('admin.settings.subtitle') }}</p>
                </div>
            </div>

            <!-- Settings Cards -->
            <div class="row g-4">
                <!-- Apariencia -->
                <div class="col-lg-6">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title mb-0">
                                <i class="pi pi-palette me-2"></i>
                                {{ t('admin.settings.appearance') }}
                            </h5>
                        </div>
                        <div class="admin-card-body">
                            <!-- Modo Oscuro -->
                            <div class="form-check form-switch mb-3">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="darkModeSwitch"
                                    v-model="form.dark_mode"
                                    @change="toggleDarkMode"
                                >
                                <label class="form-check-label" for="darkModeSwitch">
                                    <strong>{{ t('admin.settings.dark_mode') }}</strong>
                                    <small class="d-block text-muted">{{ t('admin.settings.dark_mode_description') }}</small>
                                </label>
                            </div>

                            <!-- Color del Tema -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">{{ t('admin.settings.theme_color') }}</label>
                                <div class="row g-2">
                                    <div 
                                        v-for="(name, color) in themeOptions" 
                                        :key="color"
                                        class="col-4"
                                    >
                                        <div 
                                            class="theme-color-option"
                                            :class="{ active: form.theme_color === color }"
                                            @click="form.theme_color = color"
                                        >
                                            <div :class="`theme-color-circle bg-${color}`"></div>
                                            <span class="small">{{ name }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estilo de Sidebar -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">{{ t('admin.settings.sidebar') }}</label>
                                <div class="btn-group w-100" role="group">
                                    <input 
                                        type="radio" 
                                        class="btn-check" 
                                        name="sidebarStyle" 
                                        id="sidebarExpanded"
                                        value="expanded"
                                        v-model="form.sidebar_style"
                                    >
                                    <label class="btn btn-outline-primary" for="sidebarExpanded">
                                        <i class="pi pi-bars me-1"></i> {{ t('admin.settings.expanded') }}
                                    </label>

                                    <input 
                                        type="radio" 
                                        class="btn-check" 
                                        name="sidebarStyle" 
                                        id="sidebarCollapsed"
                                        value="collapsed"
                                        v-model="form.sidebar_style"
                                    >
                                    <label class="btn btn-outline-primary" for="sidebarCollapsed">
                                        <i class="pi pi-minus me-1"></i> {{ t('admin.settings.collapsed') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Idioma y Región -->
                <div class="col-lg-6">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title mb-0">
                                <i class="pi pi-globe me-2"></i>
                                {{ t('admin.settings.language_region') }}
                            </h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="mb-3">
                                <label class="form-label fw-medium">{{ t('admin.settings.language') }}</label>
                                <Select 
                                    v-model="form.language" 
                                    :options="languageOptions" 
                                    optionLabel="label" 
                                    optionValue="value" 
                                    class="w-100"
                                    :placeholder="t('admin.settings.select_language')"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notificaciones -->
                <div class="col-lg-6">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title mb-0">
                                <i class="pi pi-bell me-2"></i>
                                {{ t('admin.settings.notifications') }}
                            </h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="form-check form-switch mb-3">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="notificationsSwitch"
                                    v-model="form.notifications_enabled"
                                >
                                <label class="form-check-label" for="notificationsSwitch">
                                    <strong>{{ t('admin.settings.notifications_enabled') }}</strong>
                                    <small class="d-block text-muted">{{ t('admin.settings.notifications_description') }}</small>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="soundSwitch"
                                    v-model="form.sound_enabled"
                                    :disabled="!form.notifications_enabled"
                                >
                                <label class="form-check-label" for="soundSwitch">
                                    <strong>{{ t('admin.settings.sound_enabled') }}</strong>
                                    <small class="d-block text-muted">{{ t('admin.settings.sounds_description') }}</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard -->
                <div class="col-lg-6">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title mb-0">
                                <i class="pi pi-th-large me-2"></i>
                                {{ t('admin.settings.dashboard') }}
                            </h5>
                        </div>
                        <div class="admin-card-body">
                            <p class="text-muted mb-3">{{ t('admin.settings.dashboard_description') }}</p>
                            
                            <div class="form-check mb-2">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="statsWidget"
                                    v-model="form.dashboard_widgets.stats"
                                >
                                <label class="form-check-label" for="statsWidget">
                                    {{ t('admin.settings.stats_widget') }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="reservationsWidget"
                                    v-model="form.dashboard_widgets.recent_reservations"
                                >
                                <label class="form-check-label" for="reservationsWidget">
                                    {{ t('admin.settings.reservations_widget') }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="actionsWidget"
                                    v-model="form.dashboard_widgets.quick_actions"
                                >
                                <label class="form-check-label" for="actionsWidget">
                                    {{ t('admin.settings.actions_widget') }}
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="calendarWidget"
                                    v-model="form.dashboard_widgets.calendar"
                                >
                                <label class="form-check-label" for="calendarWidget">
                                    {{ t('admin.settings.calendar_widget') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2">
                        <Button 
                            :label="t('admin.settings.save_changes')" 
                            icon="pi pi-check" 
                            class="p-button-primary"
                            @click="saveSettings"
                            :loading="saving"
                        />
                        <Button 
                            :label="t('admin.settings.reset_defaults')" 
                            icon="pi pi-refresh" 
                            class="p-button-secondary"
                            @click="resetToDefaults"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Button from 'primevue/button'
import Select from 'primevue/select'

const { t } = useI18n()

const props = defineProps({
    settings: Object,
    availableThemes: Object,
    availableLanguages: Object
})

const saving = ref(false)

const form = reactive({
    dark_mode: props.settings?.dark_mode || false,
    language: props.settings?.language || 'es',
    theme_color: props.settings?.theme_color || 'blue',
    notifications_enabled: props.settings?.notifications_enabled || true,
    sound_enabled: props.settings?.sound_enabled || true,
    sidebar_style: props.settings?.sidebar_style || 'expanded',
    dashboard_widgets: props.settings?.dashboard_widgets || {
        stats: true,
        recent_reservations: true,
        quick_actions: true,
        calendar: true
    }
})

const languageOptions = computed(() => {
    return Object.entries(props.availableLanguages).map(([value, label]) => ({
        label,
        value
    }))
})

const themeOptions = computed(() => {
    const themes = {}
    Object.keys(props.availableThemes).forEach(color => {
        themes[color] = t(`admin.settings.themes.${color}`)
    })
    return themes
})

const toggleDarkMode = () => {
    // Aplicar el cambio inmediatamente en el DOM
    applyDarkMode(form.dark_mode)
    
    // Enviar request para persistir el cambio
    router.patch(route('admin.settings.dark-mode'), {
        dark_mode: form.dark_mode
    }, {
        preserveState: true,
        preserveScroll: true,
        onError: () => {
            // Si hay error, revertir el cambio
            form.dark_mode = !form.dark_mode
            applyDarkMode(form.dark_mode)
        }
    })
}

const applyDarkMode = (isDark) => {
    if (isDark) {
        document.documentElement.classList.add('dark')
        document.body.classList.add('dark-mode')
    } else {
        document.documentElement.classList.remove('dark')
        document.body.classList.remove('dark-mode')
    }
}

const saveSettings = () => {
    saving.value = true
    
    router.patch(route('admin.settings.update'), form, {
        onSuccess: () => {
            saving.value = false
            // Aplicar los cambios de tema
            applyThemeColor(form.theme_color)
            applyDarkMode(form.dark_mode)
        },
        onError: () => {
            saving.value = false
        }
    })
}

const applyThemeColor = (color) => {
    document.documentElement.setAttribute('data-theme-color', color)
}

const resetToDefaults = () => {
    if (confirm(t('admin.settings.confirm_reset'))) {
        form.dark_mode = false
        form.language = 'es'
        form.theme_color = 'blue'
        form.notifications_enabled = true
        form.sound_enabled = true
        form.sidebar_style = 'expanded'
        form.dashboard_widgets = {
            stats: true,
            recent_reservations: true,
            quick_actions: true,
            calendar: true
        }
        
        saveSettings()
    }
}

// Aplicar configuraciones al cargar la página
if (typeof window !== 'undefined') {
    applyDarkMode(form.dark_mode)
    applyThemeColor(form.theme_color)
}

// Observar cambios en el color del tema
watch(() => form.theme_color, (newColor) => {
    applyThemeColor(newColor)
})
</script>

<style scoped>
.theme-color-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.75rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.theme-color-option:hover {
    background-color: var(--bs-gray-100);
}

:global(body.dark-mode) .theme-color-option:hover {
    background-color: #3a3a3a !important;
}

.theme-color-option.active {
    border-color: var(--bs-primary);
    background-color: var(--bs-primary-bg-subtle);
}

:global(body.dark-mode) .theme-color-option.active {
    border-color: #4d7cfe !important;
    background-color: rgba(77, 124, 254, 0.2) !important;
}

.theme-color-circle {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    margin-bottom: 0.5rem;
    border: 2px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.bg-blue { background-color: #0d6efd !important; }
.bg-green { background-color: #198754 !important; }
.bg-purple { background-color: #6f42c1 !important; }
.bg-orange { background-color: #fd7e14 !important; }
.bg-red { background-color: #dc3545 !important; }
.bg-gray { background-color: #6c757d !important; }

/* Dark mode styles */
:global(body.dark-mode) {
    background-color: #1a1a1a !important;
    color: #f0f0f0 !important;
    transition: background-color 0.3s ease, color 0.3s ease;
}

:global(body.dark-mode .admin-card) {
    background-color: #2d2d2d !important;
    border-color: #404040 !important;
    color: #f0f0f0 !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
}

:global(body.dark-mode .admin-card-header) {
    background-color: #262626 !important;
    border-color: #404040 !important;
}

:global(body.dark-mode .text-dark) {
    color: #f0f0f0 !important;
}

:global(body.dark-mode .text-muted) {
    color: #b3b3b3 !important;
}

:global(body.dark-mode .form-control),
:global(body.dark-mode .form-select),
:global(body.dark-mode .p-dropdown),
:global(body.dark-mode .p-inputtext) {
    background-color: #2c2c2c !important;
    border-color: #555555 !important;
    color: #f0f0f0 !important;
}

:global(body.dark-mode .form-check-input) {
    background-color: #2c2c2c !important;
    border-color: #555555 !important;
}

:global(body.dark-mode .form-check-input:checked) {
    background-color: #4d7cfe !important;
    border-color: #4d7cfe !important;
}

:global(body.dark-mode .form-switch .form-check-input) {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e") !important;
}

:global(body.dark-mode .form-check-label) {
    color: #f0f0f0 !important;
}

:global(body.dark-mode .btn-outline-primary) {
    border-color: #4d7cfe !important;
    color: #4d7cfe !important;
}

:global(body.dark-mode .btn-outline-primary:hover) {
    background-color: #4d7cfe !important;
    color: #ffffff !important;
}

:global(body.dark-mode .btn-primary) {
    background-color: #4d7cfe !important;
    border-color: #4d7cfe !important;
}

:global(body.dark-mode .btn-secondary) {
    background-color: #505050 !important;
    border-color: #505050 !important;
}

:global(body.dark-mode .p-button.p-button-primary) {
    background-color: #4d7cfe !important;
    border-color: #4d7cfe !important;
}

:global(body.dark-mode .p-button.p-button-secondary) {
    background-color: #505050 !important;
    border-color: #505050 !important;
}

:global(body.dark-mode .p-dropdown-panel) {
    background-color: #2c2c2c !important;
    border-color: #555555 !important;
}

:global(body.dark-mode .p-dropdown-item) {
    color: #f0f0f0 !important;
}
</style>
