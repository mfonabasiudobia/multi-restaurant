<template>
  <div v-if="showPopup && isMobile" class="fixed inset-0 z-50 flex items-center justify-center px-4">
    <div class="fixed inset-0 bg-black opacity-50" @click="closePopup"></div>
    <div class="relative bg-white rounded-lg p-6 max-w-sm w-full">
      <button @click="closePopup" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <div class="text-center">
        <div class="flex justify-center space-x-4 mb-3">
          <a v-if="appStoreLink" :href="appStoreLink" target="_blank">
            <img :src="'/assets/icons/applestore.png'" alt="App Store" class="w-12 h-12 object-contain hover:opacity-80 transition-opacity">
          </a>
          <a v-if="playStoreLink" :href="playStoreLink" target="_blank">
            <img :src="'/assets/icons/playstore.png'" alt="Google Play" class="w-12 h-12 object-contain hover:opacity-80 transition-opacity">
          </a>
        </div>
        <h3 class="text-xl font-semibold mb-2">Download Our App</h3>
        <p class="text-gray-600 mb-6">Get a better experience with our mobile app</p>
        
        <div class="space-y-3">
          <a v-if="appStoreLink" 
             :href="appStoreLink" 
             target="_blank"
             class="flex items-center justify-center w-full bg-black text-white rounded-lg px-4 py-2 hover:bg-gray-800 transition-colors">
            <svg class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="currentColor">
              <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
            </svg>
            App Store
          </a>
          
          <a v-if="playStoreLink" 
             :href="playStoreLink" 
             target="_blank"
             class="flex items-center justify-center w-full bg-primary-500 text-white rounded-lg px-4 py-2 hover:bg-primary-600 transition-colors">
            <svg class="w-6 h-6 mr-2" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 20.5v-17c0-.83.67-1.5 1.5-1.5l11.72 10-11.72 10c-.83 0-1.5-.67-1.5-1.5zm16.25-8.5l-11.72-10h.02l8.32 7.12 3.38 2.88zm-11.72 10l8.32-7.12 3.38-2.88-11.7 10z"/>
            </svg>
            Google Play
          </a>
        </div>
        
        <button @click="neverShowAgain" class="mt-4 text-sm text-gray-500 hover:text-gray-700">
          Don't show this again
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useMaster } from '../stores/MasterStore';

const master = useMaster();
const showPopup = ref(false);
const isMobile = ref(false);

const appStoreLink = master.appStoreLink??"https://apps.apple.com/ro/app/second-hub/id6745817664";
const playStoreLink = master.playStoreLink??"https://play.google.com/store/apps/details?id=com.second.hub&hl=en";
console.log(appStoreLink, playStoreLink);

// Popup management
const checkActivePopup = () => {
  return localStorage.getItem('activePopup');
};

const setActivePopup = (popupName) => {
  localStorage.setItem('activePopup', popupName);
};

const clearActivePopup = () => {
  localStorage.removeItem('activePopup');
};

const closePopup = () => {
  showPopup.value = false;
  clearActivePopup();
};

const showAppDownloadPopup = () => {
  const activePopup = checkActivePopup();
  if (!activePopup) {
    setActivePopup('appDownload');
    showPopup.value = true;
  } else {
    console.log('Another popup is active:', activePopup);
    // Try again in 5 seconds if the other popup is still active
    setTimeout(showAppDownloadPopup, 5000);
  }
};

onMounted(() => {
  // Check if user is on mobile
  isMobile.value = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
  
  // Check if user has previously dismissed the popup
  const neverShow = localStorage.getItem('neverShowAppDownload');
  if (!neverShow && isMobile.value) {
    // Delay showing the popup
    setTimeout(() => {
      showAppDownloadPopup();
    }, 3000);
  }
});

onUnmounted(() => {
  if (showPopup.value) {
    clearActivePopup();
  }
});

const neverShowAgain = () => {
  localStorage.setItem('neverShowAppDownload', 'true');
  closePopup();
};
</script>