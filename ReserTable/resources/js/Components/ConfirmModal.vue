<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ title }}
                </h3>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-4">
                <div class="flex items-start space-x-3">
                    <div class="text-3xl">{{ icon || '⚠️' }}</div>
                    <div class="flex-1">
                        <p class="text-gray-900 dark:text-white font-medium mb-2">
                            {{ message }}
                        </p>
                        <p v-if="description" class="text-sm text-gray-600 dark:text-gray-400">
                            {{ description }}
                        </p>
                    </div>
                </div>
                
                <!-- Additional content slot -->
                <div v-if="$slots.default" class="mt-4">
                    <slot></slot>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 rounded-b-lg flex justify-end space-x-3">
                <button
                    @click="$emit('cancel')"
                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors"
                >
                    {{ cancelText || 'Cancelar' }}
                </button>
                <button
                    @click="$emit('confirm')"
                    :disabled="loading"
                    :class="[
                        'px-4 py-2 rounded-lg transition-colors flex items-center space-x-2',
                        'disabled:opacity-50 disabled:cursor-not-allowed',
                        variant === 'danger' 
                            ? 'bg-red-600 text-white hover:bg-red-700' 
                            : 'bg-blue-600 text-white hover:bg-blue-700'
                    ]"
                >
                    <svg v-if="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>{{ confirmText || 'Confirmar' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        required: true
    },
    message: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: null
    },
    icon: {
        type: String,
        default: '⚠️'
    },
    confirmText: {
        type: String,
        default: 'Confirmar'
    },
    cancelText: {
        type: String,
        default: 'Cancelar'
    },
    variant: {
        type: String,
        default: 'primary', // 'primary' or 'danger'
        validator: value => ['primary', 'danger'].includes(value)
    },
    loading: {
        type: Boolean,
        default: false
    }
});

defineEmits(['confirm', 'cancel']);
</script>
