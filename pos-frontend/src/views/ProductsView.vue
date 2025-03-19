<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useProductStore } from "../stores/products";

const store = useProductStore();
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const selectedProduct = ref(null);
const formData = ref({
    name: "",
    category_id: "",
    price: 0,
    description: "",
    stock: 0,
    sku: "",
});

const currentPage = ref(1);
const itemsPerPage = 5;

const hasProducts = computed(() => {
    return store.products && store.products.length > 0;
});

const lastFetchTime = computed(() => {
    if (!store.lastFetchTime) return "Never";
    const date = new Date(store.lastFetchTime);
    return date.toLocaleTimeString();
});

const formatPrice = (price) => {
    const num = Number(price);
    return isNaN(num) ? "0.00" : num.toFixed(2);
};

const generateSku = async (categoryId) => {
    if (!categoryId) return "";

    const category = store.categories.find((c) => c.id === Number(categoryId));
    if (!category) return "";

    // Get prefix based on category name (first 3 letters uppercase)
    const prefix = category.name.substring(0, 3).toUpperCase();

    // Filter products with the same category and get their SKUs
    const categoryProducts = store.products.filter(
        (p) => p.category_id === Number(categoryId)
    );
    const existingSkus = categoryProducts
        .map((p) => p.sku)
        .filter((sku) => sku && sku.startsWith(prefix))
        .map((sku) => parseInt(sku.substring(3)) || 0);

    // Get the highest number and increment by 1
    const nextNumber =
        existingSkus.length > 0 ? Math.max(...existingSkus) + 1 : 1;

    // Format number to 3 digits (001, 002, etc.)
    return `${prefix}${String(nextNumber).padStart(3, "0")}`;
};

// Watch for category changes and update SKU
watch(
    () => formData.value.category_id,
    async (newCategoryId) => {
        if (!isEditing.value && newCategoryId) {
            formData.value.sku = await generateSku(newCategoryId);
        }
    }
);

onMounted(async () => {
    console.log("ProductsView: Component mounted");
    try {
        await store.fetchCategories();
        await store.fetchProducts();
        console.log(
            "ProductsView: Products and categories fetched successfully"
        );
    } catch (error) {
        console.error("ProductsView: Error fetching data:", error);
    }
});

const refreshData = async () => {
    console.log("ProductsView: Manually refreshing data");
    try {
        await store.fetchProducts();
    } catch (error) {
        console.error("ProductsView: Error refreshing data:", error);
    }
};

const openCreateModal = () => {
    isEditing.value = false;
    formData.value = {
        name: "",
        category_id: "",
        price: 0,
        description: "",
        stock: 0,
        sku: "",
    };
    showModal.value = true;
};

const openEditModal = (product) => {
    isEditing.value = true;
    selectedProduct.value = product;
    formData.value = { ...product };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    formData.value = {
        name: "",
        category_id: "",
        price: 0,
        description: "",
        stock: 0,
        sku: "",
    };
};

const handleSubmit = async () => {
    try {
        console.log("ProductsView: Submitting form:", formData.value);
        if (isEditing.value) {
            await store.updateProduct(selectedProduct.value.id, formData.value);
            console.log("ProductsView: Product updated successfully");
        } else {
            await store.createProduct(formData.value);
            console.log("ProductsView: Product created successfully");
        }
        closeModal();
    } catch (error) {
        console.error("ProductsView: Error saving product:", error);
    }
};

const confirmDelete = (product) => {
    selectedProduct.value = product;
    showDeleteModal.value = true;
};

const handleDelete = async () => {
    try {
        console.log(
            "ProductsView: Deleting product:",
            selectedProduct.value.id
        );
        await store.deleteProduct(selectedProduct.value.id);
        console.log("ProductsView: Product deleted successfully");
        showDeleteModal.value = false;
    } catch (error) {
        console.error("ProductsView: Error deleting product:", error);
    }
};

// Add computed property for paginated products
const paginatedProducts = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return store.products ? store.products.slice(start, end) : [];
});

const totalPages = computed(() => {
    return store.products ? Math.ceil(store.products.length / itemsPerPage) : 0;
});

const pageNumbers = computed(() => {
    const pages = [];
    for (let i = 1; i <= totalPages.value; i++) {
        pages.push(i);
    }
    return pages;
});

// Add function to change page
const changePage = (page) => {
    currentPage.value = page;
};
</script>

