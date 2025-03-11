import { defineStore } from 'pinia';
import axios from 'axios';

export const useSaleStore = defineStore('sale', {
  state: () => ({
    sales: [],
    currentSale: {
      customer_id: null,
      items: [],
      total: 0,
      payment_method: 'cash',
      status: 'pending'
    },
    loading: false,
    error: null
  }),

  getters: {
    saleTotal: (state) => {
      return state.currentSale.items.reduce((total, item) => {
        return total + (item.price * item.quantity);
      }, 0);
    },

    itemCount: (state) => {
      return state.currentSale.items.reduce((count, item) => {
        return count + item.quantity;
      }, 0);
    }
  },

  actions: {
    async fetchSales() {
      this.loading = true;
      try {
        const response = await axios.get('/api/sales');
        this.sales = response.data;
      } catch (error) {
        this.error = error.message;
      } finally {
        this.loading = false;
      }
    },

    addItem(product, quantity = 1) {
      const existingItem = this.currentSale.items.find(item => item.product_id === product.id);
      
      if (existingItem) {
        existingItem.quantity += quantity;
      } else {
        this.currentSale.items.push({
          product_id: product.id,
          name: product.name,
          price: product.price,
          quantity: quantity
        });
      }

      this.updateTotal();
    },

    removeItem(productId) {
      this.currentSale.items = this.currentSale.items.filter(item => item.product_id !== productId);
      this.updateTotal();
    },

    updateItemQuantity(productId, quantity) {
      const item = this.currentSale.items.find(item => item.product_id === productId);
      if (item) {
        item.quantity = quantity;
        this.updateTotal();
      }
    },

    updateTotal() {
      this.currentSale.total = this.saleTotal;
    },

    setCustomer(customerId) {
      this.currentSale.customer_id = customerId;
    },

    setPaymentMethod(method) {
      this.currentSale.payment_method = method;
    },

    resetCurrentSale() {
      this.currentSale = {
        customer_id: null,
        items: [],
        total: 0,
        payment_method: 'cash',
        status: 'pending'
      };
    },

    async completeSale() {
      try {
        const response = await axios.post('/api/sales', this.currentSale);
        this.sales.unshift(response.data);
        this.resetCurrentSale();
        return response.data;
      } catch (error) {
        this.error = error.message;
        throw error;
      }
    }
  }
});