import "./bootstrap";

import { createApp } from "vue";

import App from "./App.vue";

import router from "./router";

import VueAwesomePaginate from "vue-awesome-paginate";
import "vue-awesome-paginate/dist/style.css";

import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";

import { createPinia } from "pinia";
import piniaPluginPersistedstate from "pinia-plugin-persistedstate";

import localization from "./localization";

import axios from 'axios'

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

const app = createApp(App);

app.component("v-select", VueSelect);
localization.fetchLocalizationData();

app.use(localization.i18n);
app.use(pinia);
app.use(router);
app.use(VueAwesomePaginate);
app.use(Toast);

axios.defaults.baseURL = '/api'  // If your API is under /api
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.withCredentials = true

// Add interceptor to log requests
axios.interceptors.request.use(config => {
    console.log('Making request to:', config.url, config)
    return config
})

axios.interceptors.response.use(
    response => {
        console.log('Received response:', response)
        return response
    },
    error => {
        console.error('Request failed:', error.response || error)
        return Promise.reject(error)
    }
)

app.mount("#app");

// Add this function to your app.js or a separate JS file
function showPackageAlert() {
    Swal.fire({
        title: 'Package Limit Reached',
        html: 'You\'ve reached your product limit or need to complete payment. Please visit the <a href="/shop/package/payment">packages page</a> to upgrade.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Go to Packages',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/shop/package/payment';
        }
    });
}
