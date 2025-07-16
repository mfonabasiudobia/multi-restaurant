<template>
    <div class="margin-container relative">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 overflow-hidden pt-4 relative">
            <router-link to="/" class="w-6 h-6">
                <HomeIcon class="w-5 h-5 text-slate-600" />
            </router-link>
            <div class="grow w-full overflow-hidden">
                <div class="space-x-1 text-slate-600 text-sm font-normal truncate">
                    <router-link to="/">{{ $t("Home") }}</router-link>
                    <span>/</span>
                    <span>{{ product.name }}</span>
                </div>
            </div>                  
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <!-- Left: Media Gallery -->
            <div class="lg:col-span-3 relative">
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        <!-- Sidebar Thumbnails (Hidden on Mobile) -->
        <div class="hidden lg:block w-[100px] flex-shrink-0">
            <div class="max-h-[500px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                <div class="space-y-3">
                    <!-- Video Thumbnails -->
                    <div v-for="(video, index) in product.videos" :key="'video-'+index"
                        @click="selectMedia('video', index)"
                        class="aspect-square rounded-lg border overflow-hidden cursor-pointer transition-colors relative"
                        :class="[selectedMediaType === 'video' && selectedMediaIndex === index ? 'border-primary' : 'border-slate-200']">
                        <img :src="video.thumbnail" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 flex items-center justify-center bg-black/10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Image Thumbnails -->
                    <div v-for="(thumbnail, index) in product.thumbnails" :key="'thumb-'+index"
                        @click="selectMedia('image', index)"
                        class="aspect-square rounded-lg border overflow-hidden cursor-pointer transition-colors"
                        :class="[selectedMediaType === 'image' && selectedMediaIndex === index ? 'border-primary' : 'border-slate-200']">
                        <img :src="thumbnail.thumbnail" class="w-full h-full object-cover" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Media Display -->
        <div class="flex-1 w-full">
            <div class="w-full max-w-[600px] mx-auto">
                <div class="rounded-xl overflow-hidden flex justify-center">
                    <PhotoVideo
                        :videos="product.videos || []"
                        :photos="product.thumbnails || []"
                        :initial-media-type="selectedMediaType"
                        :initial-media-index="selectedMediaIndex"
                        class="w-full"
                    />
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Thumbnails (Horizontal Scroll) -->
    <div class="lg:hidden mt-4 overflow-x-auto">
        <div class="flex gap-3 pb-2">
            <!-- Video Thumbnails -->
            <div v-for="(video, index) in product.videos" :key="'video-'+index"
                @click="selectMedia('video', index)"
                class="w-[70px] sm:w-[80px] aspect-square flex-shrink-0 rounded-lg border overflow-hidden cursor-pointer transition-colors relative"
                :class="[selectedMediaType === 'video' && selectedMediaIndex === index ? 'border-primary' : 'border-slate-200']">
                <img :src="video.thumbnail" class="w-full h-full object-cover" />
                <div class="absolute inset-0 flex items-center justify-center bg-black/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    </svg>
                </div>
            </div>
            
            <!-- Image Thumbnails -->
            <div v-for="(thumbnail, index) in product.thumbnails" :key="'thumb-'+index"
                @click="selectMedia('image', index)"
                class="w-[70px] sm:w-[80px] aspect-square flex-shrink-0 rounded-lg border overflow-hidden cursor-pointer transition-colors"
                :class="[selectedMediaType === 'image' && selectedMediaIndex === index ? 'border-primary' : 'border-slate-200']">
                <img :src="thumbnail.thumbnail" class="w-full h-full object-cover" />
            </div>
        </div>
    </div>
