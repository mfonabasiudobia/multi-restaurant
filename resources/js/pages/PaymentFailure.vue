<template>
  <div class="container mx-auto py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8 text-center">
      <div class="mb-6">
        <XCircleIcon class="h-16 w-16 text-red-500 mx-auto" />
      </div>
      <h1 class="text-2xl font-bold text-gray-800 mb-4">Payment Failed</h1>
      <p class="text-gray-600 mb-6">
        We couldn't process your payment. Please try again or use a different payment method.
      </p>
      <div class="flex justify-center space-x-4">
        <router-link
          :to="{ name: 'checkout' }"
          class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition"
        >
          Try Again
        </router-link>
        <router-link
          :to="{ name: 'home' }"
          class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition"
        >
          Return to Home
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { XCircleIcon } from '@heroicons/vue/24/solid';
import { useToast } from 'vue-toastification';
import { onMounted } from 'vue';

const toast = useToast();

onMounted(() => {
  toast.error('Payment failed. Please try again.');
});
</script> 