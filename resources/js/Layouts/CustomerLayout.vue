<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Top Navigation -->
        <div class="sticky top-0 z-10 flex items-center justify-between h-16 px-4 bg-white shadow-sm">
            <div class="flex items-center space-x-8">
                <Link :href="route('home')" class="text-xl font-bold text-gray-900">
                    MickyesBarber
                </Link>
                <h2 class="text-lg font-semibold text-gray-800">
                    <slot name="header" />
                </h2>
            </div>

            <!-- Right Side -->
            <div class="flex items-center space-x-4">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm text-gray-700 hover:text-gray-900"
                >
                    Logout
                </Link>
            </div>
        </div>

        <!-- Page Content -->
        <main class="p-6">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
