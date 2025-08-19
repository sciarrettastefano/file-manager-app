<template>
    <q-layout view="hHh Lpr lff">

        <!-- Intestazione -->
        <q-header bordered class="bg-primary text-black border-width-4px border-black h-20">
        <q-toolbar class="flex flex-row absolute-center justify-between">
                <div class="absolute-center">
                    <q-toolbar-title class="text-h4">
                        <q-icon name="folder_open" size="65px"></q-icon>
                        File Manager App
                    </q-toolbar-title>
                </div>
                <div class="absolute-right q-pt-sm q-pr-md">
                    <UserSettingsDropdown :name="$page.props.auth.user.name" />
                </div>
        </q-toolbar>
        </q-header>


        <!-- Barra laterale con menù di navigazione -->
        <NavigationMenu />

        <!-- Contenuto principale della pagina-->
        <main>
            <slot />
        </main>

    </q-layout>
</template>

<script setup>
import NavigationMenu from '@/Components/App/NavigationMenu.vue';
import UserSettingsDropdown from '@/Components/App/UserSettingsDropdown.vue';


</script>


