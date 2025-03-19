import { defineStore } from "pinia";
import axios from "../axios";

export const useProductStore = defineStore("products", {
    state: () => ({
        products: [],
        categories: [],
        loading: false,
        error: null,
        lastFetchTime: null,
    }),

    actions: {
        async fetchCategories() {
            try {
                this.loading = true;
                const response = await axios.get("/api/v1/categories");
                console.log(
                    "ProductStore: Categories response:",
                    response.data
                );
                this.categories = response.data;
                this.error = null;
            } catch (error) {
                console.error("Error fetching categories:", error);
                this.error =
                    error.response?.data?.message ||
                    "Error fetching categories";
            } finally {
                this.loading = false;
            }
        },

        async fetchProducts() {
            this.loading = true;
            this.error = null;

            console.log("ProductStore: Fetching products from API...");

            try {
                const response = await axios.get("/api/v1/products");
                console.log(
                    "ProductStore: API response status:",
                    response.status
                );
                console.log("ProductStore: API response data:", response.data);

                // Ensure prices are numbers
                this.products = response.data.map((product) => ({
                    ...product,
                    price: Number(product.price),
                }));

                this.lastFetchTime = new Date().toISOString();
                console.log("ProductStore: Products loaded:", this.products);

                return this.products;
            } catch (err) {
                console.error("ProductStore: Error fetching from API:", err);
                this.error =
                    err.response?.data?.message ||
                    "Failed to fetch products from API";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async createProduct(product) {
            this.loading = true;
            this.error = null;

            console.log("ProductStore: Creating product:", product);

            try {
                const response = await axios.post("/api/v1/products", product);
                console.log("ProductStore: Create response:", response);
                this.products.push(response.data);
                return response.data;
            } catch (err) {
                console.error("ProductStore: Error creating product:", err);
                this.error =
                    err.response?.data?.message || "Failed to create product";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async updateProduct(id, product) {
            this.loading = true;
            this.error = null;

            console.log(`ProductStore: Updating product ${id}:`, product);

            try {
                const response = await axios.put(
                    `/api/v1/products/${id}`,
                    product
                );
                console.log("ProductStore: Update response:", response);
                const index = this.products.findIndex((p) => p.id === id);
                if (index !== -1) {
                    this.products[index] = response.data;
                }
                return response.data;
            } catch (err) {
                console.error("ProductStore: Error updating product:", err);
                this.error =
                    err.response?.data?.message || "Failed to update product";
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async deleteProduct(id) {
            this.loading = true;
            this.error = null;

            console.log(`ProductStore: Deleting product ${id}`);

            try {
                await axios.delete(`/api/v1/products/${id}`);
                console.log("ProductStore: Delete successful");
                this.products = this.products.filter((p) => p.id !== id);
            } catch (err) {
                console.error("ProductStore: Error deleting product:", err);
                this.error =
                    err.response?.data?.message || "Failed to delete product";
                throw err;
            } finally {
                this.loading = false;
            }
        },
    },
});
