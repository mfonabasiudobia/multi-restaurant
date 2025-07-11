<template>
    <div>
        <!-- login modal-->
        <TransitionRoot as="template" :show="AuthStore.loginModal">
            <Dialog as="div" class="relative z-50" @close="AuthStore.hideLoginModal()">
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
                                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all my-8 md:my-0 w-full sm:max-w-lg md:max-w-xl">
                                <div class="bg-white p-5 sm:p-8 relative">
                                    <!-- close button -->
                                    <div class="w-9 h-9 bg-slate-100 rounded-[32px] absolute top-4 right-4 flex justify-center items-center cursor-pointer"
                                        @click="AuthStore.hideLoginModal()">
                                        <XMarkIcon class="w-6 h-6 text-slate-600" />
                                    </div>
                                    <!-- end close button -->

                                    <div class="text-slate-950 text-lg sm:text-2xl font-medium leading-loose">{{
                                        $t('Welcome') }}!
                                    </div>

                                    <div class="text-slate-950 text-lg font-normal leading-7 tracking-tight mt-3">
                                        {{ $t('Please Login to continue') }}
                                    </div>

                                    <!-- social login -->
                                    <div v-if="master.socialAuths?.facebook?.is_active || master.socialAuths?.google?.is_active || master.socialAuths?.apple?.is_active">
                                        <div class="font-['Inter'] mt-6 flex flex-col gap-3 items-center text-center">
                                            <button v-if="master.socialAuths.google?.is_active" type="button"
                                                @click="googleLogin()"
                                                class="px-4 py-3 w-full flex items-center rounded-full text-black  text-sm sm:text-base tracking-wider font-semibold outline-none border border-slate-200 bg-white hover:bg-gray-50 active:bg-gray-50">
                                                <GoogleIcon />
                                                <span class="leading-none m-0">{{ $t('Sign up with Google') }}</span>
                                            </button>

                                            <button v-if="master.socialAuths.facebook?.is_active" type="button"
                                                @click="loginWithFacebook()"
                                                class="px-4 py-3 w-full flex items-center rounded-full text-white text-xs xs:text-sm sm:text-base tracking-wider font-semibold border border-blue-500 outline-none bg-blue-700 hover:bg-blue-800 active:bg-blue-600">
                                                <font-awesome-icon :icon="faFacebook" class="mr-2 text-2xl m-0" />
                                                <span class="leading-none m-0">{{ $t('Sign up with Facebook') }}</span>
                                            </button>

                                            <button v-if="master.socialAuths.apple?.is_active" type="button"
                                                @click="loginWithApple('apple')"
                                                class="px-4 py-3 w-full flex items-center rounded-full text-white text-sm sm:text-base tracking-wider font-semibold border border-black outline-none bg-black hover:bg-[#333] active:bg-black">
                                                <font-awesome-icon :icon="faApple" class="mr-2 text-2xl m-0" />
                                                <span class="leading-none m-0">{{ $t('Sign up with Apple') }}</span>
                                            </button>
                                        </div>
                                        <div class="mt-6">
                                            <div class="text-[#687387] text-sm font-normal border-b relative">
                                                <span
                                                    class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 bg-white px-2 rounded-full uppercase">{{
                                                        $t('OR CONTINUE WITH') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Phone Number -->
                                    <div class="mt-8">
                                        <label class="text-slate-700 text-base font-normal leading-normal mb-2 block">
                                            {{ $t('Phone Number') }}
                                        </label>

                                        <div class="flex">
                                            <div class="relative w-40 min-w-40 mr-2">
                                                <v-select 
                                                    class="phone-code-select"
                                                    :options="countries" 
                                                    label="name"
                                                    :reduce="country => country.phone_code"
                                                    v-model="loginFormData.phone_code" 
                                                    placeholder="Select country"
                                                >
                                                    <template #selected-option="option">
                                                        <div class="flex items-center">
                                                            <span>+{{ option.phone_code }} ({{ option.name }})</span>
                                                        </div>
                                                    </template>
                                                    <template #option="option">
                                                        <div class="flex items-center py-1">
                                                            <span>{{ option.name }} (+{{ option.phone_code }})</span>
                                                        </div>
                                                    </template>
                                                    <template #no-options>
                                                        {{ $t('No countries found') }}
                                                    </template>
                                                </v-select>
                                            </div>
                                            <input type="tel" 
                                                   v-model="loginFormData.phone"
                                                   :placeholder="$t('Enter phone number')"
                                                   class="text-base font-normal w-full p-3 placeholder:text-slate-400 rounded-lg border focus:border-primary outline-none"
                                                   :class="errors && errors?.phone ? 'border-red-500' : 'border-slate-200'"
                                                   pattern="[0-9]*"
                                                   inputmode="numeric">
                                        </div>
                                        <span v-if="errors && errors?.phone" class="text-red-500 text-sm">{{ errors?.phone[0] }}</span>
                                    </div>

                                    <!-- Password -->
                                    <div class="mt-4">
                                        <label class="text-slate-700 text-base font-normal leading-normal mb-2 block">{{
                                            $t('Password') }}</label>

                                        <div class="relative">
                                            <input :type="showLoginPassword ? 'text' : 'password'"
                                                v-model="loginFormData.password" :placeholder="$t('Enter Password')"
                                                class="text-base font-normal w-full p-3 placeholder:text-slate-400 rounded-lg border focus:border-primary outline-none"
                                                :class="errors && errors?.password ? 'border-red-500' : 'border-slate-200'">
                                            <button @click="showLoginPassword = !showLoginPassword">
                                                <EyeIcon v-if="showLoginPassword"
                                                    class="w-6 h-6 text-slate-700 absolute right-4 top-1/2 -translate-y-1/2" />
                                                <EyeSlashIcon v-else
                                                    class="w-6 h-6 text-slate-700 absolute right-4 top-1/2 -translate-y-1/2" />
                                            </button>
                                        </div>
                                        <span v-if="errors && errors?.password" class="text-red-500 text-sm">{{
                                            errors?.password[0] }}</span>
                                    </div>

                                    <!-- Forgot Password -->
                                    <div class="mt-2 text-right">
                                        <button class="text-right text-slate-700 text-base font-normal leading-normal"
                                            @click="showForgetPasswordDialog()">
                                            {{ $t('Forgot Password') }}?
                                        </button>
                                    </div>

                                    <!-- login button -->
                                    <button class="px-6 py-3 bg-primary mt-5 rounded-[10px] text-white text-base font-medium w-full"
                                        @click="loginFormSubmit">
                                        {{ $t('Log in') }}
                                    </button>

                                    <!-- register button -->
                                    <button class="px-6 py-3 bg-primary mt-5 rounded-[10px] text-white text-base font-medium w-full" @click="showRegisterDialog">
                                        {{ $t("Don't have an account") }}? {{ $t('Sign Up now') }}
                                    </button>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- forget password dialog -->
        <ForgetPasswordDialogModal :forgetPasswordDialog="forgetPasswordDialog" :countries="countries"
            @closeForget="forgetPasswordDialog = false" />

        <!-- registration dialog -->
        <RegistrationDialogModal :registerDialog="registerDialog" :countries="countries"
            @hideRegisterDialog="registerDialog = false" @showLogin="showLoginDialog" />

        <!-- Add OTP Dialog Modal -->
        <TransitionRoot as="template" :show="showOTPDialog">
            <Dialog as="div" class="relative z-10">
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
                                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all my-8 md:my-0 w-full sm:max-w-lg md:max-w-xl">
                                <div class="bg-white p-5 sm:p-8 relative">
                                    <!-- close button -->
                                    <div class="w-9 h-9 bg-slate-100 rounded-[32px] absolute top-4 right-4 flex justify-center items-center cursor-pointer"
                                        @click="showOTPDialog = false">
                                        <XMarkIcon class="w-6 h-6 text-slate-600" />
                                    </div>

                                    <div class="text-slate-950 text-lg sm:text-2xl font-medium leading-loose">
                                        {{ $t('Enter OTP') }}
                                    </div>

                                    <div class="text-slate-950 mt-3 text-lg font-normal leading-7 tracking-tight">
                                        {{ $t('Please enter the verification code sent to your phone') }}
                                    </div>

                                    <div class="flex gap-3 mt-6">
                                        <input v-for="(input, index) in otpInputs" :key="index" :id="'input' + index"
                                            type="text" v-model="input.value" @input="handleOTPInput(index)"
                                            @keydown="handleOTPKeyDown(index, $event)" placeholder="-"
                                            class="text-base font-normal w-10 grow text-center p-3 placeholder:text-slate-400 rounded-lg border border-slate-200 focus:border-primary outline-none"
                                            maxlength="1">
                                    </div>

                                    <!-- Confirm button -->
                                    <button
                                        class="px-6 py-4 bg-primary mt-6 rounded-[10px] text-white text-base font-medium w-full"
                                        @click="verifyOTP">
                                        {{ $t('Confirm OTP') }}
                                    </button>

                                    <div v-if="otpTimer > 0" class="px-4 py-2 mt-6 flex items-center justify-center gap-2">
                                        <div class="text-slate-900 text-base font-normal leading-normal">
                                            {{ $t('Resend code in') }}
                                        </div>
                                        <div class="text-primary text-base font-normal leading-normal">
                                            00:{{ otpTimer }} sec
                                        </div>
                                    </div>
                                    <!-- Resend OTP -->
                                    <div v-else class="px-4 py-2 mt-6 flex items-center justify-center gap-2">
                                        <button class="text-primary text-base font-normal leading-normal"
                                            @click="resendOTP">
                                            {{ $t('Resend OTP') }}
                                        </button>
                                    </div>
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
import { faApple, faFacebook } from '@fortawesome/free-brands-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/solid'
import { onMounted, ref, watch } from 'vue'
import GoogleIcon from '../icons/Google.vue'
import ForgetPasswordDialogModal from './ForgetPasswordDialogModal.vue'
import RegistrationDialogModal from './RegistrationDialogModal.vue'
import ToastSuccessMessage from './ToastSuccessMessage.vue'
import { messaging, getToken } from '../firebase'; // Adjust the path as necessary

