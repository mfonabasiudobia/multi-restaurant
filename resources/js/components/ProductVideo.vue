<template>
    <div class="thumbnail-container">
        <img
            :src="poster"
            class="w-full h-full object-cover"
            loading="lazy"
            alt="Product thumbnail"
        />
    </div>
</template>

<script setup>
defineProps({
    poster: String
});
</script>

<style scoped>
.thumbnail-container {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>