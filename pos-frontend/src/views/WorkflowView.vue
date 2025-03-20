<script setup>
import { ref, onMounted, computed } from "vue";
import { useWorkflowStore } from "../stores/workflows";
import api from "../axios";
import StepModal from "../components/StepModal.vue";

const store = useWorkflowStore();
const showModal = ref(false);
const showDeleteModal = ref(false);
const showDetailModal = ref(false);
const showStepModal = ref(false);
const isEditing = ref(false);
const isEditingStep = ref(false);
const selectedWorkflow = ref(null);
const selectedStep = ref(null);
const currentPage = ref(1);
const itemsPerPage = 5;

const formData = ref({
    nama: "",
    deskripsi: "",
    dibuat: "",
    tgl_dibuat: "",
    status: "Active",
    tahapan: 1,
    steps: [],
});

const stepFormData = ref({
    nama: "",
    deskripsi: "",
    urutan: 1,
    minimal_verifikasi: 1,
    verifikator: "",
    durasi: 1,
});

const statusOptions = [
    { value: "Active", label: "Active" },
    { value: "Inactive", label: "Inactive" },
    { value: "Draft", label: "Draft" },
];

// Pagination
const paginatedWorkflows = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return store.workflows ? store.workflows.slice(start, end) : [];
});

const totalPages = computed(() => {
    return store.workflows
        ? Math.ceil(store.workflows.length / itemsPerPage)
        : 0;
});

const pageNumbers = computed(() => {
    const pages = [];
    for (let i = 1; i <= totalPages.value; i++) {
        pages.push(i);
    }
    return pages;
});

const changePage = (page) => {
    currentPage.value = page;
};

