import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useSubpageStore = defineStore('subpageStore', () => {
    const subpages = ref([]);

    const fetchSubpages = async () => {
        try {
            const response = await fetch('/api/subpages');
            const data = await response.json();
            subpages.value = data;
        } catch (error) {
            console.error('Error fetching subpages:', error);
        }
    };

    // Get only "About" section subpages
    const aboutSubpages = computed(() => subpages.value.filter(sp => sp.section === 'about'));
    const informationSubpages = computed(() => subpages.value.filter(sp => sp.section === 'information'));

    return {
        subpages,
        fetchSubpages,
        aboutSubpages,
        informationSubpages
    };
});