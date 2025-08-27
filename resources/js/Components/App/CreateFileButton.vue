<template>
    <q-btn
        color="primary"
        icon="add"
        label="Create"
    >
        <q-menu
            fit
            width: auto
            auto-close
        >
            <q-list dense>
                <q-item clickable class="q-pa-xs" @click="onOptionClick('createFolder')">
                    <q-item-section no-wrap>
                        Create Folder
                    </q-item-section>
                </q-item>
                <q-item clickable class="q-pa-xs" @click="onOptionClick('upload')">
                    <q-item-section no-wrap>
                        Upload Items
                    </q-item-section>
                </q-item>
            </q-list>
        </q-menu>
    </q-btn>



</template>


<script setup>
// Imports
import { ref } from 'vue';


// Uses


// Refs
const files = ref(null)


// Props & Emit
const emit = defineEmits(['createFolder', 'upload'])


// Computed


// Methods
function onOptionClick(option) {
    emit(option)
}


// Hooks


</script>


<style scoped>

</style>
