<template>
    <div>
        <HeroBanner v-if="banners.length > 0" :banners="banners" />
        <AdsBanner :ads="ads" />
        
        <div v-if="incomingFlashSale">
            <FlashSaleIncoming :flashSale="incomingFlashSale" />
        </div>
        <div v-if="runningFlashSale">
            <FlashSaleRunning :flashSale="runningFlashSale" />
        </div>
        <LatestProducts :products="latestProducts" />
        <FilterCategories />
        <RecentlyViews :products="recentlyViewProducts" />
        <InfoCards />
        <BecomeSellerSection />
        <AppDownloadPopup />
        <WhatsAppNotificationPopup />
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import AboutSupport from "../components/AboutSupport.vue";
import Categories from "../components/Categories.vue";
import FlashSaleIncoming from "../components/FlashSaleIncoming.vue";
import FlashSaleRunning from "../components/FlashSaleRunning.vue";
import HeroBanner from "../components/HeroBanner.vue";
import JustForYou from "../components/JustForYou.vue";
import RecentlyViews from "../components/RecentlyViews.vue";
import TopRatedShops from "../components/TopRatedShops.vue";
import { useBasketStore } from "../stores/BasketStore";
import { useMaster } from "../stores/MasterStore";
import InfoCards from '../components/InfoCard.vue'
import axios from "axios";
import { useAuth } from "../stores/AuthStore";
import AdsBanner from "../components/AdsBanner.vue";
import LatestProducts from '../components/LatestProducts.vue';
import FilterCategories from '../components/FilterCategories.vue';
import AppDownloadPopup from '../components/AppDownloadPopup.vue';
import WhatsAppNotificationPopup from '../components/WhatsAppNotificationPopup.vue';
import BecomeSellerSection from '../components/BecomeSellerSection.vue';

const master = useMaster();
const basketStore = useBasketStore();

const authStore = useAuth();
const banners = ref([]);
onMounted(async () => {
    getData();
    master.fetchData();
    basketStore.fetchCart();
    fetchViewProducts();
    master.basketCanvas = false;
    authStore.loginModal = false;
    authStore.registerModal = false;
    authStore.showAddressModal = false;
    authStore.showChangeAddressModal = false;
    fetchBanners();
});
const fetchBanners = async () => {
    try {
        const response = await axios.get("/banners");
        if (response.data.data?.banners) {
            banners.value = response.data.data.banners;
        }
    } catch (error) {
        console.error("Error fetching banners:", error);
    }
};
const banner = ref([]);
const categories = ref([]);
const incomingFlashSale = ref(null);
const runningFlashSale = ref(null);
const topRatedShops = ref([]);
const justForYou = ref([]);
const recentlyViewProducts = ref([]);
const ads = ref([]);
const latestProducts = ref([]);

const getData = () => {
    axios.get("/home?page=1&per_page=12", {
        headers: {
            Authorization: authStore.token,
        },
    }).then((response) => {
        ads.value = response.data.data.ads;
        banner.value = response.data.data.banners;
        categories.value = response.data.data.categories;
        master.categories = response.data.data.categories;
        justForYou.value = response.data.data.just_for_you;
        topRatedShops.value = response.data.data.shops.slice(0, 4);
        incomingFlashSale.value = response.data.data.incoming_flash_sale;
        runningFlashSale.value = response.data.data.running_flash_sale;
        latestProducts.value = response.data.data.latest_products;
        master.filter_categories = response.data.data.filter_categories;
        console.log("Filter categories in Home:", response.data.data.filter_categories);
    }).catch((error) => {
        console.error("Error fetching data:", error);
        if (error.response?.status === 401) {
            authStore.token = null;
            authStore.user = null;
            authStore.addresses = [];
        }
    });

    // fetch categories
    // axios.get("/categories?page=1&per_page=10&all=1").then((response) => {
    //     master.categories = response.data.data.categories;
    //     console.log("categories", response.data.data);
    // }).catch(() => { });
};

const fetchViewProducts = () => {
    if (authStore.token) {
        axios.get("/recently-views", {
            headers: {
                Authorization: authStore.token,
            },
        }).then((response) => {
            recentlyViewProducts.value = response.data.data.products;
            console.log("recentlyViewProducts");
        }).catch((error) => {
            if (error.response.status === 401) {
                authStore.token = null;
                authStore.user = null;
                authStore.addresses = [];
            }
        });
    }
};
</script>

<style scoped></style>
