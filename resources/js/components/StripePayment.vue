<template>
  <div>
    <form @submit.prevent="handleSubmit">
      <div id="card-element"></div>
      <button type="submit">Pay</button>
    </form>
  </div>
</template>

<script>
import { loadStripe } from '@stripe/stripe-js';
import axios from 'axios';

export default {
  data() {
    return {
      stripe: null,
      cardElement: null,
      clientSecret: '',
    };
  },
  async mounted() {
    this.stripe = await loadStripe(process.env.VUE_APP_STRIPE_KEY);
    const elements = this.stripe.elements();
    this.cardElement = elements.create('card');
    this.cardElement.mount('#card-element');

    // Fetch the client secret from the server
    const response = await axios.post('/order/payment', { order_id: this.$route.params.orderId });
    this.clientSecret = response.data.clientSecret;
  },
  methods: {
    async handleSubmit() {
      const { error, paymentIntent } = await this.stripe.confirmCardPayment(this.clientSecret, {
        payment_method: {
          card: this.cardElement,
        },
      });

      if (error) {
        console.error('Payment failed:', error);
      } else if (paymentIntent.status === 'succeeded') {
        // Confirm payment on the server
        await axios.post('/order/confirm-payment', {
          order_id: this.$route.params.orderId,
          payment_intent_id: paymentIntent.id,
        });
        this.$router.push({ name: 'order-success' });
      }
      else {
        console.error('Payment failed:', error);
        await axios.post('/order/abandoned-payment', {
          order_id: this.$route.params.orderId,
        });
       
      }
    },
  },
};
</script>