<template>
    <TransitionRoot as="template" :show="showModal">
        <Dialog as="div" class="relative z-50" @close="closeModal">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <DialogPanel
                        class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">
                                        {{ $t('Product Package') }}
                                    </DialogTitle>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            {{ $t('You have reached your product limit. Please purchase a new package to continue.') }}
                                        </p>
                                        <div class="mt-4">
                                            <p class="font-semibold">{{ $t('Package Details:') }}</p>
                                            <ul class="list-disc pl-5 mt-2">
                                                <li>{{ $t('Product Limit:') }} {{ packageData.product_limit }}</li>
                                                <li>{{ $t('Price:') }} {{ formatCurrency(packageData.package_price) }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="button" class="btn btn-primary" @click="handlePayment">
                                {{ $t('Pay Now') }}
                            </button>
                            <button type="button" class="btn btn-secondary mr-2" @click="closeModal">
                                {{ $t('Close') }}
                            </button>
                        </div>
                    </DialogPanel>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import axios from 'axios'

const showModal = ref(false)
const packageData = ref({
    product_limit: 50,
    package_price: 250.00
})

const emit = defineEmits(['close'])

const closeModal = () => {
    showModal.value = false
    emit('close')
}

const handlePayment = async () => {
    try {
        const response = await axios.post('/api/shop/package/payment')
        if (response.data.success) {
            showModal.value = false
            // Refresh package info
            window.location.reload()
        }
    } catch (error) {
        console.error('Payment failed:', error)
    }
}

onMounted(() => {
    // Check if we need to show the modal
    checkPackageStatus()
})

const checkPackageStatus = async () => {
    try {
        const response = await axios.get('/api/shop/package/status')
        if (response.data.show_modal) {
            showModal.value = true
        }
    } catch (error) {
        console.error('Failed to check package status:', error)
    }
}
</script> 