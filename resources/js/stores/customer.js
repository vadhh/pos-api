import { defineStore } from 'pinia';
import axios from 'axios';

export const useCustomerStore = defineStore('customer', {
  state: () => ({
    customers: [],
    loading: false,
    error: null,
    filters: {
      search: '',
      status: 'all'
    }
  }),

  getters: {
    filteredCustomers: (state) => {
      let filtered = [...state.customers];

      if (state.filters.search) {
        filtered = filtered.filter(customer =>
          customer.name.toLowerCase().includes(state.filters.search.toLowerCase()) ||
          customer.email.toLowerCase().includes(state.filters.search.toLowerCase())
        );
      }

      if (state.filters.status !== 'all') {
        filtered = filtered.filter(customer => customer.status === state.filters.status);
      }

      return filtered;
    }
  },

  actions: {
    async fetchCustomers() {
      this.loading = true;
      try {
        const response = await axios.get('/api/customers');
        this.customers = response.data;
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },

    async createCustomer(customerData) {
      this.loading = true;
      try {
        const response = await axios.post('/api/customers', customerData);
        this.customers.push(response.data);
        return response.data;
      } catch (error) {
        this.error = error.message;
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateCustomer(id, customerData) {
      this.loading = true;
      try {
        const response = await axios.put(`/api/customers/${id}`, customerData);
        const index = this.customers.findIndex(c => c.id === id);
        if (index !== -1) {
          this.customers[index] = response.data;
        }
        return response.data;
      } catch (error) {
        this.error = error.message;
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteCustomer(id) {
      this.loading = true;
      try {
        await axios.delete(`/api/customers/${id}`);
        this.customers = this.customers.filter(c => c.id !== id);
      } catch (error) {
        this.error = error.message;
        throw error;
      } finally {
        this.loading = false;
      }
    },

    setFilter(key, value) {
      this.filters[key] = value;
    }
  }
});