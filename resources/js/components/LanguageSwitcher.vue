<template>
    <div class="relative">
        <button @click="isOpen = !isOpen" 
                class="flex items-center space-x-1 text-gray-700 hover:text-primary uppercase text-sm">
            <span>{{ currentLanguage }}</span>
            <svg class="w-4 h-4" :class="{ 'rotate-180': isOpen }" 
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <div v-if="isOpen" 
             class="absolute right-0 mt-2 py-1 w-24 bg-white rounded-md shadow-lg z-50 border border-gray-100">
            <a v-for="lang in availableLanguages" 
               :key="lang.name"
               @click="changeLanguage(lang.name)"
               class="block px-4 py-1 text-sm text-gray-700 hover:bg-gray-50 cursor-pointer uppercase">
                {{ lang.name }}
            </a>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, onUnmounted } from 'vue'
import { useMaster } from '../stores/MasterStore'
import localization from '../localization'

const master = useMaster()
const isOpen = ref(false)
const currentLanguage = ref('Română')
const isMobile = ref(false)

const availableLanguages = computed(() => {
    return master.languages || []
})

onMounted(() => {
    const storedLocale = localStorage.getItem('locale') || 'ro'
    setCurrentLanguage(storedLocale)
    master.locale = storedLocale
    
    if (storedLocale === 'ro') {
        localization.fetchLocalizationData()
    }
    
    document.addEventListener('click', closeDropdown)
    checkMobile()
    window.addEventListener('resize', checkMobile)
})

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown)
    window.removeEventListener('resize', checkMobile)
})

function checkMobile() {
    isMobile.value = window.innerWidth < 768
}

watch(() => master.locale, (newLocale) => {
    if (newLocale) {
        setCurrentLanguage(newLocale)
    }
})

const setCurrentLanguage = (lang) => {
    const language = master.languages?.find(l => l.name === lang)
    if (language) {
        currentLanguage.value = language.title
    }
}

const changeLanguage = async (lang) => {
    try {
        master.locale = lang
        localStorage.setItem('locale', lang)
        await localization.fetchLocalizationData()
        isOpen.value = false
    } catch (error) {
        console.error('Error changing language:', error)
    }
}

const closeDropdown = (event) => {
    if (!event.target.closest('.relative')) {
        isOpen.value = false
    }
}
</script>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
}

/* Mobile specific styles */
@media (max-width: 767px) {
    .relative {
        position: static;
    }
    
    /* Make dropdown appear below on mobile */
    div[v-if="isOpen"] {
        position: fixed;
        left: 50%;
        transform: translateX(-50%);
        width: auto;
        min-width: 120px;
    }
}
</style> 