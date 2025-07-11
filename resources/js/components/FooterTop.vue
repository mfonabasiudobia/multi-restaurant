<template>
    <div class="text-gray-800 font-serif bg-gray-100">

        <div class="py-8 mx-18 sm:mx-4 lg:mx-18 md:mx-5 xs:mx-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <!--===== About Us (col-1) =====-->
            <div v-if="subpageStore.aboutSubpages.length">
                <div class="text-[#303030] text-lg font-semibold leading-normal tracking-tight">
                    {{ $t('about_us') }} <!-- About Us -->
                </div>
                <div class="mt-6 flex flex-col gap-2 font-sans font-normal text-sm">
                    <template v-for="subpage in subpageStore.aboutSubpages" :key="subpage.id">
                        <a :href="'/' + subpage.slug" class="text-gray-500 hover:text-primary-600">
                            {{ subpage.title }}
                        </a>
                    </template>
                </div>
            </div>

            <!--===== Customer Services (col-3) =====-->
            <div class="mt-4 lg:mt-0">
                <div class="text-gray-600 text-lg font-semibold leading-normal tracking-tight">{{ $t('customer_service') }}</div>
                <div class="mt-6 flex flex-col gap-2 font-sans font-normal text-sm">
                    <div class="flex items-center gap-2">
                        <DevicePhoneMobileIcon class="w-5 h-5 text-gray-500" />
                        <a href="tel:+40755511123" class="text-gray-500 hover:text-primary-600">+40 755 511 123</a>
                        <EnvelopeIcon class="w-5 h-5 text-gray-500 ml-4" />
                        <a :href="'mailto:suport@secondhub.ro?subject=Contact%20from%20SecondHub&body=Hello%20SecondHub%20Team'" class="text-gray-500 hover:text-primary-600">suport@secondhub.ro</a>
                    </div>
                    <div class="mt-4 text-gray-500">
                        {{ $t('customer_service_hours') }}
                    </div>
                    <div class="mt-2 text-gray-500 flex items-center gap-2">
                        <GlobeAltIcon class="w-5 h-5 text-gray-500" />
                        <span class="block">09:00 - 17:00</span>
                    </div>
                </div>
            </div>

            <!--===== Delivery and Return (col-4) =====-->
            <div class="mt-4 lg:mt-0">
                <div class="uppercase text-gray-600 text-lg font-semibold leading-normal tracking-tight">{{ $t('delivery_and_return') }}</div>
                <div class="mt-4 text-sm text-gray-500 font-normal flex flex-col gap-2 font-sans">
                    <span>{{ $t('fast_delivery') }}</span>
                    <span>{{ $t('return_policy') }}</span>
                </div>
            </div>

            <!--===== Information (col-2) =====-->
            <div class="mt-4 sm:mt-0" v-if="subpageStore.informationSubpages.length">
                <div class="text-gray-600 text-lg font-semibold leading-normal tracking-tight">{{ $t('information') }}</div>
                <div class="mt-6 flex flex-col gap-2 font-sans font-normal text-sm">
                    <template v-for="subpage in subpageStore.informationSubpages" :key="subpage.id">
                        <a :href="'/' + subpage.slug" class="text-gray-500 hover:text-primary-600">
                            {{ subpage.title }}
                        </a>
                    </template>
                </div>
            </div>

            <!--===== App Download (col-5) =====-->
            <div class="mt-4 lg:mt-0">
                <div class="text-gray-600 text-lg font-semibold leading-normal tracking-tight">{{ $t('download_our_app') }}</div>
                <span class="mt-4 text-gray-500">{{ $t('download_app_description') }}</span>
                <div class="mt-6 flex flex-row gap-2">
                    <button @click="appStore" class="flex items-center gap-2 p-1 bg-black text-white rounded-lg text-sm hover:bg-primary-700">
                        <img :src="'/assets/icons/appstoreFooter.svg'" alt="appstore" class="h-6 object-fill" loading="lazy" />
                        <span>{{ $t('download_on_app_store') }}</span>
                    </button>
                    <button @click="playStore" class="flex items-center gap-2 p-1 bg-black text-white rounded-lg text-sm hover:bg-primary-700">
                        <img :src="'/assets/icons/playstoreFooter.svg'" alt="appstore" class="h-6 object-fill" loading="lazy" />
                        <span>{{ $t('get_it_on_google_play') }}</span>
                    </button>
                </div>
            </div>

            <!--===== Follow Us and Payment Methods (col-6) =====-->
            <div class="mt-4 lg:mt-0">
                <div class="flex justify-start mt-5 items-center gap-6">
                    <div class="flex items-center gap-3">
                        <a v-for="socialLink in master.socialLinks" :key="socialLink.name" target="_blank" :href="socialLink.link" class="w-8 h-8 bg-primary-800 rounded-full overflow-hidden">
                            <img :src="socialLink.logo" alt="" class="w-full h-full object-cover">
                        </a>
                    </div>
                </div>
                <div class="mt-8">
                    <div class="text-gray-600 text-lg font-semibold leading-normal tracking-tight">{{ $t('payment_methods') }}</div>
                    <div class="mt-4 flex gap-4">
                        <img :src="'/assets/icons/rambus.svg'" alt="Rambus" class="h-10 scale-125" />
                        <img src="https://download.logo.wine/logo/Visa_Inc./Visa_Inc.-Logo.wine.png" alt="Visa" class="h-10" />
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/80/Maestro_2016.svg/616px-Maestro_2016.svg.png" alt="Mastercard" class="h-10" />
                        <img src="https://images.seeklogo.com/logo-png/8/2/master-card-logo-png_seeklogo-89117.png" alt="Maestro" class="h-10" />
                    </div>
                </div>
            </div>

        </div>

    </div>
</template>


<script setup>
import { DevicePhoneMobileIcon, EnvelopeIcon ,GlobeAltIcon} from '@heroicons/vue/24/outline';
import { onMounted } from 'vue';
import { useMaster } from "../stores/MasterStore";
const subpageStore = useSubpageStore();
import { useSubpageStore } from "../stores/SubpageStore";

onMounted(() => {
    subpageStore.fetchSubpages();
});
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

</script>