// Format date
const formatDate = (date) => {
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

onMounted(async () => {
    try {
        await store.fetchWorkflows();
    } catch (error) {
        console.error("Error fetching workflows:", error);
    }
});

const openCreateModal = () => {
    isEditing.value = false;
    formData.value = {
        nama: "",
        deskripsi: "",
        dibuat: "",
        tgl_dibuat: new Date().toISOString().split("T")[0],
        status: "Active",
        tahapan: 1,
    };
    showModal.value = true;
};

const openEditModal = (workflow) => {
    isEditing.value = true;
    selectedWorkflow.value = workflow;
    formData.value = { ...workflow };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    formData.value = {
        nama: "",
        deskripsi: "",
        dibuat: "",
        tgl_dibuat: "",
        status: "Active",
        tahapan: 1,
    };
};

const handleSubmit = async () => {
    try {
        if (isEditing.value) {
            await store.updateWorkflow(
                selectedWorkflow.value.id,
                formData.value
            );
        } else {
            await store.createWorkflow(formData.value);
        }
        closeModal();
    } catch (error) {
        console.error("Error saving workflow:", error);
    }
};

const confirmDelete = (workflow) => {
    selectedWorkflow.value = workflow;
    showDeleteModal.value = true;
};

const handleDelete = async () => {
    try {
        await store.deleteWorkflow(selectedWorkflow.value.id);
        showDeleteModal.value = false;
    } catch (error) {
        console.error("Error deleting workflow:", error);
    }
};

const viewWorkflowDetails = (workflow) => {
    selectedWorkflow.value = workflow;
    showDetailModal.value = true;
};

// Step management methods
const openAddStepModal = () => {
    isEditingStep.value = false;
    stepFormData.value = {
        nama: "",
        deskripsi: "",
        urutan: selectedWorkflow.value.steps.length + 1,
        minimal_verifikasi: 1,
        verifikator: "",
        durasi: 1,
    };
    showStepModal.value = true;
};

const openEditStepModal = (step) => {
    isEditingStep.value = true;
    selectedStep.value = step;
    stepFormData.value = { ...step };
    showStepModal.value = true;
};

const closeStepModal = () => {
    showStepModal.value = false;
    stepFormData.value = {
        nama: "",
        deskripsi: "",
        urutan: 1,
        minimal_verifikasi: 1,
        verifikator: "",
        durasi: 1,
    };
};

const handleStepSubmit = async () => {
    try {
        if (isEditingStep.value && selectedStep.value) {
            // Update existing step
            const response = await api.put(
                `/api/v1/workflows/${selectedWorkflow.value.id}/steps/${selectedStep.value.id}`,
                stepFormData.value
            );
            
            // Update the step in the workflow
            const stepIndex = selectedWorkflow.value.steps.findIndex(s => s.id === selectedStep.value.id);
            if (stepIndex !== -1) {
                selectedWorkflow.value.steps[stepIndex] = response.data;
            }
        } else {
            // Create new step
            const response = await api.post(
                `/api/v1/workflows/${selectedWorkflow.value.id}/steps`,
                stepFormData.value
            );
            
            // Add the new step to the workflow
            selectedWorkflow.value.steps.push(response.data);
            
            // Update the workflow's tahapan count
            selectedWorkflow.value.tahapan = selectedWorkflow.value.steps.length;
            
            // Update the workflow in the store
            await store.updateWorkflow(selectedWorkflow.value.id, {
                ...selectedWorkflow.value,
                tahapan: selectedWorkflow.value.tahapan
            });
        }
        closeStepModal();
    } catch (error) {
        console.error("Error saving workflow step:", error);
    }
};

const deleteStep = async (step) => {
    if (!confirm(`Are you sure you want to delete the step "${step.nama}"?`)) {
        return;
    }
    
    try {
        await api.delete(`/api/v1/workflows/${selectedWorkflow.value.id}/steps/${step.id}`);
        
        // Remove the step from the workflow
        selectedWorkflow.value.steps = selectedWorkflow.value.steps.filter(s => s.id !== step.id);
        
        // Update the workflow's tahapan count
        selectedWorkflow.value.tahapan = selectedWorkflow.value.steps.length;
        
        // Update the workflow in the store
        await store.updateWorkflow(selectedWorkflow.value.id, {
            ...selectedWorkflow.value,
            tahapan: selectedWorkflow.value.tahapan
        });
    } catch (error) {
        console.error("Error deleting workflow step:", error);
    }
};
</script>

<template>
    <div class="h-full w-full flex flex-col">
        <!-- Header Section -->
        <div class="bg-white shadow-sm p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Workflows</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage your workflow processes
                    </p>
                </div>
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    <svg
                        class="-ml-1 mr-2 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    Add Workflow
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-6 space-y-6 overflow-y-auto">
            <!-- Loading State -->
            <div v-if="store.loading" class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex flex-col items-center justify-center">
                    <div
                        class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"
                    ></div>
                    <p class="text-gray-500 text-sm">Loading workflows...</p>
                </div>
            </div>

            <!-- Error State -->
            <div
                v-else-if="store.error"
                class="bg-white rounded-lg shadow-sm p-6"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-5 w-5 text-red-400"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Error loading workflows
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            {{ store.error }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workflows Table -->
            <div v-else class="bg-white shadow-sm rounded-lg">
                <div class="w-full">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Deskripsi
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dibuat
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tgl Dibuat
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tahapan
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="workflow in paginatedWorkflows"
                                :key="workflow.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ workflow.id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 break-words">
                                    {{ workflow.nama }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 break-words">
                                        {{ workflow.deskripsi }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 break-words">
                                    {{ workflow.dibuat }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ formatDate(workflow.tgl_dibuat) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="{
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                        'bg-green-100 text-green-800': workflow.status === 'Active',
                                        'bg-red-100 text-red-800': workflow.status === 'Inactive',
                                        'bg-gray-100 text-gray-800': workflow.status === 'Draft'
                                    }">
                                        {{ workflow.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ workflow.tahapan }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-right">
                                    <div class="flex justify-end space-x-1">
                                        <button @click="viewWorkflowDetails(workflow)" class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">Detail</button>
                                        <button @click="openEditModal(workflow)" class="inline-flex items-center px-2 py-1 border border-blue-300 text-xs font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-1 focus:ring-blue-500">Edit</button>
                                        <button @click="confirmDelete(workflow)" class="inline-flex items-center px-2 py-1 border border-red-300 text-xs font-medium rounded text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-1 focus:ring-red-500">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
                >
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button
                            @click="changePage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        <button
                            @click="changePage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
                        >
                            Next
                        </button>
                    </div>
                    <div
                        class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                    >
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing
                                <span class="font-medium">{{
                                    (currentPage - 1) * itemsPerPage + 1
                                }}</span>
                                to
                                <span class="font-medium">
                                    {{
                                        Math.min(
                                            currentPage * itemsPerPage,
                                            store.workflows?.length || 0
                                        )
                                    }}
                                </span>
                                of
                                <span class="font-medium">{{
                                    store.workflows?.length || 0
                                }}</span>
                                results
                            </p>
                        </div>
                        <div>
                            <nav
                                class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination"
                            >
                                <button
                                    @click="changePage(currentPage - 1)"
                                    :disabled="currentPage === 1"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                >
                                    <span class="sr-only">Previous</span>
                                    <svg
                                        class="h-5 w-5"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                                <button
                                    v-for="page in pageNumbers"
                                    :key="page"
                                    @click="changePage(page)"
                                    :class="[
                                        page === currentPage
                                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                    ]"
                                >
                                    {{ page }}
                                </button>
                                <button
                                    @click="changePage(currentPage + 1)"
                                    :disabled="currentPage === totalPages"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                >
                                    <span class="sr-only">Next</span>
                                    <svg
                                        class="h-5 w-5"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center p-4 z-50"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full p-8">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ isEditing ? "Edit Workflow" : "Add Workflow" }}
                    </h2>
                    <button
                        @click="closeModal"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <svg
                            class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >Nama</label
                        >
                        <input
                            v-model="formData.nama"
                            type="text"
                            required
                            class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Enter workflow name"
                        />
                    </div>
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >Deskripsi</label
                        >
                        <textarea
                            v-model="formData.deskripsi"
                            rows="3"
                            class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Enter workflow description"
                        ></textarea>
                    </div>
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >Dibuat</label
                        >
                        <input
                            v-model="formData.dibuat"
                            type="text"
                            required
                            class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Enter creator name"
                        />
                    </div>
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >Tgl Dibuat</label
                        >
                        <input
                            v-model="formData.tgl_dibuat"
                            type="date"
                            required
                            class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        />
                    </div>
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >Status</label
                        >
                        <select
                            v-model="formData.status"
                            required
                            class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        >
                            <option
                                v-for="option in statusOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >Tahapan</label
                        >
                        <input
                            v-model.number="formData.tahapan"
                            type="number"
                            required
                            min="1"
                            class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Enter number of stages"
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
                            :disabled="store.loading"
                        >
                            <svg
                                v-if="store.loading"
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            {{
                                isEditing
                                    ? "Update Workflow"
                                    : "Create Workflow"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Detail Modal -->
        <div
            v-if="showDetailModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center p-4 z-50"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full p-8">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">
                        Workflow Details
                    </h2>
                    <button
                        @click="showDetailModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <svg
                            class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4 text-left" v-if="selectedWorkflow">
                    <div class="border-b pb-3">
                        <p class="text-sm font-medium text-gray-500 text-left">ID</p>
                        <p class="mt-1 text-sm text-gray-900 text-left">{{ selectedWorkflow.id }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-sm font-medium text-gray-500 text-left">Nama</p>
                        <p class="mt-1 text-sm text-gray-900 text-left">{{ selectedWorkflow.nama }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-sm font-medium text-gray-500 text-left">Deskripsi</p>
                        <p class="mt-1 text-sm text-gray-900 text-left">{{ selectedWorkflow.deskripsi }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-sm font-medium text-gray-500 text-left">Dibuat</p>
                        <p class="mt-1 text-sm text-gray-900 text-left">{{ selectedWorkflow.dibuat }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-sm font-medium text-gray-500 text-left">Tgl Dibuat</p>
                        <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedWorkflow.tgl_dibuat) }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <p class="mt-1">
                            <span
                                :class="{
                                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                    'bg-green-100 text-green-800':
                                        selectedWorkflow.status === 'Active',
                                    'bg-red-100 text-red-800':
                                        selectedWorkflow.status === 'Inactive',
                                    'bg-gray-100 text-gray-800':
                                        selectedWorkflow.status === 'Draft',
                                }"
                            >
                                {{ selectedWorkflow.status }}
                            </span>
                        </p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-sm font-medium text-gray-500">Tahapan</p>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedWorkflow.tahapan }}</p>
                    </div>
                    
                    <!-- Tahapan Table -->
                    <div class="mt-4">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm font-medium text-gray-500">TAHAPAN</p>
                            <button 
                                @click="openAddStepModal"
                                class="inline-flex items-center px-2 py-1 border border-transparent rounded text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            >
                                <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Tahapan
                            </button>
                        </div>
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Minimal Verifikasi</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verifikator</th>
                                        <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durasi (hari)</th>
                                        <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="selectedWorkflow.steps && selectedWorkflow.steps.length === 0">
                                        <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">
                                            No steps found. Click "Add Step" to create a new step.
                                        </td>
                                    </tr>
                                    <tr v-for="(step, index) in selectedWorkflow.steps" :key="step.id" class="hover:bg-gray-50">
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ step.nama }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ step.minimal_verifikasi }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ step.verifikator }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ step.durasi }}</td>
                                        <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-1">
                                                <button 
                                                    @click="openEditStepModal(step)"
                                                    class="inline-flex items-center px-2 py-1 border border-blue-300 text-xs font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                >
                                                    <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button 
                                                    @click="deleteStep(step)"
                                                    class="inline-flex items-center px-2 py-1 border border-red-300 text-xs font-medium rounded text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-1 focus:ring-red-500"
                                                >
                                                    <svg class="h-3 w-3 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button
                            @click="showDetailModal = false"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center p-4 z-50"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full p-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-6 w-6 text-red-600"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <h3 class="text-lg font-medium text-gray-900">
                            Delete Workflow
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Are you sure you want to delete
                            <span class="font-medium text-gray-900">{{
                                selectedWorkflow?.nama
                            }}</span
                            >? This action cannot be undone.
                        </p>
                        <div class="mt-4 flex space-x-3">
                            <button
                                @click="handleDelete"
                                class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                :disabled="store.loading"
                            >
                                <svg
                                    v-if="store.loading"
                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                Delete
                            </button>
                            <button
                                @click="showDeleteModal = false"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step Modal -->
        <StepModal
            :show="showStepModal"
            :is-editing="isEditingStep"
            :form-data="stepFormData"
            :loading="store.loading"
            @close="closeStepModal"
            @submit="handleStepSubmit"
            @update:form-data="stepFormData = $event"
        />
    </div>
</template>
