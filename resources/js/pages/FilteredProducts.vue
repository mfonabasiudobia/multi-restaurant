<template>
    <div class="margin-container mb-6">
        <!-- Ads Banner at top -->
        <div class="main-content-wrapper">


            <!-- Filter layout with sidebar -->
            <FilterRow @filtersChanged="fetchProducts">
                <!-- Products Grid with Loading State -->
                <div
                    v-if="loading"
                    class="text-center py-4"
                >
                    <p>{{ $t('Loading products')}}...</p>
                </div>

                <div v-else>
                    <!-- Products count -->
                    <div class="products-header">
                        <p class="text-sm text-gray-600">
                            {{ products.length }} {{ $t('results')}}
                        </p>
                    </div>

                  <div class="max-sm:gap-0 md:gap-3 flex flex-wrap md:justify-start  ">

                        <div
                            v-for="product in products"
                            :key="product.id"
                            class="flex-none  md:w-[180px] max-sm:w-1/2 md:w-[225px] max-sm:p-2"
                        >
                            <ProductCard :product="product" />
                        </div>
                    </div>

                    <!-- No Results Message -->
                    <div
                        v-if="products.length === 0"
                        class="text-center py-4"
                    >
                        <p class="text-gray-500">{{ $t('No products found matching your criteria')}}</p>
                    </div>


                    <!-- Pagination -->
                    <div
                        v-if="pagination?.last_page > 1"
                        class="mt-8"
                    >
                        <Pagination
                            :pagination="pagination"
                            @page-changed="changePage"
                        />
                    </div>
                </div>
            </FilterRow>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useMaster } from '../stores/MasterStore'
import ProductCard from '../components/ProductCard.vue'
import FilterRow from '../components/FilterRow.vue';
import Pagination from '../components/Pagination.vue'
import { useFilterStore } from '../stores/FilterStore';
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const master = useMaster()

const products = ref({})
const pagination = ref({})
const loading = ref(false)
const filters = ref({
    quality: '',
    season: '',
    sort: 'newest',
    page: route.query.page || 1
})

const activeFilter = ref(null)
const showMobileFilters = ref(false)

// Computed properties for filter indicators
const selectedQuality = computed(() => filters.value.quality)
const selectedSeason = computed(() => filters.value.season)
const hasActiveFilters = computed(() => {
    return filters.value.quality || filters.value.season || filters.value.sort !== 'newest'
})
const totalActiveFilters = computed(() => {
    let count = 0
    if (filters.value.quality) count++
    if (filters.value.season) count++
    if (filters.value.sort !== 'newest') count++
    return count
})





const filterStore = useFilterStore();

const fetchProducts = async () => {
    try {
        console.log('pagination Making API request to:', '/products/filter')
        console.log('Fetching products with filters:', filters.value)
        loading.value = true
        const response = await axios.get('/products/filter', {
            params: filters.value
        })
        console.log('API Response:', response.data)
        products.value = response.data.data;
        pagination.value = response.data.meta;
        console.log('Fetched products:', products.value)
    } catch (error) {
        console.log('API Request failed:', error.response || error)
        console.error('Error fetching products:', error)
        products.value = {}
    } finally {
        loading.value = false
    }
}

const applyFilters = () => {
    // Reset page when filters change
    filters.value.page = 1

    // Close any open filter popup
    activeFilter.value = null

    // Update URL with new filters
    router.push({
        query: {
            ...filters.value,
            page: undefined // Remove page from URL if it's 1
        }
    })
}

const applyMobileFilters = () => {
    showMobileFilters.value = false
    applyFilters()
}

const clearFilters = () => {
    filters.value = {
        quality: '',
        season: '',
        sort: 'newest',
        page: 1
    }

    activeFilter.value = null
    router.push({ query: {} })
}

const changePage = (page) => {
    filters.value.page = page
    router.push({
        query: { ...filters.value }
    })
}

const toggleFilter = (name) => {
    if (activeFilter.value === name) {
        activeFilter.value = null
        return
    }
    activeFilter.value = name
}

// Handle clicks outside filter dropdowns
const handleClickOutside = (event) => {
    if (activeFilter.value && !event.target.closest('.filter-group')) {
        activeFilter.value = null
    }
}

// Now watch for route changes
watch(() => route.query, (newQuery) => {
    console.log('Route query changed:', newQuery)
    filters.value = {
        quality: newQuery.quality || '',
        season: newQuery.season || '',
        sort: newQuery.sort || 'newest',
        page: newQuery.page || 1
    }
    console.log('Updated filters:', filters.value)
    fetchProducts()
}, { immediate: true })

onMounted(() => {
    window.scrollTo(0, 0);
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.filter-group {
    @apply relative;
}

.filter-popup {
    @apply absolute top-full left-0 mt-2 bg-white rounded-lg border border-gray-200 shadow-lg z-50 min-w-[200px];
}

.filter-option {
    @apply flex items-center text-sm text-gray-600 cursor-pointer hover:bg-gray-50 px-2 py-1.5 rounded-md;
}

.filter-option input[type="radio"] {
    @apply rounded-full border-gray-300 text-primary focus:ring-primary;
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

.filter-option-mobile input[type="radio"] {
    @apply rounded-full border-gray-300 text-primary focus:ring-primary;
}

.main-content-wrapper {
    @apply mt-4;
}

.categories-wrapper {
    @apply mb-4 overflow-x-auto overflow-y-hidden;
    -webkit-overflow-scrolling: touch;
    max-width: 100vw;
}

.categories-list {
    @apply flex space-x-6 border-b border-gray-200;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding-bottom: 2px;
}

.categories-list::-webkit-scrollbar {
    display: none;
}

.category-item {
    @apply px-1 py-2 text-sm font-medium text-gray-500 whitespace-nowrap transition-colors duration-200 hover:text-primary focus:outline-none;
    border-bottom: 2px solid transparent;
}

.category-item.active {
    @apply text-primary border-primary;
    border-bottom: 2px solid currentColor;
}

.products-header {
    @apply flex justify-between items-center py-3;
}

.products-grid {
    @apply grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 3xl:grid-cols-6 gap-3 sm:gap-4;
}

.product-card {
    @apply bg-white rounded-lg shadow-sm overflow-hidden;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .categories-list {
        @apply space-x-4;
    }

    .category-item {
        @apply text-xs;
    }

    .products-grid {
        @apply grid-cols-2 gap-3;
    }
}
</style>