<template>
    <div>
        <TransitionRoot as="template" :show="authStore.showAddressModal">
            <Dialog as="div" class="relative z-10" @close="authStore.showAddressModal = false">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                    enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-50 transition-opacity" />
                </TransitionChild>
                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300"
                            enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                            leave-from="opacity-100 translate-y-0 sm:scale-100"
                            leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel
                                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all my-8 md:my-0 w-full md:max-w-3xl">
                                <div class="bg-white p-5 sm:p-8 relative">
                                    <!-- close button -->
                                    <div class="w-9 h-9 bg-slate-100 rounded-[32px] absolute top-4 right-4 flex justify-center items-center cursor-pointer"
                                        @click="authStore.showAddressModal = false">
                                        <XMarkIcon class="w-6 h-6 text-slate-600" />
                                    </div>
                                    <!-- end close button -->

                                    <div class="text-slate-950 text-2xl font-medium">
                                        {{ $t('New Address') }}
                                    </div>

                                    <form @submit.prevent="addressFormSubmit()" class="mt-4">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                            <div>
                                                <label for="name" class="form-label mb-2"> {{ $t('Name') }}
                                                    <small class="text-red-500">*</small>
                                                </label>
                                                <input type="text" id="name" v-model="formData.name"
                                                    :placeholder="$t('Enter name')" class="form-input"
                                                    :class="(errors && errors?.name) ? 'border-red-500' : 'border-slate-200'">
                                                <span v-if="errors && errors?.name" class="text-red-500 text-sm">{{errors?.name[0] }}</span>
                                            </div>
                                            <div>
                                                <label for="Phone" class="form-label mb-2"> {{ $t('Phone') }}
                                                    <small class="text-red-500">*</small>
                                                </label>
                                                <input type="text" id="Phone" :placeholder="$t('Enter phone')"
                                                    value="0123456789" class="form-input" v-model="formData.phone"
                                                    :class="errors && errors?.phone ? 'border-red-500' : 'border-slate-200'" />
                                                <span v-if="errors && errors?.phone" class="text-red-500 text-sm">{{errors?.phone[0] }}</span>
                                            </div>
                                        </div>


                                              <div v-if="formData.address_type === 'office'">
                <!-- Legal Entity Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="company_name" class="form-label mb-2"> {{ $t('Company Name') }}
                            <small class="text-red-500">*</small>
                        </label>
                        <input type="text" id="company_name" v-model="formData.company_name" :placeholder="$t('Enter company name')" class="form-input"
                            :class="(errors && errors?.company_name) ? 'border-red-500' : 'border-slate-200'">
                        <span v-if="errors && errors?.company_name" class="text-red-500 text-sm">{{ errors?.company_name[0] }}</span>
                    </div>
                    <!-- div>
                        <label for="cui" class="form-label mb-2"> {{ $t('CUI') }}
                            <small class="text-red-500">*</small>
                        </label>
                        <input type="text" id="cui" v-model="formData.cui" :placeholder="$t('Enter CUI')" class="form-input"
                            :class="errors && errors?.cui ? 'border-red-500' : 'border-slate-200'" />
                        <span v-if="errors && errors?.cui" class="text-red-500 text-sm">{{ errors?.cui[0] }}</span>
                    </!-->
                </div>

                 <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="trade_register_number" class="form-label mb-2"> {{ $t('Trade Register Number') }}
                            <small class="text-red-500">*</small>
                        </label>
                        <input type="text" id="trade_register_number" v-model="formData.trade_register_number" :placeholder="$t('Trade Register Number')" class="form-input"
                            :class="(errors && errors?.trade_register_number) ? 'border-red-500' : 'border-slate-200'">
                        <span v-if="errors && errors?.trade_register_number" class="text-red-500 text-sm">{{ errors?.trade_register_number[0] }}</span>
                    </div>
                    <div>
                        <label for="vat_payer" class="form-label mb-2"> {{ $t('VAT Payer') }}
                            <small class="text-red-500">*</small>
                        </label>
                        <select  id="vat_payer" v-model="formData.vat_payer" :placeholder="$t('VAT Payer')" class="form-input"
                            :class="errors && errors?.vat_payer ? 'border-red-500' : 'border-slate-200'" >
                        <option value="no" >{{ $t('No') }}</option>   
                            <option value="yes">{{ $t('Yes') }}</option>
                           
                        </select>
                        <span v-if="errors && errors?.vat_payer" class="text-red-500 text-sm">{{ errors?.vat_payer[0] }}</span>
                    </div>
                </div>


            </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
                                            <div>
                                                <label for="Area" class="form-label mb-2"> {{ $t('Area') }}
                                                    <small class="text-red-500">*</small>
                                                </label>
                                                <input type="text" id="Area" :placeholder="$t('Enter Area')" class="form-input"
                                                    v-model="formData.area"
                                                    :class="errors && errors?.area ? 'border-red-500' : 'border-slate-200'">
                                                <span v-if="errors && errors?.area" class="text-red-500 text-sm">{{ errors?.area[0] }}</span>
                                            </div>
                                            <div>
                                                <label for="Flat" class="form-label mb-2"> {{ $t('Flat') }}</label>
                                                <input type="text" id="Flat" :placeholder="$t('Enter Flat no')" value=""
                                                    class="form-input" v-model="formData.flat_no"
                                                    :class="errors && errors?.flat_no ? 'border-red-500' : 'border-slate-200'" />
                                                <span v-if="errors && errors?.flat_no" class="text-red-500 text-sm">{{ errors?.flat_no[0] }}</span>
                                            </div>

                                            <div>
                                                <label for="Postal" class="form-label mb-2"> {{ $t('Postal Code') }}
                                                    <small class="text-red-500">*</small>
                                                </label>
                                                <input type="text" id="Postal" v-model="formData.post_code"
                                                    :placeholder="$t('Enter Postal Code')" value="" class="form-input"
                                                    :class="errors && errors?.post_code ? 'border-red-500' : 'border-slate-200'" />
                                                <span v-if="errors && errors?.post_code" class="text-red-500 text-sm">{{ errors?.post_code[0] }}</span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                                            <div>
                                                <label for="address" class="form-label mb-2"> {{ $t('Address Line 1') }}
                                                    <small class="text-red-500">*</small>
                                                </label>
                                                <input type="text" id="address" v-model="formData.address_line"
                                                    :placeholder="$t('Enter address 1')" class="form-input"
                                                    :class="errors && errors?.address_line ? 'border-red-500' : 'border-slate-200'" />
                                                <span v-if="errors && errors?.address_line"
                                                    class="text-red-500 text-sm">{{ errors?.address_line[0] }}</span>
                                            </div>
                                            <div>
                                                <label for="address2" class="form-label mb-2"> {{ $t('Address Line 2') }}</label>
                                                <input type="text" id="address2" v-model="formData.address_line2"
                                                    :placeholder="$t('Enter address 2')" value="" class="form-input"
                                                    :class="errors && errors?.address_line2 ? 'border-red-500' : 'border-slate-200'" />
                                                <span v-if="errors && errors?.address_line2"
                                                    class="text-red-500 text-sm">{{ errors?.address_line2[0] }}</span>
                                            </div>
                                        </div>

                                        <div class="mt-5">
                                            <div class="text-slate-950 text-base font-medium leading-normal">
                                                {{ $t('Address Tag') }}
                                            </div>

                                            <div class="flex justify-between items-center gap-2 mt-3 flex-wrap">
                                                <div class="flex items-center gap-2">
                                                    <label for="home"
                                                        class="px-3 py-2 bg-white rounded-[42px] border flex gap-2 items-center text-slate-600 text-base font-normal leading-normal cursor-pointer has-[:checked]:border-primary has-[:checked]:text-primary">
                                                        <input type="radio" id="home" v-model="formData.address_type"
                                                            name="tag" value="home" class="radio-btn"
                                                            :checked="formData.address_type === 'home'">
                                                        <span class="text-base font-normal"> {{ $t('HOME') }}</span>
                                                    </label>

                                                    <label for="office"
                                                        class="px-3 py-2 bg-white rounded-[42px] border flex gap-2 items-center text-slate-600 text-base font-normal leading-normal cursor-pointer has-[:checked]:border-primary has-[:checked]:text-primary">
                                                        <input type="radio" id="office" v-model="formData.address_type"
                                                            name="tag"  value="office" class="radio-btn"
                                                            :checked="formData.address_type === 'office'">
                                                        <span class="text-base font-normal">{{ $t('OFFICE') }}</span>
                                                    </label>

                                               

                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <input id="default" v-model="formData.is_default" name="default"
                                                        type="checkbox" class="w-4 h-4"
                                                        :checked="formData.is_default" />
                                                    <label for="default"
                                                        class="text-slate-500 text-sm font-normal leading-tight m-0">
                                                        {{ $t('Make it default address') }}
                                                    </label>
                                                </div>

                                                <button type="submit"
                                                    class="px-8 py-2 bg-primary text-white rounded-[42px]">
                                                    {{ $t('Submit') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </div>
</template>

<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/solid';

import { useAuth } from '../stores/AuthStore';

import { useToast } from 'vue-toastification';

const toast = useToast();
const authStore = useAuth();


const formData = ref({
    name: '',
    phone: '',
    area: '',
    flat_no: '',
    post_code: '',
    address_line: '',
    address_line2: '',
    address_type: 'home',
    latitude: '',
    longitude: '',
   
    is_default: false,
      country: 'Romania',
        company_name: '', // For legal entity
    cui: '', // For legal entity
    trade_register_number: '', // For legal entity
    vat_payer: '', // For legal entity
});

const errors = ref({});

const addressFormSubmit = () => {
    axios.post('/address/store', formData.value, {
        headers: {
            'Authorization': authStore.token
        }
    }).then((response) => {
        toast.success(response.data.message, {
            position: "bottom-left",
        });
        authStore.fetchAddresses();
        authStore.showAddressModal = false
        authStore.showChangeAddressModal = true
    }).catch((error) => {
        errors.value = error.response.data.errors
        toast.error(error.response.data.message, {
            position: "bottom-left",
        });
    })
}

</script>

<style scoped>
.form-label {
    @apply text-slate-700 text-base font-normal leading-normal;
}

.form-input {
    @apply p-3 rounded-lg border focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}

.formInputCoupon {
    @apply rounded-lg border border-slate-200 focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}

.radio-btn {
    @apply w-5 h-5 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}

.radioBtn2 {
    @apply w-4 h-4 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}</style>

