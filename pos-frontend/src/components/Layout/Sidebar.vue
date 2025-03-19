<template>
    <aside class="bg-gray-800 text-white w-64 min-h-screen fixed left-0 top-0">
        <div class="p-4">
            <h1 class="text-2xl font-bold mb-8">POS System</h1>
            <nav>
                <ul class="space-y-2">
                    <li>
                        <router-link
                            to="/dashboard"
                            class="flex items-center p-2 rounded-lg hover:bg-gray-700 transition-colors"
                            :class="{
                                'bg-gray-700': $route.path === '/dashboard',
                            }"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"
                                />
                            </svg>
                            Dashboard
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            to="/products"
                            class="flex items-center p-2 rounded-lg hover:bg-gray-700 transition-colors"
                            :class="{
                                'bg-gray-700': $route.path === '/products',
                            }"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                />
                            </svg>
                            Products
                        </router-link>
                    </li>
                    <li>
                        <router-link
                            to="/orders"
                            class="flex items-center p-2 rounded-lg hover:bg-gray-700 transition-colors"
                            :class="{
                                'bg-gray-700': $route.path === '/orders',
                            }"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            Orders
                        </router-link>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script setup>
// No additional setup needed for now
</script>
