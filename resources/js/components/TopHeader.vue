<template>
    <div class="w-full bg-white">
        <!-- App Download Bar - Hide on Mobile -->
        <div class="bg-gray-50 border-b hidden md:block">
            <div class="margin-container flex items-center justify-between py-2">
                <!-- Logo -->
                <router-link to="/" class="flex-shrink-0">
                    <img :src="master.logo" alt="Logo" class="h-8">
                </router-link>

                <!-- Center Text and Store Links -->
                <div class="flex items-center justify-center flex-grow gap-4">
                    <span class="text-sm font-medium text-gray-700 cursor-pointer hover:text-primary" @click="playStore">
                        {{ $t('available_on_ios_android') }}
                    </span>
                    <a @click="playStore" target="_blank" class="transition-transform hover:scale-105 cursor-pointer">
                        <img :src="'/assets/icons/playstore.png'" alt="Play Store Logo" class="h-[35px] w-auto">
                    </a>
                    <a @click="appStore" target="_blank" class="transition-transform hover:scale-105 cursor-pointer">
                        <img :src="'/assets/icons/applestore.png'" alt="App Store Logo" class="h-[35px] w-auto">
                    </a>
                </div>
                
                <!-- Right Side Links -->
                <div class="flex items-center space-x-4">
                    <a href="/shop/register" 
                       class="text-sm text-gray-700 hover:text-primary whitespace-nowrap underline">
                        {{ $t('become_seller') }}
                    </a>
                    <LanguageSwitcher />
                </div>
            </div>
        </div>

        <!-- Mobile Header -->
       

        <!-- Mobile Categories Scroll -->
    

        <!-- Main Navigation -->
        <div class="bg-white border-b">
            <div class="margin-container flex items-center justify-between py-4">
                <!-- Categories -->
                <div class="hidden md:flex items-center space-x-6">
                    <div v-for="category in master.categories" 
                         :key="category.id" 
                         class="relative group">
                        <button @click="navigateToCategory(category)"
                                :data-category="category.id"
                                class="text-gray-700 hover:text-primary uppercase text-sm tracking-wide font-medium px-3 py-2 transition-colors">
                            {{ category.name }}
                        </button>
                        <!-- Dropdown Menu -->
                       
                    </div>
                </div>
                
                <!-- Mobile Logo and Search - Updated Layout -->
                <div class="block sm:hidden flex items-center flex-1">
                    <router-link to="/" class="flex-shrink-0 mr-2">
                        <img :src="master.footerLogo" alt="Logo" class="h-6">
                    </router-link>
                    
                    <div class="relative flex-1">
                        <input type="text" 
                               v-model="search"
                               :placeholder="$t('search_products')"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary bg-gray-50 text-sm"
                               @keyup.enter="searchProducts()"
                        >
                        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 hover:text-primary transition-colors"
                                @click="searchProducts()">
                            <MagnifyingGlassIcon class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <!-- Desktop Search Bar -->
                <div class="hidden md:block flex-1 max-w-2xl mx-6">
                    <div class="relative">
                        <input type="text" 
                               v-model="search"
                               :placeholder="$t('find_clothes')"
                               class="w-full px-4 py-2.5 border rounded-lg focus:outline-none focus:border-primary bg-gray-50"
                               @keyup.enter="searchProducts()"
                        >
                        <button class="absolute right-3 top-1/2 transform -translate-y-1/2 hover:text-primary transition-colors"
                                @click="searchProducts()">
                            <MagnifyingGlassIcon class="h-5 w-5" />
                        </button>
                    </div>
                </div>

                <!-- Right Icons -->
                <div class="flex items-center space-x-4">
                    <!-- <button class="p-2 hover:text-primary transition-colors" @click="showWishlist()">
                        <HeartIcon class="h-6 w-6" />
                    </button> -->
                    
                    <button class="p-2 relative hover:text-primary transition-colors" @click="master.basketCanvas = true">
                        <ShoppingBagIcon class="h-6 w-6" />
                        <span v-if="basketStore.selectedProducts.length > 0"
                            class="absolute -top-2 -right-2 w-5 h-5 bg-primary rounded-full text-white text-xs flex items-center justify-center">
                            {{ basketStore.selectedProducts.length }}
                        </span>
                    </button>

                    <button v-if="!AuthStore.user" 
                            @click="showLoginDilog" 
                            class="p-2 hover:text-primary transition-colors">
                        <UserIcon class="h-6 w-6" />
                    </button>
                    <div v-else>
                        <AuthUserDropdown />
                    </div>
                    <div class=" sm:hidden">
                        <LanguageSwitcher />
                    </div>
                </div>
            </div>
        </div>
        <div class="md:hidden overflow-x-auto whitespace-nowrap px-4 py-3 bg-white border-b">
            <div class="flex space-x-4">
                <div v-for="category in master.categories" 
                     :key="category.id"
                     @click="navigateToCategory(category)"
                     class="flex flex-col items-center space-y-1">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                        <img v-if="category.thumbnail" :src="category.thumbnail" :alt="category.name" class="w-6 h-6">
                        <div v-else class="w-6 h-6 bg-gray-300 rounded-full"></div>
                    </div>
                    <span class="text-xs text-gray-700">{{ category.name }}</span>
                </div>
            </div>
        </div>


        <!-- Login Dialog Modal -->
        <LoginModal />

    </div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue'
