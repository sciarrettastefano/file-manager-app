<template>
    <!--aggiungere nome utente nella label-->
    <q-btn
        icon-right="keyboard_arrow_down"
        label="username"
        class="text-capitalize"
        unelevated
        side-right
    >
        <q-menu fit>
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
import { Link } from '@inertiajs/vue3';

// Imports


// Uses


// Refs


// Props & Emit


// Computed


// Methods


// Hooks


</script>


<style scoped>

</style>
