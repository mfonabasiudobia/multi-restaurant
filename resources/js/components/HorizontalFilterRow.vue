<template>
    <div class="filter-layout">
        <!-- Filter Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex-1 sm:hidden">All Products</div>
            <!-- Desktop Filter Buttons -->
            <div class="hidden sm:flex items-center gap-4">
                <!-- Categories Filter -->
                <div class="filter-group" v-if="props.categories && props.categories.length > 0">
                    <button 
                        @click.stop="toggleFilter('Categories')"
                        class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 text-sm font-medium"
                        :class="{ 'border-primary text-primary': activeFilter === 'Categories' }"
                    >
                        Categories
                        <span v-if="getSelectedCount('Categories')" class="bg-primary text-white text-xs rounded-full px-2 py-0.5">
                            {{ getSelectedCount('Categories') }}
                        </span>
                    </button>
                    <!-- Categories Popup -->
                    <div v-if="activeFilter === 'Categories'" class="filter-popup">
                        <div class="p-4">
                            <div class="space-y-2">
                                <label 
                                    v-for="category in props.categories" 
                                    :key="category.id"
                                    class="filter-option"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="filters.Categories?.[category.name]"
                                        @change="handleFilterChange('Categories', category.name)"
                                    >
                                    <span class="ml-2">{{ category.name }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subcategories Filter -->
                <div class="filter-group" v-if="subcategories.length">
                    <button 
                        @click.stop="toggleFilter('Subcategories')"
                        class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 text-sm font-medium"
                        :class="{ 'border-primary text-primary': activeFilter === 'Subcategories' }"
                    >
                        Subcategories
                        <span v-if="getSelectedCount('Subcategories')" class="bg-primary text-white text-xs rounded-full px-2 py-0.5">
                            {{ getSelectedCount('Subcategories') }}
                        </span>
                    </button>
                    <!-- Subcategories Popup -->
                    <div v-if="activeFilter === 'Subcategories'" class="filter-popup">
                        <div class="p-4">
                            <div class="space-y-2">
                                <label 
                                    v-for="subcategory in subcategories" 
                                    :key="subcategory.id"
                                    class="filter-option"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="filters.Subcategories?.[subcategory.name]"
                                        @change="handleFilterChange('Subcategories', subcategory.name)"
                                    >
                                    <span class="ml-2">{{ subcategory.name }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <button 
                        @click.stop="toggleFilter('Size')"
                        class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 text-sm font-medium"
                        :class="{ 'border-primary text-primary': activeFilter === 'Size' }"
                    >
                        Size
                        <span v-if="getSelectedCount('Size')" class="bg-primary text-white text-xs rounded-full px-2 py-0.5">
                            {{ getSelectedCount('Size') }}
                        </span>
                    </button>
                    <!-- Size Popup -->
                    <div v-if="activeFilter === 'Size'" class="filter-popup">
                        <div class="p-4">
                            <div class="space-y-2">
                                <label 
                                    v-for="size in getFilterOptions('Size')" 
                                    :key="size"
                                    class="filter-option"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="filters.Size?.[size]"
                                        @change="handleFilterChange('Size', size)"
                                    >
                                    <span class="ml-2">{{ size }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <button 
                        @click.stop="toggleFilter('Season')"
                        class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 text-sm font-medium"
                        :class="{ 'border-primary text-primary': activeFilter === 'Season' }"
                    >
                        Season
                        <span v-if="getSelectedCount('Season')" class="bg-primary text-white text-xs rounded-full px-2 py-0.5">
                            {{ getSelectedCount('Season') }}
                        </span>
                    </button>
                    <!-- Season Popup -->
                    <div v-if="activeFilter === 'Season'" class="filter-popup">
                        <div class="p-4">
                            <div class="space-y-2">
                                <label 
                                    v-for="season in getFilterOptions('Season')" 
                                    :key="season"
                                    class="filter-option"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="filters.Season?.[season]"
                                        @change="handleFilterChange('Season', season)"
                                    >
                                    <span class="ml-2">{{ season }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <button 
                        @click.stop="toggleFilter('Quality')"
                        class="flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 text-sm font-medium"
                        :class="{ 'border-primary text-primary': activeFilter === 'Quality' }"
                    >
                        Quality
                        <span v-if="getSelectedCount('Quality')" class="bg-primary text-white text-xs rounded-full px-2 py-0.5">
                            {{ getSelectedCount('Quality') }}
                        </span>
                    </button>
                    <!-- Quality Popup -->
                    <div v-if="activeFilter === 'Quality'" class="filter-popup">
                        <div class="p-4">
                            <div class="space-y-2">
                                <label 
                                    v-for="quality in getFilterOptions('Quality')" 
                                    :key="quality"
                                    class="filter-option"
                                >
                                    <input 
                                        type="checkbox" 
                                        :checked="filters.Quality?.[quality]"
                                        @change="handleFilterChange('Quality', quality)"
                                    >
                                    <span class="ml-2">{{ quality }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <button 
                    v-if="hasAnyFilters"
                    @click="clearFilters"
                    class="px-4 py-2 text-red-500 hover:text-red-600 transition-colors duration-200"
                >
                    Clear All
                </button>
            </div>

            <!-- Mobile Filter Button -->
            <button 
                @click="showMobileFilters = true"
                class="sm:hidden flex items-center gap-2 px-4 py-2 bg-white rounded-lg border border-gray-200 text-sm font-medium"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                </svg>
                Filtre
                <span v-if="totalSelectedFilters" class="bg-primary text-white text-xs rounded-full px-2 py-0.5">
                    {{ totalSelectedFilters }}
                </span>
            </button>
        </div>

        <!-- Mobile Filter Dialog -->
        <TransitionRoot appear :show="showMobileFilters" as="template">
            <Dialog as="div" class="relative z-50" @close="showMobileFilters = false">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black bg-opacity-25" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-x-0 bottom-0 flex max-h-full">
                            <TransitionChild
                                as="template"
                                enter="transform transition ease-in-out duration-300"
                                enter-from="translate-y-full"
                                enter-to="translate-y-0"
                                leave="transform transition ease-in-out duration-300"
                                leave-from="translate-y-0"
                                leave-to="translate-y-full"
                            >
                                <DialogPanel class="pointer-events-auto w-full transform overflow-hidden rounded-t-xl bg-white shadow-xl transition-all max-h-[85vh]">
                                    <div class="flex h-full flex-col">
                                        <!-- Handle bar for bottom sheet -->
                                        <div class="mx-auto w-12 h-1.5 bg-gray-300 rounded-full my-3"></div>
                                        
                                        <div class="px-4 py-3 sm:px-6 border-b">
                                            <div class="flex items-center justify-between">
                                                <DialogTitle class="text-lg font-medium text-gray-900">
                                                    Filters
                                                </DialogTitle>
                                                <button 
                                                    @click="showMobileFilters = false"
                                                    class="text-gray-400 hover:text-gray-500"
                                                >
                                                    <span class="sr-only">Close</span>
                                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex-1 overflow-y-auto px-4 py-4">
                                            <!-- Filter Groups -->
                                            <div class="space-y-6">
                                                <!-- Categories Filter -->
                                                <div class="filter-group-mobile" v-if="props.categories && props.categories.length > 0">
                                                    <h3 class="font-medium mb-3">Categories</h3>
                                                    <div class="space-y-2">
                                                        <label 
                                                            v-for="category in props.categories" 
                                                            :key="category.id"
                                                            class="filter-option-mobile"
                                                        >
                                                            <input 
                                                                type="checkbox" 
                                                                :checked="filters.Categories?.[category.name]"
                                                                @change="handleFilterChange('Categories', category.name)"
                                                            >
                                                            <span class="ml-2">{{ category.name }}</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Subcategories Filter -->
                                                <div class="filter-group-mobile" v-if="subcategories.length">
                                                    <h3 class="font-medium mb-3">Subcategories</h3>
                                                    <div class="space-y-2">
                                                        <label 
                                                            v-for="subcategory in subcategories" 
                                                            :key="subcategory.id"
                                                            class="filter-option-mobile"
                                                        >
                                                            <input 
                                                                type="checkbox" 
                                                                :checked="filters.Subcategories?.[subcategory.name]"
                                                                @change="handleFilterChange('Subcategories', subcategory.name)"
                                                            >
                                                            <span class="ml-2">{{ subcategory.name }}</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Size Filter -->
                                                <div class="filter-group-mobile">
                                                    <h3 class="font-medium mb-3">Size</h3>
                                                    <div class="space-y-2">
                                                        <label 
                                                            v-for="size in getFilterOptions('Size')" 
                                                            :key="size"
                                                            class="filter-option-mobile"
                                                        >
                                                            <input 
                                                                type="checkbox" 
                                                                :checked="filters.Size?.[size]"
                                                                @change="handleFilterChange('Size', size)"
                                                            >
                                                            <span class="ml-2">{{ size }}</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Season Filter -->
                                                <div class="filter-group-mobile">
                                                    <h3 class="font-medium mb-3">Season</h3>
                                                    <div class="space-y-2">
                                                        <label 
                                                            v-for="season in getFilterOptions('Season')" 
                                                            :key="season"
                                                            class="filter-option-mobile"
                                                        >
                                                            <input 
                                                                type="checkbox" 
                                                                :checked="filters.Season?.[season]"
                                                                @change="handleFilterChange('Season', season)"
                                                            >
                                                            <span class="ml-2">{{ season }}</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Quality Filter -->
                                                <div class="filter-group-mobile">
                                                    <h3 class="font-medium mb-3">Quality</h3>
                                                    <div class="space-y-2">
                                                        <label 
                                                            v-for="quality in getFilterOptions('Quality')" 
                                                            :key="quality"
                                                            class="filter-option-mobile"
                                                        >
                                                            <input 
                                                                type="checkbox" 
                                                                :checked="filters.Quality?.[quality]"
                                                                @change="handleFilterChange('Quality', quality)"
                                                            >
                                                            <span class="ml-2">{{ quality }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="border-t bg-white px-4 py-4">
                                            <div class="flex gap-4">
                                                <button 
                                                    v-if="hasAnyFilters"
                                                    @click="clearFilters"
                                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                                                >
                                                    Clear All
                                                </button>
                                                <button 
                                                    @click="showMobileFilters = false"
                                                    class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark"
                                                >
                                                    Apply Filters
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Main Content -->
        <div class="main-content">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionRoot, TransitionChild } from '@headlessui/vue'
import { useFilterStore } from '../stores/FilterStore'
import { storeToRefs } from 'pinia'
import { useMaster } from '../stores/MasterStore'
import { useRoute, useRouter } from 'vue-router'

const filterStore = useFilterStore()
const masterStore = useMaster()
const { activeFilter, filters } = storeToRefs(filterStore)
const route = useRoute()
const router = useRouter()

const showMobileFilters = ref(false)

const hasSelectedFilters = computed(() => {
    return (filterName) => {
        if (!filters.value || !filters.value[filterName]) {
            return false
        }
        return Object.values(filters.value[filterName]).some(v => v)
    }
})

const getSelectedCount = computed(() => {
    return (filterName) => {
        if (!filters.value || !filters.value[filterName]) {
            return 0
        }
        return Object.values(filters.value[filterName]).filter(v => v).length
    }
})

const hasAnyFilters = computed(() => {
    return Object.keys(filters.value).some(key => 
        Object.values(filters.value[key]).some(v => v)
    )
})

const toggleFilter = (name) => {
    if (activeFilter.value === name) {
        activeFilter.value = null
        return
    }
    activeFilter.value = name
}

const closePopup = () => {
    activeFilter.value = null
}

const handleFilterChange = (filterName, option) => {
    if (!filters.value[filterName]) {
        filters.value[filterName] = {}
    }

    filters.value[filterName][option] = !filters.value[filterName][option]
    
    if (!filters.value[filterName][option]) {
        delete filters.value[filterName][option]
    }

    if (Object.keys(filters.value[filterName]).length === 0) {
        delete filters.value[filterName]
    }

    filterStore.saveFilters()

    const activeFilters = filterStore.getActiveFilters()
    updateUrlWithFilters(activeFilters)
    
    emit('filtersChanged', filters.value)
}

const updateUrlWithFilters = (activeFilters) => {
    const queryParams = { ...route.query }
    
    const filterKeys = ['categories', 'subcategories', 'size', 'season', 'quality']
    
    filterKeys.forEach(key => {
        delete queryParams[key]
    })
    
    Object.entries(activeFilters).forEach(([key, values]) => {
        if (values && values.length > 0) {
            const paramKey = key.toLowerCase()
            queryParams[paramKey] = values.join(',')
        }
    })

    router.push({
        path: route.path,
        query: queryParams
    }, { replace: true })
}

const clearFilters = () => {
    Object.keys(filters.value).forEach((key) => {
        filters.value[key] = {}
    })
    activeFilter.value = null
    router.push({ path: route.path, query: {} })
    emit('filtersChanged', {})
}

const getFilterOptions = (filterName) => {
    return filterStore.getFilterOptions(filterName)
}

// Add subcategories prop
const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    subcategories: {
        type: Array,
        default: () => []
    },
    title: {
        type: String,
        default: ''
    }
})
console.log('Categories prop:', props.categories);
// Emit events
const emit = defineEmits(['filtersChanged'])

// Initialize filters from URL on mount
onMounted(() => {
    console.log('Component mounted');
    console.log('Initial categories:', props.categories);
    console.log('Initial filters:', filters.value);
    
    // Initialize filters if empty
    if (!filters.value.Categories) {
        filters.value.Categories = {};
    }
    
    const queryParams = route.query;
    if (Object.keys(queryParams).length > 0) {
        const initialFilters = {};
        
        Object.entries(queryParams).forEach(([key, value]) => {
            if (typeof value === 'string') {
                const values = value.split(',');
                initialFilters[key.charAt(0).toUpperCase() + key.slice(1)] = {};
                values.forEach(v => {
                    initialFilters[key.charAt(0).toUpperCase() + key.slice(1)][v] = true;
                });
            }
        });
        
        filterStore.setFilters(initialFilters);
    }
    
    document.addEventListener('click', handleClickOutside);
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
})

// Add computed property for total selected filters
const totalSelectedFilters = computed(() => {
    return Object.keys(filters.value).reduce((total, key) => {
        return total + Object.values(filters.value[key]).filter(v => v).length
    }, 0)
})

// Add click outside handler
const handleClickOutside = (event) => {
    if (activeFilter.value && !event.target.closest('.filter-group')) {
        activeFilter.value = null;
    }
}
</script>

<style scoped>
.filter-layout {
    @apply flex flex-col gap-6;
}

.filter-group {
    @apply relative;
}

.filter-popup {
    @apply absolute top-full left-0 mt-2 bg-white rounded-lg border border-gray-200 shadow-lg z-50 min-w-[200px];
}

.filter-option {
    @apply flex items-center text-sm text-gray-600 cursor-pointer hover:bg-gray-50 px-2 py-1.5 rounded-md;
}

.filter-option input[type="checkbox"] {
    @apply rounded border-gray-300 text-primary focus:ring-primary;
}

.filter-group-mobile {
    @apply pb-4 border-b border-gray-200;
}

.filter-group-mobile:last-child {
    @apply border-b-0 pb-0;
}

.filter-option-mobile {
    @apply flex items-center text-sm text-gray-600 cursor-pointer;
}

.filter-option-mobile input[type="checkbox"] {
    @apply rounded border-gray-300 text-primary focus:ring-primary;
}

.main-content {
    @apply flex-1 min-w-0;
}
</style> 