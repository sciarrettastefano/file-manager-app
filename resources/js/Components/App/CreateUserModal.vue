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
                    <q-input
                        filled
                        v-model="form.email"
                        type="text"
                        label="Email"
                        hint="Insert the user's email"
                    />
                    <q-select
                        filled
                        v-model="form.role"
                        label="Role"
                        hint="Select the user's role"
                        :options="options"
                    />
                    <q-input
                        filled
                        v-model="form.password"
                        type="text"
                        label="Password"
                        hint="Insert the user's password"
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
import { useForm } from '@inertiajs/vue3'
import { useQuasar } from 'quasar'

// Uses
const $q = useQuasar()
const form = useForm(
    {name: '',
    email: '',
    role: null,
    password: ''}
)

const options = ['user', 'admin']


// Refs


// Props & Emit
const emit = defineEmits(['cancel'])

// Computed


// Methods
function onSubmit() {
    form.post(route('users.create'), {
        onSuccess: () => {
            $q.notify({
                color: 'green-4',
                textColor: 'white',
                icon: 'cloud_done',
                message: 'User created correctly.'
            })
        },
        onError: () => {
            $q.notify({
                color: 'red-5',
                textColor: 'white',
                icon: 'warning',
                message: 'An error was detected.'
            })
        },
        onFinish: () => onCancel()
    })
}

function onCancel() {
    form.reset()
    emit('cancel')
}


// Hooks


</script>


<style scoped>

</style>

