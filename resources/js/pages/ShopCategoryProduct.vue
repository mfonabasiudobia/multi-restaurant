<template>
    <div>

        <div class="main-container py-4 bg-slate-100">
            <div
                class="w-full p-2 md:p-4 bg-white rounded-lg sm:rounded-xl md:rounded-2xl flex gap-3 md:gap-6 items-center justify-between">
                <!-- Shop logo and name -->
                <router-link :to="`/shops/${route.params.id}`"
                    class="w-14 sm:w-20 flex flex-wrap gap-1 items-center justify-center">
                    <div class="w-10 h-10 sm:w-14 sm:h-14 rounded-full overflow-hidden">
                        <img :src="shop?.logo" class="w-full h-full object-cover" loading="lazy" alt="logo" />
                    </div>
                    <div class="text-center text-slate-950 text-xs sm:text-sm font-medium leading-tight truncate">
                        {{ shop?.name }}
                    </div>
                </router-link>

                <!-- Categories slider -->
                <div class="grow overflow-x-auto">
                    <swiper :slidesPerView="'auto'" :spaceBetween="16" class="categorySwiper">

                        <swiper-slide>
                            <div class="p-2 sm:px-4 sm:py-3 rounded-md sm:rounded-[10px] border text-base font-normal leading-normal hover:text-primary cursor-pointer transition duration-300"
                                :class="!route.query.subcategory ? 'text-primary border-primary' : 'border-slate-200 text-slate-600'"
                                @click="$router.push(`/shops/${route.params.id}/categories/${route.params.slug}`)">
                                {{ $t('All') }}
                            </div>
                        </swiper-slide>

                        <swiper-slide v-for="subcategory in subcategories" :key="subcategory.id">
                            <div class="p-2 sm:px-4 sm:py-3 rounded-md sm:rounded-[10px] border text-base font-normal leading-normal hover:text-primary cursor-pointer transition duration-300"
                                :class="(subcategory.id == route.query.subcategory) ? 'text-primary border-primary' : 'border-slate-200 text-slate-600'"
                                @click="$router.push(`/shops/${route.params.id}/categories/${route.params.slug}?subcategory=${subcategory.id}`)">
                                {{ subcategory.name }}
                            </div>
                        </swiper-slide>

                    </swiper>
                </div>

                <!-- Filter button -->
                <div>
                    <button
                        class="p-2 sm:px-4 sm:py-3 bg-slate-200 rounded-md sm:rounded-[10px] justify-center items-center gap-2 inline-flex text-slate-600 text-sm sm:text-base font-normal leading-normal border-0 outline-none hover:text-primary transition duration-300"
                        @click="showFilterCanvas = true">
                        <FunnelIcon class="w-4 h-4 sm:w-6 sm:h-6" />
                        <div class="grow shrink basis-0">
                            {{ $t('Filter') }}
                        </div>
                    </button>
                </div>

            </div>
        </div>

        <div class="main-container py-12">
            <div
                class="grid grid-cols-1 xs:grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-6 items-start">

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
            <div class="flex justify-between items-center w-full mt-8  gap-4 flex-wrap">
                <div class="text-slate-800 text-base font-normal leading-normal">
                    {{ $t('Showing') }} {{ perPage * (currentPage - 1) + 1 }} {{ $t('to') }} {{ perPage * (currentPage - 1) + products.length }}
                    {{ $t('of') }} {{ totalProducts }} {{ $t('results') }}
                </div>
                <div>
                    <vue-awesome-paginate :total-items="totalProducts" :items-per-page="perPage" type="button"
                        :max-pages-shown="5" v-model="currentPage" :hide-prev-next-when-ends="true"
                        @click="onClickHandler" />
                </div>
            </div>
        </div>

        <!-- Filter Canvas Drawer -->
        <TransitionRoot as="template" :show="showFilterCanvas">
            <Dialog as="div" class="relative z-10" @close="showFilterCanvas = false">
                <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100"
                    leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-30 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                            <TransitionChild as="template"
                                enter="transform transition ease-in-out duration-500 sm:duration-700"
                                enter-from="translate-x-full" enter-to="translate-x-0"
                                leave="transform transition ease-in-out duration-500 sm:duration-700"
                                leave-from="translate-x-0" leave-to="translate-x-full">
                                <DialogPanel class="pointer-events-auto relative w-screen max-w-md">
                                    <TransitionChild as="template" enter="ease-in-out duration-500"
                                        enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-500"
                                        leave-from="opacity-100" leave-to="opacity-0">
                                        <div class="absolute left-0 top-0 -ml-8 flex pr-2 pt-4 sm:-ml-10 sm:pr-4"></div>
                                    </TransitionChild>
                                    <div
                                        class="flex h-full flex-col justify-between overflow-y-scroll bg-white shadow-xl">
                                        <div class="p-4 flex flex-col gap-7">
                                            <div class="flex justify-between items-center">
                                                <div class="text-slate-950 text-xl font-bold leading-loose">{{ $t('Filter') }}</div>
                                                <button
                                                    class="w-8 h-8 flex justify-center items-center bg-slate-100 rounded-full"
                                                    @click="showFilterCanvas = false">
                                                    <XMarkIcon class="w-5 h-5 text-slate-700" />
                                                </button>
                                            </div>
                                        
                                            <!-- Customer Review -->
                                            <!--
                                            <div>
                                                <div class="text-slate-950 text-base font-medium leading-normal">
                                                    {{ $t('Customer Review') }}
                                                </div>
                                                
                                                <div class="flex flex-col gap-2 mt-3">
                                                    <div v-for="rating in ratings" :key="rating" class="grow">
                                                        <label :for="`rating${rating}`"
                                                            class="cursor-pointer has-[:checked]:border-primary text-slate-800 flex items-center justify-between px-2 py-1.5 bg-white rounded-lg border border-slate-100 gap-1.5">
                                                            <div class="flex items-center gap-1">
                                                                <div class="flex items-center">
                                                                    <StarIcon v-for="i in 5" :key="i" class="w-5 h-5"
                                                                        :class="i <= rating ? 'text-amber-500' : 'text-gray-200'" />
                                                                </div>
                                                                <div class="text-base font-medium leading-normal">
                                                                    {{ rating }}.0
                                                                </div>
                                                            </div>
                                                            <input type="radio" v-model="filterFormData.review"
                                                                :id="`rating${rating}`" name="review" :value="rating"
                                                                class="w-5 h-5 appearance-none checked:bg-primary rounded-full border-2 border-slate-300 shrink-0 transition duration-300" />
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        -->
                                            <!-- Sort by -->
                                            <div>
                                                <div class="text-slate-950 text-base font-medium leading-normal">
                                                    {{ $t('Sort by') }}
                                                </div>

                                                <select v-model="filterFormData.shortBy"
                                                    class="w-full mt-1 p-3 rounded bg-transparent border border-gray-100 outline-none">
                                                    <option v-for="shortBy in filterSortBy" :key="shortBy"
                                                        :value="shortBy.value">{{ shortBy.name }}</option>
                                                </select>
                                            </div>

                                            <div>
                                                <div class="flex justify-between items-center gap-2">
                                                    <div class="text-slate-950 text-base font-medium leading-normal">
                                                        {{ $t('Product Price') }}
                                                    </div>
                                                    <div class="text-primary text-base font-normal leading-normal">
                                                        {{ $t('$300 - $4200') }}
                                                    </div>
                                                </div>
                                                <div class="flex mt-2">
                                                    <input type="range" min="00" max="9200"
                                                        v-model="filterFormData.minPrice"
                                                        class="w-full rotate-180 appearance-none  bg-slate-300 accent-primary-600 focus:accent-primary h-2 rounded-r-full" />
                                                    <input type="range" min="00" max="10000"
                                                        v-model="filterFormData.maxPrice"
                                                        class="w-full appearance-none bg-slate-300 accent-primary-600  focus:accent-primary h-2 rounded-r-full -ml-0.5" />
                                                </div>
                                                <div
                                                    class="text-slate-400 text-xs font-normal leading-none flex justify-between mt-2">
                                                    <span>
                                                        ${{ filterFormData.minPrice }}
                                                    </span>
                                                    <span>
                                                        ${{ filterFormData.maxPrice }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Brand -->
                                            <!-- <div>
                                                <div class="text-slate-950 text-base font-medium leading-normal">Brand
                                                </div>
                                                <select v-model="filterFormData.brand"
                                                    class="w-full mt-1 p-3 rounded bg-transparent border border-gray-100 outline-none">
                                                    <option value="" selected disabled>Select Brand</option>
                                                    <option value="brand">Brand Name</option>
                                                    <option value="brand1">Brand Name1</option>
                                                    <option value="brand2">Brand Name2</option>
                                                    <option value="brand3">Brand Name3</option>
                                                </select>
                                            </div> -->

                                            <!-- color -->
                                            <div>
                                                <div class="text-slate-950 text-base font-medium leading-normal">
                                                    {{ $t('Color') }}
                                                </div>

                                                <div
                                                    class="flex flex-wrap gap-4 p-3 rounded-xl border border-slate-200 mt-1">

                                                    <label
                                                        class="cursor-pointer has-[:checked]:text-primary text-slate-800 flex items-center p-2 bg-white gap-2">
                                                        <input type="radio" v-model="filterFormData.color" value="red"
                                                            id="red" name="color"
                                                            class="w-5 h-5 appearance-none checked:bg-primary rounded-full border-2 border-slate-300 shrink-0 transition duration-300" />
                                                        <span>{{ $t('Red') }}</span>
                                                    </label>

                                                    <label for="Black"
                                                        class="cursor-pointer has-[:checked]:text-primary text-slate-800 flex items-center p-2 bg-white gap-2">
                                                        <input type="radio" v-model="filterFormData.color" value="black"
                                                            id="Black" name="color"
                                                            class="w-5 h-5 appearance-none checked:bg-primary rounded-full border-2 border-slate-300 shrink-0 transition duration-300" />
                                                        <span>{{ $t('Black') }}</span>
                                                    </label>

                                                    <label for="Blue"
                                                        class="cursor-pointer has-[:checked]:text-primary text-slate-800 flex items-center p-2 bg-white gap-2">
                                                        <input type="radio" v-model="filterFormData.color" value="Blue"
                                                            id="Blue" name="color"
                                                            class="w-5 h-5 appearance-none checked:bg-primary rounded-full border-2 border-slate-300 shrink-0 transition duration-300" />
                                                        <span>{{ $t('Blue') }}</span>
                                                    </label>

                                                    <label for="Orange"
                                                        class="cursor-pointer has-[:checked]:text-primary text-slate-800 flex items-center p-2 bg-white gap-2">
                                                        <input type="radio" v-model="filterFormData.color"
                                                            value="Orange" id="Orange" name="color"
                                                            class="w-5 h-5 appearance-none checked:bg-primary rounded-full border-2 border-slate-300 shrink-0 transition duration-300" />
                                                        <span>{{ $t('Orange') }}</span>
                                                    </label>

                                                    <label for="White"
                                                        class="cursor-pointer has-[:checked]:text-primary text-slate-800 flex items-center p-2 bg-white gap-2">
                                                        <input type="radio" v-model="filterFormData.color" value="White"
                                                            id="White" name="color"
                                                            class="w-5 h-5 appearance-none checked:bg-primary rounded-full border-2 border-slate-300 shrink-0 transition duration-300" />
                                                        <span>{{ $t('White') }}</span>
                                                    </label>

                                                    <label for="MultiColor"
                                                        class="cursor-pointer has-[:checked]:text-primary text-slate-800 flex items-center p-2 bg-white gap-2">
                                                        <input type="radio" v-model="filterFormData.color"
                                                            value="MultiColor" id="MultiColor" name="color"
                                                            class="w-5 h-5 appearance-none checked:bg-primary rounded-full border-2 border-slate-300 shrink-0 transition duration-300" />
                                                        <span>{{ $t('MultiColor') }}</span>
                                                    </label>

                                                </div>
                                            </div>

                                        </div>

                                        <!-- button Clear and Apply -->
                                        <div class="flex gap-6 p-6 border-t border-slate-200">
                                            <button
                                                class="grow px-4 py-3 rounded-[10px] border border-primary text-primary text-base font-medium leading-normal"
                                                @click="clearFilter">
                                                {{ $t('Clear') }}
                                            </button>
                                            <button
                                                class="grow px-4 py-3 bg-primary rounded-[10px] border border-primary text-white text-base font-medium leading-normal"
                                                @click="fetchProducts">
                                                {{ $t('Apply') }}
                                            </button>
                                        </div>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { FunnelIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { StarIcon } from '@heroicons/vue/24/solid';
import ProductCard from '../components/ProductCard.vue';
import { useMaster } from '../stores/MasterStore';
// Import Swiper styles
import 'swiper/css';

const masterStore = useMaster();
const route = useRoute();
const router = useRouter();

onMounted(() => {
    if (!masterStore.multiVendor) {
        router.push('/');
        return;
    }
    fetchSubCategories()
    fetchProducts();
    fetchShopDetails();


});

watch(() => route.params.slug, () => {
    fetchProducts();
});

watch(() => route.query.subcategory, () => {
    fetchProducts();
});

const showFilterCanvas = ref(false);

const currentPage = ref(1);
const perPage = 12;

const onClickHandler = (page) => {
    currentPage.value = page;
    fetchProducts();
};

const filterFormData = ref({
    review: null,
    shortBy: null,
    brand: null,
    color: null,
    size: null,
    minPrice: null,
    maxPrice: null
});

const clearFilter = () => {
    filterFormData.value.review = null;
    filterFormData.value.shortBy = null;
    filterFormData.value.brand = null;
    filterFormData.value.color = null;
    filterFormData.value.size = null;
    filterFormData.value.minPrice = null;
    filterFormData.value.maxPrice = null;
    fetchProducts();
}

const ratings = [5, 4, 3, 2, 1];

const filterSortBy = [
    {
        name: 'High to Low',
        value: 'high_to_low'
    },
    {
        name: 'Low to High',
        value: 'low_to_high'
    },
    {
        name: 'Most Selling',
        value: 'most_selling'
    },
    {
        name: 'Top Seller',
        value: 'top_seller'
    },
    {
        name: 'New Product',
        value: 'new_product'
    }
];

const subcategories = ref([]);

const products = ref([]);
const totalProducts = ref(0);

const shop = ref({});

const fetchSubCategories = async () => {
    axios.get('/sub-categories?category_id=' + route.params.slug).then((response) => {
        subcategories.value = response.data.data.sub_categories;
    });

};

const fetchProducts = async () => {
    axios.get('/products', {
        params: {
            shop_id: route.params.id,
            category_id: route.params.slug,
            page: currentPage.value,
            per_page: perPage,
            rating: filterFormData.value.review,
            sort_type: filterFormData.value.shortBy,
            min_price: filterFormData.value.minPrice,
            max_price: filterFormData.value.maxPrice,
            color: filterFormData.value.color,
            sub_category_id: route.query.subcategory
        }
    }).then((response) => {
        totalProducts.value = response.data.data.total;
        products.value = response.data.data.products;
        window.scrollTo(0, 0);
    });
};

const fetchShopDetails = async () => {
    axios.get('/shops/' + route.params.id).then((response) => {
        shop.value = response.data.data.shop;
    });
};

</script>

<style>
.categorySwiper .swiper-slide {
    width: auto !important;
}

input[type=range]::-webkit-slider-runnable-track,
input[type=range]::-ms-track,
input[type=range]::-moz-range-track {
    background: #000;
}

input[type=range]::-moz-range-thumb,
input[type=range]::-ms-thumb,
input[type=range]::-webkit-slider-thumb {
    background: #000;
}
</style>
