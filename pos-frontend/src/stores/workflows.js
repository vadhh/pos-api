import { defineStore } from "pinia";
import api from "../axios";

export const useWorkflowStore = defineStore("workflow", {
    state: () => ({
        workflows: [],
        loading: false,
        error: null,
        lastFetchTime: null,
    }),

    actions: {
        async fetchWorkflows() {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get("/api/v1/workflows");
                this.workflows = response.data;
                this.lastFetchTime = new Date();
            } catch (error) {
                console.error("Error fetching workflows:", error);
                this.error =
                    error.response?.data?.message || "Error fetching workflows";
            } finally {
                this.loading = false;
            }
        },
        
        async fetchWorkflowSteps(workflowId) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.get(`/api/v1/workflows/${workflowId}/steps`);
                return response.data;
            } catch (error) {
                console.error("Error fetching workflow steps:", error);
                this.error =
                    error.response?.data?.message || "Error fetching workflow steps";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createWorkflow(workflowData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.post("/api/v1/workflows", workflowData);
                this.workflows.push(response.data);
                this.lastFetchTime = new Date();
                return response.data;
            } catch (error) {
                console.error("Error creating workflow:", error);
                this.error =
                    error.response?.data?.message || "Error creating workflow";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateWorkflow(id, workflowData) {
            this.loading = true;
            this.error = null;
            try {
                const response = await api.put(`/api/v1/workflows/${id}`, workflowData);
                const index = this.workflows.findIndex((w) => w.id === id);
                if (index !== -1) {
                    this.workflows[index] = response.data;
                }
                this.lastFetchTime = new Date();
                return response.data;
            } catch (error) {
                console.error("Error updating workflow:", error);
                this.error =
                    error.response?.data?.message || "Error updating workflow";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deleteWorkflow(id) {
            this.loading = true;
            this.error = null;
            try {
                await api.delete(`/api/v1/workflows/${id}`);
                // Optimistically remove the workflow from state
                this.workflows = this.workflows.filter(w => w.id !== id);
                const index = this.workflows.findIndex((w) => w.id === id);
                if (index !== -1) {
                    this.workflows.splice(index, 1);
                }
                this.lastFetchTime = new Date();
            } catch (error) {
                console.error("Error deleting workflow:", error);
                this.error =
                    error.response?.data?.message || "Error deleting workflow";
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});
