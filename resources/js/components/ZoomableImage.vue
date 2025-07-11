<template>
    <div class="relative w-full">
        <img 
            :src="src" 
            :alt="alt"
            class="w-full h-full object-contain cursor-zoom-in"
            ref="zoomableImage"
        />
    </div>
</template>

<style>
.medium-zoom-overlay {
    z-index: 9999 !important;
}
.medium-zoom-image--opened {
    z-index: 10000 !important;
}
</style>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import mediumZoom from 'medium-zoom';

const props = defineProps({
    src: {
        type: String,
        required: true
    },
    alt: {
        type: String,
        default: ''
    }
});

const zoomableImage = ref(null);
let zoom = null;

onMounted(() => {
    zoom = mediumZoom(zoomableImage.value, {
        margin: 24,
        background: 'rgba(0, 0, 0, 0.9)',
        scrollOffset: 0,
    });
});

onBeforeUnmount(() => {
    if (zoom) {
        zoom.detach();
    }
});
</script> 