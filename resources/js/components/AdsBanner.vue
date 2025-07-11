<template>
    <div class="mt-8 margin-container">
        <swiper 
            :pagination="{ clickable: true, dynamicBullets: true }" 
            :modules="modules" 
            class="ads-swiper" 
            :loop="true"
            :breakpoints="swiperBreakpoints"
            :autoplay="{
                delay: 4000,
                disableOnInteraction: false
            }"
        >
            <swiper-slide v-for="(ad, index) in ads" :key="ad.id">
                <a :href="ad.redirectLink" target="_blank" class="promo-card">
                    <img 
                        :src="ad.thumbnail" 
                        loading="lazy" 
                        class="w-full object-cover rounded-lg promo-height"
                        :alt="`promotional-ad-${index + 1}`"
                    />
                </a>
            </swiper-slide>
        </swiper>
    </div>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Pagination, A11y, Autoplay } from 'swiper/modules';
import { ref } from 'vue';

import 'swiper/css';
import 'swiper/css/pagination';

const modules = [Pagination, A11y, Autoplay];

const props = defineProps({
    ads: {
        type: Array,
        required: true,
        default: () => []
    }
});

const swiperBreakpoints = {
    320: {
        slidesPerView: 1.2,
        spaceBetween: 12
    },
    640: {
        slidesPerView: 2,
        spaceBetween: 20
    },
    1024: {
        slidesPerView: 3,
        spaceBetween: 24
    }
};

</script>

<style scoped>
.promo-card {
    display: block;
    position: relative;
    transition: all 0.3s ease;
    overflow: hidden;
    border-radius: 0.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.promo-card:hover {
    transform: translateY(-4px);
}

.promo-height {
    height: 180px;
    transition: transform 0.3s ease;
}

/* Tablet */
@media (min-width: 640px) {
    .promo-height {
        height: 250px;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .promo-height {
        height: 300px;
    }
}

.ads-swiper {
    padding-bottom: 2.5rem !important;
}

.ads-swiper :deep(.swiper-pagination-bullet) {
    width: 8px;
    height: 8px;
    background: #cbd5e1;
    opacity: 0.5;
    transition: all 0.3s ease;
}

.ads-swiper :deep(.swiper-pagination-bullet-active) {
    @apply bg-primary;
    opacity: 1;
    width: 24px;
    border-radius: 4px;
}

.ads-swiper :deep(.swiper-pagination) {
    bottom: 0 !important;
}
</style>
