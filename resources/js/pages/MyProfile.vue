<template>
    <div>
        <!-- Header -->
        <AuthPageHeader :title="$t('My Profile')" />

        <div class="p-3 md:p-4 xl:p-6">

            <form @submit.prevent="submitForm()">
                <div class="max-w-[920px] mx-auto bg-white rounded-xl md:rounded-2xl p-3 md:p-4 xl:p-6">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">

                        <div class="text-primary text-sm font-medium">{{ $t('Profile Picture') }}</div>
                        <div class="flex flex-col gap-2 justify-center items-center">
                            <img :src="profilePhoto" alt=""
                                class="w-24 h-24 md:w-36 md:h-36 bg-white rounded-full object-cover">

                            <div class="flex gap-2">
                                <label for="upload"
                                    class="text-white text-sm font-normal px-2 py-1 md:px-3 md:py-2 bg-slate-800 rounded-md cursor-pointer">
                                    <input type="file" id="upload" class="hidden" @change="ChangeProfilePhoto">
                                    {{ $t('Change Photo') }}
                                </label>
                                <button v-if="ChangeProfile"
                                    class="px-2 py-1 md:px-3 md:py-2 bg-red-500 text-white rounded-md"
                                    @click="removePhoto">
                                    <TrashIcon class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-white rounded-xl border border-slate-100 mt-3">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="form-label"> {{ $t('Name') }}</label>
                                <input type="text" id="name" v-model="formData.name" :placeholder="$t('Enter name')"
                                    class="form-input"
                                    :class="(errors && errors?.name) ? 'border-red-500' : 'border-slate-200'">
                                <span v-if="errors && errors?.name" class="text-red-500 text-sm">
                                    {{ errors?.name[0] }}
                                </span>
                            </div>

                            <div>
                                <label for="name" class="form-label mb-2">{{ $t('Country') }}</label>
                                <v-select :options="countries" label="name" :reduce="country => country.name"
                                    v-model="formData.country" :placeholder="$t('Select Country')" />
                                <span v-if="errors && errors?.country" class="text-red-500 text-sm">
                                    {{ errors?.country[0] }}
                                </span>
                            </div>

                            <div>
                                <label for="phone" class="form-label">{{ $t('Mobile Number') }}</label>
                                <div class="flex">
                                    <span
                                        class="text-slate-700 text-base font-normal leading-normal bg-slate-100 p-2 border-y border-l rounded-tl-lg rounded-bl-lg flex items-center">
                                        +{{ formData.phone_code || '00' }}
                                    </span>
                                    <input type="text" v-model="formData.phone" :placeholder="$t('Enter phone number')"
                                        class="text-base font-normal w-full p-3 placeholder:text-slate-400 rounded-tr-lg rounded-br-lg border focus:border-primary outline-none"
                                        :class="errors?.phone ? 'border-red-500' : 'border-slate-200'">
                                </div>
                                <span v-if="errors && errors?.phone" class="text-red-500 text-sm">
                                    {{ errors?.phone[0] }}
                                </span>
                            </div>

                            <div>
                                <label for="gender" class="form-label bg-transparent">{{ $t('Gender') }}</label>
                                <select name="gender" id="gender" v-model="formData.gender"
                                    class="form-input bg-transparent h-[52px]"
                                    :class="(errors && errors?.gender) ? 'border-red-500' : 'border-slate-200'">
                                    <option value="" disabled selected>{{ $t('Select a gender') }}</option>
                                    <option value="male">{{ $t('Male') }}</option>
                                    <option value="female">{{ $t('Female') }}</option>
                                    <option value="other">{{ $t('Other') }}</option>
                                </select>
                                <span v-if="errors && errors?.gender" class="text-red-500 text-sm">
                                    {{ errors?.gender[0] }}
                                </span>
                            </div>

                            <div>
                                <label for="date" class="form-label">{{ $t('Date of Birth') }}</label>
                                <input type="date" id="date" v-model="formData.date_of_birth" class="form-input"
                                    :class="(errors && errors?.date_of_birth) ? 'border-red-500' : 'border-slate-200'">
                                <span v-if="errors && errors?.date_of_birth" class="text-red-500 text-sm">
                                    {{ errors?.date_of_birth[0] }}
                                </span>
                            </div>

                            <!-- <div>
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" v-model="formData.email" class="form-input"
                                    placeholder="Enter email"
                                    :class="(errors && errors?.email) ? 'border-red-500' : 'border-slate-200'">
                                <span v-if="errors && errors?.email" class="text-red-500 text-sm">
                                    {{ errors?.email[0] }}
                                </span>
                            </div> -->
                        </div>

                        <button type="submit"
                            class="bg-primary px-4 md:px-6 py-2 md:py-4 rounded-lg :md:rounded-[10px] text-white mt-6">
                            {{ $t('Update Profile') }}
                        </button>

                    </div>
                </div>
            </form>

        </div>

    </div>
</template>

