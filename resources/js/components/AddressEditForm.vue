<template>
    <div class="p-6 bg-white rounded-2xl border border-slate-200 mt-3">
        <form @submit.prevent="addressFormSubmit()">
            <div class="flex gap-4 mb-6">
                <label>
                    <input type="radio" v-model="entityType" value="home" />
                    {{ $t('Individual') }}
                </label>
                <label>
                    <input type="radio" v-model="entityType" value="office" />
                    {{ $t('Legal Entity') }}
                </label>
            </div>

            <!--div v-if="entityType === 'home'" -->
                <!-- Individual Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="form-label mb-2"> {{ $t('Name') }}
                            <small class="text-red-500">*</small>
                        </label>
                        <input type="text" id="name" v-model="formData.name" :placeholder="$t('Enter name')" class="form-input"
                            :class="(errors && errors?.name) ? 'border-red-500' : 'border-slate-200'">
                        <span v-if="errors && errors?.name" class="text-red-500 text-sm">{{ errors?.name[0] }}</span>
                    </div>
                    <div>
                        <label for="Phone" class="form-label mb-2"> Phone
                            <small class="text-red-500">*</small>
                        </label>
                        <input type="text" id="Phone" :placeholder="$t('Enter phone')" class="form-input"
                            v-model="formData.phone"
                            :class="errors && errors?.phone ? 'border-red-500' : 'border-slate-200'" />
                        <span v-if="errors && errors?.phone" class="text-red-500 text-sm">{{ errors?.phone[0] }}</span>
                    </div>
                </div>
            <!--/div -->

            <div v-if="entityType === 'office'">
                <!-- Legal Entity Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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

            <!-- Common Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
                <div>
                    <label for="Area" class="form-label mb-2"> {{ $t('Area') }}
                        <small class="text-red-500">*</small>
                    </label>
                    <input type="text" id="Area" :placeholder="$t('Enter Area')" class="form-input" v-model="formData.area"
                        :class="errors && errors?.area ? 'border-red-500' : 'border-slate-200'">
                    <span v-if="errors && errors?.area" class="text-red-500 text-sm">{{ errors?.area[0] }}</span>
                </div>
                <div>
                    <label for="Flat" class="form-label mb-2"> {{ $t('Flat') }}</label>
                    <input type="text" id="Flat" :placeholder="$t('Enter Flat no')" class="form-input"
                        v-model="formData.flat_no"
                        :class="errors && errors?.flat_no ? 'border-red-500' : 'border-slate-200'" />
                    <span v-if="errors && errors?.flat_no" class="text-red-500 text-sm">{{ errors?.flat_no[0] }}</span>
                </div>

                <div>
                    <label for="Postal" class="form-label mb-2"> {{ $t('Postal Code') }}
                        <small class="text-red-500">*</small>
                    </label>
                    <input type="text" id="Postal" v-model="formData.post_code" :placeholder="$t('Enter Postal Code')" class="form-input"
                        :class="errors && errors?.post_code ? 'border-red-500' : 'border-slate-200'" />
                    <span v-if="errors && errors?.post_code" class="text-red-500 text-sm">{{ errors?.post_code[0] }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                <div>
                    <label for="address" class="form-label mb-2"> {{ $t('Address Line 1') }}
                        <small class="text-red-500">*</small>
                    </label>
                    <input type="text" id="address" v-model="formData.address_line" :placeholder="$t('Enter address 1')"
                        class="form-input"
                        :class="errors && errors?.address_line ? 'border-red-500' : 'border-slate-200'" />
                    <span v-if="errors && errors?.address_line" class="text-red-500 text-sm">{{ errors?.address_line[0] }}</span>
                </div>
                <div>
                    <label for="address2" class="form-label mb-2"> {{ $t('Address Line 2') }}</label>
                    <input type="text" id="address2" v-model="formData.address_line2" :placeholder="$t('Enter address 2')"
                        class="form-input"
                        :class="errors && errors?.address_line2 ? 'border-red-500' : 'border-slate-200'" />
                    <span v-if="errors && errors?.address_line2" class="text-red-500 text-sm">{{ errors?.address_line2[0] }}</span>
                </div>
                <div>
                    <label for="Country" class="form-label mb-2"> {{ $t('Country') }}
                        <small class="text-red-500">*</small>
                    </label>
                    <input type="text" id="Country" v-model="formData.country" :placeholder="$t('Enter Country')" class="form-input"
                        :class="errors && errors?.country ? 'border-red-500' : 'border-slate-200'" />
                    <span v-if="errors && errors?.country" class="text-red-500 text-sm">{{ errors?.country[0] }}</span>
                </div>
            </div>

            <div class="mt-4">
                <div class="flex justify-between items-center gap-2 mt-6">
                    <button type="submit"
                        class="px-4 py-3 md:px-6 md:py-4 bg-primary text-white text-sm md:text-base rounded-[10px] w-[140px] md:w-[152px]">
                        {{ $t('Update') }}
                    </button>

                    <button type="button" class="bg-white rounded-lg py-2 md:py-4 px-2" @click="deleteModal = true">
                        <div class="text-red-500 text-base font-medium leading-normal">
                            {{ $t('Delete this') }}
                        </div>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Delete modal -->
    <TransitionRoot as="template" :show="deleteModal">
        <Dialog as="div" class="relative z-10" @close="deleteModal = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <div class="bg-white p-5 sm:p-8 text-center">

                                <div
                                    class="bg-red-500 w-14 h-14 md:w-20 md:h-20 rounded-full mx-auto flex justify-center items-center">
                                    <TrashIcon class="w-8 h-8 md:w-12 md:h-12 text-white" />
                                </div>

                                <div class="mt-3 text-center text-gray-900 text-2xl md:text-3xl font-bold">
                                    {{ $t('Delete address') }}
                                </div>

                                <div class="mt-4 text-center text-slate-700 text-base md:text-xl font-normal">
                                    {{ $t('Are you sure want to delete this address') }}
                                </div>

                                <div class="flex justify-between items-center gap-4 mt-8">
                                    <button
                                        class="text-slate-800 grow text-base font-medium px-4 py-3 md:px-6 md:py-4 rounded-lg md:rounded-[10px] border border-slate-300"
                                        @click="deleteModal = false">
                                        {{ $t('Cancel') }}
                                    </button>

                                    <button
                                        class="text-white grow bg-red-500 text-base font-medium px-4 py-3 md:px-6 md:py-4 rounded-lg md:rounded-[10px]"
                                        @click="deleteAddress">
                                        {{ $t('Yes') }}
                                    </button>
                                </div>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
<script setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { TrashIcon } from '@heroicons/vue/24/outline';
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useAuth } from '../stores/AuthStore';
import ToastSuccessMessage from './ToastSuccessMessage.vue';

