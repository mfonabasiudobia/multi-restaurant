<template>
    <MarginContainer v-if="props?.products?.length > 0" class="mt-4 mb-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-slate-800 text-lg md:text-3xl font-bold leading-9">
                {{ $t('Recently Viewed') }}
            </h2>
            <div class="flex gap-2">
                <button @click="scrollLeft" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="scrollRight" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="relative overflow-hidden">
            <div ref="productsContainer" class="flex max-sm:gap-1  md:gap-4 overflow-x-hidden scroll-smooth">
                <div v-for="product in filteredProducts" :key="product.id" class="flex-none  md:w-[180px] max-sm:w-1/2 md:w-[225px] max-sm:p-2">
                    <ProductCard :product="product" />
                </div>
            </div>
        </div>
    </MarginContainer>
</template>

<script setup>
import { ref, computed } from 'vue';
import ProductCard from './ProductCard.vue';
import MarginContainer from './MarginContainer.vue';

const props = defineProps({
    products: Array
});

const productsContainer = ref(null);

const filteredProducts = computed(() => {
    return props.products?.filter(product => product.quantity > 0) || [];
});

let unit = document.documentElement.scrollWidth / 100;

const scrollLeft = () => {
    productsContainer.value.scrollBy({ left: -50* unit, behavior: 'smooth' });
};

const scrollRight = () => {
    productsContainer.value.scrollBy({ left: 50* unit, behavior: 'smooth' });
};
</script>

<style>
.recentlyViewed .swiper-button-prev,
.recentlyViewed .swiper-button-next {
    @apply bg-white w-8 h-8 rounded-full shadow border border-slate-200 text-slate-600;
}

.recentlyViewed .swiper-button-prev::after,
.recentlyViewed .swiper-button-next::after {
    @apply text-lg;
}

.recentlyViewed .swiper-button-next {
    right: 4px;
}

.recentlyViewed .swiper-button-prev {
    left: 4px;
}
</style>