import { jwtDecode } from "jwt-decode"
import axios from 'axios'
import { useToast } from 'vue-toastification'
import { googleSdkLoaded } from 'vue3-google-login'
import { useAuth } from '../stores/AuthStore'
import { useBasketStore } from '../stores/BasketStore'
import { useMaster } from '../stores/MasterStore'

const toast = useToast();
const basketStore = useBasketStore();
const master = useMaster();

const AuthStore = useAuth();

const showLoginPassword = ref(false);
const firebaseAuthToken = ref(null); // Example initialization

const forgetPasswordDialog = ref(false);
const registerDialog = ref(false);

const showOTPDialog = ref(false);
const otpTimer = ref(0);
const pendingVerificationUser = ref(null);
const otpInputs = ref([
    { value: '' },
    { value: '' },
    { value: '' },
    { value: '' }
]);

const loginFormData = ref({
    phone: '',
    password: '',
    phone_code: null
});

onMounted(async () => {
    // if (master.app_environment == 'local') {
    //     loginFormData.value.phone = 'user@readyecommerce.com';
    //     loginFormData.value.password = 'secret';
    // }

    await fetchCountries();

    await loadFacebookSDK();
    initializeFB();
    try {
        const currentToken = await getToken(messaging, { vapidKey: 'BP5YfBd723K8G6X7wO-Wip-ITKh79cCPfHW6EUBRxAxmIj6PtSD2d4x5VuU5inVANG-YANBBgpNanyS5d-i-qdk' });
        if (currentToken) {
            firebaseAuthToken.value = currentToken;
            console.log('Device token:', currentToken);
        }
    } catch (error) {
        console.error('An error occurred while retrieving token. ', error);
    }
});

