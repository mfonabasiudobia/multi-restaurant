<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
      </div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                 :class="statusIconClass">
              <component :is="statusIcon" class="h-6 w-6 text-white" aria-hidden="true" />
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                {{ title }}
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  {{ message }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button v-if="showContinueButton" type="button" 
                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm"
                  @click="onContinue">
            Continue Payment
          </button>
          <button v-if="showCancelButton" type="button" 
                  class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                  @click="onCancel">
            Cancel Payment
          </button>
          <button v-if="!showContinueButton && !showCancelButton" type="button" 
                  class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                  @click="onClose">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
// Fix for Heroicons v2
import { ExclamationTriangleIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  status: {
    type: String,
    default: 'pending', // pending, success, error, warning
    validator: (value) => ['pending', 'success', 'error', 'warning'].includes(value)
  },
  title: {
    type: String,
    default: 'Payment Status'
  },
  message: {
    type: String,
    default: ''
  },
  showContinueButton: {
    type: Boolean,
    default: false
  },
  showCancelButton: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['continue', 'cancel', 'close']);

const statusIcon = computed(() => {
  switch (props.status) {
    case 'success':
      return CheckCircleIcon;
    case 'error':
      return XCircleIcon;
    case 'warning':
    case 'pending':
    default:
      return ExclamationTriangleIcon;
  }
});

const statusIconClass = computed(() => {
  switch (props.status) {
    case 'success':
      return 'bg-green-100';
    case 'error':
      return 'bg-red-100';
    case 'warning':
      return 'bg-yellow-100';
    case 'pending':
    default:
      return 'bg-blue-100';
  }
});

const onContinue = () => {
  emit('continue');
};

const onCancel = () => {
  emit('cancel');
};

const onClose = () => {
  emit('close');
};
</script> 