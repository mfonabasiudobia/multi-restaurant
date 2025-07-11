<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-lg w-full max-w-md p-6 flex flex-col items-center">
      <!-- Close button -->
      <button 
        @click="$emit('close')" 
        class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
        v-if="showCloseButton"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Processing State -->
      <template v-if="state === 'processing'">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ $t('Processing Payment') }}</h3>
        
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
        
        <p class="text-center mb-6">{{ $t('Your payment is being processed. Please do not close this window.') }}</p>
        
        <!-- Card Payment - External Window Info -->
        <template v-if="paymentMethod === 'card'">
          <p class="text-center text-sm text-gray-500 mb-4">
            {{ $t('A new window has opened to complete your payment. If you don\'t see it, please check if it was blocked by your browser.') }}
          </p>
          
          <div class="flex gap-4">
            <button 
              @click="$emit('reopen-window')" 
              class="px-4 py-2 bg-primary text-white rounded-lg"
            >
              {{ $t('Reopen Payment Window') }}
            </button>
            
            <button 
              @click="$emit('cancel')" 
              class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg"
            >
              {{ $t('Cancel') }}
            </button>
          </div>
        </template>

        <!-- Cash Payment -->
        <template v-else-if="paymentMethod === 'COD'">
          <div class="text-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <p class="text-center mb-4">{{ $t('Your order has been placed successfully!') }}</p>
          <p class="text-center text-sm text-gray-500 mb-6">{{ $t('You will pay when your order is delivered.') }}</p>
          <button 
            @click="$emit('continue')" 
            class="px-4 py-2 bg-primary text-white rounded-lg"
          >
            {{ $t('Continue Shopping') }}
          </button>
        </template>

        <!-- Bank Transfer -->
        <template v-else-if="paymentMethod === 'BANK'">
          <div class="text-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
          </div>
          <p class="text-center mb-4">{{ $t('Your order has been placed successfully!') }}</p>
          <p class="text-center text-sm text-gray-500 mb-6">{{ $t('Please complete the bank transfer using the details provided.') }}</p>
          <button 
            @click="$emit('continue')" 
            class="px-4 py-2 bg-primary text-white rounded-lg"
          >
            {{ $t('Continue Shopping') }}
          </button>
        </template>
      </template>
      
      <!-- Success State -->
      <template v-else-if="state === 'success'">
        <div class="text-green-500 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        
        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $t('Order Placed Successfully') }}</h3>
        
        <!-- Different messages based on payment method -->
        <template v-if="paymentMethod === 'card'">
          <p class="text-center mb-6">{{ message || $t('Your payment has been processed successfully.') }}</p>
        </template>
        
        <template v-else-if="paymentMethod === 'COD'">
          <p class="text-center mb-6">{{ $t('Your order has been placed. You will pay when your order is delivered. Please wait for order confirmation email.') }}</p>
        </template>
        
        <template v-else-if="paymentMethod === 'BANK'">
          <p class="text-center mb-6">{{ $t('Your order has been placed. Please wait for payment confirmation email.') }}</p>
        </template>
        
        <template v-else>
          <p class="text-center mb-6">{{ message || $t('Your order has been placed successfully.') }}</p>
        </template>
        
        <div class="flex gap-4">
          <button 
            @click="$emit('continue')" 
            class="px-4 py-2 bg-primary text-white rounded-lg"
          >
            {{ $t('Continue Shopping') }}
          </button>
          
          <button 
            @click="$emit('view-order')" 
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg"
          >
            {{ $t('View Order') }}
          </button>
        </div>
      </template>
      
      <!-- Failed State -->
      <template v-else-if="state === 'failed'">
        <div class="text-red-500 mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
        </div>
        
        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $t('Payment Failed') }}</h3>
        
        <p class="text-center mb-2">{{ message || $t('Your payment could not be processed.') }}</p>
        
        <p v-if="showCountdown" class="text-center text-sm text-gray-500 mb-6">
          {{ $t('You will be redirected to the home page in') }} {{ countdown }} {{ $t('seconds') }}...
        </p>
        
        <div class="flex gap-4">
          <button 
            @click="$emit('retry')" 
            class="px-4 py-2 bg-primary text-white rounded-lg"
          >
            {{ $t('Try Again') }}
          </button>
          
          <button 
            @click="$emit('cancel')" 
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg"
          >
            {{ $t('Go to Home Page') }}
          </button>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  state: {
    type: String,
    default: 'processing', // processing, success, failed
    validator: (value) => ['processing', 'success', 'failed'].includes(value)
  },
  paymentMethod: {
    type: String,
    default: 'card'
  },
  message: {
    type: String,
    default: ''
  },
  countdown: {
    type: Number,
    default: 10
  },
  showCountdown: {
    type: Boolean,
    default: true
  },
  showCloseButton: {
    type: Boolean,
    default: false
  }
});

defineEmits([
  'close', 
  'cancel', 
  'continue', 
  'retry', 
  'view-order', 
  'reopen-window'
]);
</script>
