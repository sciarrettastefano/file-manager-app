<template>
    <q-btn padding="xs sm"
        color="primary"
        icon="fa-solid fa-clock-rotate-left"
        @click="onClick"
    />
</template>


<script setup>
// Imports


// Uses


// Refs


// Props & Emit
const emit = defineEmits(['openVersionsModal'])

// Computed


// Methods
function onClick() {
    emit('openVersionsModal')
}

// Hooks


</script>


<style scoped>

</style>
