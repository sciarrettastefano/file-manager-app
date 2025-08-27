<template>
    <Head title="Files" />
    <AuthenticatedLayout>
        <q-page-container>
            <q-page>
                <q-toolbar class="q-pa-sm column">
                    <div class="row q-gutter-md full-width q-pa-sm">
                        <!-- implementare filtri -->
                        <q-input
                            outlined
                            placeholder="Search by name"
                            class="col"
                        />
                        <q-input
                            outlined
                            placeholder="Search by owner"
                            class="col"
                        />
                        <q-input
                            outlined
                            placeholder="Search by group"
                            class="col"
                        />
                    </div>
                    <div class="row justify-end q-gutter-md full-width q-pa-sm">
                        <!-- seconda riga - inserire pulsanti mass actions -->
                        <CreateFileButton
                            @createFolder="onShow('createFolder')"
                            @upload="onShow('upload')"
                        />
                        <q-btn color="primary" icon="add" label="prova" @click="onShow('')" />
                    </div>
                    <div class="row justify-end q-gutter-md full-width q-pa-sm">
                        <!-- terza riga - inserire pulsanti mass actions -->
                        <q-btn color="primary" icon="add" label="prova" @click="onShow('')" />
                        <q-btn color="primary" icon="add" label="prova" @click="onShow('')" />
                    </div>
                </q-toolbar>
                <q-table
                    flat
                    bordered
                    title="Files"
                    table-header-class="bg-orange-2"
                    :columns="columns"
                    :rows="rows"
                    row-key="id"
                    selection="multiple"
                    v-model:selected="selected"
                    :pagination.sync="pagination"
                    @row-click="toggleRowSelection"
                >
                    <template v-slot:header-selection="scope">
                        <q-checkbox v-model="scope.selected" />
                    </template>

                    <template v-slot:body-selection="scope">
                        <q-checkbox :model-value="scope.selected" @update:model-value="(val, evt) => { Object.getOwnPropertyDescriptor(scope, 'selected').set(val, evt) }" />
                    </template>

                    <!-- Slot per inserire tags -->

                    <!-- Slot per inserire pulsante history -->

                    <!-- Slot per inserire pulsante per in-line actions -->
                    <template v-slot:body-cell-actions="props">
                        <q-td>
                            <!-- Inserire in-line actions per files -->
                        </q-td>
                    </template>

                    <!-- Slot per eventuale messaggio se non ho dati -->
                    <template v-slot:no-data="{ message }">
                        <div class="full-width row flex-center text-accent q-py-md">
                            <q-card class="text-black">
                                <div class="row items-center">
                                    <q-card-section>
                                        <q-icon name="warning" size="2em" />
                                        <span>{{ message }}</span>
                                    </q-card-section>
                                </div>
                            </q-card>
                        </div>
                    </template>
                </q-table>

                <CreateFolderModal v-model="showCreateFolderModal" @cancel="handleCancel('createFolder')" />
                <UploadFilesModal v-model="showUploadModal" @cancel="handleCancel('upload')" />

            </q-page>
        </q-page-container>
    </AuthenticatedLayout>
</template>


<script setup>
// Imports
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue'
import _ from 'lodash'
import { useQuasar } from 'quasar';
import CreateFileButton from '@/Components/App/CreateFileButton.vue';
import CreateFolderModal from '@/Components/App/CreateFolderModal.vue';
import UploadFilesModal from '@/Components/App/UploadFilesModal.vue';


// Props & Emit
const props = defineProps({
    files: {
        type: Object,
        default: {}
    },
    folder: {
        type: Object,
        default: {}
    },
    ancestors: {
        type: Object,
        default: {}
    }
})


// Uses


// Refs
const rows = ref(props.files.data ?? [])
// ************************ !!!! COLONNE TABELLA (DA SISTEMARE) !!!! ************************
const columns = computed(() => [
    { name: 'name', align: 'left', label: 'Name', field: (row) => row.name ?? '', sortable: true },
    { name: 'tags', align: 'left' , label: 'Tags', field: (row) => row.tags ?? '' },
    { name: 'owner', align: 'left' , label: 'Owner', field: (row) => row.owner ?? '', sortable: true },
    { name: 'lastModified', align: 'left' , label: 'Last Modified', field: (row) => row.updated_at ?? '', sortable: true },
    { name: 'history', align: 'left' , label: 'History', field: (row) => row.id ?? '', sortable: true },
    { name: 'actions', align: 'left' , label: 'Actions'}
])

const selected = ref([])

const pagination = ref({
    page: 1,           // pagina corrente
    rowsPerPage: 10,   // righe per pagina
})

const showCreateFolderModal = ref(false)
const showUploadModal = ref(false)

// Computed

// Methods
function toggleRowSelection (evt, row) { // Selezione record tramite clic su riga
    const index = selected.value.findIndex(r => r.id === row.id)
    if (index > -1) {
        // già selezionato -> lo tolgo
        selected.value.splice(index, 1)
    } else {
        // non selezionato -> lo aggiungo
        selected.value.push(row)
    }
}

function onShow(modal) {
    switch (modal) {
        case 'createFolder':
            showCreateFolderModal.value = true
            break
        case 'upload':
            showUploadModal.value = true
            break
    }
}

function handleCancel(modal) {
    switch (modal) {
        case 'createFolder':
            showCreateFolderModal.value = false
            break
        case 'upload':
            showUploadModal.value = false
            break
    }
}

// Hooks
watch(() => props.files.data, (newData) => {
    rows.value = _.cloneDeep(newData)
})


</script>

