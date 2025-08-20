<template>
    <q-layout class="flex justify-center">
        <q-img
            src="/images/background.png"
            class="fit absolute"
        />
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <q-icon name="folder_open" size="90px" class="mb-4"></q-icon>
            <div class="text-h3  mb-8">Welcome to File Manager App</div>
            <div class="flex gap-4">
                <q-btn
                    label="Log In"
                    class="text-capitalize"
                    :href="route('login')"
                    unelevated
                />
                <q-btn
                    label="Register"
                    class="text-capitalize"
                    :href="route('register')"
                    unelevated
                />
            </div>
        </div>
    </q-layout>
</template>

<script setup>

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>
