<template>
    <div class="margin-container mb-6">
        <!-- Ads Banner at top -->


        <div class="main-content-wrapper">
            <!-- Categories List -->
            <div class="categories-wrapper">
                <div class="categories-list">
                    <button
                        class="category-item"
                        :class="{ 'active': !selectedSubcategory }"
                        @click="selectSubcategory(null)"
                    >
                        {{ $t('All') }}
                    </button>
                    <button
                        v-for="subcategory in subcategories"
                        :key="subcategory.id"
                        class="category-item"
                        :class="{ 'active': selectedSubcategory === subcategory.name }"
                        @click="selectSubcategory(subcategory.name)"
                    >
                        {{ subcategory.name }}
                    </button>
                </div>
            </div>

            <!-- Filter layout with sidebar -->
            <FilterRow @filtersChanged="handleFiltersChanged">
                <!-- Products Grid with Loading State -->
                <div
                    v-if="loading"
                    class="text-center py-4"
                >
                    <p>{{ $t('Loading products') }}...</p>
                </div>

                <div v-else>
                    <!-- Products count -->
                    <div class="products-header">
                        <p class="text-sm text-gray-600">
                            {{ products.length }} {{ $t('results') }}
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
import {ref, onMounted, watch, reactive} from 'vue';
import { useRoute, useRouter } from 'vue-router';

import { useMaster } from '../stores/MasterStore';
import AdsBanner from "../components/AdsBanner.vue";
import FilterRow from '../components/FilterRow.vue';
import Pagination from '../components/Pagination.vue';
import ProductCard from '../components/ProductCard.vue';
import { useFilterStore } from '../stores/FilterStore';

const master = useMaster();
const route = useRoute();
const router = useRouter();
const subcategories = ref([]);
const products = ref([]);
const pagination = ref([]);
const loading = ref(false);
const filterStore = useFilterStore();
const debug = ref(true); // Add this for debugging
const activeFilters = ref({});
const selectedSubcategory = ref(null);

// Fetch subcategories based on category slug
const fetchSubcategories = async () => {
    try {
        const response = await axios.get(`/sub-categories?category_id=${route.params.slug}`);
        subcategories.value = response.data.data.sub_categories;
    } catch (error) {
        console.error('Error fetching subcategories:', error);
    }
};

const selectSubcategory = (categoryName) => {
    selectedSubcategory.value = categoryName;

    const filters = {
        ...filterStore.filters,

      Subcategories: categoryName ? { [categoryName]: true } : {},
    };

    handleFiltersChanged(filters);
};

const changePage = (page) => {
    console.log('Page changed to:', page);

    router.push({
        query: { page: page }
    })

    handleFiltersChanged({
        ...filterStore.filters
    },
        page
    );

}

// Handle filter changes
const handleFiltersChanged = async (filters, page) => {
    loading.value = true;
    activeFilters.value = filters;

    try {
        console.log('Fetching products with filters:', filters);
        console.log('pagination products with filters:', route.query.page);

        const response = await axios.get('/products/filter/advanced', {
            params: {
                category_id: route.params.slug,
                filters: JSON.stringify(filters),
                page: page || route.query.page,
                per_page: 20
            }
        });

        if (response.data.status === 'success' && response.data.data.products) {
            products.value = response.data.data.products;

            pagination.value = {
                total: response.data.data.meta.total,
                per_page: response.data.data.meta.per_page,
                current_page: response.data.data.meta.current_page,
                last_page: response.data.data.meta.last_page
            };

            console.log('pagination:', pagination); // Debug log

        } else {
            console.error('Invalid API response structure:', response.data);
            products.value = [];
        }
    } catch (error) {
        console.error('Error fetching products:', error);
        products.value = [];
    } finally {
        loading.value = false;
    }
};

// Initialize with stored filters on mount
onMounted(async () => {
    loading.value = true;

  let filters = {
    Categories: {},
    Size: {},
    Color: {},
    Season: {},
    Quality: {}
  }




  localStorage.setItem('filters', JSON.stringify(filters))
  localStorage.setItem('filter', JSON.stringify(filters))

  filterStore.clearFilters();

    try {
        await fetchSubcategories();
        const currentFilters = {};
        console.log('Initial filters:', currentFilters); // Debug log
        await handleFiltersChanged(currentFilters);
    } catch (error) {
        console.error('Error during initialization:', error);
    } finally {
        loading.value = false;
    }
});

// Watch for route changes to update filters
watch(() => route.params.slug, async () => {
    await fetchSubcategories();
    await handleFiltersChanged(filterStore.getActiveFilters());
});
</script>

<style scoped>
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
