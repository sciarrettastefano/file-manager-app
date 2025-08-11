<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <q-btn color="primary" label="Primary" />
        <div class="q-pa-md">
            <q-card>
                <q-card-section>
                    Welcome to your dashboard!
                </q-card-section>
            </q-card>
        </div>
    </AuthenticatedLayout>
</template>
