<template>
    <q-drawer
        show-if-above
        :width="250"
        :breakpoint="500"
        bordered
        :class="$q.dark.isActive ? 'bg-orange-6' : 'bg-orange-4'"
    >
        <q-list separator>
                <Link
                    v-for="section in sections"
                    :key="section.name"
                    :href="route(section.route)"
                    class="no-decoration"
                >
                    <q-item
                        v-if="can(section.perm)"
                        clickable
                        :class="{
                            'active-item': activeSection === section.route,
                            'inactive-item': activeSection !== section.route
                        }"
                        @click="activeSection = section.route"
                    >
                        <q-item-section avatar>
                            <q-icon :name="section.icon" />
                        </q-item-section>
                        <q-item-section>{{ section.label }}</q-item-section>
                    </q-item>
                </Link>
            <q-separator />
        </q-list>
    </q-drawer>
</template>


<script setup>
// Imports
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

// Uses
const page = usePage()
const permissions = page.props.auth.permissions

const can = (perm) => (perm === true) ? true : permissions.includes(perm)


// Refs
const activeSection = ref(route().current());

const sections = [
    { name: 'files', icon: 'folder', label: 'Files', route: 'files.index', perm: true },
    { name: 'users', icon: 'person', label: 'Users', route: 'users.index', perm: 'users.manage' }, //visibile solo se utente è superadmin
    { name: 'shares', icon: 'share', label: 'Shares', route: 'dashboard', perm: true },
    { name: 'groups', icon: 'group', label: 'Groups', route: 'dashboard', perm: true },
    { name: 'trash', icon: 'delete', label: 'Trash', route: 'dashboard', perm: true }
]

// Props & Emit

// Computed


// Methods
function changeActiveSection(sectionRoute) {
    activeSection.value = sectionRoute;
}

// Hooks


</script>


<style scoped>
.active-item {
    background-color: #e2a344;
    opacity: 1;
    border-right: black 3px solid;
}

.inactive-item {
    opacity: 0.9
}

.active-item:hover,
.inactive-item:hover {
    opacity: 1;
}
</style>
