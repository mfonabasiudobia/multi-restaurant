<template>
    <div class="rounded-lg border transition-all duration-300 group bg-white overflow-hidden relative"
        :class="props.product?.quantity > 0 ? 'hover:border-primary' : ''">

        <div class="flex flex-col">
            <div class="bg-white">
                <div class="w-full h-60 sm:h-64 overflow-hidden relative"
                    :class="props.product?.quantity > 0 ? '' : 'opacity-30'">
                    <div @click.stop="showProductDetails" class="cursor-pointer">
                        <!-- Show thumbnail -->
                        <template v-if="props.product?.thumbnail.includes('default.jpg')">
                            <ProductVideo 
                            
                                :poster="props.product.videos[0].thumbnail" 
                                class="w-full h-full object-cover"
                            />
                        </template>
                        <template v-else>
                            <img 
                                :src="props.product?.thumbnail" 
                                class="w-full h-full object-cover"
                                loading="lazy"
                                alt="Product thumbnail"
                            />
                        </template>
                    </div>

                    <div v-if="props.product?.discount_percentage > 0"
                        class="px-1 py-0.5 bg-red-500 rounded-2xl text-white text-xs font-medium absolute top-2 left-2">
                        {{ props.product?.discount_percentage }}% {{ $t('OFF') }}
                    </div>

                    <!-- favorite -->
                    <!-- <button v-if="props.product?.is_favorite"
                        class="absolute top-2 right-2 w-9 h-9 rounded-[10px] justify-center items-center flex cursor-pointer bg-white"
                        @click="favoriteAddOrRemove">
                        <HeartIcon class="w-6 h-6 text-red-500" />
                    </button> -->

                    <!--unfavorite-->
                    <!-- <button v-else
                        class="absolute flex sm:hidden group-hover:flex top-2 right-2 w-9 h-9 rounded-[10px] justify-center items-center cursor-pointer bg-white transition-all duration-300"
                        @click="favoriteAddOrRemove">
                        <HeartIconOutline class="w-6 h-6 text-slate-600" />
                    </button> -->

                </div>
                <div class="cursor-pointer" @click.stop="showProductDetails">
                    <div class="bg-white p-2 flex flex-col items-start gap-2 col-span-2">

                        <div class="text-slate-950 text-base font-normal leading-normal truncate w-full"
                            :class="props.product?.quantity > 0 ? '' : 'opacity-30'">
                            {{ props.product?.name }}
                        </div>

                        <div class="flex items-center gap-2" :class="props.product?.quantity > 0 ? '' : 'opacity-30'">
                            <!-- price -->
                            <div class="text-primary text-base font-bold leading-normal">
                                {{ masterStore.showCurrency(props.product?.discount_price > 0 ?
                                    props.product?.discount_price : props.product?.price) }}
                            </div>
                            <!-- discount price -->
                            <div v-if="props.product?.discount_price > 0"
                                class="text-slate-400 text-sm font-normal line-through leading-tight">
                                {{ masterStore.showCurrency(props.product?.price) }}
                            </div>
                        </div>

                        <div class="flex justify-between items-center w-full">
                          

                            <div class="h-3 w-[0px] border border-slate-200"></div>
                            <!-- total sold -->
                            <div v-if="props.product?.quantity > 0"
                                class="text-right text-slate-500 text-sm font-normal leading-tight">
                                {{ props.product?.total_sold }} {{ $t('Sold') }}
                            </div>
                            <!-- Stock Out -->
                            <div v-else class="text-right text-red-500 text-sm font-normal leading-tight">
                                {{ $t('Stock Out') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full p-2">
                <div v-if="props.product?.quantity > 0" class="justify-start items-center gap-3 flex w-full">
                    <button
                        class="cursor-pointer w-10 h-10 bg-white rounded-[10px] border border-primary-100 justify-center items-center flex"
                        @click="addToBasket(props.product)">
                        <div class="w-5 h-5">
                            <BagIcon />
                        </div>
                    </button>

                    <button
                        class="justify-center items-center gap-0.5 flex border border-primary grow py-2.5 rounded-[10px]"
                        @click="openBuyNowOptions(props.product)">
                        <div class="text-primary text-sm font-normal leading-tight">{{ $t('Buy Now') }}</div>
                    </button>
                </div>
                <button v-else
                    class="justify-center items-center gap-0.5 flex border border-red-300 py-2.5 rounded-[10px] w-full"
                    disabled>
                    <div class="text-red-300 text-sm font-normal leading-tight">
                        <!-- Request Stock -->
                        {{ $t('Buy Now') }}
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Buy Modal -->
    <TransitionRoot as="template" :show="showQuickBuyModal">
        <Dialog as="div" class="relative z-50" @close="showQuickBuyModal = false">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                        leave-from="opacity-100 translate-y-0 sm:scale-100"
                        leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel
                            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                        <DialogTitle as="h3" class="text-lg font-semibold leading-6 text-gray-900">
                                            {{ $t('Select Options') }}
                                        </DialogTitle>
                                        
                                        <div class="mt-4">
                                            <!-- Product Info -->
                                            <div class="flex items-center gap-4 mb-4 pb-4 border-b">
                                                <img :src="props.product?.thumbnail" alt="Product" class="w-16 h-16 object-cover rounded">
                                                <div>
                                                    <h4 class="font-medium">{{ props.product?.name }}</h4>
                                                    <div class="text-primary font-bold">
                                                        {{ masterStore.showCurrency(calculateTotalPrice()) }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Size Selection -->
                                            <div v-if="props.product?.sizes && props.product?.sizes.length > 0" class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Size') }}</label>
                                                <div class="flex flex-wrap gap-2">
                                                    <button 
                                                        v-for="size in props.product.sizes" 
                                                        :key="size.id"
                                                        @click="selectSize(size)"
                                                        class="px-3 py-2 border rounded-md text-sm transition-colors"
                                                        :class="[
                                                            selectedSize?.id === size.id 
                                                                ? 'border-primary bg-primary/10 text-primary' 
                                                                : 'border-gray-300 hover:border-primary'
                                                        ]"
                                                    >
                                                        <span>{{ size.name }}</span>
                                                        <span v-if="size.price > 0" class="ml-1 text-xs">+{{ masterStore.showCurrency(size.price) }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Quantity -->
                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $t('Quantity') }}</label>
                                                <div class="flex items-center">
                                                    <button 
                                                        @click="quantity > 1 ? quantity-- : null"
                                                        class="p-2 bg-slate-100 rounded-l border border-gray-300"
                                                    >
                                                        <MinusIcon class="w-5 h-5" />
                                                    </button>
                                                    <input 
                                                        type="number" 
                                                        v-model="quantity" 
                                                        min="1" 
                                                        class="w-16 text-center border-t border-b border-gray-300 h-10"
                                                    />
                                                    <button 
                                                        @click="quantity++"
                                                        class="p-2 bg-slate-100 rounded-r border border-gray-300"
                                                    >
                                                        <PlusIcon class="w-5 h-5" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                                <!-- <button 
                                    type="button"
                                    class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 sm:ml-3 sm:w-auto"
                                    @click="proceedToBuyNow"
                                >
                                    {{ $t('Buy Now') }}
                                </button> -->
                                <button 
                                    type="button"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                                    @click="showQuickBuyModal = false"
                                >
                                    {{ $t('Cancel') }}
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
<script setup>
import { HeartIcon as HeartIconOutline, MinusIcon, PlusIcon } from '@heroicons/vue/24/outline';
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { HeartIcon, StarIcon } from '@heroicons/vue/24/solid';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import BagIcon from '../icons/Bag.vue';
import { useAuth } from '../stores/AuthStore';
import { useBasketStore } from '../stores/BasketStore';
import { useMaster } from '../stores/MasterStore';

const router = useRouter();

const masterStore = useMaster();

const basketStore = useBasketStore();
const authStore = useAuth();
import ProductVideo from './ProductVideo.vue';

const toast = useToast();

const props = defineProps({
    product: Object
});

// Quick Buy Modal
const showQuickBuyModal = ref(false);
const selectedSize = ref(null);
const quantity = ref(1);

const orderData = {
    is_buy_now: false,
    product_id: props.product?.id,
    quantity: 1,
    size: null,
    color: null,
    unit: null
};

const addToBasket = (product) => {
    // add product to basket
    basketStore.addToCart(orderData, product);
};

const openBuyNowOptions = (product) => {
     basketStore.addToCart(orderData, product);

     setTimeout(() => {
        window.location = '/checkout';
     }, 500)
    // if (authStore.token === null) {
    //     return authStore.loginModal = true;
    // }
    
    // // If product has sizes, show the modal
    // if (props.product?.sizes && props.product.sizes.length > 0) {
    //     // Reset selections
    //     selectedSize.value = null;
    //     quantity.value = 1;
    //     showQuickBuyModal.value = true;
    // } else {
    //     // If no sizes, proceed directly
    //     buyNow();
    // }
};

const selectSize = (size) => {
    selectedSize.value = size;
};

const calculateTotalPrice = () => {
    let basePrice = props.product?.discount_price > 0 
        ? props.product?.discount_price 
        : props.product?.price;
    
    // Add size price if a size is selected
    if (selectedSize.value && selectedSize.value.price) {
        basePrice += selectedSize.value.price;
    }
    
    // Multiply by quantity
    return basePrice * quantity.value;
};

const proceedToBuyNow = async () => {
    // Validate size selection if sizes exist
    if (props.product?.sizes && props.product.sizes.length > 0 && !selectedSize.value) {
        toast.error('Please select a size', {
            position: "bottom-left",
        });
        return;
    }
    
    // Close modal
    showQuickBuyModal.value = false;
    
    try {
        // Prepare buy now data
        const buyNowData = {
            product_id: props.product?.id,
            is_buy_now: true,
            quantity: quantity.value,
            size: selectedSize.value ? selectedSize.value.id : null,
            color: null,
            unit: null
        };
        
        // Wait for the addToCart operation to complete
        await basketStore.addToCart(buyNowData, props.product);
        
        // Make sure the product is properly set with the selected size
        if (selectedSize.value) {
            // Create a copy of the product with the selected size and quantity
            const productWithDetails = { ...props.product };
            productWithDetails.size = selectedSize.value;
            productWithDetails.quantity = quantity.value;
            
            // Set the buyNowProduct directly with our custom object
            basketStore.buyNowProduct = productWithDetails;
        }
        
        basketStore.buyNowShopId = props.product?.shop?.id;
        
        // Add a small delay to ensure store updates are processed
        setTimeout(() => {
            router.push({ name: 'buynow' });
        }, 100);
    } catch (error) {
        console.error('Error processing buy now:', error);
        toast.error('Failed to process buy now request. Please try again.');
    }
};

const buyNow = async () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }

    try {
        // For products without size options
        const buyNowData = {
            product_id: props.product?.id,
            is_buy_now: true,
            quantity: 1,
            size: null,
            color: null,
            unit: null
        };
        
        // Wait for the addToCart operation to complete
        await basketStore.addToCart(buyNowData, props.product);
        
        // Create a copy of the product with quantity
        const productWithQuantity = { ...props.product };
        productWithQuantity.quantity = 1;
        
        // Set the buyNowProduct directly
        basketStore.buyNowProduct = productWithQuantity;
        basketStore.buyNowShopId = props.product?.shop?.id;
        
        // Add a small delay to ensure store updates are processed
        setTimeout(() => {
            router.push({ name: 'buynow' });
        }, 100);
    } catch (error) {
        console.error('Error processing buy now:', error);
        toast.error('Failed to process buy now request. Please try again.');
    }
};

const isFavorite = ref(props.product?.is_favorite);

const favoriteAddOrRemove = () => {
    if (authStore.token === null) {
        return authStore.loginModal = true;
    }
    axios.post('/favorite-add-or-remove', {
        product_id: props.product.id
    }, {
        headers: {
            Authorization: authStore.token
        }
    }).then((response) => {
        props.product.is_favorite = !props.product.is_favorite
        isFavorite.value = response.data.data.product.is_favorite
        if (isFavorite.value === false) {
            toast.warning('Product removed from favorite', {
                position: "bottom-left",
            });
        } else {
            toast.success('Product added to favorite', {
                position: "bottom-left",
            });
        }
        authStore.favoriteRemove = true
        authStore.fetchFavoriteProducts();
    });
}

const showProductDetails = async () => {
    try {
        if (props.product?.quantity > 0) {
            await router.push({ 
                name: 'productDetails', 
                params: { id: props.product.id }
            });
        }
    } catch (error) {
        console.error('Navigation error:', error);
    }
}

</script>
