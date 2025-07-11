<template>
    <div class="margin-container">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-4">{{ $t('Catalog de Produse') }}</h2>
            <p class="text-gray-600 max-w-3xl mx-auto">
                {{ $t('Descoperiți produsele noastre organizate după calitate și anotimp. Alegeți categoria dorită pentru a găsi produsele care se potrivesc nevoilor dumneavoastră.') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Quality Section -->
            <div class="group bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="relative h-56 overflow-hidden bg-gradient-to-br from-primary-100 via-primary-200 to-primary-300">
                    <!-- Quality Icon -->
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <svg class="w-28 h-28 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" 
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $t('Calitate') }}</h3>
                        <span class="px-4 py-1 bg-primary-50 text-primary-600 rounded-full text-sm font-medium">
                            {{ getTotalQualityProductCount() }} {{ $t('articole') }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <router-link 
                            v-for="quality in getQualityItems()"
                            :key="quality.id"
                            :to="{
                                name: 'filtered-products',
                                query: { quality: quality.id }
                            }"
                            @click.native="scrollToTop"
                            class="flex items-center justify-between p-4 rounded-xl bg-gray-50 hover:bg-primary hover:text-white transition-all duration-300"
                        >
                            <span class="font-medium">{{ quality.name }}</span>
                            <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full">{{ quality.count }}</span>
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- Seasons Section -->
            <div class="group bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="relative h-56 overflow-hidden bg-gradient-to-br from-[#FFB6C1] via-[#FFA07A] to-[#FFD700]">
                    <!-- Seasons Icon -->
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                        <svg class="w-28 h-28 text-white opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" 
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800">{{ $t('Anotimpuri') }}</h3>
                        <span class="px-4 py-1 bg-pink-50 text-pink-600 rounded-full text-sm font-medium">
                            {{ getTotalSeasonProductCount() }} {{ $t('articole') }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <router-link 
                            v-for="season in getSeasonItems()"
                            :key="season.id"
                            :to="{
                                name: 'filtered-products',
                                query: { season: season.id }
                            }"
                            @click.native="scrollToTop"
                            class="flex items-center justify-between p-4 rounded-xl bg-gray-50 hover:bg-[#FFA07A] hover:text-white transition-all duration-300"
                        >
                            <span class="font-medium">{{ season.name }}</span>
                            <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full">{{ season.count }}</span>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useMaster } from '../stores/MasterStore'
import { onMounted, computed, ref } from 'vue'
import axios from 'axios'

const router = useRouter()
const master = useMaster()
const qualityItems = ref([])
const seasonItems = ref([])

// Function to scroll to top of page
const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    })
}

// Direct access to filter_categories data
const getQualityItems = () => {
    console.log("Accessing quality items");
    return qualityItems.value;
}

// Direct access to season items
const getSeasonItems = () => {
    console.log("Accessing season items");
    return seasonItems.value;
}

// Get total count of products across all qualities
const getTotalQualityProductCount = () => {
    return qualityItems.value.reduce((total, item) => total + (parseInt(item.count) || 0), 0);
}

// Get total count of products across all seasons
const getTotalSeasonProductCount = () => {
    return seasonItems.value.reduce((total, item) => total + (parseInt(item.count) || 0), 0);
}

// Fetch data directly from API if needed
const fetchFilterCategories = async () => {
    try {
        console.log("Fetching filter categories directly");
        const response = await axios.get('/filter-categories');
        console.log("Direct API Response:", response.data);
        
        if (response.data.status === 'success' && response.data.data && response.data.data.filter_categories) {
            const filterData = response.data.data.filter_categories;
            
            if (filterData.qualities && filterData.qualities.items) {
                qualityItems.value = filterData.qualities.items;
                console.log("Quality items set from direct API:", qualityItems.value);
            }
            
            if (filterData.seasons && filterData.seasons.items) {
                seasonItems.value = filterData.seasons.items;
                console.log("Season items set from direct API:", seasonItems.value);
            }
        }
    } catch (error) {
        console.error("Error fetching filter categories:", error);
    }
}

onMounted(async () => {
    console.log("Component mounted");
    
    // First try to fetch directly from our dedicated endpoint
    await fetchFilterCategories();
    
    // If we got data, we're done
    if (qualityItems.value.length > 0 && seasonItems.value.length > 0) {
        console.log("Successfully loaded data from direct API");
        return;
    }
    
    // Otherwise try from master store
    console.log("Master store data:", master);
    console.log("Seasons:", master.seasons);
    console.log("Qualities:", master.qualities);
    console.log("Filter Categories:", master.filter_categories);
    
    // Try to get data from master store
    if (master.filter_categories) {
        console.log("Using filter_categories from master store");
        
        if (master.filter_categories.qualities && master.filter_categories.qualities.items) {
            qualityItems.value = master.filter_categories.qualities.items;
            console.log("Quality items set from store:", qualityItems.value);
        }
        
        if (master.filter_categories.seasons && master.filter_categories.seasons.items) {
            seasonItems.value = master.filter_categories.seasons.items;
            console.log("Season items set from store:", seasonItems.value);
        }
    }
    
    // If no data in store, try to refresh master data
    if (qualityItems.value.length === 0 || seasonItems.value.length === 0) {
        console.log("No data in store, refreshing master data");
        await master.fetchData();
        
        // Check again after refresh
        if (master.filter_categories) {
            if (master.filter_categories.qualities && master.filter_categories.qualities.items) {
                qualityItems.value = master.filter_categories.qualities.items;
                console.log("Quality items set after refresh:", qualityItems.value);
            }
            
            if (master.filter_categories.seasons && master.filter_categories.seasons.items) {
                seasonItems.value = master.filter_categories.seasons.items;
                console.log("Season items set after refresh:", seasonItems.value);
            }
        }
    }
    
    // Fallback to using qualities and seasons arrays if filter_categories is not available
    if (qualityItems.value.length === 0 && master.qualities && master.qualities.length > 0) {
        console.log("Using fallback for qualities");
        qualityItems.value = master.qualities.map(quality => ({
            id: quality.id,
            name: quality.name,
            count: 0
        }));
    }
    
    if (seasonItems.value.length === 0 && master.seasons && master.seasons.length > 0) {
        console.log("Using fallback for seasons");
        seasonItems.value = master.seasons.map(season => ({
            id: season.id,
            name: season.name,
            count: 0
        }));
    }
    
    console.log("Final quality items:", qualityItems.value);
    console.log("Final season items:", seasonItems.value);
})
</script>

<style scoped>
.router-link-active {
    @apply bg-primary text-white;
}

/* Smooth hover transitions */
.group {
    transform: translateY(0);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.group:hover {
    transform: translateY(-4px);
}

/* Gradient animation */
@keyframes gradientFlow {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.bg-gradient-to-br {
    background-size: 200% 200%;
    animation: gradientFlow 15s ease infinite;
}
</style> 