import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'dashboard',
      component: () => import('../views/Dashboard.vue')
    },
    {
      path: '/products',
      name: 'products',
      component: () => import('../views/Products.vue')
    },
    {
      path: '/sales',
      name: 'sales',
      component: () => import('../views/Sales.vue')
    },
    {
      path: '/customers',
      name: 'customers',
      component: () => import('../views/Customers.vue')
    }
  ]
});

export default router;