import { defineStore } from 'pinia';
import { ref } from 'vue';

export const usePaymentStore = defineStore('payment', () => {
  const paymentType = ref('cash');
  const paymentMethod = ref(null);
  const proofUploaded = ref(false);
  const uploadedProof = ref(null);

  return { paymentType, paymentMethod, proofUploaded, uploadedProof };
});