import { createRouter, createWebHistory } from "vue-router";
import MainLayout from "../components/Layout/MainLayout.vue";
import ProductsView from "../views/ProductsView.vue";
import DashboardView from "../views/DashboardView.vue";
import TestView from "../views/TestView.vue";
import DirectProducts from "../views/DirectProducts.vue";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/test",
            name: "test",
            component: TestView,
        },
        {
            path: "/direct-products",
            name: "directProducts",
            component: DirectProducts,
        },
        {
            path: "/",
            component: MainLayout,
            children: [
                {
                    path: "",
                    redirect: "/dashboard",
                },
                {
                    path: "/dashboard",
                    name: "dashboard",
                    component: DashboardView,
                },
                {
                    path: "/products",
                    name: "products",
                    component: ProductsView,
                },
            ],
        },
    ],
});

export default router;
