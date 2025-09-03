<template>
    <q-btn
        color="primary"
        icon="download"
        label="Download"
        target="_blank"
        @click="downloadFiles"
    />
</template>


<script setup>
// Imports
import {router} from '@inertiajs/vue3'

// Uses


// Refs


// Props & Emit
const props = defineProps({
    fileIds: {
        type: Array,
        required: true
    }
})


// Computed


// Methods
function downloadFiles() {
    let params = new URLSearchParams()
    props.fileIds.forEach(id => {
        params.append('file_ids[]', id)
    })
    window.open(route('files.download') + '?' + params.toString(), '_blank')
}

// Hooks


</script>


<style scoped>

</style>
