<template>
    <div class="margin-container">
        <!-- Clean, Minimal Header -->
        <div class="bg-white border-b border-gray-200">
            <div class="w-full px-4 py-3 flex items-center justify-between">
                <!-- Simple Back Button -->
                <router-link to="/" class="flex items-center gap-2 text-gray-700 hover:text-primary transition-colors">
                    <ArrowLeftIcon class="w-4 h-4" />
                    <span class="text-sm font-medium">{{ $t('Back') }}</span>
                </router-link>

                <!-- Clean Search Results Display -->
                <div class="text-sm">
                    <span class="text-primary font-medium">"{{ master.search || 'all' }}"</span>
                    <span class="text-gray-600 ml-2">{{ totalProducts }} {{ $t('items found') }}</span>
                </div>
            </div>
        </div>

        

        <div class="py-4">
            <!-- Horizontal Filter Row -->
            <HorizontalFilterRow 
                :categories="categories"
                @filtersChanged="handleFiltersChanged"
            >
                <div
                    class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-6 items-start mt-4">

                    <div v-for="product in products" :key="product.id" class="w-full">
                        <ProductCard :product="product" />
                    </div>
                </div>
                
                <div v-if="products.length == 0" class="flex justify-center items-center w-full mt-8">
                    <div class="text-slate-800 text-base font-normal leading-normal">
                        {{ $t('No products found') }}
                    </div>
                </div>

                <!-- Pagination -->
                <div class="flex justify-between items-center w-full mt-8 gap-4 flex-wrap">
                    <div class="text-slate-800 text-base font-normal leading-normal">
                        {{ $t('Showing') }} {{ perPage * (currentPage - 1)+1 }} {{ $t('to') }} {{ perPage * (currentPage - 1) + products.length }} {{ $t('of') }} {{ totalProducts }} {{ $t('results') }}
                    </div>
                    <div>
                        <vue-awesome-paginate :total-items="totalProducts" :items-per-page="perPage" type="button" :max-pages-shown="5"
                            v-model="currentPage" :hide-prev-next-when-ends="true" @click="onClickHandler" />
                    </div>
                </div>
            </HorizontalFilterRow>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';
import ProductCard from '../components/ProductCard.vue';
import HorizontalFilterRow from '../components/HorizontalFilterRow.vue';
import { useMaster } from '../stores/MasterStore';
import { useFilterStore } from '../stores/FilterStore';

const master = useMaster();
const filterStore = useFilterStore();

onMounted(() => {
    fetchProducts();
    fetchCategories();
    window.scrollTo(0, 0);
});

const search = master.search;

watch(() => master.search, () => {
    fetchProducts();
});

const currentPage = ref(1);
const perPage = 12;

const onClickHandler = (page) => {
    currentPage.value = page;
    fetchProducts();
};

const categories = ref([]);
const products = ref([]);
const totalProducts = ref(0);

// Handle filter changes from HorizontalFilterRow
const handleFiltersChanged = async (filters) => {
    try {
        const response = await axios.get('/products/filter/advanced', {
            params: {
                filters: JSON.stringify(filters),
                page: currentPage.value,
                per_page: perPage,
                search: master.search
            }
        });
        
        if (response.data.status === 'success' && response.data.data.products) {
            products.value = response.data.data.products;
            totalProducts.value = response.data.data.meta.total;
        }
    } catch (error) {
        console.error('Error fetching filtered products:', error);
    }
};

const fetchProducts = async () => {
    try {
        // Use the filter/advanced endpoint with stored filters
        const filters = filterStore.getActiveFilters();
        const response = await axios.get('/products/filter/advanced', {
            params: {
                page: currentPage.value,
                per_page: perPage,
                search: master.search,
                filters: JSON.stringify(filters)
            }
        });
        
        if (response.data.status === 'success' && response.data.data.products) {
            totalProducts.value = response.data.data.meta.total;
            products.value = response.data.data.products;
        } else {
            // Fallback to regular endpoint if needed
            const regularResponse = await axios.get('/products', {
                params: {
                    page: currentPage.value,
                    per_page: perPage,
                    search: master.search
                }
            });
            totalProducts.value = regularResponse.data.data.total;
            products.value = regularResponse.data.data.products;
        }
    } catch (error) {
        console.error('Error fetching products:', error);
        // Fallback to regular endpoint
        const regularResponse = await axios.get('/products', {
            params: {
                page: currentPage.value,
                per_page: perPage,
                search: master.search
            }
        });
        totalProducts.value = regularResponse.data.data.total;
        products.value = regularResponse.data.data.products;
    }
};

const fetchCategories = async () => {
    if (categories.value.length === 0) {
        try {
            const response = await axios.get('/categories');
            categories.value = response.data.data.categories;
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    }
};
</script>

<style>
/* Clean up any unnecessary styles */
</style>