<template>
    <div class="h-full w-full flex flex-col">
        <!-- Header Section -->
        <div class="bg-white shadow-sm p-6 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Products</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Manage your product inventory
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button
                        @click="refreshData"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        :disabled="store.loading"
                    >
                        <svg
                            v-if="!store.loading"
                            class="-ml-1 mr-2 h-4 w-4 text-gray-500"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                        <svg
                            v-else
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500"
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
                        Refresh
                    </button>
                    <button
                        @click="openCreateModal"
                        class="inline-flex inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
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
                        Add Product
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-6 space-y-6 overflow-y-auto">
            <!-- Status Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div
                        class="text-sm items-center font-medium text-gray-500 text-center"
                    >
                        Status
                    </div>
                    <div class="mt-1 flex items-center justify-center">
                        <span
                            :class="{
                                'bg-green-100 text-green-800':
                                    !store.loading && !store.error,
                                'bg-yellow-100 text-yellow-800': store.loading,
                                'bg-red-100 text-red-800': store.error,
                            }"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        >
                            {{
                                store.loading
                                    ? "Loading"
                                    : store.error
                                    ? "Error"
                                    : "Ready"
                            }}
                        </span>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="text-sm font-medium text-gray-500">
                        Total Products
                    </div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                        {{ store.products ? store.products.length : 0 }}
                    </div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="text-sm font-medium text-gray-500">
                        Last Updated
                    </div>
                    <div class="mt-1 text-sm text-gray-900">
                        {{ lastFetchTime }}
                    </div>
                </div>
            </div>

            <!-- Error, Loading, Empty States in a consistent container -->
            <div class="space-y-6">
                <!-- Error State -->
                <div
                    v-if="store.error"
                    class="bg-white rounded-lg shadow-sm p-6"
                >
                    <div class="flex">
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
                                Error fetching products
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                {{ store.error }}
                            </div>
                            <div class="mt-4">
                                <button
                                    @click="refreshData"
                                    class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                >
                                    Try Again
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div
                    v-if="store.loading"
                    class="bg-white rounded-lg shadow-sm p-6"
                >
                    <div class="flex flex-col items-center justify-center">
                        <div
                            class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"
                        ></div>
                        <p class="text-gray-500 text-sm">Loading products...</p>
                    </div>
                </div>

                <!-- Empty State -->
                <div
                    v-else-if="!hasProducts"
                    class="bg-white rounded-lg shadow-sm p-6"
                >
                    <div class="mx-auto h-16 w-16 text-gray-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                            />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">
                        No Products Found
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Get started by adding your first product
                    </p>
                    <div class="mt-6">
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
                            Add Your First Product
                        </button>
                    </div>
                </div>

                <!-- Products Table -->
                <div
                    v-else
                    class="bg-white shadow-sm overflow-hidden rounded-lg"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Name
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Category
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Price
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Description
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="product in paginatedProducts"
                                    :key="product.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ product.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                        >
                                            {{ product.category?.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            ${{ formatPrice(product.price) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div
                                            class="text-sm text-gray-500 truncate max-w-xs"
                                        >
                                            {{ product.description }}
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <button
                                            @click="openEditModal(product)"
                                            class="text-blue-600 hover:text-blue-900 mr-4"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="confirmDelete(product)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Controls -->
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
                                                store.products?.length || 0
                                            )
                                        }}
                                    </span>
                                    of
                                    <span class="font-medium">{{
                                        store.products?.length || 0
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
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
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
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:bg-gray-100 disabled:cursor-not-allowed"
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
        </div>

        <!-- Create/Edit Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center p-4 z-50"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ isEditing ? "Edit Product" : "Add Product" }}
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
                            >Name</label
                        >
                        <input
                            v-model="formData.name"
                            type="text"
                            required
                            class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Enter product name"
                        />
                    </div>
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >Category</label
                        >
                        <select
                            v-model="formData.category_id"
                            required
                            class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                        >
                            <option value="" disabled>Select a category</option>
                            <option
                                v-for="category in store.categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >Price</label
                        >
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                            >
                                <span class="text-gray-500 sm:text-sm">$</span>
                            </div>
                            <input
                                v-model.number="formData.price"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                class="block w-full pl-7 bg-white text-gray-700 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="0.00"
                            />
                        </div>
                    </div>
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >Description</label
                        >
                        <textarea
                            v-model="formData.description"
                            rows="3"
                            class="mt-1 block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Enter product description"
                        ></textarea>
                    </div>
                    <div>
                        <label
                            class="text-sm font-medium text-gray-700 text-left"
                            >SKU</label
                        >
                        <div class="mt-1 relative">
                            <input
                                v-model="formData.sku"
                                type="text"
                                required
                                :readonly="!isEditing"
                                class="block w-full bg-white text-gray-700 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-100 disabled:cursor-not-allowed"
                                :class="{ 'bg-gray-50': !isEditing }"
                                :placeholder="
                                    isEditing
                                        ? 'Enter SKU'
                                        : 'Auto-generated after selecting category'
                                "
                            />
                            <div
                                v-if="!isEditing"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                            >
                                <svg
                                    class="h-5 w-5 text-gray-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            {{
                                isEditing
                                    ? "You can modify the SKU when editing"
                                    : ""
                            }}
                        </p>
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
                                isEditing ? "Update Product" : "Create Product"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity flex items-center justify-center p-4 z-50"
        >
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
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
                            Delete Product
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Are you sure you want to delete
                            <span class="font-medium text-gray-900">{{
                                selectedProduct?.name
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
    </div>
</template>
```
