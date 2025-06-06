<template>
  <nav v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6" aria-label="Pagination">
    <div class="hidden sm:block">
      <p class="text-sm text-gray-700">
        Mostrando
        <span class="font-medium">{{ from }}</span>
        a
        <span class="font-medium">{{ to }}</span>
        de
        <span class="font-medium">{{ total }}</span>
        resultados
      </p>
    </div>
    <div class="flex flex-1 justify-between sm:justify-end">
      <Link
        v-if="links[0].url"
        :href="links[0].url"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
        :class="{ 'cursor-not-allowed': !links[0].url }"
      >
        Anterior
      </Link>
      <span
        v-else
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed"
      >
        Anterior
      </span>

      <div class="hidden md:flex">
        <template v-for="(link, index) in links.slice(1, -1)" :key="index">
          <Link
            v-if="link.url"
            :href="link.url"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium border-t border-b border-gray-300 hover:bg-gray-50"
            :class="{
              'z-10 bg-indigo-50 border-indigo-500 text-indigo-600': link.active,
              'bg-white text-gray-700 hover:text-gray-500': !link.active,
              'border-l': index === 0,
              'border-r': index === links.slice(1, -1).length - 1
            }"
            v-html="link.label"
          />
          <span
            v-else
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border-t border-b border-gray-300"
            :class="{
              'border-l': index === 0,
              'border-r': index === links.slice(1, -1).length - 1
            }"
            v-html="link.label"
          />
        </template>
      </div>

      <Link
        v-if="links[links.length - 1].url"
        :href="links[links.length - 1].url"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
      >
        Siguiente
      </Link>
      <span
        v-else
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-gray-100 px-4 py-2 text-sm font-medium text-gray-400 cursor-not-allowed"
      >
        Siguiente
      </span>
    </div>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  links: {
    type: Array,
    required: true
  },
  from: Number,
  to: Number,
  total: Number
})

// Si no se proporcionan from, to, total explícitamente, 
// intentamos extraerlos de la estructura de links
const from = computed(() => props.from || (props.links.length > 0 ? 1 : 0))
const to = computed(() => props.to || props.links.length)
const total = computed(() => props.total || props.links.length)
</script>
