<template>
    <q-btn
        color="primary"
        icon="change_circle"
        label="Change Status"
    >
        <q-menu
            fit
            width: auto
            auto-close
        >
            <q-list dense>
                <q-item clickable class="q-pa-xs" @click="onOptionClick('activate')">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="power" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        Activate
                    </q-item-section>
                </q-item>
                <q-item clickable class="q-pa-xs" @click="onOptionClick('deactivate')">
                    <q-item-section avatar class="q-pr-xs" style="min-width: 20px;">
                        <q-icon name="power_off" size="sm" />
                    </q-item-section>
                    <q-item-section no-wrap>
                        Deactivate
                    </q-item-section>
                </q-item>
            </q-list>
        </q-menu>
    </q-btn>

    <ConfirmationDialog
        :show="showDialog"
        :message="'Are you sure you want to change the status of the selected users?'"
        @cancel="onCancel"
        @confirm="onConfirm(action)"
        />

</template>


<script setup>
// Imports
import { ref } from 'vue';
import ConfirmationDialog from './ConfirmationDialog.vue';
import { useForm } from '@inertiajs/vue3';
import { useQuasar } from 'quasar';


// Uses
const $q = useQuasar()
const form = useForm({
    ids: [],
    status: null
})


// Refs
const showDialog = ref(false)
const action = ref('')


// Props & Emit
const props = defineProps({
    changeIds: {
        type: Array,
        required: true
    }
})

const emit = defineEmits(['change'])


// Computed


// Methods
function onOptionClick(option) {
    if (!props.changeIds.length) {
        console.log('da mostrare errorDialog perchè lista id è vuota')
        return
    }
    action.value = option
    showDialog.value = true
}

function onConfirm(action) {
    form.ids = props.changeIds
    switch(action) {
        case('activate'):
            form.status = true
            break
        case('deactivate'):
            form.status = false
            break
    }
    form.patch(route('users.massChangeStatus'), {
        onSuccess: () => {
            emit('change')
            $q.notify({
                color: 'green-4',
                textColor: 'white',
                icon: 'cloud_done',
                message: 'Users status updated correctly.'
            })
        },
        onError: () => {
            $q.notify({
                color: 'red-5',
                textColor: 'white',
                icon: 'warning',
                message: 'An error was detected.'
            })
        }
    })
    onCancel()
}

function onCancel() {
    showDialog.value = false
}

// Hooks


</script>


<style scoped>

</style>
