<script setup>
import { computed } from 'vue'

const props = defineProps({
    dish: {
        type: String,
        required: true
    },
    size: {
        type: Number,
        default: 200
    }
})

const svgContent = computed(() => {
    const svgs = {
        // SVGs simplificados pero atractivos para cada plato
        bruschetta: `<circle cx="100" cy="100" r="80" fill="#E74C3C"/><rect x="60" y="130" width="80" height="20" rx="10" fill="#D4A574"/><circle cx="80" cy="80" r="8" fill="#27AE60"/>`,
        antipasto: `<circle cx="100" cy="100" r="80" fill="#F8F9FA" stroke="#E9ECEF" stroke-width="3"/><circle cx="80" cy="90" r="10" fill="#8B4513"/><circle cx="120" cy="110" r="8" fill="#CD853F"/><rect x="90" y="120" width="12" height="8" rx="2" fill="#FFF8DC"/>`,
        carbonara: `<circle cx="100" cy="100" r="80" fill="#F8F9FA" stroke="#E9ECEF" stroke-width="3"/><path d="M50 90 Q100 60 150 90" stroke="#F4A460" stroke-width="4" fill="none"/><circle cx="90" cy="85" r="3" fill="#8B4513"/><circle cx="110" cy="95" r="2" fill="#FFF8DC"/>`,
        margherita: `<circle cx="100" cy="100" r="75" fill="#DEB887"/><circle cx="100" cy="100" r="70" fill="#DC143C"/><circle cx="85" cy="85" r="8" fill="#FFF8DC"/><circle cx="115" cy="115" r="8" fill="#FFF8DC"/><ellipse cx="100" cy="90" rx="4" ry="2" fill="#228B22"/>`,
        tiramisu: `<rect x="50" y="80" width="100" height="40" rx="5" fill="#DEB887"/><rect x="52" y="95" width="96" height="6" fill="#8B4513"/><ellipse cx="100" cy="75" rx="45" ry="8" fill="#8B4513"/>`,
        salmon: `<ellipse cx="100" cy="100" rx="70" ry="40" fill="#F8F9FA" stroke="#E9ECEF" stroke-width="3"/><ellipse cx="100" cy="100" rx="50" ry="25" fill="#FA8072"/><line x1="70" y1="90" x2="130" y2="90" stroke="#8B4513" stroke-width="2"/>`,
        default: `<circle cx="100" cy="100" r="75" fill="#E9ECEF"/><text x="100" y="110" text-anchor="middle" font-size="40">🍽️</text>`
    }

    const selectedSvg = svgs[props.dish] || svgs.default
    
    return `
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
            ${selectedSvg}
        </svg>
    `
})
</script>

<template>
    <div 
        :style="{ width: size + 'px', height: size + 'px' }"
        class="dish-svg rounded-lg shadow-lg bg-gradient-to-br from-gray-50 to-gray-100"
        v-html="svgContent"
    />
</template>

<style scoped>
.dish-svg {
    display: inline-block;
    overflow: hidden;
}
</style>