const showForgetPasswordDialog = () => {
    forgetPasswordDialog.value = true
    AuthStore.hideLoginModal();
}

const errors = ref({});

const content = {
    component: ToastSuccessMessage,
    props: {
        title: 'Login Successful',
        message: 'You have successfully logged in.',
    },
};

const countries = ref([]);

const fetchCountries = () => {
    axios.get('/countries').then((response) => {
        countries.value = response.data.data.countries;
        
        // Set default country to Romania
        const romania = countries.value.find(c => c.name === 'Romania');
        if (romania) {
            loginFormData.value.phone_code = romania.phone_code;
        }
    });
}

const loginFormSubmit = () => {
  axios.post('/login', {
    phone: loginFormData.value.phone,
    password: loginFormData.value.password,
    phone_code: loginFormData.value.phone_code,
    device_key: firebaseAuthToken.value
  }).then((response) => {
    // Check for 403 status and requires_verification flag
    if (response.status === 403 && response.data.data.requires_verification) {
      // Store user data temporarily for verification
      pendingVerificationUser.value = response.data.data.user;
      // Show OTP dialog
      showOTPDialog.value = true;
      startOTPTimer();
      AuthStore.hideLoginModal();
    } else {
      // User is already verified, proceed with login
      AuthStore.setToken(response.data.data.access.token.token);
      AuthStore.setUser(response.data.data.user);
      AuthStore.hideLoginModal();
      toast.success('Login Successful', {
        position: "bottom-left",
      });
    }
  }).catch((error) => {
    // Handle error response
    if (error.response && error.response.status === 403 && error.response.data.data.requires_verification) {
      // Store user data temporarily for verification
      pendingVerificationUser.value = error.response.data.data.user;
      // Show OTP dialog
      showOTPDialog.value = true;
      startOTPTimer();
      AuthStore.hideLoginModal();
    } else {
      toast.error(error.response.data.message, {
        position: "bottom-left",
      });
      errors.value = error.response.data.errors;
    }
  });
}

