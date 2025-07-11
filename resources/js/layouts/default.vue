<template>
    <div class="min-h-screen flex flex-col">
        <!-- Fixed Header Section -->
        <header class="w-full">
            <!-- Fixed Header Container -->
            <div class="">
                
                <Navbar />
            </div>
            <!-- Header Spacer -->
            
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            
            <slot />
        </main>

        <!-- Footer Section -->
        <footer>
            <FooterTop />
            <Footer />
        </footer>

        <!-- Floating Components -->
        <BasketCard />
    </div>
</template>

<script setup>
import { onMounted, watch, ref, computed } from "vue";
import { useRoute } from 'vue-router';
import TopHeader from "../components/TopHeader.vue"
import Navbar from "../components/Navbar.vue";
import Footer from "../components/Footer.vue";
import BasketCard from '../components/BasketCard.vue';
import HeroBanner from '../components/HeroBanner.vue';
import { useMaster } from '../stores/MasterStore';
import { useAuth } from '../stores/AuthStore';
import axios from 'axios';

const master = useMaster();
const auth = useAuth();
const route = useRoute();
const banners = ref([]);

// Check if we're on the home page
const isHomePage = computed(() => route.name === 'home');

onMounted(async () => {
    window.scrollTo(0, 0);
    setupThemeColors();
    await fetchBanners();
});

watch(() => master.themeColors, () => {
    setupThemeColors();
})

const areThemeColorsValid = () => {
    const colorProperties = [
        'primary', 'primary50', 'primary100', 'primary200', 'primary300',
        'primary400', 'primary500', 'primary600', 'primary700', 'primary800',
        'primary900', 'primary950'
    ];

    for (const color of colorProperties) {
        if (!master.themeColors[color]) {
            return false;
        }
    }
    return true;
}

const setupThemeColors = () => {
    if (!areThemeColorsValid()) {
        return;
    }
    document.documentElement.style.setProperty('--primary', master.themeColors.primary);
    document.documentElement.style.setProperty('--primary-50', master.themeColors.primary50);
    document.documentElement.style.setProperty('--primary-100', master.themeColors.primary100);
    document.documentElement.style.setProperty('--primary-200', master.themeColors.primary200);
    document.documentElement.style.setProperty('--primary-300', master.themeColors.primary300);
    document.documentElement.style.setProperty('--primary-400', master.themeColors.primary400);
    document.documentElement.style.setProperty('--primary-500', master.themeColors.primary500);
    document.documentElement.style.setProperty('--primary-600', master.themeColors.primary600);
    document.documentElement.style.setProperty('--primary-700', master.themeColors.primary700);
    document.documentElement.style.setProperty('--primary-800', master.themeColors.primary800);
    document.documentElement.style.setProperty('--primary-900', master.themeColors.primary900);
    document.documentElement.style.setProperty('--primary-950', master.themeColors.primary950);
}

const fetchBanners = async () => {
    try {
        const response = await axios.get('/banners')
        master.banners = response.data.data
    } catch (error) {
        console.error('Error fetching banners:', error)
    }
}

</script>

<style scoped>
/* Add any additional styling needed */
</style>
