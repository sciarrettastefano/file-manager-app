<template>
    <q-btn padding="xs sm" color="primary" icon="more_horiz">
        <q-menu
            fit
            :offset="[140, 0]"
            width: auto
            auto-close
        >
            <q-list dense>
                <q-item clickable class="q-pa-xs" @click="onClick('option1')">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="edit" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        option1
                    </q-item-section>
                </q-item>
                <q-item clickable class="q-pa-xs" @click="onClick('option2')">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="change_circle" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        option2
                    </q-item-section>
                </q-item>
                <q-item clickable class="q-pa-xs" @click="onClick('option3')">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="edit" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        option3
                    </q-item-section>
                </q-item>
                <q-item clickable class="q-pa-xs" @click="onClick('option4')">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="change_circle" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        option4
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
const emit = defineEmits(['option1', 'option2', 'option3', 'option4'])

// Computed


// Methods
function onClick(toEmit) {
    emit(toEmit)
}

// Hooks


</script>


<style scoped>

</style>
