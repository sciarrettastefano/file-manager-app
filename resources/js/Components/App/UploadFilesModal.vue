<template>
    <q-dialog persistent>
        <q-card style="width: 50vw; max-width: 50vw;">
            <q-card-section>
                <div class="q-px-sm q-pt-md">
                    <q-select
                        filled
                        v-model="mode"
                        label="Mode"
                        hint="Select the upload mode"
                        :options="options"
                    />
                </div>
                <div class="q-px-sm q-py-md">
                    <q-file
                        v-if="mode == 'Upload Files'"
                        v-model="form.files"
                        filled
                        type="file"
                        label="Pick Files"
                        use-chips
                        clearable
                        multiple
                        @update:model-value="onFilesUpdate"
                    />
                    <q-file
                        v-if="mode == 'Upload Folder'"
                        v-model="form.files"
                        filled
                        type="file"
                        label="Pick Folder"
                        use-chips
                        clearable
                        multiple
                        directory
                        webkitdirectory
                        @update:model-value="onFilesUpdate"
                    />
                </div>
                <div class="row justify-end q-gutter-sm">
                    <q-btn label="Cancel" flat color="primary" @click="onCancel" />
                    <q-btn label="Submit" color="primary" @click="onSubmitUpload" />
                </div>
            </q-card-section>
        </q-card>
    </q-dialog>
</template>



<script setup>
// Imports
import { useForm, usePage } from '@inertiajs/vue3'
import { useQuasar } from 'quasar'
import { ref } from 'vue'


// Uses
const $q = useQuasar()
const form = useForm({
    files: [],
    relative_paths: [],
    parent_id: null
})
const page = usePage()

const options = ['Upload Files', 'Upload Folder']

// Refs
const mode = ref('Upload Files') // Mode impostata a 'files' di default

// Props & Emit
const emit = defineEmits(['cancel'])
const props = defineProps({
    errors: {
        type: Array,
        default: []
    }
})

// Computed


// Methods
function onFilesUpdate(files) {
  form.files = files.filter(f => !f.name.startsWith('.'))
}

function onSubmitUpload() {
    form.parent_id = page.props.folder.data.id
    form.files = [...form.files].filter(f => !f.name.startsWith('.'))
    form.relative_paths = [...form.files].map(f => f.webkitRelativePath)

    form.post(route('files.store'), {
        onSuccess: () => {
            $q.notify({
                color: 'green-4',
                textColor: 'white',
                icon: 'cloud_done',
                message: 'Files uploaded correctly.'
            })
        },
        onError: (error) => {
            console.error(error)
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

