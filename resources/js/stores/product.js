import { defineStore } from 'pinia';
import axios from 'axios';

export const useProductStore = defineStore('product', {
  state: () => ({
    products: [],
    categories: [],
    loading: false,
    error: null,
    filters: {
      search: '',
      category: '',
      stock: 'all'
    }
  }),

  getters: {
    filteredProducts: (state) => {
      let filtered = [...state.products];

      if (state.filters.search) {
        filtered = filtered.filter(product =>
          product.name.toLowerCase().includes(state.filters.search.toLowerCase())
        );
      }

      if (state.filters.category) {
        filtered = filtered.filter(product =>
          product.category_id === state.filters.category
        );
      }

      if (state.filters.stock !== 'all') {
        filtered = filtered.filter(product => {
          switch (state.filters.stock) {
            case 'in_stock':
              return product.stock > 0;
            case 'low_stock':
              return product.stock > 0 && product.stock <= 10;
            case 'out_of_stock':
              return product.stock === 0;
            default:
              return true;
          }
        });
      }

      return filtered;
    }
  },

  actions: {
    async fetchProducts() {
      this.loading = true;
      try {
        const response = await axios.get('/api/products');
        this.products = response.data;
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },

    async fetchCategories() {
      try {
        const response = await axios.get('/api/categories');
        this.categories = response.data;
      } catch (error) {
        this.error = error.message;
      }
    },

    updateFilters(filters) {
      this.filters = { ...this.filters, ...filters };
    },

    async createProduct(productData) {
      try {
        const response = await axios.post('/api/products', productData);
        this.products.push(response.data);
        return response.data;
      } catch (error) {
        this.error = error.message;
        throw error;
      }
    },

    async updateProduct(id, productData) {
      try {
        const response = await axios.put(`/api/products/${id}`, productData);
        const index = this.products.findIndex(p => p.id === id);
        if (index !== -1) {
          this.products[index] = response.data;
        }
        return response.data;
      } catch (error) {
        this.error = error.message;
        throw error;
      }
    },

    async deleteProduct(id) {
      try {
        await axios.delete(`/api/products/${id}`);
        this.products = this.products.filter(p => p.id !== id);
      } catch (error) {
        this.error = error.message;
        throw error;
      }
    }
  }
});