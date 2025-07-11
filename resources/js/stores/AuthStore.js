import axios from "axios";
import { defineStore } from "pinia";
import { useBasketStore } from "./BasketStore";

export const useAuth = defineStore("authStore", {
    state: () => ({
        user: null,
        addresses: [],
        token: null,
        favoriteProducts: 0,
        loginModal: false,
        registerModal: false,
        showAddressModal: false,
        showChangeAddressModal: false,
        orderCancel: false,
        favoriteRemove: false,
    }),

    getters: {
        getAddressById: (state) => (id) => {
            return state.addresses.find((address) => address.id == id);
        },
        isLoggedIn: (state) => {
            return !!state.token && !!state.user && !!state.user.phone_verified_at;
        }
    },

    actions: {
        setToken(token) {
            console.log(token);
            this.token = `Bearer ${token}`;

            console.log(this.token);
        },

        setUser(user) {
            this.user = user;
        },

        showLoginModal() {
            this.loginModal = true;
        },

        hideLoginModal() {
            this.loginModal = false;
        },

        fetchAddresses() {
            axios.get("/addresses", {
                headers: {
                    Authorization: this.token,
                },
            }).then((response) => {
                this.addresses = response.data.data.addresses;
                console.log('fetchAddresses', this.addresses);
                const basketStore = useBasketStore();
                this.addresses.forEach((address) => {
                    if (address.is_default) {
                        basketStore.address = address;
                        return true;
                    }else{
                        basketStore.address = this.addresses[0];
                    }
                });
            })
            .catch((error) => {
                if (error.response.status === 401) {
                    this.token = null;
                    this.user = null;
                    this.addresses = [];
                }
            });
        },
        fetchFavoriteProducts() {
            if (this.token) {
                axios.get("/favorite-products", {
                    headers: {
                        Authorization: this.token,
                    },
                }).then((response) => {
                    this.favoriteProducts = response.data.data.products?.length ?? 0;
                }).catch((error) => {
                    if (error.response.status === 401) {
                        this.token = null;
                        this.user = null;
                        this.addresses = [];
                    }
                });
            } else {
                this.favoriteProducts = 0;
            }
        },

        logout() {
            axios.get("/logout", {
                headers: {
                    Authorization: this.token,
                },
            }).then((response) => {
                this.user = null;
                this.addresses = [];
                this.token = null;
            }).catch((error) => {
                this.user = null;
                this.addresses = [];
                this.token = null;
            });
        },
    },

    persist: true,
});
