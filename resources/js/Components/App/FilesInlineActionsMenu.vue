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
                <q-item clickable class="q-pa-xs" @click="onClick('delete')">
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
</template>


<script setup>
// Imports


// Uses


// Refs


// Props & Emit
const emit = defineEmits(['edit', 'share', 'delete'])
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


// Hooks


</script>


<style scoped>

</style>
