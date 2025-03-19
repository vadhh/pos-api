<template>
    <div class="flex h-screen bg-gray-100 overflow-hidden">
        <Sidebar />
        <main class="flex-1 overflow-y-auto">
            <div class="h-full">
                <router-view></router-view>
            </div>
        </main>
    </div>
</template>

<script setup>
import Sidebar from "./Sidebar.vue";
</script>

<style>
/* Ensure the layout takes full height and handles overflow properly */
html,
body {
    height: 100%;
    margin: 0;
    overflow: hidden;
}

#app {
    height: 100%;
}
</style>
