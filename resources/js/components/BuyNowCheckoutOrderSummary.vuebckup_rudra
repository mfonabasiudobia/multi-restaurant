<template>
    <div>
        <div class="p-6 bg-white rounded-2xl border border-slate-200">
            <div class="text-slate-950 text-xl font-medium leading-7">
                {{ $t('Order Summary') }}
            </div>

            <!-- Subtotal -->
            <div class="my-4 flex justify-between gap-4">
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ $t('Subtotal') }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ master.showCurrency(orderData.total_amount) }}
                </div>
            </div>

            <!-- Discount -->
            <div class="my-4 flex justify-between gap-4">
                <div class="text-red-500 text-base font-normal leading-normal">
                    {{ $t('Discount') }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    -{{ master.showCurrency(orderData.coupon_discount) }}
                </div>
            </div>

            <div class="w-full h-[0px] border-t border-dashed border-slate-400"></div>

            <!-- Subtotal After Discount -->
            <div class="my-4 flex justify-between gap-4">
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ $t('Subtotal After Discount') }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ master.showCurrency((orderData.total_amount - orderData.coupon_discount).toFixed(2)) }}
                </div>
            </div>

            <!-- Total Weight -->
            <div class="my-4 flex justify-between gap-4">
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ $t('Total Weight') }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ calculateProductWeight().toFixed(2) }} {{ weightUnit }}
                </div>
            </div>

            <!-- Shipping Charge -->
            <div class="my-4 flex justify-between gap-4">
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ $t('Shipping Charge') }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ master.showCurrency(orderData.delivery_charge) }}
                </div>
            </div>

            <!-- vat and tax -->
            <div class="my-4 flex justify-between gap-4">
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ $t('Vat & Tax (19%)') }}
                </div>
                <div class="text-slate-950 text-base font-normal leading-normal">
                    {{ master.showCurrency(orderData.order_tax_amount) }}
                </div>
            </div>

            <div class="w-full h-[0px] border border-slate-500"></div>

            <!-- Total Payable -->
            <div class="my-4 flex justify-between gap-4">
                <div class="text-slate-950 text-lg font-medium leading-normal tracking-tight">
                    {{ $t('Total Payable') }}
                </div>
                <div class="text-slate-950 text-lg font-medium leading-normal tracking-tight">
                    {{ master.showCurrency(orderData.payable_amount) }}
                </div>
            </div>

            <!-- Delivery Options -->
            <div class="mt-4">
                <div 
                    @click="selectDeliveryMethod"
                    class="flex items-center justify-between p-4 rounded-lg border border-gray-200 shadow-sm w-full max-w-xl bg-white cursor-pointer hover:border-primary transition-colors"
                    :class="{'border-primary': isGLSSelected}"
                >
                    <div class="flex items-center gap-4">
                        <input
                            type="radio"
                            :checked="isGLSSelected"
                            class="w-5 h-5 text-primary border-gray-300 focus:primary"
                            @click.stop
                        />
                        <div class="flex flex-col">
                            <span class="text-lg font-medium">{{ $t('Delivery') }}</span>
                            <span class="text-gray-600">{{ $t('via GLS') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-primary font-medium">
                            {{ master.showCurrency(orderData.delivery_charge) }}
                        </div>
                        <div class="w-16 h-10 rounded flex items-center justify-center">
                            <img 
                                src="https://logowik.com/content/uploads/images/gls-shipping3559.jpg" 
                                alt="gls" 
                                class="w-full h-full object-contain" 
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Have a coupon -->
            <div class="p-4 mt-6 bg-slate-100 rounded-xl">
                <div class="text-black text-base font-normal leading-normal">
                    {{ $t('Have a coupon') }}?
                </div>

                <!-- Coupon Input -->
                <div class="relative mt-2">
                    <input type="text" v-model="coupon" class="formInputCoupon pr-14 p-3"
                        :placeholder="$t('Enter coupon code')" :class="hasCoupon ? 'text-green-500 pl-10' : ''" />

                    <button v-if="!hasCoupon"
                        class="bg-slate-700 absolute top-1/2 -translate-y-1/2 right-1.5 h-10 w-10 rounded flex justify-center items-center"
                        @click="ApplyCoupon">
                        <ArrowRightIcon class="w-6 h-6 text-white" />
                    </button>

                    <button v-else
                        class="bg-slate-100 absolute top-1/2 -translate-y-1/2 right-1.5 h-10 w-10 rounded flex justify-center items-center"
                        @click="removeCoupon">
                        <TrashIcon class="w-6 h-6 text-red-500" />
                    </button>

                    <span class="absolute top-1/2 -translate-y-1/2 left-3">
                        <CheckCircleIcon class="w-6 h-6 text-green-500" v-if="hasCoupon" />
                    </span>
                </div>
            </div>
        </div>

        <button class="px-6 py-4 w-full mt-4 bg-primary rounded-[10px] text-white text-base font-medium"
            @click="hasPendingPayment ? resumePayment() : processOrderConfirm()">
            {{ hasPendingPayment ? $t('Complete Payment') : $t('Place Order') }}
        </button>

        <!-- Unified Payment Modal -->
        <UnifiedPaymentModal
            :show="showPaymentProcessingModal"
            :state="paymentProcessingState"
            :payment-method="props.paymentMethod"
            :message="paymentFailureMessage"
            :countdown="redirectCountdown"
            :show-countdown="paymentProcessingState === 'failed'"
            :show-close-button="props.paymentMethod === 'COD' || props.paymentMethod === 'BANK'"
            @close="showPaymentProcessingModal = false"
            @cancel="cancelPaymentProcess"
            @continue="handleContinueShopping"
            @retry="retryPayment"
            @view-order="viewOrder"
            @reopen-window="reopenPaymentWindow"
        />

        <!-- Payment Status Modal -->
        <PaymentStatusModal
            :show="showPaymentStatusModal"
            :status="paymentModalStatus"
            :title="paymentModalTitle"
            :message="paymentModalMessage"
            :showContinueButton="paymentModalShowContinue"
            :showCancelButton="paymentModalShowCancel"
            @continue="handleContinuePayment"
            @cancel="handleCancelPayment"
            @close="showPaymentStatusModal = false"
        />

        <!-- End Order Confirm Dialog Modal -->
        <OrderConfirmModal />
    </div>
</template>

<script setup>
import { ArrowRightIcon, TrashIcon } from "@heroicons/vue/24/outline";
import { CheckCircleIcon } from "@heroicons/vue/24/solid";
import { onMounted, ref, watch, onBeforeUnmount, inject } from "vue";
import OrderConfirmModal from "../components/OrderConfirmModal.vue";
import ToastSuccessMessage from "../components/ToastSuccessMessage.vue";
import PaymentStatusModal from "../components/PaymentStatusModal.vue";
import UnifiedPaymentModal from "../components/UnifiedPaymentModal.vue";

import { useToast } from "vue-toastification";
import { useAuth } from "../stores/AuthStore";
import { useBasketStore } from "../stores/BasketStore";
import { useMaster } from "../stores/MasterStore";

import { useRouter } from "vue-router";
import axios from 'axios';
import { loadStripe } from '@stripe/stripe-js';

const router = new useRouter();

const basketStore = useBasketStore();
const master = useMaster();
const authStore = useAuth();

const toast = useToast();

const hasCoupon = ref(false);

const coupon = ref("");

// Get payment-related props and inject values
const props = defineProps({
    note: String,
    paymentMethod: String,
});

// Get payment proof from parent component
const uploadedProof = inject('uploadedProof', ref(null));
const proofUploaded = inject('proofUploaded', ref(false));

const orderData = ref({
    total_amount: 0,
    delivery_charge: 0,
    coupon_discount: 0,
    payable_amount: 0,
    shop_ids: [],
    order_tax_amount: 0
});

const isGLSSelected = ref(true);
const weightUnit = ref('KG');

// Payment status modal
const showPaymentStatusModal = ref(false);
const paymentModalStatus = ref('pending');
const paymentModalTitle = ref('Payment Status');
const paymentModalMessage = ref('');
const paymentModalShowContinue = ref(false);
const paymentModalShowCancel = ref(false);

// Track payment state
const stripe = ref(null);
const sessionId = ref('');
const orderId = ref(null);
const paymentStarted = ref(false);
const paymentInProgress = ref(false);
const hasPendingPayment = ref(false);
const pendingPaymentDetails = ref(null);
const paymentFailed = ref(false);

// Payment processing modal
const showPaymentProcessingModal = ref(false);
const paymentWindow = ref(null);
const paymentUrl = ref('');
const paymentStatusCheckInterval = ref(null);
const paymentProcessingState = ref('processing'); // 'processing' or 'failed'
const paymentFailureMessage = ref('');
const redirectCountdown = ref(10);
const redirectTimer = ref(null);

onMounted(async () => {
    coupon.value = basketStore.coupon_code;

    // Add visibility change listener
    document.addEventListener('visibilitychange', handleVisibilityChange);

    // Fetch weight unit
    try {
        const response = await axios.get('/weight-unit');
        weightUnit.value = response.data.unit;
    } catch (error) {
        console.error('Error fetching weight unit:', error);
    }

    if (!basketStore.isLoadingCart) {
        fetchBuyNowCartCheckout();
    }
});

onBeforeUnmount(() => {
    // Remove visibility change listener
    document.removeEventListener('visibilitychange', handleVisibilityChange);
    
    // Clear payment status check interval
    if (paymentStatusCheckInterval.value) {
        clearInterval(paymentStatusCheckInterval.value);
    }
    
    // Clear redirect timer if it exists
    if (redirectTimer.value) {
        clearInterval(redirectTimer.value);
    }
    
    // Close payment window if it's still open
    if (paymentWindow.value && !paymentWindow.value.closed) {
        paymentWindow.value.close();
    }
    
    // Check if payment was started but not completed
    if (paymentStarted.value && orderId.value) {
        axios.post('/abandoned-payment', { order_id: orderId.value })
            .catch(error => console.error('Failed to mark payment as abandoned:', error));
    }
});

watch(() => basketStore.isLoadingCart, () => {
    if (!basketStore.isLoadingCart) {
        fetchBuyNowCartCheckout();
    }
});

const calculateProductWeight = () => {
    if (!basketStore.buyNowProduct) return 0;
    
    let totalWeight = 0;
    
    // If buyNowProduct has products array (grouped by shop)
    if (basketStore.buyNowProduct.products) {
        basketStore.buyNowProduct.products.forEach(product => {
            // If product has size, use size as weight
            if (product.size) {
                totalWeight += (parseFloat(product.size.name) || 0) * (product.quantity || 1);
            }
        });
    } 
    // If buyNowProduct is a single product
    else {
        const product = basketStore.buyNowProduct;
        if (product.size) {
            // Make sure to use the product quantity or default to 1
            const quantity = product.quantity || 1;
            totalWeight += (parseFloat(product.size.name) || 0) * quantity;
        }
    }
    
    return totalWeight;
};

const fetchBuyNowCartCheckout = () => {
    // Calculate product weight from buy now product
    const productWeight = calculateProductWeight();
    
    // Get shop IDs from buyNowProduct
    let shopIds = [];
    if (basketStore.buyNowShopId) {
        shopIds = [basketStore.buyNowShopId];
    }
    
    // Calculate the total price including size price
    let totalPrice = 0;
    let sizePrice = 0;
    let quantity = 1;
    
    if (basketStore.buyNowProduct) {
        // Get the product quantity
        quantity = basketStore.buyNowProduct.quantity || 1;
        
        // Get base product price
        const basePrice = basketStore.buyNowProduct.discount_price > 0 
            ? basketStore.buyNowProduct.discount_price 
            : basketStore.buyNowProduct.price;
            
        // Add size price if available
        if (basketStore.buyNowProduct.size) {
            sizePrice = basketStore.buyNowProduct.size.price || 0;
        }
        
        // Calculate total price with quantity
        totalPrice = (basePrice + sizePrice) * quantity;
    }
    
    axios.post("/cart/checkout", {
        shop_ids: shopIds,
        is_buy_now: true,
        coupon_code: coupon.value,
        total_weight: productWeight,
        selected_products: basketStore.selectedProducts,
        size_price: sizePrice, // Pass the size price to the backend
        quantity: quantity // Pass the quantity to the backend
    }, {
        headers: {
            Authorization: authStore.token,
        },
    }).then((response) => {
        orderData.value = response.data.data.checkout;

        basketStore.fetchCart();

      
        
        // Don't override buyNowProduct here as it's already set
        // basketStore.buyNowProduct = response.data.data.checkout_items[0];

        hasCoupon.value = response.data.data.apply_coupon;

        if (hasCoupon.value && coupon.value.length > 0) {
            toast.success(response.data.message, {
                position: "bottom-left",
            });
            basketStore.coupon_code = coupon.value;
        } else if (!hasCoupon.value && coupon.value.length > 0) {
            toast.error(response.data.message, {
                position: "bottom-left",
            });
            basketStore.coupon_code = '';
        }
    }).catch((error) => {
        toast.error(error.response.data.message, {
            position: "bottom-left",
        });
    });
};

const ApplyCoupon = () => {
    if (coupon.value.length > 0) {
        fetchBuyNowCartCheckout();
    }
};

const removeCoupon = () => {
    coupon.value = "";
    hasCoupon.value = false;
    basketStore.coupon_code = "";
    fetchBuyNowCartCheckout();
};

const selectDeliveryMethod = () => {
    isGLSSelected.value = !isGLSSelected.value;
};

const content = {
    component: ToastSuccessMessage,
    props: {
        title: 'Order Placed',
        message: 'Your order has been placed successfully.',
    },
};

const processOrderConfirm = async () => {
    try {
        let addressId = basketStore.address?.id;

        // If no existing address, silently save the pending address first
        if (!addressId && basketStore.pendingAddress) {
            try {
                // Prepare the address data
                const addressData = {
                    ...basketStore.pendingAddress,
                    address_type: basketStore.pendingAddress.address_type || 'home',
                    is_default: true // Set as default address
                };
                
                // Set country if not present
                if (!addressData.country) {
                    addressData.country = 'Romania';
                }

                const addressResponse = await axios.post('/address/store', addressData, {
                    headers: {
                        'Authorization': authStore.token
                    }
                });
                
                // Get the new address ID
                addressId = addressResponse.data.data.address.id;
                
                // Update basketStore with new address
                basketStore.address = addressResponse.data.data.address;
                
                // Update the auth store addresses list
                authStore.fetchAddresses();
                
            } catch (error) {
                const errorMessage = error.response?.data?.errors 
                    ? Object.values(error.response.data.errors)[0][0] 
                    : error.response?.data?.message || 'Failed to save address';
                toast.error(errorMessage);
                return;
            }
        }

        // Proceed with order placement
        if (!addressId) {
            toast.error('Please add a shipping address', {
                position: "bottom-left",
            });
            return;
        }

        if (!props.paymentMethod) {
            toast.error('Please select a payment method', {
                position: "bottom-left",
            });
            return;
        }

        if (props.paymentMethod === 'BANK' && !proofUploaded) {
            toast.error('Please upload payment proof', {
                position: "bottom-left",
            });
            return;
        }

        // Log the payable amount to verify it includes shipping
        console.log('Placing order with payable amount:', orderData.value.payable_amount);
        console.log('Delivery charge:', orderData.value.delivery_charge);
        console.log('Total amount:', orderData.value.total_amount);
        console.log('Coupon discount:', orderData.value.coupon_discount);

        // Get shop IDs from buyNowProduct
        let shopIds = [];
        if (basketStore.buyNowShopId) {
            shopIds = [basketStore.buyNowShopId];
        }

        // Place the order
        const formData = new FormData();
        
        // Add required fields to formData
        formData.append('address_id', addressId);
        formData.append('payment_method', props.paymentMethod);
        formData.append('note', props.note || '');
        formData.append('coupon_code', basketStore.coupon_code || '');
        formData.append('is_buy_now', '1'); // Use numeric value instead of string 'true'
        formData.append('delivery_method', isGLSSelected.value ? 'gls' : 'standard');

  
        // Add shop IDs
         formData.append('shop_ids[]', shopIds);
        
        // Set payment type correctly for card payments
        if (props.paymentMethod === 'card') {
            formData.append('payment_type', 'card');
        } else {
            formData.append('payment_type', props.paymentMethod === 'BANK' ? 'bank' : 'cash');
        }
        
        // Add delivery charge and payable amount to ensure they're stored correctly
        formData.append('delivery_charge', orderData.value.delivery_charge);
        formData.append('payable_amount', orderData.value.payable_amount);

        // Add payment proof if available
        if (uploadedProof && uploadedProof.value) {
            formData.append('payment_proof', uploadedProof.value);
        }

        const response = await axios.post('/place-order', formData, {
            headers: {
                'Authorization': authStore.token,
                'Content-Type': 'multipart/form-data'
            }
        });
        
        console.log('Order response:', response.data);

        // Handle successful order placement
        if (response.data.data && response.data.data.order_payment_url) {
            console.log('Order payment URL response:', response.data.data.order_payment_url);
            
            // Check the structure of the response
            if (response.data.data.order_payment_url.original && 
                response.data.data.order_payment_url.original.data) {
                
                // Access the correct path to the payment data
                const orderPaymentData = response.data.data.order_payment_url.original.data;
                console.log('Order Payment Data:', orderPaymentData);
                
                if (orderPaymentData.order_payment_url) {
                    sessionId.value = orderPaymentData.session_id;
                    orderId.value = orderPaymentData.orderId;
                    
                    // Store order ID in localStorage for recovery
                    localStorage.setItem('pendingPaymentOrderId', orderPaymentData.orderId);
                    
                    // Set payment URL
                    paymentUrl.value = orderPaymentData.order_payment_url;
                    
                    // Show payment processing modal
                    showPaymentProcessingModal.value = true;
                    
                    // Open payment in a new window
                    paymentWindow.value = window.open(orderPaymentData.order_payment_url, 'stripe_checkout', 'width=600,height=600');
                    
                    // Check if window was blocked
                    if (!paymentWindow.value || paymentWindow.value.closed) {
                        toast.error('Payment window was blocked by your browser. Please allow popups for this site.');
                        showPaymentProcessingModal.value = false;
                        return;
                    }
                    
                    // Start checking payment status
                    startPaymentStatusCheck(orderPaymentData.orderId);
                }
            } else if (typeof response.data.data.order_payment_url === 'string') {
                // Direct URL string
                const paymentUrlString = response.data.data.order_payment_url;
                paymentUrl.value = paymentUrlString;
                
                // Show payment processing modal
                showPaymentProcessingModal.value = true;
                
                // Open payment in a new window
                paymentWindow.value = window.open(paymentUrlString, 'stripe_checkout', 'width=600,height=600');
                
                // Check if window was blocked
                if (!paymentWindow.value || paymentWindow.value.closed) {
                    toast.error('Payment window was blocked by your browser. Please allow popups for this site.');
                    showPaymentProcessingModal.value = false;
                    return;
                }
                
                // Start checking payment status
                if (response.data.data.order_id) {
                    orderId.value = response.data.data.order_id;
                    startPaymentStatusCheck(response.data.data.order_id);
                }
            } else {
                console.error('Unexpected response structure:', response.data.data.order_payment_url);
                toast.error('Error processing payment. Please try again or contact support.');
            }
        } else if (response.data.order_payment_url) {
            // Handle direct response structure
            console.log('Direct order payment URL:', response.data.order_payment_url);
            
            if (typeof response.data.order_payment_url === 'string') {
                const paymentUrlString = response.data.order_payment_url;
                paymentUrl.value = paymentUrlString;
                
                // Show payment processing modal
                showPaymentProcessingModal.value = true;
                
                // Open payment in a new window
                paymentWindow.value = window.open(paymentUrlString, 'stripe_checkout', 'width=600,height=600');
                
                // Check if window was blocked
                if (!paymentWindow.value || paymentWindow.value.closed) {
                    toast.error('Payment window was blocked by your browser. Please allow popups for this site.');
                    showPaymentProcessingModal.value = false;
                    return;
                }
                
                // Start checking payment status
                if (response.data.order_id) {
                    orderId.value = response.data.order_id;
                    startPaymentStatusCheck(response.data.order_id);
                }
            } else if (response.data.order_payment_url.original && 
                       response.data.order_payment_url.original.data) {
                const orderPaymentData = response.data.order_payment_url.original.data;
                
                if (orderPaymentData.order_payment_url) {
                    sessionId.value = orderPaymentData.session_id;
                    orderId.value = orderPaymentData.orderId;
                    
                    // Store order ID in localStorage for recovery
                    localStorage.setItem('pendingPaymentOrderId', orderPaymentData.orderId);
                    
                    // Set payment URL
                    paymentUrl.value = orderPaymentData.order_payment_url;
                    
                    // Show payment processing modal
                    showPaymentProcessingModal.value = true;
                    
                    // Open payment in a new window
                    paymentWindow.value = window.open(orderPaymentData.order_payment_url, 'stripe_checkout', 'width=600,height=600');
                    
                    // Check if window was blocked
                    if (!paymentWindow.value || paymentWindow.value.closed) {
                        toast.error('Payment window was blocked by your browser. Please allow popups for this site.');
                        showPaymentProcessingModal.value = false;
                        return;
                    }
                    
                    // Start checking payment status
                    startPaymentStatusCheck(orderPaymentData.orderId);
                }
            }
        } else {
            // For cash on delivery or other non-online payments
            // Store the order ID if available
            if (response.data.data && response.data.data.order_id) {
                orderId.value = response.data.data.order_id;
                localStorage.setItem('lastOrderId', response.data.data.order_id);
            }
            
            // Show the payment processing modal with success state for non-card payments
            showPaymentProcessingModal.value = true;
            paymentProcessingState.value = 'success';
            
            // No need to redirect - we're showing the result in the same window
            toast.success('Order placed successfully!', {
                position: "bottom-left",
            });
        }

    } catch (error) {
        console.error('Order placement failed:', error.response?.data);
        const errorMessage = error.response?.data?.errors 
            ? Object.values(error.response.data.errors)[0][0] 
            : error.response?.data?.message || 'Order placement failed';
        toast.error(errorMessage, {
            position: "bottom-left",
        });
    }
};

// Cancel the payment process
const cancelPaymentProcess = async () => {
    // Close the payment window if it's open
    if (paymentWindow.value && !paymentWindow.value.closed) {
        paymentWindow.value.close();
    }
    
    // Hide the processing modal
    showPaymentProcessingModal.value = false;
    
    // Reset payment processing state
    paymentProcessingState.value = 'processing';
    
    // Clear redirect timer if it exists
    if (redirectTimer.value) {
        clearInterval(redirectTimer.value);
    }
    
    // Mark payment as abandoned if we have an order ID
    if (orderId.value) {
        try {
            await axios.post('/abandoned-payment', { order_id: orderId.value });
            toast.info('Payment has been cancelled');
        } catch (error) {
            console.error('Error cancelling payment:', error);
        }
    }
    
    // Reset payment state
    paymentStarted.value = false;
    paymentInProgress.value = false;
};

// Reopen the payment window
const reopenPaymentWindow = () => {
    if (paymentUrl.value) {
        // Close existing window if it's still open
        if (paymentWindow.value && !paymentWindow.value.closed) {
            paymentWindow.value.close();
        }
        
        // Open a new window with the payment URL
        paymentWindow.value = window.open(paymentUrl.value, 'stripe_checkout', 'width=600,height=600');
        
        // Check if window was blocked
        if (!paymentWindow.value || paymentWindow.value.closed) {
            toast.error('Payment window was blocked by your browser. Please allow popups for this site.');
        }
    }
};

// Check payment status periodically
const startPaymentStatusCheck = (orderIdToCheck) => {
    const checkInterval = setInterval(async () => {
        try {
            // Check if payment window is still open
            if (paymentWindow.value && paymentWindow.value.closed) {
                // Window was closed, check payment status one final time
                await checkPaymentStatus(orderIdToCheck);
                clearInterval(checkInterval);
                
                // Don't hide the modal here, as we might want to show failure state
                // showPaymentProcessingModal.value = false;
            }
            
            // Check payment status
            const response = await axios.get(`/check-payment-status/${orderIdToCheck}`);
            console.log('Payment status response (Buy Now):', response.data);
            
            // Parse the response based on the new structure
            const paymentStatus = response.data.data?.status?.toLowerCase();
            const orderStatus = response.data.data?.order_status?.toLowerCase();
            
            if (paymentStatus === 'paid' || orderStatus === 'confirm') {
                // Payment successful, clear interval and redirect
                clearInterval(checkInterval);
                showPaymentProcessingModal.value = false;
                
                // Close payment window if it's still open
                if (paymentWindow.value && !paymentWindow.value.closed) {
                    paymentWindow.value.close();
                }
                
                // Store the order details for reference
                const orderDetails = response.data.data?.order_details;
                if (orderDetails) {
                    localStorage.setItem('lastOrderDetails', JSON.stringify(orderDetails));
                }
                
                // Redirect to success page
                router.push({ name: 'order-success', query: { order_id: orderIdToCheck } });
            } else if (paymentStatus === 'failed' || paymentStatus === 'abandoned' || orderStatus === 'cancelled') {
                // Payment failed, clear interval and show error in the same modal
                clearInterval(checkInterval);
                
                // Close payment window if it's still open
                if (paymentWindow.value && !paymentWindow.value.closed) {
                    paymentWindow.value.close();
                }
                
                // Update modal to show failure state
                paymentProcessingState.value = 'failed';
                paymentFailureMessage.value = `Payment for Order #${orderIdToCheck} was not completed. You can try again or go to the home page.`;
                
                // Start countdown for redirection
                startRedirectCountdown();
                
                // Store order ID for retry
                orderId.value = orderIdToCheck;
                paymentFailed.value = true;
                
                // Reset payment method selection
                props.paymentMethod = null;
            }
        } catch (error) {
            console.error('Error checking payment status:', error);
        }
    }, 3000); // Check every 3 seconds
    
    // Store the interval ID to clear it when component is unmounted
    paymentStatusCheckInterval.value = checkInterval;
};

// Watch for navigation events
const handleVisibilityChange = () => {
    if (document.visibilityState === 'visible' && paymentInProgress.value) {
        // User returned to the page after starting payment
        paymentInProgress.value = false;
        
        if (orderId.value) {
            // Check payment status
            checkPaymentStatus(orderId.value);
        }
    }
};

// Handle payment status modal actions
const handleContinuePayment = async () => {
    if (paymentFailed.value) {
        // If payment failed, close modal and let user select payment method again
        showPaymentStatusModal.value = false;
        paymentFailed.value = false;
        
        // Show message to select payment method
        toast.info('Please select a payment method to continue');
        
    } else if (pendingPaymentDetails.value) {
        // For pending payments, refresh the session and open in popup
        try {
            const response = await axios.post('/refresh-payment-session', {
                order_id: pendingPaymentDetails.value.orderId
            });
            
            if (response.data.status === 'success') {
                sessionId.value = response.data.sessionId;
                orderId.value = pendingPaymentDetails.value.orderId;
                
                // Get payment URL
                const paymentUrlFromResponse = response.data.payment_url || `https://checkout.stripe.com/pay/${response.data.sessionId}`;
                paymentUrl.value = paymentUrlFromResponse;
                
                // Show payment processing modal
                showPaymentProcessingModal.value = true;
                
                // Open payment in a new window
                paymentWindow.value = window.open(paymentUrlFromResponse, 'stripe_checkout', 'width=600,height=600');
                
                // Check if window was blocked
                if (!paymentWindow.value || paymentWindow.value.closed) {
                    toast.error('Payment window was blocked by your browser. Please allow popups for this site.');
                    showPaymentProcessingModal.value = false;
                    return;
                }
                
                // Start checking payment status
                startPaymentStatusCheck(pendingPaymentDetails.value.orderId);
            } else {
                throw new Error(response.data.message || 'Failed to refresh payment session');
            }
        } catch (error) {
            console.error('Error refreshing payment session:', error);
            toast.error('Could not continue payment. Please try again.');
        } finally {
            showPaymentStatusModal.value = false;
        }
    }
};

const handleCancelPayment = () => {
    showPaymentStatusModal.value = false;
    hasPendingPayment.value = false;
    pendingPaymentDetails.value = null;
    
    // Reset payment method selection
    props.paymentMethod = 'CASH';
    // paymentType.value = 'cash';
};

// Check payment status for a given order
const checkPaymentStatus = async (orderIdToCheck) => {
    try {
        const response = await axios.get(`/check-payment-status/${orderIdToCheck}`);
        
        // Store the order ID
        orderId.value = orderIdToCheck;
        
        if (response.data.status === 'paid') {
            // Payment was successful, redirect to success page
            router.push({ name: 'order-success', query: { order_id: orderIdToCheck } });
        } else if (response.data.status === 'pending') {
            // Payment still pending, show modal asking what to do
            showPaymentStatusModal.value = true;
            paymentModalStatus.value = 'pending';
            paymentModalTitle.value = `Payment Pending for Order #${orderIdToCheck}`;
            paymentModalMessage.value = 'Your payment is still being processed. Would you like to continue with the payment or try a different method?';
            paymentModalShowContinue.value = true;
            paymentModalShowCancel.value = true;
            
            // Store pending payment details
            hasPendingPayment.value = true;
            pendingPaymentDetails.value = {
                orderId: orderIdToCheck,
                stripeKey: response.data.stripe_key
            };
            
            // Also show a toast notification
            toast.info(`Payment for Order #${orderIdToCheck} is still pending. You can continue the payment or try a different method.`, {
                position: "bottom-left",
                timeout: 8000,
                closeButton: true
            });
        } else if (response.data.status === 'failed' || response.data.status === 'abandoned') {
            // Payment failed, show modal with retry option
            showPaymentStatusModal.value = true;
            paymentModalStatus.value = 'error';
            paymentModalTitle.value = `Payment Failed for Order #${orderIdToCheck}`;
            paymentModalMessage.value = 'Your payment was not completed. You can try again with the same or a different payment method.';
            paymentModalShowContinue.value = true;
            paymentModalShowCancel.value = false;
            
            // Store order ID for retry
            orderId.value = orderIdToCheck;
            paymentFailed.value = true;
            
            // Reset payment method selection to allow user to choose again
            props.paymentMethod = null;
            
            // Also show a toast notification
            toast.error(`Payment for Order #${orderIdToCheck} failed. You can try again with the same or a different payment method.`, {
                position: "bottom-left",
                timeout: 8000,
                closeButton: true
            });
        }
    } catch (error) {
        console.error('Error checking payment status:', error);
        toast.error(`Error checking payment status for Order #${orderIdToCheck}`, {
            position: "bottom-left"
        });
    }
};

// Resume a pending payment
const resumePayment = async () => {
    if (!pendingPaymentDetails.value) {
        return;
    }
    
    try {
        // Get a fresh session ID
        const response = await axios.post('/refresh-payment-session', {
            order_id: pendingPaymentDetails.value.orderId
        }, {
            headers: {
                'Authorization': authStore.token
            }
        });
        
        if (response.data.status === 'success') {
            // Mark that payment has started
            paymentStarted.value = true;
            paymentInProgress.value = true;
            
            // Store order ID
            orderId.value = pendingPaymentDetails.value.orderId;
            
            // Get payment URL
            const paymentUrlFromResponse = response.data.payment_url || `https://checkout.stripe.com/pay/${response.data.sessionId}`;
            paymentUrl.value = paymentUrlFromResponse;
            
            // Show payment processing modal
            showPaymentProcessingModal.value = true;
            
            // Open payment in a new window
            paymentWindow.value = window.open(paymentUrlFromResponse, 'stripe_checkout', 'width=600,height=600');
            
            // Check if window was blocked
            if (!paymentWindow.value || paymentWindow.value.closed) {
                toast.error('Payment window was blocked by your browser. Please allow popups for this site.');
                showPaymentProcessingModal.value = false;
                return;
            }
            
            // Start checking payment status
            startPaymentStatusCheck(pendingPaymentDetails.value.orderId);
        } else {
            toast.error('Could not create payment session. Please try again.', {
                position: "bottom-left"
            });
        }
    } catch (error) {
        console.error('Error resuming payment:', error);
        toast.error('Error resuming payment. Please try again.', {
            position: "bottom-left"
        });
        paymentInProgress.value = false;
    }
};

// Go to home page
const goToHomePage = () => {
    if (redirectTimer.value) {
        clearInterval(redirectTimer.value);
    }
    router.push({ name: 'home' });
};

// Retry payment
const retryPayment = () => {
    // Reset payment processing state
    paymentProcessingState.value = 'processing';
    
    // Clear redirect timer if it exists
    if (redirectTimer.value) {
        clearInterval(redirectTimer.value);
    }
    
    // Reopen payment window
    reopenPaymentWindow();
};

// Handle continue shopping action
const handleContinueShopping = () => {
    // Clear buy now data
    basketStore.buyNowProduct = null;
    basketStore.buyNowShopId = null;
    
    // Close the modal
    showPaymentProcessingModal.value = false;
    
    // Redirect to home page
    router.push({ name: 'home' });
};

// View order details
const viewOrder = () => {
    // Close the modal
    showPaymentProcessingModal.value = false;
    
    // Redirect to order history page
    router.push({ name: 'order-history' });
};

// Start countdown for redirection
const startRedirectCountdown = () => {
    // Clear any existing timer
    if (redirectTimer.value) {
        clearInterval(redirectTimer.value);
    }
    
    // Reset countdown
    redirectCountdown.value = 10;
    
    // Start countdown
    redirectTimer.value = setInterval(() => {
        redirectCountdown.value--;
        
        if (redirectCountdown.value <= 0) {
            clearInterval(redirectTimer.value);
            goToHomePage();
        }
    }, 1000);
};

</script>

<style scoped>
.formInputCoupon {
    @apply rounded-lg border border-slate-200 focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}

/* Add styles for disabled state */
.delivery-option-disabled {
    @apply opacity-50 cursor-not-allowed;
}
</style>
