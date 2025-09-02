<template>
    <q-dialog persistent>
        <q-card style="width: 50vw; max-width: 50vw;">
            <q-card-section>
                <q-form
                    class="q-gutter-md"
                    @submit="onSubmit"
                >
                    <q-input
                        filled
                        v-model="form.name"
                        type="text"
                        label="Name"
                        hint="Insert the user's name"
                    />
                    <div class="row justify-end q-gutter-sm">
                        <q-btn label="Cancel" flat color="primary" @click="onCancel"/>
                        <q-btn label="Submit" type="submit" color="primary"/>
                    </div>
                </q-form>
            </q-card-section>
        </q-card>
    </q-dialog>
</template>



<script setup>
// Imports
import { useForm, usePage } from '@inertiajs/vue3'
import { useQuasar } from 'quasar'

// Uses
const $q = useQuasar()
const form = useForm(
    {name: '',
    parent_id: null}
)
const page = usePage()


// Refs


// Props & Emit
const emit = defineEmits(['cancel'])

// Computed


// Methods
function onSubmit() {
    form.parent_id = page.props.folder.data.id
    form.post(route('files.createFolder'), {
        onSuccess: () => {
            $q.notify({
                color: 'green-4',
                textColor: 'white',
                icon: 'cloud_done',
                message: 'Folder created correctly.'
            })
        },
        onError: () => {
            console.error(error)
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
    form.reset()
    emit('cancel')
}


// Hooks


</script>


<style scoped>

</style>

