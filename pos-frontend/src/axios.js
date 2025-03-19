import axios from "axios";

// Configure API endpoints
const API_PORT = 8000; // Change this to your API port
const BASE_URL =
    window.location.hostname === "localhost"
        ? `http://127.0.0.1:${API_PORT}`
        : window.location.origin;

console.log("API Base URL:", BASE_URL);

// Configure axios defaults
const api = axios.create({
    baseURL: "http://127.0.0.1:8000",
    timeout: 10000,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

// Request interceptor for debugging
api.interceptors.request.use(
    (config) => {
        console.log("API Request:", {
            method: config.method,
            url: config.url,
            data: config.data,
            headers: config.headers,
        });
        return config;
    },
    (error) => {
        console.error("API Request Error:", error);
        return Promise.reject(error);
    }
);

// Response interceptor for debugging
api.interceptors.response.use(
    (response) => {
        console.log("API Response:", {
            status: response.status,
            data: response.data,
            headers: response.headers,
        });
        return response;
    },
    (error) => {
        console.error("API Response Error:", {
            message: error.message,
            response: error.response
                ? {
                      status: error.response.status,
                      data: error.response.data,
                  }
                : "No response",
            config: error.config,
        });
        return Promise.reject(error);
    }
);

export default api;
