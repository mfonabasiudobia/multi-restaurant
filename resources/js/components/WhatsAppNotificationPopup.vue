<template>
  <div v-if="showPopup && auth.isLoggedIn" class="fixed inset-0 z-50 flex items-center justify-center px-4">
    <div class="fixed inset-0 bg-black opacity-50" @click="closePopup"></div>
    <div class="relative bg-white rounded-lg p-6 max-w-sm w-full">
      <button @click="closePopup" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
      
      <div class="text-center">
        <div class="w-16 h-16 mx-auto mb-4 bg-green-500 rounded-full flex items-center justify-center">
          <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
        </div>
        
        <h3 class="text-xl font-semibold mb-2">WhatsApp Notifications</h3>
        <p class="text-gray-600 mb-4">Stay updated about new products and offers via WhatsApp</p>
        
        <div v-if="userPhone" class="mb-4">
          <div class="text-sm mb-3">
            <p class="text-gray-600">Notifications will be sent to:</p>
            <p class="font-semibold">{{ userPhone }}</p>
          </div>
          
          <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <p class="text-sm font-medium mb-2">Current Status:</p>
            <div class="flex items-center justify-center gap-2">
              <span class="inline-flex h-2.5 w-2.5 rounded-full" 
                :class="preferences.whatsapp_enabled ? 'bg-green-500' : 'bg-red-500'">
              </span>
              <span class="text-sm" :class="preferences.whatsapp_enabled ? 'text-green-700' : 'text-red-700'">
                {{ preferences.whatsapp_enabled ? 'Notifications Enabled' : 'Notifications Disabled' }}
              </span>
            </div>
          </div>

          <button 
            @click="toggleWhatsApp" 
            class="w-full px-4 py-2 rounded-lg font-medium transition-colors duration-200"
            :class="preferences.whatsapp_enabled ? 
              'bg-red-100 text-red-700 hover:bg-red-200' : 
              'bg-green-100 text-green-700 hover:bg-green-200'"
          >
            {{ preferences.whatsapp_enabled ? 'Disable Notifications' : 'Enable Notifications' }}
          </button>
        </div>
        
        <div v-else class="mb-4">
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <p class="text-yellow-700 text-sm font-medium">Please add a phone number to your profile first</p>
            <a href="/profile" class="inline-block mt-2 text-primary-500 hover:text-primary-600 text-sm font-medium">
              Go to Profile Settings →
            </a>
          </div>
        </div>
        
        <p class="text-xs text-gray-500 mt-4">
          You can change this setting anytime in your profile settings
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue';
import { useAuth } from '../stores/AuthStore';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const auth = useAuth();
const showPopup = ref(false);
const preferences = ref({
  whatsapp_enabled: false
});
const userPhone = ref(null);

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

// Define all functions first
const closePopup = () => {
  showPopup.value = false;
  clearActivePopup();
};

const showWhatsAppPopup = async () => {
  const activePopup = checkActivePopup();
  if (!activePopup) {
    setActivePopup('whatsapp');
    showPopup.value = true;
  } else {
    console.log('Another popup is active:', activePopup);
  }
};

const fetchPreferences = async () => {
  try {
    if (!auth.token) {
      console.log('No auth token available');
      return;
    }

    console.log('Fetching preferences with token:', auth.token);
    const response = await axios.get('/user/notification-preferences', {
      headers: {
        'Authorization': auth.token
      }
    });
    
    if (response.data.success) {
      preferences.value = response.data.preferences;
      userPhone.value = response.data.phone_number || auth.user?.phone;
      
      // Only show popup if no other popup is active
      if (!preferences.value.whatsapp_enabled) {
        await showWhatsAppPopup();
      }
    } else {
      console.error('Unsuccessful response:', response.data);
      toast.error('Failed to load preferences');
    }
  } catch (error) {
    console.error('Error fetching preferences:', error.response || error);
    preferences.value = { whatsapp_enabled: false };
    userPhone.value = auth.user?.phone;
  }
};

const toggleWhatsApp = async () => {
  try {
    if (!userPhone.value) {
      toast.error('Please add a phone number to your profile first');
      return;
    }

    const response = await axios.post('/user/notification-preferences', {
      whatsapp_enabled: !preferences.value.whatsapp_enabled
    }, {
      headers: {
        'Authorization': auth.token
      }
    });
    
    if (response.data.success) {
      preferences.value = response.data.preferences;
      toast.success(response.data.message);
      
      if (preferences.value.whatsapp_enabled) {
        localStorage.setItem('neverShowWhatsAppPopup', 'true');
        setTimeout(() => {
          closePopup();
        }, 1500);
      }
    } else {
      toast.error(response.data.message || 'Failed to update preferences');
    }
  } catch (error) {
    console.error('Error toggling WhatsApp:', error.response || error);
    toast.error(error.response?.data?.message || 'Error updating preferences');
  }
};

// Initialize component
const initializeComponent = async () => {
  try {
    console.log('Initializing WhatsApp popup, auth state:', {
      isLoggedIn: auth.isLoggedIn,
      token: auth.token,
      user: auth.user
    });

    if (auth.isLoggedIn) {
      const neverShow = localStorage.getItem('neverShowWhatsAppPopup');
      if (!neverShow) {
        await fetchPreferences();
      }
    }

    if (auth.user?.phone) {
      userPhone.value = auth.user.phone;
    }
  } catch (error) {
    console.error('Error initializing component:', error);
  }
};

// Component lifecycle hooks
onMounted(() => {
  initializeComponent();
});

// Cleanup on unmount
onUnmounted(() => {
  if (showPopup.value) {
    clearActivePopup();
  }
});

// Watch for auth state changes
let initialized = false;
watch(() => auth.isLoggedIn, async (isLoggedIn) => {
  if (!initialized) {
    initialized = true;
    return;
  }
  
  try {
    console.log('Auth state changed:', isLoggedIn);
    if (isLoggedIn) {
      const neverShow = localStorage.getItem('neverShowWhatsAppPopup');
      if (!neverShow) {
        await fetchPreferences();
      }
    } else {
      closePopup();
    }
  } catch (error) {
    console.error('Error in auth state watcher:', error);
  }
});
</script>