<template>
  <div class="space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
      <h1 class="text-2xl font-semibold text-gray-900">Sales</h1>
      <div class="mt-4 sm:mt-0">
        <button
          type="button"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
        >
          New Sale
        </button>
      </div>
    </div>

    <!-- Sales List -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex flex-col">
          <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
              <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale ID</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                      <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-if="saleStore.loading">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" colspan="7">Loading...</td>
                    </tr>
                    <tr v-else-if="saleStore.error">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500" colspan="7">{{ saleStore.error }}</td>
                    </tr>
                    <tr v-else-if="saleStore.sales.length === 0">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" colspan="7">No sales found</td>
                    </tr>
                    <tr v-for="sale in saleStore.sales" :key="sale.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ sale.id }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ sale.customer?.name || 'N/A' }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ sale.total.toFixed(2) }}</td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          :class="{
                            'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                            'bg-green-100 text-green-800': sale.status === 'paid',
                            'bg-yellow-100 text-yellow-800': sale.status === 'pending',
                            'bg-red-100 text-red-800': sale.status === 'cancelled'
                          }"
                        >
                          {{ sale.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ sale.payment_method }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ new Date(sale.created_at).toLocaleDateString() }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="text-primary hover:text-primary/80">View</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- New Sale Modal (to be implemented) -->
    <!-- This will be a modal component for creating new sales -->
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useSaleStore } from '../stores/sale';

const saleStore = useSaleStore();

onMounted(() => {
  saleStore.fetchSales();
});
</script>