<template>
    <div>
        <div v-if="routerName !== 'checkout'"
            class="w-[72px] bg-white rounded-l-[10px] shadow border-l border-t border-b border-primary flex-col justify-start items-center gap-1 flex fixed top-1/2 -translate-y-1/2 right-0 z-10 overflow-hidden cursor-pointer"
            @click="showCardCanvas()">
            <div class="pt-2 pb-0.5 flex flex-col items-center gap-1 justify-center">
                <!-- <img :src="'/assets/icons/bag-2.svg'" loading="lazy" class="w-6 h-6" /> -->
                <font-awesome-icon :icon="faBagShopping" class="w-6 h-6 text-primary"/>
                <div class="text-center text-slate-600 text-xs font-normal">{{ BasketStore.selectedProducts.length }} {{ $t('items') }}</div>
            </div>
            <div class="px-2 py-1 bg-primary">
                <div class="w-14 text-center text-white text-xs font-bold">{{
                    master.showCurrency(BasketStore.selectedProductsTotal) }}</div>
            </div>

        </div>

        <TransitionRoot as="template" :show="master.basketCanvas">
            <Dialog as="div" class="relative z-10" @close="showCardCanvas()">
                <TransitionChild as="template" enter="ease-in-out duration-500" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in-out duration-500" leave-from="opacity-100"
                    leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-30 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full sm:pl-10">
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
                                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                                        <div class="p-6 border-b border-slate-200 flex justify-between">

                                            <DialogTitle class="flex gap-3 items-center">
                                                <font-awesome-icon :icon="faBagShopping" class="w-8 h-8 text-primary"/>
                                                <div class="text-slate-950 text-lg md:text-3xl font-bold leading-9">
                                                    {{ $t('My Cart') }}
                                                </div>
                                                <div class="text-primary-600 text-lg font-medium  tracking-tight">({{
                                                    BasketStore.selectedProducts.length }} {{ $t('items') }})
                                                </div>
                                            </DialogTitle>

                                            <button class="outline-none border-0" @click="showCardCanvas()">
                                                <XMarkIcon class="h-6 w-6 text-slate-600" />
                                            </button>
                                        </div>

                                        <!-- Cart Items -->
                                        <div v-if="BasketStore.selectedProducts.length > 0" class="relative flex-1 py-3 px-4">
                                            <Basket />
                                        </div>

                                        <div v-else class="flex flex-1 items-center justify-center flex-col gap-3">
                                            <div
                                                class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center">
                                                <ArchiveBoxIcon class="w-12 h-12 text-red-300" />
                                            </div>
                                            <div class="text-slate-600 text-lg font-medium italic">
                                                {{ $t('Cart is empty') }}
                                            </div>
                                        </div>

                                        <!-- Proceed to Checkout -->
                                        <div v-if="BasketStore.selectedProducts.length > 0"
                                            class="flex gap-6 p-6 border-t border-slate-200">
                                            <div>
                                                <div
                                                    class="text-center text-slate-500 text-base font-normal leading-tight">
                                                    {{ $t('Cart Amount') }}
                                                </div>
                                                <div
                                                    class="text-slate-900 text-lg sm:text-2xl font-bold leading-normal">
                                                    {{ master.showCurrency(BasketStore.selectedProductsTotal) }}</div>
                                            </div>
                                            <button
                                                class="py-2 sm:py-4 inline-flex gap-2 items-center justify-center px-3 sm:px-6 text-white bg-primary grow rounded-xl"
                                                @click="processToCheckout">
                                                <div class="text-base font-medium">
                                                    {{ $t('Checkout Selected') }} ({{ BasketStore.selectedProducts.length }})
                                                </div>
                                                <ArrowRightIcon class="w-5 h-5 hidden sm:block" />
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
import { faBagShopping } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { ArchiveBoxIcon, ArrowRightIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { ref, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import Basket from './Basket.vue';

import { useBasketStore } from '../stores/BasketStore';
import { useMaster } from '../stores/MasterStore';

const route = new useRoute();
const router = new useRouter();
const BasketStore = useBasketStore();
const master = useMaster();

const toast = useToast();

const showCardCanvas = () => {
    if (!master.basketCanvas) {
        BasketStore.fetchCheckoutProducts();
    }
    master.basketCanvas = !master.basketCanvas
};

const routerName = ref(route.name);

watch(route, () => {
    routerName.value = route.name;
})

const processToCheckout = () => {
    if (BasketStore.selectedProducts.length === 0) {
        toast.error('Please select at least one product', {
            position: "bottom-left",
        });
        return;
    }

    // Set the selected shop IDs based on the selected products
    const selectedShopIds = new Set();
    BasketStore.products.forEach(shop => {
        shop.products.forEach(product => {
            if (BasketStore.selectedProducts.includes(product.id)) {
                selectedShopIds.add(shop.shop_id);
            }
        });
    });
    BasketStore.selectedShopIds = [...selectedShopIds];

    // Update the checkout products based on selected products
    BasketStore.fetchCheckoutProducts();
    
    // Close the cart canvas and navigate to checkout
    showCardCanvas();
    router.push({ name: "checkout" });
};

onMounted(() => {
    // Initialize the store and fetch the cart
    BasketStore.initializeStore();
    BasketStore.fetchCart();
});

</script>
