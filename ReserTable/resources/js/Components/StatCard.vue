<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    value: {
        type: [Number, String],
        required: true
    },
    icon: {
        type: String,
        default: '📊'
    },
    color: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'green', 'yellow', 'red', 'purple', 'indigo', 'pink', 'gray'].includes(value)
    }
});

const getColorClasses = (color) => {
    const colorMap = {
        blue: {
            bg: 'bg-blue-50 dark:bg-blue-900/20',
            text: 'text-blue-600 dark:text-blue-400',
            icon: 'text-blue-500 dark:text-blue-400'
        },
        green: {
            bg: 'bg-green-50 dark:bg-green-900/20',
            text: 'text-green-600 dark:text-green-400',
            icon: 'text-green-500 dark:text-green-400'
        },
        yellow: {
            bg: 'bg-yellow-50 dark:bg-yellow-900/20',
            text: 'text-yellow-600 dark:text-yellow-400',
            icon: 'text-yellow-500 dark:text-yellow-400'
        },
        red: {
            bg: 'bg-red-50 dark:bg-red-900/20',
            text: 'text-red-600 dark:text-red-400',
            icon: 'text-red-500 dark:text-red-400'
        },
        purple: {
            bg: 'bg-purple-50 dark:bg-purple-900/20',
            text: 'text-purple-600 dark:text-purple-400',
            icon: 'text-purple-500 dark:text-purple-400'
        },
        indigo: {
            bg: 'bg-indigo-50 dark:bg-indigo-900/20',
            text: 'text-indigo-600 dark:text-indigo-400',
            icon: 'text-indigo-500 dark:text-indigo-400'
        },
        pink: {
            bg: 'bg-pink-50 dark:bg-pink-900/20',
            text: 'text-pink-600 dark:text-pink-400',
            icon: 'text-pink-500 dark:text-pink-400'
        },
        gray: {
            bg: 'bg-gray-50 dark:bg-gray-800',
            text: 'text-gray-600 dark:text-gray-400',
            icon: 'text-gray-500 dark:text-gray-400'
        }
    };
    
    return colorMap[color] || colorMap.blue;
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div :class="[
                        'w-12 h-12 rounded-lg flex items-center justify-center text-2xl',
                        getColorClasses(color).bg
                    ]">
                        <span :class="getColorClasses(color).icon">{{ icon }}</span>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                        {{ title }}
                    </div>
                    <div :class="[
                        'text-2xl font-bold',
                        getColorClasses(color).text
                    ]">
                        {{ value }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
