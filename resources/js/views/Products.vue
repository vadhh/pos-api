<template>
  <div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
      <h1 class="text-2xl font-semibold text-gray-900">Products</h1>
      <div class="mt-4 sm:mt-0">
        <button
          type="button"
          @click="showAddProductModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
        >
          Add Product
        </button>
      </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div>
          <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
          <div class="mt-1">
            <input
              type="text"
              name="search"
              id="search"
              v-model="productStore.filters.search"
              class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md"
              placeholder="Search products..."
            />
          </div>
        </div>

        <div>
          <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
          <select
            id="category"
            name="category"
            v-model="productStore.filters.category"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
          >
            <option value="">All Categories</option>
            <option v-for="category in productStore.categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>

        <div>
          <label for="stock" class="block text-sm font-medium text-gray-700">Stock Status</label>
          <select
            id="stock"
            name="stock"
            v-model="productStore.filters.stock"
            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md"
          >
            <option value="all">All</option>
            <option value="in_stock">In Stock</option>
            <option value="low_stock">Low Stock</option>
            <option value="out_of_stock">Out of Stock</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white shadow rounded-lg">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="productStore.loading">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" colspan="7">Loading...</td>
            </tr>
            <tr v-else-if="productStore.filteredProducts.length === 0">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" colspan="7">No products found</td>
            </tr>
            <tr v-for="product in productStore.filteredProducts" :key="product.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.sku }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ productStore.categories.find(c => c.id === product.category_id)?.name }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ product.price }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ product.stock }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                    'bg-green-100 text-green-800': product.stock > 10,
                    'bg-yellow-100 text-yellow-800': product.stock > 0 && product.stock <= 10,
                    'bg-red-100 text-red-800': product.stock === 0
                  }"
                >
                  {{ product.stock > 10 ? 'In Stock' : product.stock > 0 ? 'Low Stock' : 'Out of Stock' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="editProduct(product)"
                  class="text-primary hover:text-primary/80 mr-2"
                >
                  Edit
                </button>
                <button
                  @click="deleteProduct(product.id)"
                  class="text-danger hover:text-danger/80"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useProductStore } from '@/stores/product';

const productStore = useProductStore();

onMounted(async () => {
  await Promise.all([
    productStore.fetchProducts(),
    productStore.fetchCategories()
  ]);
});

const editProduct = (product) => {
  // To be implemented
};

const deleteProduct = async (id) => {
  if (confirm('Are you sure you want to delete this product?')) {
    await productStore.deleteProduct(id);
  }
};
</script>