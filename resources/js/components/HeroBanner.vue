<!-- BannerSection.vue -->
<template>
    <div class="mt-3 mx-18 sm:mx-4 lg:mx-18 md:mx-5 xs:mx-2">
        <!-- Simplified Header Text -->
        <div class="text-center mb-4">
            <p class="text-base text-gray-600">
                {{ $t('banner.saci_cu_haine') }}
            </p>
        </div>
        
        <!-- Main Banner Slider -->
        <div class="w-full mb-4 relative">
            <swiper 
                :pagination="{
                    clickable: true,
                    el: '.swiper-custom-pagination'
                }" 
                :slides-per-view="1" 
                :space-between="20"
                :modules="modules" 
                class="mySwiper" 
                :loop="true" 
                :autoplay="{
                    delay: 3000,
                    disableOnInteraction: false
                }"
            >
                <swiper-slide v-for="banner in banners" :key="banner.id">
                    <a :href="banner.redirectLink" target="_blank" class="block w-full">
                        <img 
                            :src="banner.thumbnail" 
                            loading="lazy" 
                            class="w-full object-cover rounded-lg banner-height"
                            alt="promotion banner"
                        />
                    </a>
                </swiper-slide>
            </swiper>
            <!-- Custom Pagination -->
            <div class="swiper-custom-pagination absolute bottom-4 left-0 right-0 z-10"></div>
        </div>
    </div>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Pagination, A11y, Autoplay } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/pagination';

const modules = [Pagination, A11y, Autoplay];

const props = defineProps({
    banners: {
        type: Array,
        required: true
    }
});
</script>

<style scoped>
/* Mobile first approach */
.banner-height {
    height: 160px;
}

.promo-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: flex-start;
}

.promo-card {
    flex: 0 0 calc(50% - 4px);
    width: calc(50% - 4px);
    min-width: 140px;
    max-width: 160px;
    transition: transform 0.2s ease;
    overflow: hidden;
}

.promo-height {
    height: 140px;
}

/* Tablet */
@media (min-width: 640px) {
    .banner-height {
        height: 220px;
    }
    
    .promo-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .promo-card {
        flex: none;
        width: auto;
        max-width: none;
    }
    
    .promo-height {
        height: 240px;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .banner-height {
        height: 280px;
    }
    
    .promo-container {
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }
    
    .promo-height {
        height: 300px;
    }
}

.promo-card:hover {
    transform: translateY(-4px);
}

/* Custom Pagination Styling */
:deep(.swiper-custom-pagination) {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
    position: absolute;
    bottom: 16px;
    left: 0;
    right: 0;
    z-index: 10;
}

:deep(.swiper-pagination-bullet) {
    width: 8px;
    height: 8px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 50%;
    opacity: 1;
    cursor: pointer;
    transition: all 0.3s ease;
}

:deep(.swiper-pagination-bullet-active) {
    width: 24px;
    border-radius: 4px;
    background: white;
}

/* Remove navigation buttons */
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
    display: none;
}

/* Container padding */
.main-container {
    
   
}

@media (min-width: 640px) {
    .main-container {
        padding: 0 1.5rem;
    }
}

@media (min-width: 1024px) {
    .main-container {
        padding: 0 2rem;
    }
}
</style>