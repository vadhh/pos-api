<script setup>
import { onMounted, onErrorCaptured, ref } from "vue";

const error = ref(null);

onMounted(() => {
    console.log("App component mounted");
});

onErrorCaptured((err, instance, info) => {
    console.error("Error captured in App.vue:", err, instance, info);
    error.value = err;
    return false;
});
</script>

<template>
    <div>
        <!-- Error Display -->
        <div v-if="error" class="bg-red-600 text-white p-4">
            <h2 class="font-bold">Error occurred:</h2>
            <pre>{{ error.stack || error.message || error }}</pre>
        </div>

        <!-- Main Content -->
        <router-view v-else></router-view>
    </div>
</template>

<style>
/* Global styles can be added here */
body {
    margin: 0;
    padding: 0;
}
</style>
