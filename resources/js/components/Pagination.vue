<template>
    <nav class="flex items-center justify-center space-x-1">
        <!-- Previous Button -->
        <button 
            @click="$emit('page-changed', pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            :class="[
                'px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                pagination.current_page === 1 
                    ? 'text-gray-400 bg-gray-100 cursor-not-allowed' 
                    : 'text-gray-700 bg-gray-100 hover:bg-primary hover:text-white'
            ]"
        >
            <ChevronLeftIcon class="h-5 w-5" />
        </button>

        <!-- Page Numbers -->
        <div class="hidden sm:flex space-x-1">
            <!-- First Page -->
            <button 
                v-if="showFirstPage"
                @click="$emit('page-changed', 1)"
                :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                    pagination.current_page === 1 
                        ? 'bg-primary text-white' 
                        : 'text-gray-700 hover:bg-primary hover:text-white'
                ]"
            >
                1
            </button>

            <!-- Left Ellipsis -->
            <span 
                v-if="showLeftEllipsis" 
                class="px-3 py-2 text-gray-400"
            >
                ...
            </span>

            <!-- Middle Pages -->
            <button 
                v-for="page in middlePages" 
                :key="page"
                @click="$emit('page-changed', page)"
                :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                    pagination.current_page === page 
                        ? 'bg-primary text-white' 
                        : 'text-gray-700 hover:bg-primary hover:text-white'
                ]"
            >
                {{ page }}
            </button>

            <!-- Right Ellipsis -->
            <span 
                v-if="showRightEllipsis" 
                class="px-3 py-2 text-gray-400"
            >
                ...
            </span>

            <!-- Last Page -->
            <button 
                v-if="showLastPage"
                @click="$emit('page-changed', pagination.last_page)"
                :class="[
                    'px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                    pagination.current_page === pagination.last_page 
                        ? 'bg-primary text-white' 
                        : 'text-gray-700 hover:bg-primary hover:text-white'
                ]"
            >
                {{ pagination.last_page }}
            </button>
        </div>

        <!-- Mobile Page Indicator -->
        <span class="sm:hidden px-3 py-2 text-sm text-gray-700">
            {{ pagination.current_page }} / {{ pagination.last_page }}
        </span>

        <!-- Next Button -->
        <button 
            @click="$emit('page-changed', pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            :class="[
                'px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200',
                pagination.current_page === pagination.last_page 
                    ? 'text-gray-400 bg-gray-100 cursor-not-allowed' 
                    : 'text-gray-700 bg-gray-100 hover:bg-primary hover:text-white'
            ]"
        >
            <ChevronRightIcon class="h-5 w-5" />
        </button>
    </nav>
</template>

<script setup>
import { computed } from 'vue'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    pagination: {
        type: Object,
        required: true
    }
})

// Calculate which page numbers to show
const middlePages = computed(() => {
    const current = props.pagination.current_page
    const last = props.pagination.last_page
    const delta = 2 // Number of pages to show on each side of current page
    
    let pages = []
    
    for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
        pages.push(i)
    }
    
    return pages
})

// Show/hide first page and ellipsis
const showFirstPage = computed(() => {
    return props.pagination.last_page > 1
})

const showLeftEllipsis = computed(() => {
    return props.pagination.current_page - 3 > 1
})

// Show/hide last page and ellipsis
const showRightEllipsis = computed(() => {
    return props.pagination.current_page + 3 < props.pagination.last_page
})

const showLastPage = computed(() => {
    return props.pagination.last_page > 1 && props.pagination.current_page !== props.pagination.last_page
})

defineEmits(['page-changed'])
</script> 