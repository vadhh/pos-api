<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    show: Boolean,
    isEditing: Boolean,
    formData: Object,
    loading: Boolean
});

const emit = defineEmits(['close', 'submit', 'update:formData']);

const updateFormData = (field, value) => {
    const updatedData = { ...props.formData, [field]: value };
    emit('update:formData', updatedData);
};

const handleSubmit = () => {
    emit('submit');
};

const closeModal = () => {
    emit('close');
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-gray-900">
                    {{ isEditing ? "Edit Tahapan" : "Tambah Tahapan" }}
                </h2>
                <button @click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input 
                        :value="formData.nama" 
                        @input="updateFormData('nama', $event.target.value)" 
                        type="text" 
                        required 
                        class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-left" 
                        placeholder="Enter step name"
                    />
                </div>
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea 
                        :value="formData.deskripsi" 
                        @input="updateFormData('deskripsi', $event.target.value)" 
                        rows="3" 
                        class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-left" 
                        placeholder="Enter step description"
                    ></textarea>
                </div>
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700">Urutan</label>
                    <input 
                        :value="formData.urutan" 
                        @input="updateFormData('urutan', parseInt($event.target.value) || 1)" 
                        type="number" 
                        required 
                        min="1" 
                        class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-left" 
                        placeholder="Enter step order"
                    />
                </div>
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700">Minimal Verifikasi</label>
                    <input 
                        :value="formData.minimal_verifikasi" 
                        @input="updateFormData('minimal_verifikasi', parseInt($event.target.value) || 1)" 
                        type="number" 
                        required 
                        min="1" 
                        class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-left" 
                        placeholder="Enter minimum verification"
                    />
                </div>
                <div class="text-left">
                    <label class="block text-sm font-medium text-gray-700">Verifikator</label>
                    <input 
                        :value="formData.verifikator" 
                        @input="updateFormData('verifikator', $event.target.value)" 
                        type="text" 
                        required 
                        class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm text-left" 
                        placeholder="Enter verifier"
                    />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 text-left">Durasi (hari)</label>
                    <input 
                        :value="formData.durasi" 
                        @input="updateFormData('durasi', parseInt($event.target.value) || 1)" 
                        type="number" 
                        required 
                        min="1" 
                        class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                        placeholder="Enter duration in days"
                    />
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button 
                        type="button" 
                        @click="closeModal" 
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                        :disabled="loading"
                    >
                        <svg 
                            v-if="loading" 
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" 
                            xmlns="http://www.w3.org/2000/svg" 
                            fill="none" 
                            viewBox="0 0 24 24"
                        >
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isEditing ? "Update Step" : "Create Step" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>