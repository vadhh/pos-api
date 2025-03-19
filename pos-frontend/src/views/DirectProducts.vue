<template>
    <div class="p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Products (Direct Component)</h1>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                Add Product
            </button>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Name
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Category
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Price
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            Test Product
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">Electronics</td>
                        <td class="px-6 py-4 whitespace-nowrap">$99.99</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button
                                class="text-blue-600 hover:text-blue-900 mr-2"
                            >
                                Edit
                            </button>
                            <button class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
console.log("Direct Products component loaded");
</script>
