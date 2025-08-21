<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <q-page-container>
            <q-page class="q-pa-md">
                <q-card class="q-pa-sm full-width">
                    <q-card-section>
                        <q-toolbar-title class="text-h4">Profile</q-toolbar-title>
                    </q-card-section>
                </q-card>

                <q-card class="q-pa-sm full-width q-mt-md">
                    <q-card-section>
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </q-card-section>
                </q-card>

                <q-card class="q-pa-sm full-width q-mt-md">
                    <q-card-section>
                        <UpdatePasswordForm class="max-w-xl" />
                    </q-card-section>
                </q-card>

                <q-card class="q-pa-sm full-width q-mt-md">
                    <q-card-section>
                        <DeleteUserForm class="max-w-xl" />
                    </q-card-section>
                </q-card>
            </q-page>
        </q-page-container>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>