</div>

            <!-- Right: Product Details -->
            <div class="lg:col-span-2 bg-slate-100 rounded-lg px-6 py-4 relative">
                <!-- Product Name & Price -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold">{{ product.name }}</h1>
                    <p class="text-gray-600 mt-1" v-html="product.short_description || ''"></p>
                   
                    <div class="mt-4">
                        <div class="text-2xl text-[#EF4444] font-bold">
                            {{ masterStore.showCurrency(parseFloat(productPrice).toFixed(2)) }}
                        </div>
                        <div v-if="product.taxes?.length" class="text-sm text-gray-500 mt-1">
                            <span v-for="tax in product.taxes" :key="tax.id">
                                + {{ tax.percentage }}% {{ tax.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="flex border-b">
                    <button 
                        @click="activeTab = 'details'"
                        :class="[
                            'px-6 py-3 text-sm font-medium',
                            activeTab === 'details' ? 'text-primary border-b-2 border-primary' : 'text-gray-500'
                        ]"
                    >
                        {{ $t("PRODUCT INFO") }}
                    </button>
                    <button 
                        @click="activeTab = 'delivery'"
                        :class="[
                            'px-6 py-3 text-sm font-medium',
                            activeTab === 'delivery' ? 'text-primary border-b-2 border-primary' : 'text-gray-500'
                        ]"
                    >
                        {{ $t("SHIPPING") }}
                    </button>
                    <button 
                        @click="activeTab = 'shop'"
                        :class="[
                            'px-6 py-3 text-sm font-medium',
                            activeTab === 'shop' ? 'text-primary border-b-2 border-primary' : 'text-gray-500'
                        ]"
                    >
                        {{ $t("SHOP INFO") }}
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="mt-6">
                    <!-- Product Info Tab -->
                    <div v-if="activeTab === 'details'" class="space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600 min-w-[100px]">{{ $t("Quality:") }}</span>
                            <span>{{ product.quality?.name }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-gray-600 min-w-[100px]">{{ $t("Season:") }}</span>
                            <span>{{ product.season?.name }}</span>
                        </div>

                        

                        <div class="mt-4">
                            <span class="text-gray-600">{{ $t("Description:") }}</span>
                            <div class="mt-2" v-html="product.description"></div>
                        </div>

                        <div v-if="activeTab === 'details'">
                            <div v-if="product.sizes && product.sizes.length > 0" class="mb-4">
                                <div class="text-gray-600 mb-2">{{ $t("Size:") }}</div>
                                <div class="flex flex-wrap gap-2">
                                    <button 
                                        v-for="size in product.sizes" 
                                        :key="size.id"
                                        @click="handleSizeSelect(size)"
                                        class="px-3 py-2 border rounded-md text-sm transition-colors"
                                        :class="[
                                            formData.size === size.id 
                                                ? 'border-primary bg-primary/10' 
                                                : 'border-gray-300 hover:border-primary'
                                        ]"
                                    >
                                        {{ size.name }} {{ product.unit?.name }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Tab -->
                    <div v-if="activeTab === 'delivery'" class="space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-600 min-w-[140px]">{{ $t("Estimated Time:") }}</span>
                            <span>{{ product.shop?.estimated_delivery_time }}</span>
                        </div>

                        <div class="mt-4">
                            <h3 class="font-medium mb-2">{{ $t("Delivery Charges:") }}</h3>
                            <div class="space-y-2">
                                <div v-if="!deliveryWeights || deliveryWeights.length === 0" class="text-gray-500 text-sm">
                                    {{ $t("No delivery options available") }}
                                </div>
                                
                                <div v-else-if="!selectedSize" class="text-gray-500 text-sm">
                                    {{ $t("Please select a size to see delivery charges") }}
                                </div>
                                
                                <div v-else>
                                    <div v-for="weight in deliveryWeights" :key="weight.id" class="mb-2">
                                        <div v-if="weight && parseFloat(selectedSize.name) >= weight.min_weight && parseFloat(selectedSize.name) <= weight.max_weight"
                                            class="flex items-center justify-between p-3 bg-white rounded shadow-sm">
                                            <span>{{ weight.min_weight }} - {{ weight.max_weight }} {{ weightUnit }}</span>
                                            <span class="font-medium text-primary">{{ masterStore.showCurrency(weight.price) }}</span>
                                        </div>
                                    </div>

                                    <div v-if="!deliveryWeights.some(w => parseFloat(selectedSize.name) >= w.min_weight && parseFloat(selectedSize.name) <= w.max_weight)"
                                        class="text-gray-500 text-sm p-3 bg-gray-50 rounded">
                                        {{ $t("No delivery option available for this product weight") }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shop Info Tab -->
                    <div v-if="activeTab === 'shop'" class="space-y-4">
                        <router-link :to="{ name: 'shop-detail', params: { id: product.shop?.id }}" 
                            class="block hover:bg-slate-50 transition-colors rounded-lg p-3">
                            <div class="flex items-center gap-4">
                                <img :src="product.shop?.logo" alt="Shop logo" 
                                    class="w-16 h-16 rounded-full object-cover">
                                <div>
                                    <h3 class="font-medium">{{ product.shop?.name }}</h3>
                                    <div class="flex items-center gap-1 mt-1">
                                        <StarIcon class="w-4 h-4 text-yellow-400" />
                                        <span>{{ product.shop?.rating }}/5</span>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4 mt-6">
                    <!-- Quantity -->
                    <div v-if="cartProduct" class="flex items-center gap-4">
                        <button class="p-2 bg-slate-100 rounded" @click="decrementQty">
                            <MinusIcon class="w-5 h-5" />
                        </button>
                        <span class="w-12 text-center">{{ cartProduct.quantity }}</span>
                        <button class="p-2 bg-slate-100 rounded" @click="incrementQty">
                            <PlusIcon class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Action Buttons Row -->
                    <div class="flex gap-3">
                        <!-- Add to Cart -->
                        <button v-if="!cartProduct" @click="addToCart"
                            class="flex-1 py-3 bg-primary text-white rounded font-medium hover:bg-primary/90 transition-colors">
                            {{ $t("ADD TO CART") }}
                        </button>

                        <!-- Buy Now -->
                        <button @click="buyNow2"
                            class="flex-1 py-3 bg-primary text-white rounded font-medium hover:bg-primary/90 transition-colors">
                            {{ $t("BUY NOW") }}
                        </button>
                    </div>
                </div>

              

                <!-- App Promo -->
                <div class="mt-8">
                    <div v-if="ads && ads.length > 0" class="relative overflow-hidden rounded-lg">
                        <!-- Slider container -->
                        <div class="flex transition-transform duration-500 ease-in-out" 
                             :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
                            <div v-for="ad in ads" :key="ad.id" class="w-full flex-shrink-0">
                                <a :href="ad.link" target="_blank" class="block">
                                    <img :src="ad.image" :alt="ad.title" 
                                         class="w-full h-32 object-cover rounded-lg">
                                </a>
                            </div>
                        </div>

                        <!-- Navigation dots -->
                        <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-2">
                            <button v-for="(ad, index) in ads" 
                                    :key="index"
                                    @click="currentSlide = index"
                                    class="w-2 h-2 rounded-full transition-colors"
                                    :class="currentSlide === index ? 'bg-primary' : 'bg-gray-300'">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Products Section -->
        <div class="mt-12 mb-8">
            <h2 class="text-xl font-bold mb-6">{{ $t("Similar Products") }}</h2>
              <div  class=" gap-3 flex flex-wrap md:justify-start justify-center">         
<div  v-for="product in filteredRelatedProducts" 
                    :key="product.id" class="flex-none  w-[180px] md:w-[225px]">
                      <ProductCard 
                    :product="product" 
                    
                />
            </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, watch, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { HeartIcon, HomeIcon, MinusIcon, PlusIcon, ShareIcon } from "@heroicons/vue/24/outline";
import { HeartIcon as HeartIconFill, StarIcon } from "@heroicons/vue/24/solid";
import ProductCard from "../components/ProductCard.vue";
import PhotoVideo from "../components/PhotoVideo.vue";
import { useAuth } from "../stores/AuthStore";
import { useBasketStore } from "../stores/BasketStore";
import { useMaster } from "../stores/MasterStore";
import { useToast } from "vue-toastification";

const ads = ref([]);

const props = defineProps({
    product: {
        type: Object,
        required: true,
        default: () => ({
            videos: [],
            thumbnails: []
        })
    }
});

const route = useRoute();
const router = useRouter();
const masterStore = useMaster();
const basketStore = useBasketStore();
const authStore = useAuth();

const formData = ref({
    product_id: route.params.id,
    size: null,
    color: null,
    unit: null,
});

const product = ref({});
const productPrice = ref(0);
const mainPrice = ref(0);
const discountPercentage = ref(0);

const relatedProducts = ref([]);
const popularProducts = ref([]);

const aboutProduct = ref(true);
const review = ref(false);

const cartProduct = ref(null);

const weightUnit = ref('KG');
const deliveryWeights = ref([]);

const selectedSize = ref(null);
const selectedVariant = ref(null);
const master = useMaster();

const activeTab = ref('details');

const currentSlide = ref(0);
const autoSlideInterval = ref(null);

const selectedMediaType = ref('video');
const selectedMediaIndex = ref(0);

onMounted(() => {
    fetchProductDetails();
    window.scrollTo(0, 0);
    findProductInCart(route.params.id);
    if (product.value?.sizes?.length > 0) {
        handleSizeSelect(product.value.sizes[0]);
    }
    ads.value =master.ads;
    startAutoSlide();
});

watch(formData, () => {
    calculateProductPrice();
}, { deep: true });

const calculateProductPrice = () => {
    var sizePrice = 0;

    const size = product.value.sizes?.find((size) => size.id == formData.value.size);

    if (size) {
        sizePrice = size.price ?? 0;
    }

    if (product.value.discount_price > 0) {
        productPrice.value = product.value.discount_price + sizePrice;
        mainPrice.value = product.value.price + sizePrice;
    } else {
        productPrice.value = product.value.price + sizePrice;
        mainPrice.value = productPrice.value;
    }

    discountPercentage.value = (((mainPrice.value - productPrice.value) / mainPrice.value) * 100).toFixed(2);
}

const buyNow = async () => {
    if (authStore.token === null) {
        return (authStore.loginModal = true);
    }
    
    // Create a product object with all necessary properties
    const productWithDetails = {
        ...product.value,
        thumbnail: product.value.thumbnails?.[0]?.thumbnail || product.value.thumbnail, // Get thumbnail from thumbnails array
        quantity: 1
    };
    
    console.log('Product with details:', productWithDetails); // Debug log
    
    // If size is selected, include it
    if (formData.value.size) {
        const selectedSize = product.value.sizes?.find(size => size.id === formData.value.size);
        if (selectedSize) {
            productWithDetails.size = selectedSize;
        }
    }
    
    basketStore.buyNowProduct = productWithDetails; // Set buyNowProduct directly
    
   let addtocartres = await  basketStore.addToCart({
        product_id: formData.value.product_id,
        is_buy_now: true,
        quantity: 1,
        size: formData.value.size,
        color: formData.value.color,
        unit: null
    }, productWithDetails);

    basketStore.buyNowShopId = product.value?.shop.id;

    if (addtocartres?.status === 200) {
         router.push({ name: "buynow" });
    }
   
};

watch(route, () => {
    console.log("route",route.params.id);
    fetchProductDetails();
    aboutProduct.value = true;
    // console.log(product.value);
    review.value = false;
    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
    formData.value.product_id = route.params.id;
    findProductInCart(route.params.id);
});

const findProductInCart = (productId) => {
    let foundProduct = null;
    basketStore.products.forEach((item) => {
        item.products.find((product) => {
            if (product.id == productId) {
                return (foundProduct = product);
            }
        });
    });
    cartProduct.value = foundProduct;
    if (foundProduct) {
        formData.value.size = foundProduct.size?.id;
        formData.value.color = foundProduct.color?.id;
        formData.value.unit = foundProduct.unit;
    }
};

const addToCart = () => {
    basketStore.addToCart(formData.value, product.value);
    setTimeout(() => {
        findProductInCart(route.params.id);
    }, 500);
};

const buyNow2 = () => {
    basketStore.addToCart(formData.value, product.value);
    setTimeout(() => {
        findProductInCart(route.params.id);

         window.location = '/checkout';
    }, 500);

}

const decrementQty = () => {
    basketStore.decrementQuantity(product.value);
    setTimeout(() => {
        findProductInCart(route.params.id);
    }, 500);
};

const incrementQty = () => {
    basketStore.incrementQuantity(product.value);
    setTimeout(() => {
        findProductInCart(route.params.id);
    }, 500);
};

const favoriteAddOrRemove = () => {
    if (authStore.token === null) {
        return (authStore.loginModal = true);
    }
    axios.post('/favorite-add-or-remove', {
        product_id: product.value.id
    }, {
        headers: {
            Authorization: authStore.token
        }
    }).then(() => {
        product.value.is_favorite = !product.value.is_favorite
        if (product.value.is_favorite === false) {
            const content = {
                component: ToastSuccessMessage,
                props: {
                    title: 'Product removed from favorite',
                    message: 'Product removed from favorite successfully',
                },
            };
            toast(content, {
                type: "default",
                hideProgressBar: true,
                icon: false,
                position: "top-right",
                toastClassName: "vue-toastification-alert",
                timeout: 3000
            });
        } else {
            const content = {
                component: ToastSuccessMessage,
                props: {
                    title: 'Product added to favorite',
                    message: 'Product added to favorite successfully',
                },
            };
            toast(content, {
                type: "default",
                hideProgressBar: true,
                icon: false,
                position: "top-right",
                toastClassName: "vue-toastification-alert",
                timeout: 3000
            });
        }
        authStore.fetchFavoriteProducts();
    }).catch((error) => {
        console.log(error);
    });
};

const showReview = () => {
    aboutProduct.value = false;
    review.value = true;
    // fetchReviews();
};

const flashSale = ref({});
const vatTax = ref([]);
const fetchProductDetails = async () => {
    axios
        .get("/product-details", {
            params: { product_id: route.params.id },
            headers: {
                Authorization: authStore.token,
            },
        })
        .then((response) => {
            console.log("product details in product details", response.data.data);
            product.value = response.data.data.product;
            vatTax.value = response.data.data.vat_taxes || [];
            relatedProducts.value = response.data.data.related_products;
            popularProducts.value = response.data.data.popular_products;
            weightUnit.value = response.data.data.weight_unit || product.value.unit?.name || 'KG';
            deliveryWeights.value = response.data.data.delivery_weights || [];
            
            flashSale.value = response.data.data.product.flash_sale;
            ads.value = response.data.data.inner_ads;

            if (flashSale.value) {
                startCountdown();
            }

            // Select the first size by default if available
            if (product.value.sizes && product.value.sizes.length > 0) {
                const firstSize = product.value.sizes[0];
                formData.value.size = firstSize.id;
                selectedSize.value = firstSize;
            } else {
                formData.value.size = null;
                selectedSize.value = null;
            }
            
            calculateProductPrice();
            findProductInCart(route.params.id);
            console.log("weights",deliveryWeights.value);
        });
};

const averageRatings = ref({});

// const totalReviews = ref(0);
// const reviews = ref([]);

const currentPage = ref(1);
const perPage = ref(6);

const onClickHandler = (page) => {
    currentPage.value = page;
    // fetchReviews();
};

// const fetchReviews = async () => {
//     axios
//         .get("/reviews", {
//             params: {
//                 product_id: route.params.id,
//                 page: currentPage.value,
//                 per_page: perPage.value,
//             },
//         })
//         .then((response) => {
//             totalReviews.value = response.data.data.total;
//             reviews.value = response.data.data.reviews;
//             averageRatings.value = response.data.data.average_rating_percentage;
//         });
// };


const endDay = ref("");
const endHour = ref("");
const endMinute = ref("");
const endSecond = ref("");
let countdownInterval = null;

const startCountdown = () => {
    const endDate = new Date(flashSale.value?.end_date).getTime();

    if (flashSale.value?.end_date) {
        countdownInterval = setInterval(() => {
            const now = new Date().getTime();
            const timeLeft = endDate - now;

            if (timeLeft <= 0) {
                clearInterval(countdownInterval);
                endDay.value = "00";
                endHour.value = "00";
                endMinute.value = "00";
                endSecond.value = "00";
            } else {
                endDay.value = String(Math.floor(timeLeft / (1000 * 60 * 60 * 24))).padStart(2, "0");
                endHour.value = String(Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, "0");
                endMinute.value = String(Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, "0");
                endSecond.value = String(Math.floor((timeLeft % (1000 * 60)) / 1000)).padStart(2, "0");
            }
        }, 1000);
    }
};
onUnmounted(() => {
    clearInterval(countdownInterval);
    if (autoSlideInterval.value) {
        clearInterval(autoSlideInterval.value);
    }
});

// Watch for size changes
watch(selectedSize, (newSize) => {
    if (newSize) {
        selectedVariant.value = {
            weight: parseFloat(newSize.name), // Use size.name for the weight value
            size: newSize.name,
            price: newSize.price || 0
        };
    } else {
        selectedVariant.value = null;
    }
});

// Update size selection handler
const handleSizeSelect = (size) => {
    selectedSize.value = size;
    formData.value.size = size.id;
    calculateProductPrice();
};

// Watch for changes in ads data
watch(() => ads.value, (newAds) => {
    if (newAds && newAds.length > 0) {
        startAutoSlide();
    }
});

const startAutoSlide = () => {
    // Clear existing interval if any
    if (autoSlideInterval.value) {
        clearInterval(autoSlideInterval.value);
    }

    // Set new interval
    autoSlideInterval.value = setInterval(() => {
        if (ads.value && ads.value.length > 0) {
            currentSlide.value = (currentSlide.value + 1) % ads.value.length;
        }
    }, 3000); // Change slide every 3 seconds
};

// Optional: Pause sliding on hover
const pauseSlide = () => {
    if (autoSlideInterval.value) {
        clearInterval(autoSlideInterval.value);
    }
};

// Optional: Resume sliding after hover
const resumeSlide = () => {
    startAutoSlide();
};

const selectMedia = (type, index) => {
    selectedMediaType.value = type;
    selectedMediaIndex.value = index;
};

const filteredRelatedProducts = computed(() => {
    return relatedProducts.value?.filter(product => product.quantity > 0) || [];
});

</script>

<style scoped>
/* Remove any z-index from product details card */
.margin-container {
    position: relative;
    z-index: 1; /* Base z-index */
}

/* Add these styles */
.transition-transform {
    transition: transform 0.5s ease-in-out;
}

/* Optional: Add hover effect on ads */
.ad-hover:hover {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}

/* Custom scrollbar styles */
.scrollbar-thin {
    scrollbar-width: thin;
}

.scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
    background-color: #D1D5DB;
    border-radius: 6px;
}

.scrollbar-track-gray-100::-webkit-scrollbar-track {
    background-color: #F3F4F6;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}
</style>

