<template>
    <div class="p-6 bg-white rounded-2xl border border-slate-200 mt-3">
        <div class="flex gap-4 mb-6">
            <label>
                <input type="radio" v-model="entityType" value="individual" />
                {{ $t('Individual') }}
            </label>
            <label>
                <input type="radio" v-model="entityType" value="legalEntity" />
                {{ $t('Legal Entity') }}
            </label>
        </div>
        <form @submit.prevent="addressFormSubmit()">
            <div v-if="entityType === 'individual'">
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
                        <label for="Phone" class="form-label mb-2"> {{ $t('Phone') }}
                            <small class="text-red-500">*</small>
                        </label>
                        <input type="text" id="Phone" :placeholder="$t('Enter phone')" class="form-input"
                            v-model="formData.phone"
                            :class="errors && errors?.phone ? 'border-red-500' : 'border-slate-200'" />
                        <span v-if="errors && errors?.phone" class="text-red-500 text-sm">{{ errors?.phone[0] }}</span>
                    </div>
                </div>
            </div>

            <div v-if="entityType === 'legalEntity'">
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
                    <!--div>
                        <label for="cui" class="form-label mb-2"> {{ $t('CUI') }}
                            <small class="text-red-500">*</small>
                        </label>
                        <input type="text" id="cui" v-model="formData.cui" :placeholder="$t('Enter CUI')" class="form-input"
                            :class="errors && errors?.cui ? 'border-red-500' : 'border-slate-200'" />
                        <span v-if="errors && errors?.cui" class="text-red-500 text-sm">{{ errors?.cui[0] }}</span>
                    </!-->
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
                    <label for="Postal" class="form-label mb-2"> {{ $t('Postal Code') }}
                        <small class="text-red-500">*</small>
                    </label>
                    <input type="text" id="Postal" v-model="formData.post_code" :placeholder="$t('Enter Postal Code')" class="form-input"
                        :class="errors && errors?.post_code ? 'border-red-500' : 'border-slate-200'" />
                    <span v-if="errors && errors?.post_code" class="text-red-500 text-sm">{{ errors?.post_code[0] }}</span>
                </div>
                <div>
                    <label for="Flat" class="form-label mb-2"> {{ $t('Flat No') }}</label>
                    <input type="text" id="Flat" v-model="formData.flat_no" :placeholder="$t('Enter Flat No')" class="form-input"
                        :class="errors && errors?.flat_no ? 'border-red-500' : 'border-slate-200'" />
                    <span v-if="errors && errors?.flat_no" class="text-red-500 text-sm">{{ errors?.flat_no[0] }}</span>
                </div>
            </div>

            <div class="mt-6">
                <div>
                    <label for="address" class="form-label mb-2"> {{ $t('Address') }}
                        <small class="text-red-500">*</small>
                    </label>
                    <input type="text" id="address" v-model="formData.address_line" :placeholder="$t('Enter Address')"
                        class="form-input"
                        :class="errors && errors?.address_line ? 'border-red-500' : 'border-slate-200'" />
                    <span v-if="errors && errors?.address_line" class="text-red-500 text-sm">{{ errors?.address_line[0] }}</span>
                </div>
            </div>

            <!-- Submit Button - only show if not hidden -->
            <button 
                v-if="!hideSubmitButton"
                type="submit" 
                class="px-4 py-3 md:px-6 md:py-4 bg-primary text-white text-sm md:text-base rounded-[10px] mt-6"
            >
                {{ $t('Save') }}
            </button>
        </form>
    </div>
</template>

<script setup>
import axios from 'axios';
import { ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import { useAuth } from '../stores/AuthStore';
import ToastSuccessMessage from './ToastSuccessMessage.vue';

const toast = useToast();
const route = useRoute();
const router = useRouter();
const authStore = useAuth();

const entityType = ref('individual'); // Default to 'individual'

const formData = ref({
    name: '',
    phone: '',
    area: '',
    flat_no: '',
    post_code: '',
    address_line: '',
    address_type: 'home', // This will be set based on entityType
    is_default: false,
    company_name: '', // For legal entity
    cui: '', // For legal entity
    country: 'Romania'
});

const errors = ref({});

const content = {
    component: ToastSuccessMessage,
    props: {
        title: 'Address Added',
        message: 'Address added successfully',
    },
};

const props = defineProps({
    autoSave: {
        type: Boolean,
        default: true
    },
    hideSubmitButton: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['addressData', 'formData']);

const addressFormSubmit = () => {
    if (props.autoSave) {
        formData.value.address_type = entityType.value==='individual' ? 'home' : 'office';
        axios.post('/address/store', formData.value, {
            headers: {
                'Authorization': authStore.token
            }
        }).then(() => {
            formData.value = {};
            authStore.fetchAddresses();
            toast(content, {
                type: "default",
                hideProgressBar: true,
                icon: false,
                position: "bottom-left",
                toastClassName: "vue-toastification-alert",
                timeout: 2000,
            });
            if (route.name === 'add-new-address') {
                router.push({ name: 'manage-address' })
            }
        }).catch((error) => {
            errors.value = error.response.data.errors
            toast.error(error.response.data.message, {
                position: "bottom-left",
            });
        })
    } else {
        emit('formData', formData.value);
    }
}

// Watch for entity type changes
watch(entityType, (newType) => {
    formData.value.address_type = newType === 'individual' ? 'home' : 'office';
    // Also emit updated form data when entity type changes
    emit('formData', formData.value);
});

// Update the emit to include address type
watch(formData, (newData) => {
    // Ensure address_type is set correctly before emitting
    newData.address_type = entityType.value === 'individual' ? 'home' : 'office';
    emit('formData', newData);
}, { deep: true });

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
