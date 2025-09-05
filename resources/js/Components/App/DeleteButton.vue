<template>
    <q-btn
        class="q-ml-md"
        color="primary"
        icon="delete"
        label="Delete"
        @click="onDeleteClick"
    />

    <ConfirmationDialog
        :show="showConfirmationDeleteDialog"
        :message="'Are you sure you want to delete this file?'"
        @cancel="onDeleteCancel"
        @confirm="onDeleteConfirm"
    />
</template>


<script setup>
// Imports
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmationDialog from './ConfirmationDialog.vue';


// Uses
const deleteFilesForm = useForm({
    file_ids: {
        type: Array,
        required: true,
        default: []
    }
})


// Refs
const showConfirmationDeleteDialog = ref(false)


// Props & Emit
const props = defineProps({
    fileIds: {
        type: Array,
        required: true
    }
})

const emit = defineEmits(['deleted'])


// Computed


// Methods
function onDeleteClick() {
    if (!props.fileIds.length) {
        console.log('No file IDs provided for delete.')
        return
    }
    showConfirmationDeleteDialog.value = true
}

function onDeleteCancel() {
    showConfirmationDeleteDialog.value = false
}

function onDeleteConfirm() {
    deleteFilesForm.file_ids = props.fileIds
    deleteFilesForm.delete(route('files.delete'), {
        onSuccess: () => {
            emit('deleted', deleteFilesForm.file_ids)
        },
        onError: (errors) => {
            console.error('Error deleting file:', errors)
        },
        onFinish: () => {
            showConfirmationDeleteDialog.value = false
            deleteFilesForm.reset()
        }
    })
}

// Hooks


</script>


<style scoped>

</style>
