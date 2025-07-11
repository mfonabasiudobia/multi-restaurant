<template>
    <div class="margin-container">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 overflow-hidden pt-4">
            <router-link to="/" class="w-6 h-6">
                <HomeIcon class="w-5 h-5 text-slate-600" />
            </router-link>

            <div class="grow w-full overflow-hidden">
                <div class="space-x-1 text-slate-600 text-sm font-normal truncate">
                    <span>{{ $t('Home') }}</span>
                    <span>/</span>
                    <span>{{ $t('Cart') }}</span>
                    <span>/</span>
                    <span>{{ $t('Checkout') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 my-3 gap-8">

            <div class="col-span-1 xl:col-span-2">

                <div class="py-4 border-b tran" :class="showProductItems ? 'border-primary' : 'border-slate-200'">
                    <!-- checkout -->
                    <div class="flex gap-2 justify-between items-center">
                        <div class="text-slate-950 text-lg sm:text-3xl font-medium leading-10">{{ $t('Checkout') }}
                        </div>
                        <div class="flex items-center gap-2 cursor-pointer"
                            @click="showProductItems = !showProductItems">
                            <div class="text-primary-600 text-lg font-medium leading-normal tracking-tight">
                                ({{ getSelectedProductsCount() }} {{ $t('items') }})
                            </div>
                            <ChevronDownIcon class="w-5 h-5 text-primary-600 transition duration-300"
                                :class="showProductItems ? 'rotate-180' : ''" />
                        </div>
                    </div>

                    <!-- Product items -->
                    <div v-if="showProductItems">
                        <BuyNowCheckoutProduct />
                    </div>
                </div>

                <!-- Shipping Address -->
                <ShippingAddress />

                <div class="mt-6">
                    <div class="mb-1">
                        <span class="text-slate-950 text-xl font-medium leading-7">{{ $t('Note') }}</span>
                        <span class="text-slate-500 text-lg font-normal leading-7 tracking-tight">
                            ({{ $t('Optional') }})
                        </span>
                    </div>
                    <textarea v-model="note" rows="3" class="form-input"
                        :placeholder="$t('Write your note here') + '...'"></textarea>
                </div>

                <!-- Payment Method -->
                <PaymentMethods />

            </div>

            <!-- Order Summary -->
            <BuyNowCheckoutOrderSummary :note="note" :paymentMethod="paymentMethod" />

        </div>

    </div>
</template>

<script setup>
import { ChevronDownIcon, HomeIcon } from '@heroicons/vue/24/outline';
import { onMounted, ref, watch, provide, onBeforeUnmount } from 'vue';
import { useToast } from 'vue-toastification';
import axios from 'axios';

import BuyNowCheckoutOrderSummary from '../components/BuyNowCheckoutOrderSummary.vue';
import BuyNowCheckoutProduct from '../components/BuyNowCheckoutProduct.vue';
import ShippingAddress from '../components/CheckoutShippingAddress.vue';
import PaymentMethods from '../components/PaymentMethods.vue';

import { useBasketStore } from '../stores/BasketStore';
import { useMaster } from '../stores/MasterStore';

import { useRouter } from 'vue-router';
const router = new useRouter();

import { useAuth } from '../stores/AuthStore';
const AuthStore = useAuth();
const toast = useToast();

const master = useMaster();
const basketStore = useBasketStore();

const showProductItems = ref(true);

const note = ref("");

const paymentType = ref('cash');
const paymentMethod = ref('CASH');
const proofUploaded = ref(false);
const uploadedProof = ref(null);

// Provide state variables
provide('paymentType', paymentType);
provide('paymentMethod', paymentMethod);
provide('proofUploaded', proofUploaded);
provide('uploadedProof', uploadedProof);

const paymentGateway = ref(null);

// Add handlePopState and handleBeforeUnload methods
const handlePopState = (event) => {
  // Check if we're coming back from Stripe
  const pendingOrderId = localStorage.getItem('pendingPaymentOrderId');
  if (pendingOrderId) {
    // We have a pending payment, check its status
    axios.get(`/check-payment-status/${pendingOrderId}`)
      .then(response => {
        if (response.data.status === 'pending') {
          // Clear the pending order ID from localStorage
          localStorage.removeItem('pendingPaymentOrderId');
        }
      })
      .catch(error => {
        console.error('Error checking payment status:', error);
      });
  }
};

const handleBeforeUnload = (event) => {
  // Only show confirmation if there's a product to checkout
  if (basketStore.buyNowProduct) {
    event.preventDefault();
    event.returnValue = 'You have items in your cart. Are you sure you want to leave?';
  }
};

onMounted(() => {
    // Add popstate event listener for browser back/forward buttons
    window.addEventListener('popstate', handlePopState);
    
    // Add beforeunload event listener
    window.addEventListener('beforeunload', handleBeforeUnload);
    
    window.scrollTo(0, 0);
    basketStore.coupon_code = "";
    
    // Check for any pending payments
    const pendingOrderId = localStorage.getItem('pendingPaymentOrderId');
    if (pendingOrderId) {
        // We have a pending payment, check its status
        axios.get(`/check-payment-status/${pendingOrderId}`)
            .then(response => {
                if (response.data.status === 'pending') {
                    // Clear the pending order ID from localStorage
                    localStorage.removeItem('pendingPaymentOrderId');
                }
            })
            .catch(error => {
                console.error('Error checking payment status:', error);
            });
    }
    
    if (!AuthStore.user) {
        router.push({ name: 'home' });
    }
    
    // Make sure we have a product to checkout
    if (!basketStore.buyNowProduct) {
       // router.push({ name: 'home' });
        toast.error('No product selected for checkout', {
            position: "bottom-left",
        });
    }
});

onBeforeUnmount(() => {
    // Remove event listeners
    window.removeEventListener('popstate', handlePopState);
    window.removeEventListener('beforeunload', handleBeforeUnload);
});

watch(paymentType, () => {
    if (paymentType.value === 'card') {
        paymentMethod.value = paymentGateway.value;
    } else {
        paymentMethod.value = paymentType.value;
    }
});

watch(paymentGateway, () => {
    if (paymentType.value === 'card') {
        paymentMethod.value = paymentGateway.value;
    }
});

const calculateDeliveryCharge = () => {
    const product = basketStore.buyNowProduct;
    if (!product?.selectedVariant?.weight) return 0;

    const weight = product.selectedVariant.weight;
    const deliveryWeights = product.delivery_weights || [];

    const charge = deliveryWeights.find(
        dw => weight >= dw.min_weight && weight <= dw.max_weight
    );
    
    return charge ? charge.price : 0;
};

const calculateTotal = () => {
    const subtotal = basketStore.buyNowProduct?.price || 0;
    const deliveryCharge = calculateDeliveryCharge();
    return subtotal + deliveryCharge;
};

const getSelectedProductsCount = () => {
    console.log('basketStore.buyNowProduct', basketStore);
    if (!basketStore.buyNowProduct) return 0;
    
    // If buyNowProduct has products array (grouped by shop)
    if (basketStore.buyNowProduct.products) {
        return basketStore.buyNowProduct.products.length;
    }
    
    // If buyNowProduct is a single product
    return 1;
};
</script>
<style scoped>
.form-label {
    @apply text-slate-700 text-base font-normal leading-normal;
}

.form-input {
    @apply p-3 rounded-lg border border-slate-200 focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}

.formInputCoupon {
    @apply rounded-lg border border-slate-200 focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}

.radio-btn {
    @apply w-5 h-5 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}

.radioBtn2 {
    @apply w-4 h-4 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}
</style>
