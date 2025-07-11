<template>
  <div class="space-y-4 mt-3 transition duration-300">
    <div v-for="(shop, shopIndex) in groupedProducts" :key="'shop-' + shopIndex" class="px-4 py-3 bg-slate-50 rounded-xl border border-slate-100">
      <!-- Shop Name -->
      <div v-if="master.multiVendor" class="text-slate-950 text-base font-medium leading-normal mb-2">
        {{ shop.shop_name }}
      </div>

      <div class="space-y-2 divide-y divide-slate-200">
        <!-- Product items -->
        <div v-for="product in shop.products" :key="product.id" class="flex gap-4 justify-start w-full items-center pt-1">
          <div class="w-[72px] h-[95px]">
            <img
              :src="product?.thumbnail"
              class="w-full h-full object-contain"
            />
          </div>
          <div class="flex flex-col gap-1 w-full">
            <!-- Brand -->
            <div class="text-primary text-xs font-normal leading-none">
              {{ product?.brand }}
            </div>
            <!-- Product Name -->
            <div class="text-slate-950 text-base font-normal leading-normal">
              {{ product?.name }}
            </div>
            <div class="flex flex-wrap justify-between items-center gap-3">
              <!-- Size and color -->
              <div class="flex items-center gap-1">
                <div v-if="product?.size"
                    class="min-w-8 text-center px-2 py-1 bg-slate-100 rounded text-slate-800 text-xs font-normal"
                >
                    {{ product?.size?.name }}{{ product?.unit?.name ? ' ' + product?.unit?.name : '' }}
                    <span v-if="product?.size?.price" class="text-primary">
                      (+{{ master.showCurrency(product?.size?.price) }})
                    </span>
                </div>
                <div v-if="product?.color"
                    class="px-2 py-1 bg-slate-100 rounded text-slate-800 text-xs font-normal"
                >
                    {{ product?.color?.name }}
                </div>
              </div>
              <!-- quantity and price -->
              <div class="text-slate-800 text-base font-normal leading-normal">
                {{ product.quantity }} X {{ master.showCurrency((product?.discount_price > 0) ? product?.discount_price : product?.price) }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuth } from "../stores/AuthStore";
import { useBasketStore } from "../stores/BasketStore";
import { useMaster } from "../stores/MasterStore";
import { computed } from 'vue';

const AuthStore = useAuth();
const master = useMaster();
const basketStore = useBasketStore();

// Group products by shop
const groupedProducts = computed(() => {
  if (!basketStore.buyNowProduct) return [];
  
  // If buyNowProduct already has products grouped by shop
  if (basketStore.buyNowProduct.products) {
    return [basketStore.buyNowProduct];
  }
  
  // Otherwise, group products by shop
  const shopGroups = {};
  
  // Handle case where buyNowProduct is a single product
  const products = Array.isArray(basketStore.buyNowProduct) 
    ? basketStore.buyNowProduct 
    : [basketStore.buyNowProduct];
  
  products.forEach(product => {
    console.log(product);
    const shopId = product.shop_id;
    if (!shopGroups[shopId]) {
      shopGroups[shopId] = {
        shop_id: shopId,
        shop_name: product.shop_name || 'Shop',
        products: []
      };
    }
    shopGroups[shopId].products.push(product);
  });
  
  return Object.values(shopGroups);
});

</script>

<style lang="scss" scoped></style>
