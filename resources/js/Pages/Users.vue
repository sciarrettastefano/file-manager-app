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

                    <!-- Slot per inserire toggle switch per status -->
                    <template v-slot:body-cell-status="props">
                        <q-td>
                            <q-toggle
                                v-model="props.row.status"
                                color="primary"
                                :true-value="1"
                                :false-value="0"
                                @update:model-value="onToggleChange(props.row)"
                            />
                        </q-td>
                    </template>

                    <!-- Slot per inserire pulsante per in-line actions -->
                    <template v-slot:body-cell-actions>
                        <q-td>
                            test
                        </q-td>
                    </template>

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
                <CreateUserModal v-model="showCreateModal" @cancel="handleCancel"/>

            </q-page>
        </q-page-container>
    </AuthenticatedLayout>
</template>


<script setup>
// Imports
import CreateUserModal from '@/Components/App/CreateUserModal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, toRaw, nextTick, watch, computed } from 'vue'
import _ from 'lodash'

const props = defineProps({
  users: Object
});

// Uses
const form = useForm({
    active: null
})

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
    console.log('apro modale creazione utente')
    showCreateModal.value = true
}

function handleCancel() {
    console.log('chiudo modale creazione utente')
    showCreateModal.value = false
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
    console.log(selected.value)
}

function onToggleChange(row) {
    form.active = row.status
    form.patch(route('users.changeStatus', row.id), {
        onSuccess: () => {
            $q.notify({
                color: 'green-4',
                textColor: 'white',
                icon: 'cloud_done',
                message: 'User status updated correctly.'
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
}

// Hooks
watch(() => props.users.data, (newData) => {
    rows.value = _.cloneDeep(newData)
})


</script>

<style scoped>

</style>
