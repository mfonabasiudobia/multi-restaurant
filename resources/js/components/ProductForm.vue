<template>
    <!-- Add this in your form -->
    <div class="form-group">
        <label>{{ $t('Weight (KG)') }}</label>
        <input 
            type="number" 
            v-model="form.weight" 
            class="form-control" 
            step="0.01"
            min="0"
            required
        >
    </div>
</template>

<script setup>
const form = ref({
    // ... existing form fields
    weight: 0,
});
</script> 