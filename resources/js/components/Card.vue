<template>
    <div class="bg-white rounded-lg p-6 text-center transition-transform duration-300 ease-in-out border border-primary hover:transform hover:-translate-y-2 hover:shadow-xl">
        <div class="icon mb-6 flex justify-center">
            <img :src="getIconPath" alt="icon" class="w-12 h-12 md:w-14 md:h-14 lg:w-16 lg:h-16 ">
        </div>
        <h3 class="text-lg font-bold mb-4 text-gray-800 font-roboto md:text-xl">{{ title }}</h3>
        <p class="text-sm leading-relaxed text-gray-600 font-roboto md:text-base">{{ description }}</p>
    </div>
</template>

<script>
export default {
    name: 'Card',
    props: {
        icon: {
            type: String,
            required: true
        },
        title: {
            type: String,
            required: true
        },
        description: {
            type: String,
            required: true
        }
    },
    computed: {
        getIconPath() {
            return `/assets/icons/${this.icon}.png` // Assuming you have SVG icons in public/icons folder
        }
    }
}
</script>

<style scoped>



/* Remove duplicate CSS classes since we're using Tailwind */
</style>
