import axios from "axios";
import { defineStore } from "pinia";
import { useToast } from "vue-toastification";
import AddToCartDialog from "../components/AddCartPopupDialog.vue";
import RemoveCartPopupDialog from "../components/RemoveCartPopupDialog.vue";

import { useAuth } from "./AuthStore";

const toast = useToast();

export const useBasketStore = defineStore("basketStore", {
    state: () => ({
        total: 0,
        products: [],
        checkoutProducts: [],
        selectedShopIds: [],
        selectedProducts: [],
        total_amount: 0,
        delivery_charge: 0,
        coupon_discount: 0,
        payable_amount: 0,
        order_tax_amount: 0,
        coupon_code: "",
        showOrderConfirmModal: false,
        orderPaymentCancelModal: false,
        address: null,
        buyNowShopId: null,
        buyNowProduct: null,
        isLoadingCart: false,
        pendingAddress: null,
    }),

    getters: {
        totalAmount: (state) => {
            let total = 0;
            state.products.forEach((item) => {
                item.products.forEach((product) => {
                    let price =
                        product.discount_price > 0
                            ? product.discount_price
                            : product.price;
                    total += price * product.quantity;
                });
            });
            return total.toFixed(2);
        },

        selectedProductsTotal: (state) => {
            let total = 0;
            if (state.selectedProducts.length === 0) {
                return "0.00";
            }

            state.products.forEach((item) => {
                item.products.forEach((product) => {
                    // Only include selected products
                    if (state.selectedProducts.includes(product.id)) {
                        let price =
                            product.discount_price > 0
                                ? product.discount_price
                                : product.price;
                        total += price * product.quantity;
                    }
                });
            });
            return total.toFixed(2);
        },

        totalCheckoutAmount: (state) => {
            let total = 0;
            state.checkoutProducts.forEach((item) => {
                item.products.forEach((product) => {
                    let price =
                        product.discount_price > 0
                            ? product.discount_price
                            : product.price;
                    total += price * product.quantity;
                });
            });
            return total.toFixed(2);
        },

        checkoutTotalItems: (state) => {
            let total = 0;
            state.checkoutProducts.forEach((item) => {
                total += item.products.length;
            });
            return total;
        },
    },

    actions: {
        /**
         * Add a product to cart.
         * @param {object} data - object containing product id, quantity, color, size, unit.
         * @param {object} product - the product to add to cart.
         * @returns {Promise}
         */
        addToCart(data, product) {
            console.log("data", data);
            if (data.product_id) {
                this.isLoadingCart = true;
                const content = {
                    component: AddToCartDialog,
                    props: {
                        product: product,
                    },
                };
                const authStore = useAuth();

                return axios
                    .post("/cart/store", data, {
                        headers: {
                            Authorization: authStore.token,
                        },
                    })
                    .then((response) => {
                        this.isLoadingCart = false;
                        if (!data.is_buy_now) {
                            this.total = response.data.data.total;
                            this.products = response.data.data.cart_items;

                            // Add the new product to selected products if it's not already selected
                            const newProductId = data.product_id;
                            if (!this.selectedProducts.includes(newProductId)) {
                                this.selectedProducts.push(newProductId);
                            }

                            // Update total amount based on selected products
                            this.total_amount = parseFloat(this.selectedProductsTotal);

                            toast(content, {
                                type: "default",
                                hideProgressBar: true,
                                icon: false,
                                position: "bottom-left",
                                toastClassName: "vue-toastification-alert",
                                timeout: 3000,
                            });
                        } else {
                            // For buy now, we need to set the buyNowProduct with the selected size and quantity
                            const productWithDetails = { ...product };

                            // Add the selected size to the product if provided
                            if (data.size) {
                                // Find the selected size from the product
                                const selectedSize = product.sizes?.find(size => size.id === data.size);

                                // Add the selected size to the product
                                if (selectedSize) {
                                    productWithDetails.size = selectedSize;
                                }
                            }

                            // Set the quantity
                            productWithDetails.quantity = data.quantity || 1;

                            // Set the buyNowProduct
                            this.buyNowProduct = productWithDetails;

                            // Set the shop ID
                            if (product.shop && product.shop.id) {
                                this.buyNowShopId = product.shop.id;
                            }
                        }

                        return response;
                    })
                    .catch((error) => {
                        console.log("error is basket store", error);
                        this.isLoadingCart = false;
                        if (error.response.status == 401) {
                            toast.error("Please login first!", {
                                position: "bottom-left",
                            });
                            const authStore = useAuth();
                            authStore.showLoginModal();
                        } else {
                            toast.error(error.response.data.message, {
                                position: "bottom-left",
                            });
                        }
                        throw error;
                    });
            }
        },

        /**
         * Initialize selected products based on current cart contents
         */
        initializeSelectedProducts() {
            // Count total products in the cart
            let totalProducts = 0;
            const allProductIds = [];

            this.products.forEach(shop => {
                shop.products.forEach(product => {
                    totalProducts++;
                    allProductIds.push(product.id);
                });
            });

            // If there are products in the cart but none are selected, select all
            if (totalProducts > 0 && this.selectedProducts.length === 0) {
                this.selectedProducts = [...allProductIds];
            }
            // If there are selected products, make sure they're valid
            else if (this.selectedProducts.length > 0) {
                // Filter out selected products that are no longer in the cart
                this.selectedProducts = this.selectedProducts.filter(id =>
                    allProductIds.includes(id)
                );

                // If no valid selections remain, select all products
                if (this.selectedProducts.length === 0 && totalProducts > 0) {
                    this.selectedProducts = [...allProductIds];
                }
            }

            // Update total amount based on selected products
            this.total_amount = parseFloat(this.selectedProductsTotal);
        },

        /**
         * Fetches the cart data from the server and updates the state.
         * If the user is not logged in, it clears the cart and related state.
         */
        fetchCart() {
            const authStore = useAuth();
            if (authStore.token) {
                axios
                    .get("/carts", {
                        headers: {
                            Authorization: authStore.token,
                        },
                    })
                    .then((response) => {
                        this.total = response.data.data.total;
                        this.products = response.data.data.cart_items;

                        const product = this.products[0]?.products[0];

                        this.fetchCheckoutProducts();

                        const productWithDetails = { ...product };




                        // Set the buyNowProduct
                        this.buyNowProduct = productWithDetails;

                        this.selectedShopIds = this.products.map(
                            (shop) => shop.shop_id
                        );


                        // Initialize selected products
                        this.initializeSelectedProducts();
                    })
                    .catch((error) => {
                        if (error.response.status === 401) {
                            authStore.token = null;
                            authStore.user = null;
                            authStore.addresses = [];
                        }
                    });
            } else {
                this.total = 0;
                this.products = [];
                this.checkoutProducts = [];
                this.selectedShopIds = [];
                this.selectedProducts = [];
                this.total_amount = 0;
                this.delivery_charge = 0;
                this.coupon_discount = 0;
                this.payable_amount = 0;
                this.coupon_code = "";
                this.address = null;
            }
        },

        /**
         * Decrement the quantity of a given product in the cart
         * @param {object} product - the product to decrement the quantity for
         */
        decrementQuantity(product) {
            const authStore = useAuth();
            if (product) {
                const content = {
                    component: RemoveCartPopupDialog,
                    props: {
                        product: product,
                    },
                };
                axios
                    .post(
                        "/cart/decrement",
                        { product_id: product.id },
                        {
                            headers: {
                                Authorization: authStore.token,
                            },
                        }
                    )
                    .then((response) => {
                        this.total = response.data.data.total;
                        this.products = response.data.data.cart_items;
                        this.fetchCheckoutProducts();

                        if (
                            response.data.message == "product removed from cart"
                        ) {
                            const shopIds = this.products.map(
                                (shop) => shop.shop_id
                            );
                            this.selectedShopIds = this.selectedShopIds.filter(
                                (id) => shopIds.includes(id)
                            );
                            // const exists = shopIds.some((id) => selectedShopIds.includes(id));

                            if (this.products.length === 0) {
                                this.selectedShopIds = [];
                                this.checkoutProducts = [];
                                this.total_amount = 0;
                                this.delivery_charge = 0;
                                this.coupon_discount = 0;
                                this.payable_amount = 0;
                            }

                            toast(content, {
                                type: "default",
                                hideProgressBar: true,
                                icon: false,
                                position: "bottom-left",
                                toastClassName: "vue-toastification-alert",
                                timeout: 3000,
                            });
                        }
                    })
                    .catch((error) => {
                        if (error.response.status == 401) {
                            authStore.token = null;
                            authStore.user = null;
                            authStore.addresses = [];
                        }
                    });
            }
        },

        /**
         * Increment the quantity of the given product in the cart
         * @param {object} product - the product to increment the quantity for
         */
        incrementQuantity(product) {
            const authStore = useAuth();
            if (product) {
                axios.post("/cart/increment",
                    { product_id: product.id },
                    {
                        headers: {
                            Authorization: authStore.token,
                        },
                    }).then((response) => {
                        this.total = response.data.data.total;
                        this.products = response.data.data.cart_items;
                        this.fetchCheckoutProducts();

                        // Update total amount if the product is selected
                        if (this.selectedProducts.includes(product.id)) {
                            this.total_amount = parseFloat(this.selectedProductsTotal);
                        }
                    }).catch((error) => {
                        toast.error(error.response.data.message, {
                            position: "bottom-left",
                        });
                        if (error.response.status == 401) {
                            authStore.token = null;
                            authStore.user = null;
                            authStore.addresses = [];
                        }
                    });
            }
        },

        /**
         * Remove the given product from the cart
         * @param {object} product - the product to remove from the cart
         */
        removeFromBasket(product) {
            const authStore = useAuth();
            if (product) {
                axios.post("/cart/delete",
                    { product_id: product.id },
                    {
                        headers: {
                            Authorization: authStore.token,
                        },
                    }).then((response) => {
                        this.total = response.data.data.total;
                        this.products = response.data.data.cart_items;
                        this.fetchCheckoutProducts();

                        // Remove the product from selectedProducts if it was selected
                        if (this.selectedProducts.includes(product.id)) {
                            this.selectedProducts = this.selectedProducts.filter(id => id !== product.id);
                        }

                        // Update total amount based on remaining selected products
                        this.total_amount = parseFloat(this.selectedProductsTotal);
                    }).catch((error) => {
                        toast.error(error.response.data.message, {
                            position: "bottom-left",
                        });
                        if (error.response.status == 401) {
                            authStore.token = null;
                            authStore.user = null;
                            authStore.addresses = [];
                        }
                    });
            }
        },

        /**
         * Select or deselect the given shop for checkout
         * @param {number} shop - the shop to select or deselect
         */
        selectCartItemsForCheckout(shop) {
            alert("shop", shop);
            console.log("rudrashop", shop);
            if (!this.selectedShopIds.includes(shop)) {
                this.selectedShopIds.push(shop);
            } else {
                this.selectedShopIds = this.selectedShopIds.filter(
                    (item) => item !== shop
                );
            }
            this.fetchCheckoutProducts();
        },

        /**
         * Fetches the checkout products for the currently selected shops and updates
         * the checkout-related state, including total amount, delivery charge, coupon
         * discount, and payable amount. If the checkout products are empty, it clears
         * the selected shop IDs. Uses the auth token for authorization.
         */
        fetchCheckoutProducts() {
            const authStore = useAuth();
            if (authStore.token) {
                axios
                    .post(
                        "/cart/checkout",
                        {
                            shop_ids: this.selectedShopIds,
                            selected_products: this.selectedProducts
                        },
                        {
                            headers: {
                                Authorization: authStore.token,
                            },
                        }
                    )
                    .then((response) => {
                        this.checkoutProducts =
                            response.data.data.checkout_items;

                        // If we have selected products, use the selectedProductsTotal
                        if (this.selectedProducts.length > 0) {
                            this.total_amount = parseFloat(this.selectedProductsTotal);
                        } else {
                            this.total_amount =
                                response.data.data.checkout.total_amount;
                        }

                        this.delivery_charge =
                            response.data.data.checkout.delivery_charge;
                        this.coupon_discount =
                            response.data.data.checkout.coupon_discount;
                        this.payable_amount =
                            response.data.data.checkout.payable_amount;
                        this.order_tax_amount =
                            response.data.data.checkout.order_tax_amount;
                        if (this.checkoutProducts.length === 0) {
                            this.selectedShopIds = [];
                        }
                    })
                    .catch((error) => {
                        toast.error(error.response.data.message);
                    });
            }
        },

        checkShopIsSelected(shopId) {
            return this.selectedShopIds.includes(shopId);
        },

        addToBuyNow(product, variant) {
            this.buyNowProduct = {
                ...product,
                selectedVariant: variant,
                delivery_weights: product.delivery_weights
            };
        },

        async processBuyNow(orderData) {
            try {
                // Check if we need to save the pending address
                const authStore = useAuth();
                if (!this.address && this.pendingAddress) {
                    try {
                        // Prepare the address data
                        const addressPayload = { ...this.pendingAddress };

                        // Set as default address
                        addressPayload.is_default = true;

                        // Set country if not present
                        if (!addressPayload.country) {
                            addressPayload.country = 'Romania';
                        }

                        // Send the request to save the address
                        const addressResponse = await axios.post('/address/store', addressPayload, {
                            headers: {
                                Authorization: authStore.token
                            },
                        });

                        // If successful, update the address in the store
                        if (addressResponse.data && addressResponse.data.data && addressResponse.data.data.address) {
                            const newAddress = addressResponse.data.data.address;

                            // Add to addresses list
                            authStore.addresses.push(newAddress);

                            // Set as current address
                            this.address = newAddress;

                            // Use the new address ID for the order
                            orderData.address_id = newAddress.id;

                            // Clear pending address
                            this.pendingAddress = null;
                        }
                    } catch (addressError) {
                        console.error('Failed to save address:', addressError);
                        // Continue with the order even if address save fails
                    }
                }

                const response = await axios.post('/buy-now', {
                    ...orderData,
                    weight: this.buyNowProduct?.selectedVariant?.weight,
                    delivery_charge: this.calculateDeliveryCharge()
                });
                // Handle success
                return response;
            } catch (error) {
                // Handle error
                throw error;
            }
        },

        calculateDeliveryCharge() {
            const weight = this.buyNowProduct?.selectedVariant?.weight;
            if (!weight) return 0;

            const charge = this.buyNowProduct?.delivery_weights?.find(
                dw => weight >= dw.min_weight && weight <= dw.max_weight
            );

            return charge ? charge.price : 0;
        },

        async placeOrder(payload) {
            try {
                const response = await axios.post('/place-order', payload, {
                    headers: {
                        'Authorization': useAuth().token,
                        'Content-Type': 'multipart/form-data'
                    }
                });
                return response.data;
            } catch (error) {
                console.error('Error placing order:', error);
                throw error;
            }
        },

        /**
         * Toggle product selection for checkout
         * @param {object} product - The product to toggle selection
         */
        toggleProductSelection(product) {
            const index = this.selectedProducts.findIndex(id => id === product.id);
            if (index === -1) {
                // Product not selected, add it
                this.selectedProducts.push(product.id);
            } else {
                // Product already selected, remove it
                this.selectedProducts.splice(index, 1);
            }

            // Update the total_amount based on selected products
            this.total_amount = parseFloat(this.selectedProductsTotal);
        },

        /**
         * Check if a product is selected
         * @param {object} product - The product to check
         * @returns {boolean} - Whether the product is selected
         */
        isProductSelected(product) {
            return this.selectedProducts.includes(product.id);
        },

        /**
         * Select all products in the cart
         */
        selectAllProducts() {
            this.selectedProducts = [];
            this.products.forEach(shop => {
                shop.products.forEach(product => {
                    this.selectedProducts.push(product.id);
                });
            });
            // Update total amount based on selected products
            this.total_amount = parseFloat(this.selectedProductsTotal);
        },

        /**
         * Deselect all products in the cart
         */
        deselectAllProducts() {
            this.selectedProducts = [];
            // Update total amount based on selected products
            this.total_amount = parseFloat(this.selectedProductsTotal);
        },

        /**
         * Proceed to checkout with selected products
         */
        checkoutSelectedProducts() {
            if (this.selectedProducts.length === 0) {
                return false;
            }

            // Create a buy now product with all selected products
            const selectedProductsData = [];
            this.products.forEach(shop => {
                shop.products.forEach(product => {
                    if (this.selectedProducts.includes(product.id)) {
                        selectedProductsData.push({
                            ...product,
                            shop_id: shop.shop_id,
                            shop_name: shop.shop_name
                        });
                    }
                });
            });

            if (selectedProductsData.length > 0) {
                // Group by shop
                const shopGroups = {};
                selectedProductsData.forEach(product => {
                    if (!shopGroups[product.shop_id]) {
                        shopGroups[product.shop_id] = {
                            shop_id: product.shop_id,
                            shop_name: product.shop_name,
                            products: []
                        };
                    }
                    shopGroups[product.shop_id].products.push(product);
                });

                // Set buyNowProduct to the first shop's products
                const firstShopId = Object.keys(shopGroups)[0];
                this.buyNowShopId = firstShopId;
                this.buyNowProduct = shopGroups[firstShopId];

                return true;
            }

            return false;
        },

        /**
         * Initialize the store with default values
         */
        initializeStore() {
            this.total = 0;
            this.products = [];
            this.checkoutProducts = [];
            this.selectedShopIds = [];
            this.selectedProducts = [];
            this.total_amount = 0;
            this.delivery_charge = 0;
            this.coupon_discount = 0;
            this.payable_amount = 0;
            this.order_tax_amount = 0;
            this.coupon_code = "";
            this.showOrderConfirmModal = false;
            this.orderPaymentCancelModal = false;
            this.address = null;
            this.buyNowShopId = null;
            this.buyNowProduct = null;
            this.isLoadingCart = false;
            this.pendingAddress = null;
        },
    },

    persist: true,
});