<script setup>
import { TrashIcon } from '@heroicons/vue/24/solid';
import { onMounted, ref, watch } from "vue";
import { useToast } from "vue-toastification";
import { TYPE } from "vue-toastification";
import AuthPageHeader from "../components/AuthPageHeader.vue";
import ToastSuccessMessage from "../components/ToastSuccessMessage.vue";
import { useAuth } from "../stores/AuthStore";

const toast = useToast();
const authStore = useAuth();

const errors = ref(null);

const profilePhoto = ref(null);
const ChangeProfile = ref(false);

const removePhoto = () => {
    profilePhoto.value = authStore.user?.profile_photo;
    ChangeProfile.value = false;
}

const ChangeProfilePhoto = (event) => {
    const file = event.target.files[0];
    
    // Log file information
    console.log('Selected profile photo:', {
        name: file.name,
        type: file.type,
        size: `${(file.size / 1024).toFixed(2)} KB`
    });
    
    // Check if file is an image
    if (!file.type.match('image.*')) {
        toast.error('Please select an image file', {
            position: "bottom-left",
        });
        return;
    }
    
    // Check file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        toast.error('Image size should be less than 5MB', {
            position: "bottom-left",
        });
        return;
    }
    
    profilePhoto.value = URL.createObjectURL(file);
    formData.value.profile_photo = file;
    ChangeProfile.value = true;
}

const formData = ref({
    name: authStore.user?.name,
    phone: authStore.user?.phone,
    gender: authStore.user?.gender ? authStore.user?.gender : '',
    date_of_birth: authStore.user?.date_of_birth,
    email: authStore.user?.email,
    profile_photo: null,
    phone_code: authStore.user?.phone_code,
    country: authStore.user?.country
});

const countries = ref([]);

const demoUser = ref(false);

onMounted(() => {
    if (authStore.user) {
        profilePhoto.value = authStore.user?.profile_photo;
    }
    window.scrollTo(0, 0);

    if (authStore.user?.email === 'user@readyecommerce.com') {
        demoUser.value = true;
    }

    fetchCountries();
});

watch(() => formData.value.country, (value) => {
    const country = countries.value.find((item) => item.name == value);
    formData.value.phone_code = country?.phone_code
})

const fetchCountries = () => {
    axios.get('/countries').then((response) => {
        countries.value = response.data.data.countries
    })
}

const content = {
    component: ToastSuccessMessage,
    props: {
        title: 'Profile Updated',
        message: 'Your profile updated successfully',
    },
};

const submitForm = () => {
    const processData = new FormData();
    processData.append('name', formData.value.name);
    processData.append('phone', formData.value.phone);
    processData.append('gender', formData.value.gender ? formData.value.gender : '');
    processData.append('date_of_birth', formData.value.date_of_birth ? formData.value.date_of_birth : '');
    processData.append('email', formData.value.email ? formData.value.email : '');
    processData.append('country', formData.value.country ? formData.value.country : '');
    processData.append('phone_code', formData.value.phone_code ? formData.value.phone_code : '');
    
    if (formData.value.profile_photo) {
        console.log('Adding profile photo to form data', {
            name: formData.value.profile_photo.name,
            type: formData.value.profile_photo.type,
            size: `${(formData.value.profile_photo.size / 1024).toFixed(2)} KB`
        });
        processData.append('profile_photo', formData.value.profile_photo);
    }

    // Log the form data for debugging
    console.log('Form data being sent:', {
        name: formData.value.name,
        phone: formData.value.phone,
        gender: formData.value.gender,
        hasPhoto: formData.value.profile_photo ? 'yes' : 'no'
    });
    // Show loading toast
    const loadingToast = toast('Updating profile...', {
        type: TYPE.INFO,
        timeout: 0,
        position: "bottom-left"
    });

    axios.post('/update-profile', processData, {
        headers: {
            'Authorization': authStore.token,
            'Content-Type': 'multipart/form-data'
        }
    }).then((response) => {
        // Dismiss loading toast
        toast.dismiss(loadingToast);
        
        console.log('Profile update response:', response.data);
        authStore.user = response.data.data.user;
        ChangeProfile.value = false;
        
        // Show success toast
        toast(content, {
            type: "default",
            hideProgressBar: true,
            icon: false,
            position: "bottom-left",
            toastClassName: "vue-toastification-alert",
            timeout: 2000,
        });
        errors.value = {}
    }).catch(error => {
        // Dismiss loading toast
        toast.dismiss(loadingToast);
        
        console.error('Profile update error:', error.response?.data || error);
        
        // Show error toast
        toast.error(error.response?.data?.message || 'Failed to update profile', {
            position: "bottom-left",
        });
        
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
    });
}

</script>

<style scoped>
.form-label {
    @apply text-slate-700 text-base font-normal block mb-1;
}

.form-input {
    @apply p-3 rounded-lg border focus:border-primary w-full outline-none text-base font-normal leading-normal placeholder:text-slate-400;
}
</style>
