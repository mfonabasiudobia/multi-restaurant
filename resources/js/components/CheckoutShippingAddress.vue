<template>
    <div>
        <div class="mt-6 sm:mt-8 flex justify-between items-center gap-2">
            <div class="text-slate-950 text-xl font-medium leading-7">
                {{ $t('Shipping Address') }}
            </div>

            <button v-if="authStore.addresses.length > 0" class="text-slate-950 text-base font-normal leading-normal"
                @click="authStore.showChangeAddressModal = true">
                {{ $t('Change') }}
            </button>
        </div>

        <!-- Shipping Address form -->
        <Transition leave-active-class="transition ease-in duration-300"
            enter-active-class="transition ease-out duration-300" enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <div v-if="authStore.addresses.length == 0">
                <address-form 
                    :hideSubmitButton="true"
                    @formData="updatePendingAddress"
                />
            </div>
        </Transition>

        <!-- Selected Address -->
        <div v-if="authStore.addresses.length > 0"
            class="mt-4 p-4 flex gap-6 rounded-lg border border-slate-200 w-full">
            <div
                class="flex w-[60px] sm:w-[88px] bg-slate-50 rounded-lg flex-col gap-2 justify-center items-center shrink-0">
                <MapPinIcon class="w-6 h-6 text-primary-600" />
                <div class="px-1.5 py-[3px] bg-slate-800 rounded-md text-white text-xs font-medium uppercase">
                    {{ basketStore.address?.address_type }}
                </div>
            </div>
            <div class="overflow-hidden">
                <div class="text-slate-950 text-lg font-medium leading-normal tracking-tight">
                    {{ basketStore.address?.name }}
                </div>
                <div class="text-slate-500 text-base font-normal leading-normal">
                    {{ basketStore.address?.phone }}
                </div>
                <div class="text-slate-500 text-base font-normal leading-normal truncate">
                    {{ (basketStore.address?.flat_no ? basketStore.address?.flat_no + ', ' : '') + basketStore.address?.address_line + ', ' +
                (basketStore.address?.address_line2 ? basketStore.address?.address_line2 + ', ' : '') }} {{ basketStore.address?.area + '-' +
                basketStore.address?.post_code }}
                </div>
            </div>
        </div>

        <!-- Change Address Dialog Modal -->
        <AddressChangeDialogModal />
        <!-- End Change Address Dialog Modal -->

        <!-- new Address Dialog Modal -->
        <AddressFormModal />
        <!-- End new Address Dialog Modal -->

    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';

import { MapPinIcon } from '@heroicons/vue/24/solid';
import AddressChangeDialogModal from './AddressChangeDialogModal.vue';
import AddressForm from './AddressForm.vue';
import AddressFormModal from './AddressFormModal.vue';

import { useAuth } from '../stores/AuthStore';
import { useBasketStore } from '../stores/BasketStore';

const basketStore = useBasketStore();

const changeAddress = ref(false);

const authStore = useAuth();

// Add ref for pending address
const pendingAddress = ref(null);

// Method to update pending address
const updatePendingAddress = (addressData) => {
    // Ensure we have all required fields
    if ((!addressData.name && !addressData.company_name) || !addressData.phone || !addressData.area || !addressData.post_code || !addressData.address_line) {
        console.warn('Missing required address fields, not updating pending address');
        return; // Don't update if essential fields are missing
    }
    
    // Set address type if not already set
    if (!addressData.address_type) {
        addressData.address_type = addressData.company_name ? 'office' : 'home';
    }
    
    // Store the pending address
    pendingAddress.value = addressData;
    basketStore.pendingAddress = addressData;
    
    console.log('Updated pending address:', addressData);
};

onMounted(() => {
    authStore.fetchAddresses()
    fetchADefaultAddress()
})

const fetchADefaultAddress = () => {
    if (!basketStore.address) {
        authStore.addresses.forEach((address) => {
            if (address.is_default) {
                basketStore.address = address
                return true;
            }
        })
    }
}

</script>
