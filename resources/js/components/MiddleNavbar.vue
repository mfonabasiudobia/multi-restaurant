<template>
    
    <!-- Login Dialog Modal -->
    <LoginModal />
    <!-- End Login Dialog Modal -->
</template>

<script setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import { Bars3Icon, ChevronRightIcon, UserIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { MagnifyingGlassIcon } from '@heroicons/vue/24/solid'
import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AuthUserDropdown from './AuthUserDropdown.vue'
import LoginModal from './LoginModal.vue'
import { onUnmounted } from 'vue'
import { useAuth } from '../stores/AuthStore'
import { useBasketStore } from '../stores/BasketStore'
import { useMaster } from '../stores/MasterStore'
import MenuCategory from './MenuCategory.vue';
const route = useRoute();
const isMobile = ref(window.innerWidth < 768);

window.addEventListener('resize', () => {
    isMobile.value = window.innerWidth < 768;
});

const router = useRouter();
const basketStore = useBasketStore();
const isScrolled = ref(false);

const handleScroll = () => {
    isScrolled.value = window.scrollY > 0;
};

window.addEventListener('scroll', handleScroll);

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const AuthStore = useAuth();
const master = useMaster();

const search = ref('');
const showSearch = ref(false);

const toggleSearch = () => {
    showSearch.value = !showSearch.value
}

const showMyCart = () => {
    mobileMenuOpen.value = false;
    master.basketCanvas = true
}

// const showWishlist = () => {
//     mobileMenuOpen.value = false;
//     if (!AuthStore.token) {
//         return showLoginDilog();
//     } else {
//         router.push('/wishlist')
//     }
// }

watch(() => route.path, () => {
    mobileMenuOpen.value = false;
});

const mobileMenuOpen = ref(false);

const showLoginDilog = () => {
    AuthStore.showLoginModal();
}

const searchProducts = () => {
    master.search = search.value
    search.value = '';
    router.push({ name: 'products' })
}

</script>

<style scoped>
.router-link-active {
    @apply border-primary text-primary
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
