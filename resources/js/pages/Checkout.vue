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
                <div class="py-4 border-b" :class="showProductItems ? 'border-primary' : 'border-slate-200'">
                    <!-- Checkout -->
                    <div class="flex gap-2 justify-between items-center">
                        <div class="text-slate-950 text-lg sm:text-3xl font-medium leading-10">{{ $t('Checkout') }}</div>
                        <div class="flex items-center gap-2 cursor-pointer"
                            @click="showProductItems = !showProductItems">
                            <div class="text-primary-600 text-lg font-medium leading-normal tracking-tight">
                                ({{ basketStore.checkoutTotalItems }} {{ $t('items') }})
                            </div>
                            <ChevronDownIcon class="w-5 h-5 text-primary-600 transition duration-300"
                                :class="showProductItems ? 'rotate-180' : ''" />
                        </div>
                    </div>

                    <!-- Product items -->
                    <div v-if="showProductItems">
                        <checkoutProducts />
                    </div>
                </div>

                <!-- Shipping Address -->
                <ShippingAddress />
               

                <div class="mt-4 border border-slate-200 p-4 rounded-md">
                    <div class="mb-1">
                        <span class="text-slate-950 text-lg font-medium leading-6">{{ $t('Note') }}</span>
                        <span class="text-slate-500 text-sm font-normal leading-6 tracking-tight">
                            ({{ $t('Optional') }})
                        </span>
                    </div>
                    <textarea v-model="note" rows="2" class="form-input"
                        :placeholder="$t('Write your note') + '...'"></textarea>
                </div>
                

                <!-- Payment Method -->
                <PaymentMethods />

            </div>
            

            <!-- Order Summary -->
            <CheckoutOrderSummary :note="note"/>
        </div>
    </div>
</template>

<script setup>
import { ChevronDownIcon, HomeIcon } from '@heroicons/vue/24/outline';
import { ref, onMounted, watch, onBeforeUnmount } from 'vue';

import CheckoutOrderSummary from '../components/CheckoutOrderSummary.vue';
import checkoutProducts from '../components/checkoutProducts.vue';
import ShippingAddress from '../components/CheckoutShippingAddress.vue';
import { provide } from 'vue';

// Define shared states here
const paymentType = ref('cash');
const paymentMethod = ref('CASH');
const proofUploaded = ref(false);
const uploadedProof = ref(null);
// Provide state variables
provide('paymentType', paymentType);
provide('paymentMethod', paymentMethod);
provide('proofUploaded', proofUploaded);
provide('uploadedProof', uploadedProof);
import { useAuth } from '../stores/AuthStore';
import { useBasketStore } from '../stores/BasketStore';
import { useMaster } from '../stores/MasterStore';
import { useRouter } from 'vue-router';
import PaymentMethods from '../components/PaymentMethods.vue';

// Import toast
import { useToast } from 'vue-toastification';
const toast = useToast();

const AuthStore = useAuth();
const basketStore = useBasketStore();
const master = useMaster();
const router = useRouter();

const showProductItems = ref(false);
const note = ref('');

// Handle navigation away from checkout
const handleBeforeUnload = (event) => {
  // Only show confirmation if there are items in the cart
  if (basketStore.checkoutTotalItems > 0) {
    event.preventDefault();
    event.returnValue = 'You have items in your cart. Are you sure you want to leave?';
  }
};

// Handle browser back/forward navigation
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

onMounted(() => {
  // Add popstate event listener for browser back/forward buttons
  window.addEventListener('popstate', handlePopState);
  
  // Add beforeunload event listener
  window.addEventListener('beforeunload', handleBeforeUnload);
  
  window.scrollTo(0, 0);
  basketStore.coupon_code = '';
   
  if (!AuthStore.user) {
    router.push({ name: 'home' });
  }
    
  AuthStore.showAddressModal = false;
  AuthStore.showChangeAddressModal = false;
});

onBeforeUnmount(() => {
  // Remove event listeners
  window.removeEventListener('popstate', handlePopState);
  window.removeEventListener('beforeunload', handleBeforeUnload);
});

</script>



