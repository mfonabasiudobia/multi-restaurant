<template>
    <div class="flex flex-col md:flex-row xl:flex-col items-center gap-6">

        <!-- Shipping Charge and Estimated delivery time -->
        <div
            class="w-full grow md:flex lg:gap-6 xl:block p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2 xl:space-y-4">

            <!-- Weight Based Delivery charge -->
            <div class="flex grow flex-col items-start xl:flex-row xl:items-center gap-2 xl:gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-xl justify-center items-center flex">
                    <img :src="'/assets/icons/weight-scale.svg'" alt="" class="w-6 h-6">
                </div>
                <div>
                    <div class="text-slate-500 text-base font-normal leading-normal">
                        {{ $t('Weight Based Delivery') }} ({{ displayWeight }} {{ masterStore.settings?.weight_unit }})
                    </div>
                    <div class="mt-1 text-slate-950 text-base font-bold leading-normal">
                        {{ masterStore.showCurrency(calculateDeliveryCharge()) }}
                    </div>
                </div>
            </div>

            <!-- Estimated delivery time -->
            <div class="flex grow flex-col items-start xl:flex-row xl:items-center gap-2 xl:gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-xl justify-center items-center flex">
                    <img :src="'/assets/icons/clock.svg'" alt="" class="w-6 h-6">
                </div>
                <div>
                    <div class="text-slate-500 text-base font-normal leading-normal">
                        {{ $t('Estimated delivery time') }}
                    </div>
                    <div class="mt-1 text-slate-950 text-base font-bold leading-normal">
                        {{ product.shop?.estimated_delivery_time }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Shop info -->
        <div v-if="masterStore.multiVendor"
            class="bg-slate-50 hover:border-primary transition w-full grow rounded-xl border border-slate-100 xl:mt-6">
            <div class="flex justify-between items-center gap-4 p-4">
                <div class="flex items-center gap-4 overflow-hidden">
                    <div class="w-14 h-14 rounded-full overflow-hidden shrink-0">
                        <img :src="product.shop?.logo" loading="lazy" class="w-full h-full object-cover">
                    </div>

                    <div class="overflow-hidden">
                        <div class="text-slate-500 text-sm font-normal leading-tight">
                            {{ $t('Sold by') }}
                        </div>
                        <div class="mt-1.5 text-slate-950 text-base font-medium leading-normal truncate">
                            {{ product.shop?.name }}
                        </div>
                    </div>
                </div>

                <div class="">
                    <StarIcon class="w-5 h-5 text-amber-500" />
                    <div class="text-slate-800 text-sm font-bold mt-1">
                        {{ product.shop?.rating.toFixed(1) }}
                    </div>
                </div>
            </div>
            <router-link :to="`/shops/` + product.shop?.id"
                class="border-t border-slate-100 text-slate-600 text-sm font-normal leading-tight block">
                <span class="py-2 block text-center">
                    {{ $t('Visit Store') }}
                </span>
            </router-link>
        </div>

    </div>

    <!-- Popular Products -->
    <div class="mt-8">
        <div class="text-slate-800 text-base font-medium leading-normal">
            {{ $t('Popular Products From Them') }} 
        </div>

        <div class="flex gap-4 md:grid md:grid-cols-2 lg:grid-cols-3 xl:block xl:space-y-4 mt-4 overflow-x-auto">
            <div v-for="product in popularProducts" :key="product.id" class="w-[320px]  md:w-full shrink-0">
                <ProductCardHorizontal :product="product" />
            </div>
        </div>

    </div>
</template>

<script setup>
import { computed } from 'vue';
import { StarIcon } from '@heroicons/vue/24/solid';
import ProductCardHorizontal from './ProductCardHorizontal.vue';
import { useMaster } from "../stores/MasterStore";
const masterStore = useMaster();

const props = defineProps({
    product: Object,
    popularProducts: Array,
    deliveryWeights: {
        type: Array,
        default: () => []
    },
    selectedVariant: {
        type: Object,
        default: null
    }
});

const displayWeight = computed(() => {
    return props.selectedVariant?.weight || props.product?.weight || 0;
});

const calculateDeliveryCharge = () => {
    if (!props.deliveryWeights?.length) return 0;
    
    const weight = displayWeight.value;
    if (!weight) return 0;

    // Find matching weight range
    const charge = props.deliveryWeights.find(
        dw => weight >= dw.min_weight && weight <= dw.max_weight
    );
    
    return charge ? charge.price : 0;
};
</script>

<style lang="scss" scoped></style>
