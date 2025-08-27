<template>
    <Head title="Users" />
    <AuthenticatedLayout>
        <q-page-container>
            <q-page>
                <q-toolbar class="q-pa-sm column">
                    <div class="row q-gutter-md full-width q-pa-sm">
                        <q-input
                            v-model="filters.email"
                            outlined
                            placeholder="Search by email"
                            class="col"
                        />
                        <q-input
                            v-model="filters.group"
                            outlined
                            placeholder="Search by group"
                            class="col"
                        />
                    </div>
                    <!-- inserire metodi al click-->
                    <div class="row justify-end q-gutter-md full-width q-pa-sm">
                        <q-btn color="primary" icon="add" label="Create" @click="onShow('createUserModal')" />
                        <MassChangeStatusButton
                            :change-ids="selectedIds"
                            @change="onChange"
                        />
                    </div>
                </q-toolbar>
                <q-table
                    flat
                    bordered
                    title="Users"
                    table-header-class="bg-orange-2"
                    :columns="columns"
                    :rows="rows"
                    row-key="id"
                    :filter="filters"
                    :filter-method="customFilter"
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

                    <!-- Slot per inserire toggle switch per status -->
                    <template v-slot:body-cell-status="props">
                        <q-td>
                            <q-toggle
                                v-model="props.row.status"
                                color="primary"
                                :true-value="1"
                                :false-value="0"
                                @update:model-value="onToggleChangeStatus(props.row)"
                            />
                        </q-td>
                    </template>

                    <!-- Slot per inserire pulsante per in-line actions -->
                    <template v-slot:body-cell-actions="props">
                        <q-td>
                            <UsersInlineActionsMenu
                                @click.prevent.stop
                                @edit="onShow('editUserModal', props.row)"
                                @changeStatus="handleChangeStatusInline(props.row)"
                            />
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

                <!--modale per creazione users-->
                <CreateUserModal v-model="showCreateModal" @cancel="handleCancel('createUserModal')"/>
                <EditUserModal v-model="showEditModal" :user="selectedUser" @cancel="handleCancel('editUserModal')"/>

            </q-page>
        </q-page-container>
    </AuthenticatedLayout>
</template>


<script setup>
// Imports
import CreateUserModal from '@/Components/App/CreateUserModal.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue'
import _ from 'lodash'
import { useQuasar } from 'quasar';
import UsersInlineActionsMenu from '@/Components/App/UsersInlineActionsMenu.vue';
import EditUserModal from '@/Components/App/EditUserModal.vue';
import MassChangeStatusButton from '@/Components/App/MassChangeStatusButton.vue';


// Props & Emit
const props = defineProps({
  users: {
    type: Object,
    default: {}
}});


// Uses
const $q = useQuasar()
const form = useForm({
    active: null
})

// Refs
const rows = ref(props.users.data ?? [])
console.log(rows)
const columns = computed(() => [
    { name: 'name', align: 'left', label: 'Name', field: (row) => row.name ?? '', sortable: true },
    { name: 'email', align: 'left' , label: 'Email', field: (row) => row.email ?? '', sortable: true },
    { name: 'role', align: 'left' , label: 'Role', field: (row) => _.join(_.map(row.roles, (obj) => obj.name ?? ''), ','), sortable: true },
    { name: 'status', align: 'left' , label: 'Status', field: (row) => row.status ?? '' },
    { name: 'actions', align: 'left' , label: 'Actions' }
])

const showCreateModal = ref(false)
const showEditModal = ref(false)
const selectedUser = ref({})

const selected = ref([])

const filters = ref({ email: '', group: '' })

const pagination = ref({
    page: 1,          // pagina corrente
    rowsPerPage: 10,   // righe per pagina
})

// Computed
const selectedIds = computed(() => selected.value.map(row => row.id))


// Methods
function onShow(modal, row = null) {
    // In base al modale passato come parametro, apro quel modale
    if (modal === 'createUserModal') {
        showCreateModal.value = true
    }
    if (modal === 'editUserModal') {
        selectedUser.value = row
        showEditModal.value = true
    }
}

function handleCancel(modal) {
    // In base al modale passato come parametro, chiudo quel modale
    switch(modal) {
        case 'createUserModal':
            showCreateModal.value = false
            break
        case 'editUserModal':
            showEditModal.value = false
            break
    }

}

function onToggleChangeStatus(row) {
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

function handleChangeStatusInline(row) {
    row.status = !row.status
    onToggleChangeStatus(row)
}

function onChange() {
    selected.value = []
}

function customFilter(rows, terms) { // Per filtrare record visualizzati
    const search = (terms.email ?? '').toLowerCase()
    return rows.filter(row =>
        row.email.toLowerCase().includes(search)
    )

    /* !!aggiungere parte filtro per gruppo, una volta implementati!! */

}

function toggleRowSelection (evt, row) { // Selezione record tramite clic su riga
    const index = selected.value.findIndex(r => r.id === row.id)
    if (index > -1) {
        // già selezionato → lo tolgo
        selected.value.splice(index, 1)
    } else {
        // non selezionato → lo aggiungo
        selected.value.push(row)
    }
}

// Hooks
watch(() => props.users.data, (newData) => {
    rows.value = _.cloneDeep(newData)
})


</script>

<style scoped>

</style>
