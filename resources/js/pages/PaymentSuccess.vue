<template>
  <div class="container mx-auto py-12 px-4">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8 text-center">
      <div class="mb-6">
        <CheckCircleIcon class="h-16 w-16 text-green-500 mx-auto" />
      </div>
      <h1 class="text-2xl font-bold text-gray-800 mb-4">Payment Successful!</h1>
      <p class="text-gray-600 mb-6">
        Your payment has been processed successfully. Your order is now confirmed.
      </p>
      <div class="flex justify-center space-x-4">
        <router-link
          :to="{ name: 'order-history' }"
          class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition"
        >
          View Orders
        </router-link>
        <router-link
          :to="{ name: 'home' }"
          class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition"
        >
          Continue Shopping
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { CheckCircleIcon } from '@heroicons/vue/24/solid';
import { onMounted, onBeforeUnmount } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { useToast } from 'vue-toastification';
import { useBasketStore } from '../stores/BasketStore';

const route = useRoute();
const router = useRouter();
const toast = useToast();
const basketStore = useBasketStore();

// Handle browser back button
const handleBeforeUnload = (event) => {
  // This will show a confirmation dialog when the user tries to navigate away
  event.preventDefault();
  event.returnValue = '';
};

onMounted(async () => {
  // Add event listener for page unload
  window.addEventListener('beforeunload', handleBeforeUnload);
  
  // Get the session ID or order ID from the URL
  const sessionId = route.query.session_id;
  const orderId = route.query.order_id;
  
  if (sessionId) {
    try {
      // Verify the payment on the server using session ID
      await axios.post('/verify-payment', { session_id: sessionId });
      
      // Clear cart
      basketStore.fetchCart();
      
      toast.success('Payment completed successfully!');
    } catch (error) {
      console.error('Error verifying payment:', error);
      
      // Show more specific error message if available
      const errorMessage = error.response?.data?.message || 'Could not verify payment. Please contact support.';
      toast.error(errorMessage);
      
      // Log additional details for debugging
      if (error.response) {
        console.error('Error response:', error.response.data);
      }
    }
  } else if (orderId) {
    // If we have order_id but no session_id, the payment might have been verified by webhook
    // Just show success message
    toast.success('Payment completed successfully!');
    
    // Clear cart
    basketStore.fetchCart();
  }
});

onBeforeUnmount(() => {
  // Remove event listener
  window.removeEventListener('beforeunload', handleBeforeUnload);
});
</script> 