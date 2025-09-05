<template>
    <q-btn padding="xs sm" color="primary" icon="more_horiz">
        <q-menu
            fit
            :offset="[140, 0]"
            width: auto
            auto-close
        >
            <q-list dense>
                <q-item clickable class="q-pa-xs" @click="onClick('edit')">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="edit" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        Edit
                    </q-item-section>
                </q-item>
                <q-item clickable class="q-pa-xs" @click="onClick('share')">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="share" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        Share
                    </q-item-section>
                </q-item>
                <q-item clickable class="q-pa-xs" target="_blank" @click="downloadFile">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="download" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        Download
                    </q-item-section>
                </q-item>
                <q-item clickable class="q-pa-xs" @click="onDeleteClick">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="delete" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        Delete
                    </q-item-section>
                </q-item>
            </q-list>
        </q-menu>
    </q-btn>

    <ConfirmationDialog
        :show="showConfirmationDeleteDialog"
        :message="'Are you sure you want to delete this file?'"
        @cancel="onDeleteCancel"
        @confirm="onDeleteConfirm"
    />

</template>


<script setup>
// Imports
import { ref } from 'vue'
import ConfirmationDialog from './ConfirmationDialog.vue'
import { useForm } from '@inertiajs/vue3'


// Uses
const deleteFileForm = useForm({
    file_ids: []
})


// Refs
const showConfirmationDeleteDialog = ref(false)


// Props & Emit
const emit = defineEmits(['edit', 'share', 'deleted'])
const props = defineProps({
    id: {
        type: Number,
        required: true
    }
})

// Computed


// Methods
function onClick(toEmit) {
    emit(toEmit)
}

function downloadFile() {
    let params = new URLSearchParams()
    params.append('file_ids[]', props.id)
    window.open(route('files.download') + '?' + params.toString(), '_blank')
}

function onDeleteClick() {
    if (!props.id) {
        console.log('No file ID provided for delete.')
        return
    }
    showConfirmationDeleteDialog.value = true
}

function onDeleteCancel() {
    showConfirmationDeleteDialog.value = false
}

function onDeleteConfirm() {
    deleteFileForm.file_ids = [props.id]
    deleteFileForm.delete(route('files.delete'), {
        onSuccess: () => {
            emit('deleted', deleteFileForm.file_ids)
        },
        onError: (errors) => {
            console.error('Error deleting file:', errors)
        },
        onFinish: () => {
            showConfirmationDeleteDialog.value = false
            deleteFileForm.reset()
        }
    })
}

// Hooks


</script>


<style scoped>

</style>
