<template>
    <div id="payment-methods-section" class="p-6 mt-4 bg-white rounded-2xl border border-slate-200">
        <div class="text-slate-950 text-lg font-medium leading-6">
            {{ $t('Payment Method') }}
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div 
                v-for="option in paymentOptions" 
                :key="option.value" 
                @click="selectPaymentMethod(option.value)" 
                :class="['p-4 rounded-xl cursor-pointer flex items-center gap-4 transition-all duration-200 hover:border-primary', 
                         paymentMethod.toLowerCase() === option.value.toLowerCase() ? 'shadow-lg bg-primary bg-opacity-5' : 'border border-slate-200']"
            >
                <img :src="option.icon" alt="" class="w-7 h-7">
                <span class="text-slate-700 text-sm font-medium leading-normal">{{ $t(option.label) }}</span>
            </div>
        </div>

        <!-- Bank Transfer Details -->
        <Transition leave-active-class="transition ease-in duration-300"
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <div v-if="paymentMethod.toLowerCase() === 'bank'" class="mt-5 border-t border-slate-200 pt-4">
                <div class="text-slate-600 pt-2 block text-sm font-medium leading-6">
                    {{ $t('Bank Details') }}
                </div>
                <div v-if="bankDetails?.data" class="mt-3 p-4 bg-slate-100 rounded-lg">
                    <div class="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-2 lg:gap-4">
                        <div class="flex items-center gap-4">
                            <div>
                                <div class="text-slate-700 text-sm font-medium">{{ $t('Company Name') }}</div>
                                <div class="text-slate-500 text-xs">{{ bankDetails.data.company_name }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div>
                                <div class="text-slate-700 text-sm font-medium">{{ $t('IBAN') }}</div>
                                <div class="text-slate-500 text-xs">{{ bankDetails.data.iban }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div>
                                <div class="text-slate-700 text-sm font-medium">{{ $t('SWIFT/BIC') }}</div>
                                <div class="text-slate-500 text-xs">{{ bankDetails.data.swift_bic }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div>
                                <div class="text-slate-700 text-sm font-medium">{{ $t('Bank Name') }}</div>
                                <div class="text-slate-500 text-xs">{{ bankDetails.data.bank_name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="text-slate-700 text-sm font-medium">
                        {{ $t('Please upload the payment proof (confirmation of payment)') }}
                    </div>
                    <input type="file" class="form-input mt-2" @change="handleFileUpload" />
                </div>
            </div>
        </Transition>

        <!-- Payment Gateways -->
        <Transition leave-active-class="transition ease-in duration-300"
            enter-active-class="transition ease-out duration-300"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
            <div v-if="paymentMethod.toLowerCase() === 'card'" class="mt-5 border-t border-slate-200">
                <span class="text-slate-600 pt-2 block text-sm font-medium leading-6">
                    {{ $t('Available Payment Gateways') }}
                </span>
                <div class="mt-3 flex flex-wrap gap-4">
                    <label v-for="gateway in master.paymentGateways" :key="gateway.id" :for="gateway.name"
                        class="flex items-center gap-4 border relative has-[:checked]:border-primary has-[:checked]:shadow-lg p-2 rounded-md border-slate-200 cursor-pointer hover:border-primary transition-all duration-200">
                        <input v-model="paymentGateway" :id="gateway.name" name="paymentGateway" type="radio"
                            class="sr-only" :value="gateway.name" />
                        <div class="">
                            <img :src="gateway.logo" alt="" class="w-32 h-16 object-contain">
                        </div>
                    </label>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, inject } from 'vue';
import { useAuth } from '../stores/AuthStore';
import { useBasketStore } from '../stores/BasketStore';
import { useMaster } from '../stores/MasterStore';
import { useRouter } from 'vue-router';

const AuthStore = useAuth();
const basketStore = useBasketStore();
const master = useMaster();
const router = useRouter();

// Injected values
const paymentType = inject('paymentType');
const paymentMethod = inject('paymentMethod');
const proofUploaded = inject('proofUploaded');
const uploadedProof = inject('uploadedProof');

// Payment gateway selection
const paymentGateway = ref('stripe'); // Default to Stripe

// File upload handling
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Check file type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            console.error('Invalid file type');
            return;
        }
        
        // Check file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            console.error('File too large');
            return;
        }
        
        proofUploaded.value = true;
        uploadedProof.value = file;
    }
};

// Payment options - normalize all values to lowercase
const paymentOptions = [
    { value: 'cash', label: 'Cash on delivery', icon: 'assets/icons/money-2.svg' },
    { value: 'card', label: 'Credit or Debit Card', icon: 'assets/icons/card.svg' },
    { value: 'bank', label: 'Bank Transfer', icon: 'assets/icons/bank-icon.png' }
];

// Bank details
const bankDetails = ref(null);

// Select payment method
const selectPaymentMethod = (method) => {
    // Normalize to lowercase
    const normalizedMethod = method.toLowerCase();

    paymentMethod.value = normalizedMethod;


   console.log('Selected payment method:', paymentMethod);
    
    // Set the correct payment type based on the method
    if (normalizedMethod === 'card') {
        paymentType.value = 'card';
        paymentGateway.value = 'stripe';
    } else if (normalizedMethod === 'bank') {
        paymentType.value = 'bank';
        fetchBankDetails(); // Fetch bank details immediately when bank is selected
    } else {
        paymentType.value = 'cash';
    }
console.log('after Selected payment method:', paymentMethod);
    
    
    // Emit event for other components
    const checkoutOrderSummary = document.querySelector('#checkout-order-summary');
    if (checkoutOrderSummary) {
        const event = new CustomEvent('payment-method-selected', { detail: { method: normalizedMethod } });
        checkoutOrderSummary.dispatchEvent(event);
    }
};
console.log(bankDetails.value);

// Fetch bank details
const fetchBankDetails = async () => {
    try {
        const response = await axios.get('/bank-details', {
            headers: {
                Authorization: AuthStore.token,
            },
        });
        
        if (response.data.status === 'success') {
            bankDetails.value = response.data;
        } else {
            throw new Error(response.data.message);
        }
    } catch (err) {
        console.error('Error fetching bank details:', err);
    }
};

// Watch for changes in payment type
watch(paymentMethod, (newValue) => {
    console.log('Payment method changed:', paymentMethod);
  
    if (newValue === null) {
        newValue = 'card';
    }
    if (paymentMethod.value === null) {

       paymentMethod.value = newValue;
    }
    if (newValue?.toLowerCase() === 'bank' && !bankDetails.value) {
        fetchBankDetails();
    }
});

// Component initialization
onMounted(() => {
         console.log('Selected payment paymentMethod:', paymentMethod);
    // Set default payment method if not already set
    if (!paymentMethod.value) {
        paymentMethod.value = 'cash';
        paymentType.value = 'cash';
    }
    
    // Redirect if not authenticated
    if (!AuthStore.user) {
        router.push({ name: 'home' });
    }
});
</script>

<style scoped>
.form-input {
    @apply p-3 rounded-lg border border-slate-200 focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}

/* Add hover effect for payment options */
.payment-option:hover {
    @apply border-primary;
    transform: translateY(-2px);
}
</style>