const toast = useToast();
const router = useRouter();
const authStore = useAuth();

const deleteModal = ref(false);

const props = defineProps({
    address: Object
});

const entityType = ref('home'); // Default to 'individual'

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
    company_name: '', // For legal entity
    cui: '', // For legal entity
    trade_register_number: '', // For legal entity
    vat_payer: '', // For legal entity
    country: 'Romania'
});

watch(() => props.address, (newAddress) => {
    if (newAddress) {
        formData.value = {
            ...formData.value,
            ...newAddress
        };
        entityType.value = newAddress.company_name ? 'office' : 'home';
    }
});

const errors = ref({});

const UpdateContent = {
    component: ToastSuccessMessage,
    props: {
        title: 'Address Updated!',
        message: 'Address updated successfully',
    },
};

const content = {
    component: ToastSuccessMessage,
    props: {
        title: 'Address Deleted!',
        message: 'Address deleted successfully',
    },
};

const deleteAddress = () => {
    axios.delete(`/address/${props.address.id}/delete`, {
        headers: {
            'Authorization': authStore.token
        }
    }).then(() => {
        toast(content, {
            type: "default",
            hideProgressBar: true,
            icon: false,
            position: "bottom-left",
            toastClassName: "vue-toastification-alert",
            timeout: 2000,
        });
        router.push({ name: 'manage-address' })
    }).catch((error) => {
        toast.error(error.response.data.message, {
            position: "bottom-left",
        });
    })
}

const addressFormSubmit = () => {
    formData.value.address_type = entityType.value;
    console.log(formData.value);
    axios.post(`/address/${props.address.id}/update`, formData.value, {
        headers: {
            'Authorization': authStore.token
        }
    }).then(() => {
        formData.value = {};
        toast(UpdateContent, {
            type: "default",
            hideProgressBar: true,
            icon: false,
            position: "bottom-left",
            toastClassName: "vue-toastification-alert",
            timeout: 2000,
        });
        router.push({ name: 'manage-address' })
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
    @apply w-4 h-4 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}

.radioBtn2 {
    @apply w-4 h-4 border appearance-none border-slate-300 rounded-full checked:bg-primary ring-primary checked:outline-1 outline-offset-1 checked:outline-primary checked:outline transition duration-100 ease-in-out m-0;
}
</style>
