<template>
    <div class="margin-container">
        <div class="h-56 md:h-64 w-full">
            <img :src="shop?.banner" loading="lazy" class="w-full h-full object-cover" />
        </div>

        <div class=" relative bg-primary-50 py-10 pt-24">
            <!-- Shop Details -->
            <div class="-top-32 sm:-top-28 lg:-top-[108px] absolute left-0 right-0 main-container">
                <div class="w-full p-4 sm:p-6 bg-white rounded-2xl shadow-lg">
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                        <!-- Logo and Basic Info -->
                        <div class="flex items-start gap-4 sm:gap-6">
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full overflow-hidden shrink-0 border-2 border-primary/10">
                                <img :src="shop?.logo" loading="lazy" class="w-full h-full object-cover" />
                            </div>

                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="text-slate-950 text-xl font-bold">{{ shop?.name }}</h1>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                        :class="shop?.shop_status === 'Online' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-700'">
                                        {{ shop?.shop_status }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-3 text-sm text-slate-600">
                                    <span>{{ shop?.total_products }}+ Items</span>
                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                    <span>{{ shop?.total_categories }}+ Categories</span>
                                </div>

                                <p class="text-sm text-slate-500 line-clamp-2" v-html="shop?.description"></p>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="flex-1 flex justify-end">
                            <div class="w-full lg:w-[448px] relative">
                                <input type="text" :placeholder="$t('Search product')"
                                    class="w-full p-3 pr-10 bg-slate-50 rounded-lg border border-slate-200 outline-none focus:border-primary transition-colors"
                                    v-model="search" />
                                <MagnifyingGlassIcon class="w-5 h-5 absolute top-1/2 right-3 -translate-y-1/2 text-slate-400" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banners Section -->
            <div class="pt-10">
                <swiper :breakpoints="breakpoints" :spaceBetween="20" :freeMode="true" :modules="modules" class="w-full">
                    <swiper-slide v-for="banner in shop?.banners" :key="banner.id" class="w-full">
                        <img :src="banner.thumbnail" alt="banner" loading="lazy" 
                            class="w-full aspect-[6/2] object-cover rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300" />
                    </swiper-slide>
                </swiper>
            </div>
        </div>

        <div class="py-6 pt-6">
            <!-- Categories Section -->
            <!-- <div class="mb-12">
                <h2 class="text-xl font-bold mb-6">{{ $t('Categories') }}</h2>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 xl:grid-cols-6 2xl:grid-cols-7 gap-6">
                    <div v-for="category in categories" :key="category.id">
                        <router-link :to="`/shops/${shop.id}/categories/${category.slug}`">
                            <CategoryCard :category="category" />
                        </router-link>
                    </div>
                </div>
            </div> -->

            <!-- Products Section -->
            <div>
            
                <h2 class="text-xl font-bold mb-6 hidden sm:block">{{ $t('All Products') }}</h2>
                <HorizontalFilterRow 
                    @filtersChanged="handleFilterChange"
                    :categories="categories"
                    :subcategories="subcategories"
                >
                  <div class="max-sm:gap-0 md:gap-3 flex flex-wrap md:justify-start  ">
                         <div v-for="product in products" :key="product.id"
                              class="flex-none  md:w-[180px] max-sm:w-1/2 md:w-[225px] max-sm:p-2">
                            <ProductCard :product="product" />
                        </div>
                        <div v-if="products.length == 0" class="text-slate-950 text-xl font-medium leading-7">
                            {{ $t('No Products Found') }}
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center w-full mt-8 gap-4 flex-wrap">
                        <div class="text-slate-800 text-base font-normal leading-normal">
                            {{ $t('Showing') }} {{ (perPage * (currentPage - 1) + 1) }} {{ $t('to') }} 
                            {{ (perPage * (currentPage - 1) + products.length) }} {{ $t('of') }} 
                            {{ totalProducts }} {{ $t('results') }}
                        </div>
                        <div>
                            <vue-awesome-paginate 
                                :total-items="totalProducts" 
                                :items-per-page="perPage" 
                                type="button"
                                :max-pages-shown="5" 
                                v-model="currentPage" 
                                :hide-prev-next-when-ends="true"
                                @click="onClickHandler" 
                            />
                        </div>
                    </div>
                </HorizontalFilterRow>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { StarIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/solid';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { FreeMode } from 'swiper/modules';
import ProductCard from '../components/ProductCard.vue';
import CategoryCard from '../components/CategoryCard.vue';
import HorizontalFilterRow from '../components/HorizontalFilterRow.vue';
import { useMaster } from '../stores/MasterStore';

const masterStore = useMaster();
const router = new useRouter();

import 'swiper/css';
import 'swiper/css/free-mode';

const route = useRoute();

const modules = [FreeMode];

const isLoading = ref(true);

const currentPage = ref(1);
const perPage = ref(12);

const onClickHandler = (page) => {
    currentPage.value = page;
    fetchProducts();
};

const shop = ref({});

const categories = computed(() => masterStore.categories || []);
const subcategories = ref([]);

const products = ref([]);
const totalProducts = ref(0);

const search = ref('');
const searchProducts = () => {
    currentPage.value = 1;
    fetchProducts();
};

watch(search, () => {
    searchProducts();
});

const activeFilters = ref({});

const handleFilterChange = (filters) => {
    activeFilters.value = filters;
    currentPage.value = 1;

    // If a category is selected/deselected, update subcategories
    if (filters.Categories) {
        const selectedCategories = Object.entries(filters.Categories)
            .filter(([_, selected]) => selected)
            .map(([name]) => {
                const category = categories.value.find(c => c.name === name);
                return category ? category.id : null;
            })
            .filter(id => id !== null);

        if (selectedCategories.length === 1) {
            fetchSubcategories(selectedCategories[0]);
        } else {
            subcategories.value = [];
        }
    }

    fetchProducts();
};

onMounted(async () => {
    isLoading.value = false;

    if (!masterStore.multiVendor) {
        router.push('/');
        return;
    }

    await fetchDetails();
    await fetchProducts();
    
    window.scrollTo(0, 0);
});

const fetchDetails = async () => {
    axios.get('/shops/' + route.params.id).then((response) => {
        shop.value = response.data.data.shop;
    })
};

const fetchProducts = async () => {
    try {
        const selectedCategories = activeFilters.value.Categories ? 
            Object.entries(activeFilters.value.Categories)
                .filter(([_, selected]) => selected)
                .map(([name]) => {
                    const category = categories.value.find(c => c.name === name);
                    return category ? category.id : null;
                })
                .filter(id => id !== null)
            : [];

        const selectedSubcategories = activeFilters.value.Subcategories ?
            Object.entries(activeFilters.value.Subcategories)
                .filter(([_, selected]) => selected)
                .map(([name]) => {
                    const subcategory = subcategories.value.find(c => c.name === name);
                    return subcategory ? subcategory.id : null;
                })
                .filter(id => id !== null)
            : [];

        const params = {
            shop_id: route.params.id,
            page: currentPage.value,
            per_page: perPage.value,
            search: search.value,
            filters: JSON.stringify(activeFilters.value),
            categories: selectedCategories.length ? selectedCategories.join(',') : undefined,
            subcategories: selectedSubcategories.length ? selectedSubcategories.join(',') : undefined
        };

        const response = await axios.get('/products/shop/filter', { params });
        
        if (response.data.status === 'success') {
            totalProducts.value = response.data.data.meta.total;
            products.value = response.data.data.products;
        } else {
            console.error('Error fetching products:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching products:', error);
    }
};

const fetchSubcategories = async (categoryId) => {
    try {
        const response = await axios.get('/sub-categories', {
            params: { category_id: categoryId }
        });
        subcategories.value = response.data.data.sub_categories;
    } catch (error) {
        console.error('Error fetching subcategories:', error);
    }
};

const breakpoints = {
    320: {
        slidesPerView: 1,
        spaceBetween: 10
    },
    768: {
        slidesPerView: 2,
        spaceBetween: 10
    },
    1024: {
        slidesPerView: 2,
        spaceBetween: 30
    },

    1280: {
        slidesPerView: 3,
        spaceBetween: 30
    }
};

</script>
