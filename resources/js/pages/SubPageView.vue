<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSubpageStore } from '../stores/SubpageStore';

const route = useRoute();
const router = useRouter();
const subpageStore = useSubpageStore();
const subpage = ref(null);

const fetchSubpage = async () => {
    await subpageStore.fetchSubpages(); // Ensure subpages are loaded
    subpage.value = subpageStore.subpages.find(sp => sp.slug === route.params.slug);

    if (!subpage.value) {
        router.push('/'); // Redirect to home if subpage not found
    }
};

onMounted(fetchSubpage);
watch(() => route.params.slug, fetchSubpage); // Fetch again when slug changes
</script>

<template>
    <div v-if="subpage" class="container mx-auto p-6">
        <h1 class="text-2xl font-bold text-gray-800 text-center">{{ subpage.title }}</h1>
        <div class="mt-4 text-gray-600 prose prose-lg max-w-full" v-html="subpage.content"></div>
    </div>
</template>