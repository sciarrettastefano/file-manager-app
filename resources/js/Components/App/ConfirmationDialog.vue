<template>
    <q-dialog :model-value="props.show" persistent>
        <q-card>
            <q-card-section>
                <q-toolbar class="bg-primary q-pa-sm">
                    <q-toolbar-title>Please Confirm</q-toolbar-title>
                </q-toolbar>
            </q-card-section>

            <q-card-section>
                <div class="q-ml-sm">
                    {{ props.message }}
                </div>
            </q-card-section>

            <q-card-actions align="right">
                <q-btn flat label="Cancel" color="primary" @click="onCancel" v-close-popup />
                <q-btn flat label="Confirm" color="primary" @click="onConfirm" v-close-popup />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>


<script setup>
// Imports


// Uses


// Refs


// Props & Emit
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    message: {
        type: String,
        default: '...'
    }
})

const emit = defineEmits(['cancel', 'confirm'])


// Computed


// Methods
function onCancel() {
    emit('cancel')
}

function onConfirm() {
    emit('confirm')
}


// Hooks


</script>


<style scoped>

</style>
