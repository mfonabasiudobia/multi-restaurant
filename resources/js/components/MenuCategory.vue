<template>
    <div class="relative">
        <router-link 
            :to="'/categories/' + category.id"
            class="p-2 uppercase text-sm font-semibold transition-all category-link"
            :class="{ 'active-category': isActive }"
            v-slot="{ navigate, isActive: routerIsActive }"
        >
            <span @click="handleClick(navigate)" :class="{ 'active': routerIsActive || isActive }">
                {{ category.name }}
            </span>
        </router-link>
    </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();

const props = defineProps({
    category: { type: Object, required: true }
});

const emit = defineEmits(['update:click']);

// Check if this category is currently active based on the route
const isActive = computed(() => {
    // Check if we're on a category page and if the ID matches
    return route.path.includes('/categories/') && 
           route.params.id == props.category.id;
});

const handleClick = (navigate) => {
    emit('update:click', true);
    navigate(); // This will handle the navigation
}
</script>

<style scoped>
.relative {
    position: relative;
    display: inline-block;
}

.category-link {
    display: inline-block;
    position: relative;
    text-decoration: none;
}

.active-category {
    color: var(--primary-color, #4f46e5);
    font-weight: 700;
}

.active-category::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: var(--primary-color, #4f46e5);
    transform: scaleX(1);
    transition: transform 0.3s ease;
}

.category-link:not(.active-category)::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: var(--primary-color, #4f46e5);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.category-link:not(.active-category):hover::after {
    transform: scaleX(1);
}

span.active {
    color: var(--primary-color, #4f46e5);
    font-weight: 700;
}
</style>
