<template>
    <div class="filter-layout">
        <!-- Filter Title -->
        

        <!-- Mobile Filter Button - Only visible on mobile -->
        <div class="lg:hidden">
            <button 
                @click="showMobileFilters = true"
                class="mobile-filter-btn"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
              {{ $t('Filters') }}
            </button>
        </div>

        <!-- Mobile Filter Modal -->
        <div 
            v-if="showMobileFilters" 
            class="mobile-filter-modal md:hidden"
            @click.self="showMobileFilters = false"
        >
            <div class="mobile-filter-content">
                <div class="mobile-filter-header">
                    <h3 class="text-lg font-semibold">Filters</h3>
                    <button 
                        @click="showMobileFilters = false"
                        class="mobile-close-btn"
                    >
                        ×
                    </button>
                </div>

                <!-- Filter Groups -->
                <div class="mobile-filter-body">
                    <div class="filter-section">
                        <!-- Your existing filter groups here -->
                        <!-- Categories Filter -->
                        <!-- <div class="filter-group" v-if="subcategories.length">
                            <h4 class="filter-group-title">Categories</h4>
                            <div class="filter-options">
                                <label 
                                    v-for="subcategory in subcategories" 
                                    :key="subcategory.id"
                                    class="filter-option"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="filters.Categories[subcategory.name] || false"
                                        @change="handleFilterChange('Categories', subcategory.name)"
                                    >
                                    <span class="option-text">{{ subcategory.name }}</span>
                                </label>
                            </div>
                        </div> -->

                        <!-- Size Filter -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">{{ $t("Size") }}</h4>
                            <div class="filter-options">
                                <label 
                                    v-for="size in getFilterOptions('Size')" 
                                    :key="size"
                                    class="filter-option"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="filters.Size[size]"
                                        @change="handleFilterChange('Size', size)"
                                    >
                                    <span class="option-text">{{ size }}</span>
                                </label>
                            </div>
                        </div>

                     
                        <!-- Season Filter -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">{{ $t("Season") }}</h4>
                            <div class="filter-options">
                                <label 
                                    v-for="season in getFilterOptions('Season')" 
                                    :key="season"
                                    class="filter-option"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="filters.Season[season]"
                                        @change="handleFilterChange('Season', season)"
                                    >
                                    <span class="option-text">{{ season }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Quality Filter -->
                        <div class="filter-group">
                            <h4 class="filter-group-title">{{ $t("Quality") }}</h4>
                            <div class="filter-options">
                                <label 
                                    v-for="quality in getFilterOptions('Quality')" 
                                    :key="quality"
                                    class="filter-option"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="filters.Quality[quality]"
                                        @change="handleFilterChange('Quality', quality)"
                                    >
                                    <span class="option-text">{{ quality }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Apply Filters Button -->
                <div class="mobile-filter-footer">
                    <button 
                        @click="applyFilters"
                        class="apply-filters-btn"
                    >
                      {{ $t("Apply Filters") }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Layout -->
        <div class="main-content">
            <slot></slot>
        </div>

        <!-- Desktop Sidebar -->
        <div class="filter-sidebar hidden lg:block mb-4">
            <div class="filter-section pt-0">
                <div class="mb-4 lg:mb-6">
                    <h2 class="text-lg font-bold text-slate-700 leading-7">{{ $t("Filters") }}</h2>
                </div>
                <!-- Size Filter -->
                <div class="filter-group">
                    <h4 class="filter-group-title">{{ $t("Size") }}</h4>
                    <div class="filter-options">
                        <label 
                            v-for="size in getFilterOptions('Size')" 
                            :key="size"
                            class="filter-option"
                        >
                            <input 
                                type="checkbox" 
                                :checked="filters.Size[size]"
                                @change="handleFilterChange('Size', size)"
                            >
                            <span class="option-text">{{ size }}</span>
                        </label>
                    </div>
                </div>

       
                <!-- Season Filter -->
                <div class="filter-group">
                    <h4 class="filter-group-title">{{ $t("Season") }}</h4>
                    <div class="filter-options">
                        <label 
                            v-for="season in getFilterOptions('Season')" 
                            :key="season"
                            class="filter-option"
                        >
                            <input 
                                type="checkbox" 
                                :checked="filters.Season[season]"
                                @change="handleFilterChange('Season', season)"
                            >
                            <span class="option-text">{{ season }}</span>
                        </label>
                    </div>
                </div>

                <!-- Quality Filter -->
                <div class="filter-group">
                    <h4 class="filter-group-title">{{ $t("Quality") }}</h4>
                    <div class="filter-options">
                        <label 
                            v-for="quality in getFilterOptions('Quality')" 
                            :key="quality"
                            class="filter-option"
                        >
                            <input 
                                type="checkbox" 
                                :checked="filters.Quality[quality]"
                                @change="handleFilterChange('Quality', quality)"
                            >
                            <span class="option-text">{{ quality }}</span>
                        </label>
                    </div>
                </div>

                <!-- Clear Filters button -->
                <button 
                    v-if="Object.values(filters).some(f => Object.values(f).some(v => v))"
                    @click="clearFilters"
                    class="clear-filters-btn"
                >
                  {{ $t("Clear Filters") }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.filter-layout {
    @apply flex gap-6;
}

.main-content {
    @apply flex-1 min-w-0;
}

.filter-sidebar {
    @apply w-48 flex-shrink-0 bg-white rounded-lg border border-gray-200;
}

.filter-section {
    @apply p-4;
}

.filter-group {
    @apply mb-4 pb-4 border-b border-gray-200;
}

.filter-group:last-child {
    @apply border-b-0 mb-0 pb-0;
}

.filter-group-title {
    @apply text-sm font-medium text-gray-900 mb-2;
}

.filter-options {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.25rem 0;
}

.filter-option input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
    border-radius: 0.25rem;
    border-color: #d1d5db;
}

.option-text {
    font-size: 0.875rem;
    color: #4b5563;
}

.clear-filters-btn {
    width: 100%;
    padding: 0.5rem;
    background-color: #ef4444;
    color: white;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background-color 0.2s;
}

.clear-filters-btn:hover {
    background-color: #dc2626;
}

@media (max-width: 768px) {
    .filter-layout {
        flex-direction: column;
    }

    .filter-sidebar {
        width: 100%;
        order: -1;
    }
}

/* Mobile Styles */
.mobile-filter-header {
    padding: 1rem;
    background-color: white;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 40;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mobile-filter-btn {
    @apply lg:hidden; /* Hide on desktop */
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    color: #374151;
    margin: 1rem 0;
}

.mobile-filter-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 50;
    display: flex;
    justify-content: flex-end;
}

.mobile-filter-content {
    width: 100%;
    max-width: 320px;
    background-color: white;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.mobile-filter-body {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.mobile-filter-footer {
    padding: 1rem;
    border-top: 1px solid #e5e7eb;
    background-color: white;
}

.apply-filters-btn {
    width: 100%;
    padding: 0.75rem;
    background-color: theme('colors.primary');
    color: white;
    border-radius: 0.375rem;
    font-weight: 500;
    text-align: center;
}

.mobile-close-btn {
    font-size: 1.5rem;
    color: #374151;
    padding: 0.5rem;
}
</style>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useFilterStore } from '../stores/FilterStore'
import { storeToRefs } from 'pinia'
import { useMaster } from '../stores/MasterStore'
import { useRoute, useRouter } from 'vue-router'
import type { PropType } from 'vue'

const filterStore = useFilterStore()
const masterStore = useMaster()
const { activeFilter, popupPosition, filters } = storeToRefs(filterStore)
const containerRef = ref<HTMLElement | null>(null)
const route = useRoute()
const router = useRouter()

const availableFilters = {
    Categories: true,
    Season: true,
    Size: true,
    Color: true,
    Quality: true,
    Price: true
}

const hasSelectedFilters = computed(() => (filterName: string) => {
    if (!filters.value || !filters.value[filterName]) {
        return false
    }
    return Object.values(filters.value[filterName]).some(v => v)
})

const getSelectedCount = computed(() => (filterName: string) => {
    if (!filters.value || !filters.value[filterName]) {
        return 0
    }
    return Object.values(filters.value[filterName]).filter(v => v).length
})

const hasAnyFilters = computed(() => {
    return Object.keys(filters.value).some(key => 
        Object.values(filters.value[key]).some(v => v)
    )
})

const toggleFilter = (name, event) => {
    
    if (activeFilter.value === name) {
        activeFilter.value = null
        return
    }
    
    activeFilter.value = name
    nextTick(() => {
        setPopupPosition(event.currentTarget)
    })
}

const closePopup = () => {
    activeFilter.value = null
}

const handleFilterChange = (filterName: string, option: string) => {
    if (!filters.value[filterName]) {
        filters.value[filterName] = {}
    }

    // Create a new object to ensure reactivity
    const updatedFilters = {
        ...filters.value[filterName],
        [option]: !filters.value[filterName][option]
    }

    // Remove the option if it's being unchecked
    if (!updatedFilters[option]) {
        delete updatedFilters[option]
    }

    filters.value[filterName] = updatedFilters
    filterStore.saveFilters()

    // Get active filters and emit changes
    const activeFilters = filterStore.getActiveFilters()
    updateUrlWithFilters(activeFilters)
    
    // Emit the filter changes with the complete filters object
    emit('filtersChanged', filters.value)
}

const updateUrlWithFilters = (activeFilters: Record<string, string[]>) => {
    const queryParams: Record<string, string> = {}
    
    // Only add parameters for filters that have selected values
    Object.entries(activeFilters).forEach(([key, values]) => {
        if (values && values.length > 0) {
            queryParams[key.toLowerCase()] = values.join(',')
        }
    })

    // If no active filters, remove all filter params from URL
    if (Object.keys(queryParams).length === 0) {
        // Keep other query params that aren't related to filters
        const nonFilterParams = { ...route.query }
        delete nonFilterParams.categories
        delete nonFilterParams.size
        delete nonFilterParams.color
        delete nonFilterParams.season
        delete nonFilterParams.quality
        
        router.push({ 
            path: route.path,
            query: nonFilterParams
        })
    } else {
        router.push({ 
            path: route.path,
            query: {
                ...route.query,
                ...queryParams
            }
        })
    }
}

const clearFilters = () => {
    Object.keys(filters.value).forEach((key) => {
        filters.value[key] = {}
    })
    activeFilter.value = null

    // Clear all query parameters
    router.push({ path: route.path, query: {} })
    emit('filtersChanged', {})
}

const getFilterOptions = (filterName) => {
    return filterStore.getFilterOptions(filterName)
}

// Price range state
const priceRange = ref({
    min: 0,
    max: 1000
})
const maxPrice = 10000

// Update popup position on scroll/resize
const updatePopupPosition = () => {
    if (activeFilter.value && containerRef.value) {
        const activeButton = containerRef.value.querySelector('.filter-btn.active')
        if (activeButton) {
            setPopupPosition(activeButton)
        }
    }
}

const setPopupPosition = (element) => {
    const rect = element.getBoundingClientRect()
    popupPosition.value = {
        top: `${rect.bottom + window.scrollY}px`,
        left: `${rect.left + window.scrollX}px`,
        minWidth: '300px',
        transform: 'none'
    }
}

// Handle price change
const handlePriceChange = () => {
    // Ensure min <= max
    if (priceRange.value.min > priceRange.value.max) {
        priceRange.value.min = priceRange.value.max
    }

    const filters = {
        min_price: priceRange.value.min,
        max_price: priceRange.value.max
    }

    emit('filtersChanged', filters)
}

// Watch for filter changes to update UI
watch(() => filters.value, (newFilters) => {
    console.log('Filters updated:', newFilters) // Debug log
}, { deep: true })

// Initialize filters from URL on mount
onMounted(() => {
    const queryParams = route.query
    if (Object.keys(queryParams).length > 0) {
        const initialFilters: Record<string, Record<string, boolean>> = {}
        
        Object.entries(queryParams).forEach(([key, value]) => {
            if (typeof value === 'string') {
                const values = value.split(',')
                initialFilters[key.charAt(0).toUpperCase() + key.slice(1)] = {}
                values.forEach(v => {
                    initialFilters[key.charAt(0).toUpperCase() + key.slice(1)][v] = true
                })
            }
        })
        
        filterStore.setFilters(initialFilters)
    }

    window.addEventListener('scroll', updatePopupPosition)
    window.addEventListener('resize', updatePopupPosition)
})

onUnmounted(() => {
    window.removeEventListener('scroll', updatePopupPosition)
    window.removeEventListener('resize', updatePopupPosition)
})

// Emit events for parent components
const emit = defineEmits(['filtersChanged'])

// Click outside directive
const clickOutside = {
    mounted(el, { value }) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                value(event)
            }
        }
        document.addEventListener('click', el.clickOutsideEvent)
    },
    unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent)
    },
}

// Add subcategories prop
const props = defineProps({
    subcategories: {
        type: Array as PropType<Array<{ id: number; name: string }>>,
        default: () => []
    }
})

// Add new refs
const showMobileFilters = ref(false)

// Add new method
const applyFilters = () => {
    showMobileFilters.value = false
    // Any additional filter application logic
}
</script>