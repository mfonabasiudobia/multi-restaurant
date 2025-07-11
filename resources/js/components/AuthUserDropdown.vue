<template>
    <Menu as="div" class="relative hidden md:inline-block text-left" v-slot="{ open }">
        <div>
            <MenuButton class="inline-flex w-full items-center gap-2 p-2 rounded-lg"
                :class="open ? 'bg-primary-100 text-primary' : 'text-slate-600'">
                <span class="max-w-24 truncate text-base font-normal leading-normal">
                    {{ authStore.user?.name }}
                </span>
                <div class="relative">
                    <img :src="authStore.user?.profile_photo" alt="" class="w-8 h-8 object-cover rounded-full">
                    <div class="w-4 h-4 absolute -bottom-2 right-2/4 translate-x-2/4 rounded-2xl"
                        :class="open ? 'bg-primary-100' : 'bg-white'">
                        <ChevronDownIcon class="w-4 h-4" />
                    </div>
                </div>
            </MenuButton>
        </div>

        <transition enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95">
            <MenuItems
                class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg border border-primary-300">
                <div class="py-3 px-4 flex flex-col gap-2">
                    <MenuItem>
                    <router-link to="/dashboard"
                        class="flex gap-2 text-slate-600 text-base py-2 hover:text-primary menuLinkItem">
                        <DashboardIcon width="24" height="24" colorClass="currentColor"/> {{ $t('Dashboard') }}
                    </router-link>
                    </MenuItem>

                    <MenuItem>
                    <router-link to="/change-password"
                        class="flex gap-2 text-slate-600 text-base py-2 hover:text-primary menuLinkItem">
                        <KeyIcon width="24" height="24" colorClass="currentColor"/> {{ $t('Change Password') }}
                    </router-link>
                    </MenuItem>

                    <MenuItem>
                    <button class="flex gap-2 text-slate-600 text-base py-2 hover:text-primary menuLinkItem"
                        @click="logoutModal = true">
                        <LogoutIcon width="24" height="24" colorClass="currentColor" /> {{ $t('Log Out') }}
                    </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>

    <div class="block md:hidden mobile-menu" ref="mobileMenuContainer">
        <button @click="toggleMobileMenu" 
                class="flex items-center gap-2 p-2 rounded-lg">
            <span class="truncate text-base font-normal leading-normal">
                {{ authStore.user?.name }}
            </span>
            <div class="relative">
                <img :src="authStore.user?.profile_photo" alt="" class="w-8 h-8 object-cover rounded-full">
                <div class="w-4 h-4 absolute -bottom-2 right-2/4 translate-x-2/4 rounded-2xl bg-white">
                    <ChevronDownIcon class="w-4 h-4" :class="showMobileMenu ? 'rotate-180' : ''" />
                </div>
            </div>
        </button>

        <div v-show="showMobileMenu" 
             class="fixed inset-x-0 top-[120px] bg-white shadow-lg border-t border-gray-200 z-50">
            <div class="p-4">
                <!-- User Info -->
                <div class="flex items-center gap-3 p-3 mb-2 bg-gray-50 rounded-lg">
                    <img :src="authStore.user?.profile_photo" alt="" class="w-12 h-12 object-cover rounded-full">
                    <div>
                        <div class="font-medium text-gray-900">{{ authStore.user?.name }}</div>
                        <div class="text-sm text-gray-500">{{ authStore.user?.email }}</div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="space-y-1">
                    <router-link to="/dashboard"
                        class="flex items-center justify-between p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                        @click="showMobileMenu = false">
                        <span class="flex items-center gap-3">
                            <DashboardIcon width="20" height="20" colorClass="currentColor"/>
                            <span>{{ $t('Dashboard') }}</span>
                        </span>
                        <ChevronRightIcon class="w-5 h-5 text-gray-400" />
                    </router-link>

                    <router-link to="/change-password"
                        class="flex items-center justify-between p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                        @click="showMobileMenu = false">
                        <span class="flex items-center gap-3">
                            <KeyIcon width="20" height="20" colorClass="currentColor"/>
                            <span>{{ $t('Change Password') }}</span>
                        </span>
                        <ChevronRightIcon class="w-5 h-5 text-gray-400" />
                    </router-link>

                    <button @click="logoutModal = true"
                        class="w-full flex items-center justify-between p-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <span class="flex items-center gap-3">
                            <LogoutIcon width="20" height="20" colorClass="currentColor"/>
                            <span>{{ $t('Log Out') }}</span>
                        </span>
                        <ChevronRightIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logout modal -->
    <TransitionRoot as="template" :show="logoutModal">
        <Dialog as="div" class="relative z-10" @close="logoutModal = false">
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

                                <div class="bg-red-500 w-20 h-20 rounded-full mx-auto flex justify-center items-center">
                                    <img :src="'/assets/icons/logoutWhite.svg'" alt="icon" loading="lazy" />
                                </div>

                                <div
                                    class="mt-3 text-center text-gray-900 text-3xl font-bold font-['Roboto'] leading-9">
                                    {{ $t('Log Out') }}!</div>

                                <div
                                    class="mt-4 text-center text-slate-700 text-xl font-normal font-['Roboto'] leading-7">
                                    {{ $t('logout_confirmation') }}
                                </div>

                                <div class="flex justify-between items-center gap-4 mt-8">
                                    <button
                                        class="text-slate-800 grow text-base font-medium  px-6 py-4 rounded-[10px] border border-slate-300"
                                        @click="logoutModal = false">{{ $t('Cancel') }}</button>

                                    <button
                                        class="text-white grow bg-red-500 text-base font-medium px-6 py-4 rounded-[10px]"
                                        @click="logout">{{ $t('Yes') }}</button>
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
import { Dialog, DialogPanel, Menu, MenuButton, MenuItem, MenuItems, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { ChevronDownIcon, ChevronRightIcon } from '@heroicons/vue/20/solid'
import { ref, onMounted, onUnmounted } from 'vue'

import { useToast } from 'vue-toastification'
import DashboardIcon from '../icons/Dashboard.vue'
import KeyIcon from '../icons/Key.vue'
import LogoutIcon from '../icons/Logout.vue'
import { useAuth } from '../stores/AuthStore'
import { useBasketStore } from '../stores/BasketStore'


const authStore = useAuth();
const basketStore = useBasketStore();

const toast = useToast();

const logoutModal = ref(false)
const showMobileMenu = ref(false)
const mobileMenuContainer = ref(null)

const toggleMobileMenu = () => {
    showMobileMenu.value = !showMobileMenu.value
}

const logout = () => {
    authStore.logout();
    basketStore.total = 0;
    basketStore.checkoutProducts = [];
    basketStore.products = [];
    basketStore.address = null;
    basketStore.selectedShopIds = [];
    basketStore.coupon_code = '';
    basketStore.payable_amount = 0;
    basketStore.delivery_charge = 0;
    basketStore.coupon_discount = 0;

    toast.success('Logout successfully', {
        position: "bottom-left",
    });
}

const handleClickOutside = (event) => {
    if (mobileMenuContainer.value && 
        !mobileMenuContainer.value.contains(event.target) && 
        showMobileMenu.value) {
        showMobileMenu.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<style>
.menuLinkItem:hover img {
    filter: brightness(0) saturate(100%) invert(39%) sepia(96%) saturate(6525%) hue-rotate(256deg) brightness(97%) contrast(91%);
}
</style>
