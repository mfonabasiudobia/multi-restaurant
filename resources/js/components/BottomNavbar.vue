<template>
    <div
        class="main-container flex items-center justify-between md:gap-3 lg:gap-8 border-t border-b border-slate-100 flex-wrap md:flex-nowrap relative bg-gray-100 rounded-lg">

        <!-- <div class="flex flex-wrap">
            <div v-for="category in master.categories" :key="category.id" class="">
                <MenuCategory :category="category" />
            </div>
        </div> -->

        <!-- Main menu -->
        <div class="hidden md:inline-flex justify-start items-center gap-2.5 lg:gap-4 xl:gap-8 grow">
            <router-link to="/" class="h-10 py-2 border-b-2 border-transparent text-base font-normal text-slate-600">
                {{ $t('Home') }}
            </router-link>

            <div class="w-[0px] h-4 border border-slate-200"></div>

            <router-link v-if="master.getMultiVendor" to="/shops"
                class="h-10 py-2 border-b-2 border-transparent text-base font-normal text-slate-600">
                {{ $t('Shops') }}
            </router-link>

            <div v-if="master.getMultiVendor" class="w-[0px] h-4 border border-slate-200"></div>

            <router-link to="/most-popular"
                class="h-10 py-2 border-b-2 border-transparent text-base font-normal text-slate-600">
                {{ $t('Most Popular') }}
            </router-link>

            <div class="w-[0px] h-4 border border-slate-200"></div>

            <router-link to="/best-deal"
                class="h-10 py-2 border-b-2 border-transparent text-base font-normal text-slate-600">
                {{ $t('Best Deal') }}
            </router-link>

            <div class="w-[0px] h-4 border border-slate-200"></div>

            <router-link to="/contact-us"
                class="h-10 py-2 border-b-2 border-transparent text-base font-normal text-slate-600">
                {{ $t('Contact') }}
            </router-link>

        </div>

        <!-- Download our app -->
        <div v-if="master.showDownloadApp" class="inline-block">
            <Menu as="div" class="relative text-left" v-slot="{ open }">
                <div>
                    <MenuButton class="flex items-center gap-1 lg:gap-2 pr-1 lg:pr-3 p-3 rounded-lg"
                        :class="open ? 'bg-primary-100 text-primary' : 'text-slate-600'">
                        <DevicePhoneMobileIcon class="w-4 h-5" />
                        <div class="text-base font-normal leading-normal">{{ $t('Download our app') }}</div>
                        <ChevronDownIcon class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" />
                    </MenuButton>
                </div>

                <transition enter-active-class="transition ease-out duration-100"
                    enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <MenuItems
                        class="absolute right-0 z-10 mt-0 lg:w-full origin-top-right p-3 bg-white rounded-xl shadow border border-primary-300  ring-1 ring-black ring-opacity-5 focus:outline-none">
                        <div class="flex-col flex gap-2">
                            <MenuItem v-slot="{ active }">
                            <button :class="active ? 'bg-gray-100 text-gray-900' : 'text-gray-700'" @click="playStore">
                                <img :src="'/assets/icons/playstore.png'" alt="">
                            </button>
                            </MenuItem>

                            <MenuItem v-slot="{ active }">
                            <button :class="active ? 'bg-gray-100 text-gray-900' : 'text-gray-700'" @click="appStore">
                                <img :src="'/assets/icons/applestore.png'" alt="">
                            </button>
                            </MenuItem>
                        </div>
                    </MenuItems>
                </transition>
            </Menu>

        </div>

    </div>
</template>

<script setup>
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue'
import { DevicePhoneMobileIcon, ChevronDownIcon } from '@heroicons/vue/24/outline'
import MenuCategory from './MenuCategory.vue';
import MenuIcon from '../icons/Menu.vue';

import { useMaster } from "../stores/MasterStore";
const master = useMaster();

const appStore = () => {
    if (master.appStoreLink) {
        window.open(master.appStoreLink, '_blank');
    }
}

const playStore = () => {
    if (master.playStoreLink) {
        window.open(master.playStoreLink, '_blank');
    }
}

const hiddenPopover = () => {
   open = false
}

</script>

<style scoped>
.router-link-active {
    @apply border-b-2 border-primary text-primary
}
</style>
