<template>
    <div class="shop-layout overflow-x-hidden w-full relative">
        <!-- Existing layout content -->
        <PackagePaymentModal v-if="showPackageModal" @close="showPackageModal = false" />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import PackagePaymentModal from '../components/PackagePaymentModal.vue'
import axios from 'axios'

const showPackageModal = ref(false)

// Add this to your router navigation guards or main layout component
const checkPackageStatus = async () => {
    try {
        const response = await axios.get('/api/shop/package/status')
        showPackageModal.value = response.data.show_modal
    } catch (error) {
        console.error('Failed to check package status:', error)
    }
}

// Check on mount and when accessing products page
onMounted(() => {
    checkPackageStatus()
})
</script> 