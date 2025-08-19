<template>
    <!--aggiungere nome utente nella label-->
    <q-btn
        class="text-capitalize"
        unelevated
        side-right
        :label="props.name"
        :icon-right="menuOpen ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"
    >
        <q-menu
            v-model="menuOpen"
            fit
            @click="onClick"
        >
            <q-list dense>
                <Link
                    :href="route('profile.edit')"
                    class="fit text-left"
                >
                    <q-item clickable dense>
                        <q-item-section>Profile</q-item-section>
                    </q-item>
                </Link>
                <Link
                    :href="route('logout')"
                    method="post"
                    class="fit text-left"
                >
                    <q-item clickable dense>
                        <q-item-section>Logout</q-item-section>
                    </q-item>
                </Link>
            </q-list>
        </q-menu>
    </q-btn>
</template>


<script setup>
// Imports
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';


// Uses


// Refs
const menuOpen = ref(false)

// Props & Emit
const props = defineProps({
    name: {
        type: String,
        default: 'username'
    }
})


// Computed


// Methods
function onClick() {
    menuOpen.value = !menuOpen.value
    console.log('nuova visibilità: ', menuOpen.value)
}

// Hooks


</script>


<style scoped>

</style>
