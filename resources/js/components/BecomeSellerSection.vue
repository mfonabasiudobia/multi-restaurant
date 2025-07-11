<template>
    <div class="bg-gradient-to-r from-primary-50 to-primary-100 overflow-hidden">
        <div class="margin-container py-16">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <!-- Left Content -->
                <div class="text-center md:text-left relative z-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 leading-tight">
                        {{ title }} <span class="text-primary-500">SecondHub</span>
                    </h2>
                    <div class="space-y-4 mb-8">
                        <div v-for="(feature, index) in localizedFeatures" :key="index" class="flex items-start space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-500 mt-1 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <p class="text-gray-700">{{ feature }}</p>
                        </div>
                    </div>
                    <a v-if="multiVendor" 
                       href="/shop/register" 
                       class="inline-flex items-center px-8 py-3 rounded-full bg-primary-500 text-white font-semibold hover:bg-primary-700 transition duration-300 transform hover:scale-105">
                        {{ $t('Become a Seller') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
                
                <!-- Right Decorative Elements -->
                <div class="relative hidden md:block">
                    <div class="absolute w-72 h-72 bg-primary-500 rounded-full opacity-20 -top-10 -right-10"></div>
                    <div class="absolute w-48 h-48 bg-primary-500 rounded-full opacity-20 bottom-0 left-10"></div>
                    <div class="relative z-10 flex justify-center items-center">
                        <!-- Existing SVG elements -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useMaster } from '../stores/MasterStore';
import { useI18n } from 'vue-i18n';

const master = useMaster();
const { locale } = useI18n();
const multiVendor = computed(() => master.getMultiVendor);

const features = {
    ro: [
        'Listare rapidă și simplă a produselor',
        'Acces la o comunitate mare de cumpărători',
        'Suport dedicat pentru vânzători'
    ],
    en: [
        'Quick and easy product listing',
        'Access to a large community of buyers',
        'Dedicated seller support'
    ]
};

const titles = {
    ro: 'Vinde Simplu și Rapid pe',
    en: 'Sell Simply and Quickly on'
};

const title = computed(() => titles[locale.value] || titles.en);
const localizedFeatures = computed(() => features[locale.value] || features.en);
</script> 