const showRegisterDialog = () => {
    AuthStore.hideLoginModal();
    registerDialog.value = true
}

const showLoginDialog = () => {
    registerDialog.value = false
    AuthStore.showLoginModal();
}

/**
 * Initiates the Google login process.
 *
 * Uses the Google Accounts JavaScript library to initialize a client that
 * requests authorization to access the user's email and profile information.
 * Once authorized, the client receives an authorization code which is then
 * sent to the backend to exchange for an access token.
 */
const googleLogin = () => {
    googleSdkLoaded((google) => {
        google.accounts.oauth2.initCodeClient({
            client_id: master.socialAuths.google.client_id,
            scope: 'email profile openid',
            redirect_uri: 'postmessage',
            callback: (response) => {
                if (response.code) {
                    sendCodeToBackend(response.code, 'google');
                }
            },
        }).requestCode();
    });
};

/**
 * Loads the Facebook SDK by appending a script tag to the document body.
 * @returns {Promise<void>}
 */
const loadFacebookSDK = () => {
    return new Promise((resolve) => {
        if (window.FB) {
            resolve();
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://connect.facebook.net/en_US/sdk.js';
        script.async = true;
        script.defer = true;
        script.onload = () => resolve();
        document.body.appendChild(script);
    });
};

/**
 * Initializes the Facebook SDK.
 * This function is called after the Facebook SDK has been loaded.
 * @see loadFacebookSDK
 */
const initializeFB = () => {
    window.fbAsyncInit = () => {
        FB.init({
            appId: master.socialAuths?.facebook?.client_id, // Replace with your Facebook App ID
            autoLogAppEvents: true,
            xfbml: true,
            version: 'v20.0', // Use the latest Graph API version
        });
    };
};

/**
 * Logs the user in with their Facebook account.
 * @returns {void}
 * @private
 */
const loginWithFacebook = () => {
    FB.login((response) => {
            if (response.authResponse) {
                FB.api('/me', { fields: 'name,email' }, (userInfo) => {
                    console.log('User Info:', userInfo);
                    // Handle login success here, such as sending info to your backend
                    sendCodeToBackend(response.authResponse?.accessToken, 'facebook', userInfo);
                });
            } else {
                console.error('User cancelled login or did not fully authorize.');
            }
        },
        { scope: 'public_profile,email' }
    );
};

/**
 * Loads the Apple ID SDK by appending a script tag to the document body.
 * @returns {Promise<void>}
 */
const loadAppleSDK = () => {
    return new Promise((resolve, reject) => {
        if (window.AppleID) {
            resolve();
            return;
        }
        const script = document.createElement('script');
        script.src = 'https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js';
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Failed to load Apple ID SDK'));
        document.body.appendChild(script);
    });
};

/**
 * Signs in with Apple using the Apple ID SDK.
 *
 * @returns {Promise<void>}
 */
const loginWithApple = async () => {
    try {
        await loadAppleSDK();

        window.AppleID.auth.init({
            clientId: master.socialAuths.apple?.client_id,
            scope: 'name email',
            redirectURI: master.socialAuths.apple.redirect_url,
            state: '123456',
            usePopup: true,
        });

        // Sign in with Apple
        const data = await window.AppleID.auth.signIn();
        const { authorization: { id_token: token, code } } = data;

        if (token && code) {
            const decoded = jwtDecode(token);
            sendCodeToBackend('1122', 'apple', decoded);
        } else {
            console.error('Token or code is missing');
        }
    } catch (error) {
        console.error('Error during sign in:', error);
    }
};

/**
 * Sends the authorization code to the backend to get an access token.
 *
 * @param {String} code - The authorization code
 * @param {String} provider - The provider ('google' or 'apple'), defaults to 'google'
 * @param {Object} data - Additional data to send with the request, defaults to empty object
 *
 * @returns {Promise<void>}
 */
async function sendCodeToBackend(code, provider = 'google', data = {}) {
    try {
        const response = await axios.post('/auth/' + provider + '/token', {
            code,
            data,
        });

        if (response.data?.data?.user) {
            AuthStore.setToken(response.data.data.access.token);
            AuthStore.setUser(response.data.data.user);
            AuthStore.hideLoginModal();
            toast.success('Login Successful', {
                position: "bottom-left",
            });
            basketStore.fetchCart();
        }
    } catch (error) {
        toast.error(error.response.data.message, {
            position: "bottom-left",
        });
    }
}

// Update phone input to only allow numbers
watch(() => loginFormData.value.phone, (newValue) => {
    // Remove any non-numeric characters
    loginFormData.value.phone = newValue.replace(/[^0-9]/g, '');
});

const startOTPTimer = () => {
    otpTimer.value = 60;
    const timer = setInterval(() => {
        if (otpTimer.value > 0) {
            otpTimer.value--;
        } else {
            clearInterval(timer);
        }
    }, 1000);
};

const handleOTPInput = (index) => {
    let nextIndex = index + 1;
    if (nextIndex < otpInputs.value.length && otpInputs.value[index].value != '') {
        nextTick(() => {
            const inputElement = document.getElementById('input' + nextIndex);
            if (inputElement) {
                inputElement.focus();
            }
        });
    }
};

const handleOTPKeyDown = (index, event) => {
    if (event.key === 'Backspace' && index > 0 && otpInputs.value[index].value === '') {
        let previousIndex = index - 1;
        if (previousIndex >= 0) {
            nextTick(() => {
                const inputElement = document.getElementById('input' + previousIndex);
                if (inputElement) {
                    inputElement.focus();
                }
            });
        }
    }
};

const verifyOTP = () => {
    const otp = otpInputs.value.map(input => input.value).join('');
    axios.post('/verify-otp-register', {
        phone: pendingVerificationUser.value.phone,
        otp: otp
    }).then((response) => {
        // Set token and user after successful verification
        AuthStore.setToken(response.data.data.access.token.token);
        AuthStore.setUser(response.data.data.user);
        
        toast.success('Phone number verified successfully!', {
            position: "bottom-left",
        });

        showOTPDialog.value = false;
        pendingVerificationUser.value = null;
        otpInputs.value.forEach(input => input.value = '');

    }).catch((error) => {
        toast.error(error.response.data.message, {
            position: "bottom-left",
        });
    });
};

const resendOTP = () => {
    if (!pendingVerificationUser.value) return;

    axios.post('/login', {
        phone: pendingVerificationUser.value.phone,
        password: loginFormData.value.password,
        phone_code: pendingVerificationUser.value.phone_code
    }).then(() => {
        startOTPTimer();
        toast.success('OTP resent successfully', {
            position: "bottom-left",
        });
    }).catch((error) => {
        toast.error(error.response.data.message, {
            position: "bottom-left",
        });
    });
};

</script>

<style scoped>
.phone-code-select {
    width: 100%;
}

.phone-code-select :deep(.vs__dropdown-toggle) {
    padding: 0 12px;
    border-radius: 0.5rem;
    border-color: #e2e8f0;
    background-color: white;
    height: 46px;
    display: flex;
    align-items: center;
}

.phone-code-select :deep(.vs__selected-options) {
    padding: 0;
    flex-wrap: nowrap;
}

.phone-code-select :deep(.vs__selected) {
    margin: 0;
    padding: 0;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}

.phone-code-select :deep(.vs__search) {
    padding: 0;
    margin: 0 0 0 8px;
    font-size: 14px;
}

.phone-code-select :deep(.vs__dropdown-menu) {
    min-width: 250px;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-top: 4px;
    z-index: 100;
    max-height: 300px;
    overflow-y: auto;
}

.phone-code-select :deep(.vs__dropdown-option) {
    padding: 8px 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.phone-code-select :deep(.vs__dropdown-option--highlight) {
    background: #f3f4f6;
    color: #000;
}

.phone-code-select :deep(.vs__actions) {
    padding: 0;
}

.phone-code-select :deep(.vs__clear) {
    display: none;
}

.phone-code-select :deep(.vs__open-indicator) {
    fill: #64748b;
}
</style>
