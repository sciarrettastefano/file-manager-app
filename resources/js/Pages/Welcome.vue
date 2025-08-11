<template>
    <q-layout class="flex justify-center">
        <q-img
            src="/images/background.png"
            class="fit absolute"
        />
        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-40 h-40 p-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75
                3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621
                0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75
                3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
            </svg>
            <q-title class="text-h3  mb-8">Welcome to File Manager App</q-title>
            <div class="flex gap-4">
                <q-btn
                    label="Log In"
                    :href="route('login')"
                    unelevated
                />
                <q-btn
                    label="Register"
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