import { Dialog, DialogPanel } from '@headlessui/vue'
import { 
    MagnifyingGlassIcon, 
    UserIcon, 
    HeartIcon, 
    ShoppingBagIcon,
    Bars3Icon,
    XMarkIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline'
import { useBasketStore } from '../stores/BasketStore'
import { useMaster } from '../stores/MasterStore'
import { useAuth } from '../stores/AuthStore'
import { useRouter } from 'vue-router'
import AuthUserDropdown from './AuthUserDropdown.vue'
import LoginModal from './LoginModal.vue'
import localization from '../localization'
import LanguageSwitcher from './LanguageSwitcher.vue'

const router = useRouter()
const master = useMaster()
const basketStore = useBasketStore()
const AuthStore = useAuth()
const mobileMenuOpen = ref(false)
const search = ref('')
const currentLanguage = ref('English')
const selectedCategory = ref(null)
const dropdownContainer = ref(null)
console.log("categories", master.categories);

onMounted(() => {
    setCurrentLanguage(master.locale)
    document.addEventListener('click', handleClickOutside)
    window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
    window.removeEventListener('scroll', handleScroll)
})

watch(() => master.locale, () => {
    setCurrentLanguage(master.locale)
})
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
const setCurrentLanguage = (lang) => {
    master.locale = lang
    localStorage.setItem('locale', lang)
    localization.fetchLocalizationData()
    const language = master.languages.find(lang => lang.name === master.locale)
    if (language) {
        currentLanguage.value = language.title
    }
}

const showLoginDilog = () => {
    AuthStore.showLoginModal()
}

const showWishlist = () => {
    mobileMenuOpen.value = false
    if (!AuthStore.token) {
        return showLoginDilog()
    } else {
        router.push('/wishlist')
    }
}

const searchProducts = () => {
    master.search = search.value
    search.value = ''
    router.push({ name: 'products' })
}

const searchMobile = () => {
    searchProducts();
    mobileMenuOpen.value = false;
}

const navigateToCategory = (category) => {
    router.push(`/categories/${category.id}`);
    mobileMenuOpen.value = false;
}

const toggleDropdown = (categoryId) => {
    const category = master.categories.find(c => c.id === categoryId);
    if (selectedCategory.value?.id === categoryId) {
        selectedCategory.value = null;
    } else {
        selectedCategory.value = category;
    }
};

const subcategoryProduct = (category, subcategory) => {
    router.push(`/categories/${category.id}?subcategory=${subcategory.id}`);
    selectedCategory.value = null;
};

const seasonsProduct = (category, season) => {
    router.push(`/categories/${category.id}?season=${season.id}`);
    selectedCategory.value = null;
};

const qualitiesProduct = (category, quality) => {
    router.push(`/categories/${category.id}?quality=${quality.id}`);
    selectedCategory.value = null;
};

const sizesProduct = (category, size) => {
    router.push(`/categories/${category.id}?size=${size.id}`);
    selectedCategory.value = null;
};

const handleClickOutside = (event) => {
    if (selectedCategory.value && 
        dropdownContainer.value && 
        !dropdownContainer.value.contains(event.target) &&
        !event.target.matches(`button[data-category="${selectedCategory.value.id}"]`)) {
        selectedCategory.value = null;
    }
};

const handleScroll = () => {
    if (selectedCategory.value) {
        selectedCategory.value = null;
    }
};

const handleMouseLeave = () => {
    selectedCategory.value = null;
};
</script>

<style scoped>
.router-link-active {
    @apply text-primary;
}
</style>
