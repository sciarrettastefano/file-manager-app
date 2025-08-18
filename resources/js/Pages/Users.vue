<template>
    <Head title="Users" />
    <AuthenticatedLayout>
        <q-page-container>
            <q-page>
                <q-toolbar class="q-pa-sm column">
                    <div class="row q-gutter-md full-width q-pa-sm">
                        <q-input
                            outlined
                            placeholder="Search by email"
                            class="col"
                        />
                        <q-input
                            outlined
                            placeholder="Search by group"
                            class="col"
                        />
                    </div>
                    <!-- inserire metodi al click-->
                    <div class="row justify-end q-gutter-md full-width q-pb-sm">
                        <q-btn color="primary" icon="add" label="Create" @click="handleCreate()" />
                        <q-btn color="primary" icon="account_circle_off" label="Deactivate" />
                    </div>
                </q-toolbar>
                <q-table
                    flat bordered
                    ref="tableRef"
                    title="Users"
                    table-header-class="bg-orange-2"
                    :columns="columns"
                    :rows="rows"
                    row-key="name"
                    selection="multiple"
                    v-model:selected="selected"
                    @selection="handleSelection"
                >
                    <template v-slot:header-selection="scope">
                        <q-checkbox v-model="scope.selected" />
                    </template>

                    <template v-slot:body-selection="scope">
                        <q-checkbox :model-value="scope.selected" @update:model-value="(val, evt) => { Object.getOwnPropertyDescriptor(scope, 'selected').set(val, evt) }" />
                    </template>

                    <!--<template v-slot:body-cell-status="value">
                        <q-td :props="props">
                            <q-toggle
                                v-model="props.row.status"
                                color="primary"
                                :label="props.row.status ? 'Active' : 'Inactive'"
                                @update:model-value="onToggleChange(props.row)"
                            </q-toggle>
                        </q-td>
                    </template> -->

                    <!--inserire actions in-line-->

                    <template v-slot:no-data="{ message }">
                        <div class="full-width row flex-center text-accent">
                            <q-card class="bg-warning text-black">
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

                <!--modale per creazione users-->
                <CreateUserModal v-model="showCreateModal"/>

            </q-page>
        </q-page-container>
    </AuthenticatedLayout>
</template>


<script setup>
// Imports
import CreateUserModal from '@/Components/App/CreateUserModal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, toRaw, nextTick, watch, computed } from 'vue'
import _ from 'lodash'

const props = defineProps({
  users: {type:Object, default:() => {}}
});

// Uses


// Refs
const rows = ref(props.users.data ?? [])
const tableRef = ref()
const selected = ref([])
const columns = computed(() => [
    { name: 'name', align: 'left', label: 'Name', field: (row) => row.name ?? '', sortable: true },
    { name: 'email', align: 'left' , label: 'Email', field: (row) => row.email ?? '', sortable: true },
    { name: 'role', align: 'left' , label: 'Role', field: (row) => _.join(_.map(row.roles, (obj) => obj.name ?? ''), ','), sortable: true },
    { name: 'status', align: 'left' , label: 'Status', field: (row) => row.status ?? '' },
    { name: 'actions', align: 'left' , label: 'Actions' }
])

const showCreateModal = ref(false)

let storedSelectedRow

// Props & Emit


// Computed


// Methods
function handleCreate() {
    console.log('test')
    showCreateModal.value = true
}

function handleSelection({ rows, added, evt }) {
    // ignore selection change from header of not from a direct click event
    if (rows.length !== 1 || evt === void 0) return

    const oldSelectedRow = storedSelectedRow
    const [newSelectedRow] = rows
    const { ctrlKey, shiftKey, metaKey } = evt

    if (shiftKey !== true) {
    storedSelectedRow = newSelectedRow
    }

    // wait for the default selection to be performed
    nextTick(() => {
    if (shiftKey === true) {
        const tableRows = tableRef.value.filteredSortedRows
        let firstIndex = tableRows.indexOf(oldSelectedRow)
        let lastIndex = tableRows.indexOf(newSelectedRow)

        if (firstIndex < 0) {
        firstIndex = 0
        }

        if (firstIndex > lastIndex) {
        [ firstIndex, lastIndex ] = [ lastIndex, firstIndex ]
        }

        const rangeRows = tableRows.slice(firstIndex, lastIndex + 1)
        // we need the original row object so we can match them against the rows in range
        const selectedRows = selected.value.map(toRaw)

        selected.value = added === true
        ? selectedRows.concat(rangeRows.filter(row => selectedRows.includes(row) === false))
        : selectedRows.filter(row => rangeRows.includes(row) === false)
    }
    else if ((ctrlKey || metaKey) !== true && added === true) {
        selected.value = [newSelectedRow]
    }
    })
}

/*function onToggleChange(row) {
    // Logica per gestire il cambiamento dello stato dell'utente chiamata a un'API o aggiornamento del database
    console.log(`User ${row.name} status changed to ${row.status ? 'Active' : 'Inactive'}`);
}*/

// Hooks
watch(() => props.users.data, (newData) => {
  rows.value = _.cloneDeep(newData)
})


</script>

<style scoped>

</style>
