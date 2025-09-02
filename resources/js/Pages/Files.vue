<template>
    <Head title="Files" />
    <AuthenticatedLayout>
        <q-page-container>
            <q-page>
                <q-toolbar class="q-pa-sm column">
                    <!-- Prima Riga Toolbar -->
                    <div class="row justify-between q-gutter-md full-width q-pa-sm">
                        <!-- filtri -->
                        <q-select
                            outlined
                            v-model="filters.search_owner"
                            use-input
                            input-debounce="0"
                            label="Select User"
                            :options="filteredUserOptions"
                            @filter="filterUser"
                            @update:model-value="fetchFiles"
                        >
                            <template v-slot:no-option>
                            <q-item>
                                <q-item-section class="text-grey">
                                No results
                                </q-item-section>
                            </q-item>
                            </template>
                        </q-select>
                        <q-input v-model="filters.search_name" clearable outlined placeholder="Search by name" class="col"
                            @keydown.enter="fetchFiles" />
                        <q-input v-model="filters.group" clearable outlined placeholder="Search by group" class="col"
                            @keydown.enter="fetchFiles" />
                    </div>
                    <!-- Seconda Riga Toolbar -->
                    <div class="row justify-between items-center q-gutter-md full-width">
                        <!-- pulsanti tags -->
                        <div class="q-pa-sm">
                            <p> *** Pulsanti Tags ***</p>
                        </div>
                        <!-- pulsanti mass actions -->
                        <div class="q-pa-sm">
                            <CreateFileButton class="q-ml-md" @createFolder="onShow('createFolder')"
                                @upload="onShow('upload')" />
                            <q-btn class="q-ml-md" color="primary" icon="add" label="prova" @click="onShow('')" />
                        </div>
                    </div>
                    <!-- Terza Riga Toolbar -->
                    <div class="row justify-between items-center q-gutter-md full-width">
                        <!-- breadcrumbs -->
                        <div class="q-pa-sm">
                             <div class="q-pa-md q-gutter-sm">
                                <q-breadcrumbs>
                                    <q-breadcrumbs-el
                                        label="Home"
                                        icon="home"
                                        class="cursor-pointer"
                                        @click="fetchFiles"
                                    />
                                    <q-breadcrumbs-el
                                        v-for="ans in props.ancestors.data.filter(a => a.parent_id !== null)"
                                        :key="ans.id"
                                        :label="ans.name"
                                        class="cursor-pointer"
                                        @click="fetchFiles(ans)"
                                    />
                                </q-breadcrumbs>
                            </div>
                        </div>
                        <!-- pulsanti mass actions -->
                        <div class="q-pa-sm">
                            <q-btn class="q-ml-md" color="primary" icon="add" label="prova" @click="onShow('')" />
                            <q-btn class="q-ml-md" color="primary" icon="add" label="prova" @click="onShow('')" />
                        </div>
                    </div>
                </q-toolbar>
                <q-table flat bordered title="Files" table-header-class="bg-orange-2" :columns="columns" :rows="rows"
                    row-key="id" selection="multiple" v-model:selected="selected" :pagination.sync="pagination"
                    @row-click="toggleRowSelection" @row-dblclick="openFolder">
                    <template v-slot:header-selection="scope">
                        <q-checkbox v-model="scope.selected" />
                    </template>

                    <template v-slot:body-selection="scope">
                        <q-checkbox :model-value="scope.selected"
                            @update:model-value="(val, evt) => { Object.getOwnPropertyDescriptor(scope, 'selected').set(val, evt) }" />
                    </template>

                    <!-- Slot per inserire icona e nome -->
                    <template v-slot:header-cell-name="props">
                        <q-th>
                            <div class="q-ml-lg text-left">
                                {{ props.col.label }}
                            </div>
                        </q-th>
                    </template>

                    <template v-slot:body-cell-name="props">
                        <q-td>
                            <div class="flex flex-row items-center">
                                <FileIcon :file="props.row" />
                                <div class="q-pl-sm">
                                    {{ props.row.name }}
                                </div>
                            </div>
                        </q-td>
                    </template>

                    <!-- Slot per inserire tags -->
                    <template v-slot:body-cell-tags="props">
                        <q-td>
                            <div class="flex flex-row items-center q-gutter-sm">
                                <q-badge size="md" color="primary" text-color="white" label="tag1" />
                                <q-badge color="primary" text-color="white" label="tag2" />
                                <span v-for="tag in props.tags">
                                    <q-badge class="color-blue-500 text-color-white q-mr-xs">
                                        {{ tag.name }}
                                    </q-badge>
                                </span>
                            </div>
                        </q-td>
                    </template>

                    <!-- Slot per inserire pulsante history -->
                    <template v-slot:body-cell-history="props">
                        <q-td>
                            <FilesInlineVersionsMenu @click.prevent.stop @openVersionsModal="" />
                        </q-td>
                    </template>

                    <!-- Slot per inserire pulsante per in-line actions -->
                    <template v-slot:body-cell-actions="props">
                        <q-td>
                            <FilesInlineActionsMenu @click.prevent.stop @option1="" @option2="" @option3=""
                                @option4="" />
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
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue'
import _ from 'lodash'
import CreateFileButton from '@/Components/App/CreateFileButton.vue';
import CreateFolderModal from '@/Components/App/CreateFolderModal.vue';
import UploadFilesModal from '@/Components/App/UploadFilesModal.vue';
import FileIcon from '@/Components/App/FileIcon.vue';
import FilesInlineActionsMenu from '@/Components/App/FilesInlineActionsMenu.vue';
import FilesInlineVersionsMenu from '@/Components/App/FilesInlineVersionsMenu.vue';


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
    },
    filters: {
        type: Object,
        default: {}
    },
    users: {
        type: Object,
        default: {}
    }
})


// Uses


// Refs
const filters = ref(_.cloneDeep(props.filters))

const selected = ref([])

const pagination = ref({
    page: 1,           // pagina corrente
    rowsPerPage: 10,   // righe per pagina
})

const showCreateFolderModal = ref(false)
const showUploadModal = ref(false)

const userOptions = ref(_.cloneDeep(props.users.data.map(u => u.email)))
const filteredUserOptions = ref(_.cloneDeep(userOptions.value))

// Computed
const rows = computed(() => _.cloneDeep(props.files.data ?? []))
const columns = computed(() => [
    { name: 'name', align: 'left', label: 'Name' },
    { name: 'tags', align: 'left', label: 'Tags', field: (row) => row.tags ?? [] },
    { name: 'lastModified', align: 'left', label: 'Last Modified', field: (row) => row.updated_at ?? '', sortable: true },
    { name: 'history', align: 'left', label: 'History', field: (row) => row.id ?? '' },
    { name: 'actions', align: 'left', label: 'Actions' }
])


// Methods
function toggleRowSelection(evt, row) { // Selezione record tramite clic su riga
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

function openFolder(evt, row) {
    if (!row.is_folder) return

    fetchFiles(row)
}

function fetchFiles(folder = null) {
    router.get(route('files.index', _.get(folder, "id", props.folder)), {
        ...filters.value,
    }, {
        preserveState: false
    })
}

function filterUser (val, update) {
    if (val === '') {
        update(() => {
            filteredUserOptions.value = userOptions.value
        })
        return
    }
    update(() => {
        const needle = val.toLowerCase()
        filteredUserOptions.value = userOptions.value.filter(v => v.toLowerCase().indexOf(needle) > -1)
    })
}


// Hooks
watch(props.files.data, () => {
    rows.value = _.cloneDeep(props.files.data)
})


</script>
