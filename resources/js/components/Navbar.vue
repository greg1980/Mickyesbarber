<template>
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <Link :href="route('home')" class="text-xl font-bold">
                            Mickye's Barbers
                        </Link>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-8">
                    <Link
                        :href="route('home')"
                        class="text-gray-700 hover:text-gray-900 transition"
                    >
                        Home
                    </Link>
                    <a href="#services" class="text-gray-700 hover:text-gray-900 transition">
                        Services
                    </a>
                    <a href="#transformations" class="text-gray-700 hover:text-gray-900 transition">
                        Transformations
                    </a>
                    <a href="#reviews" class="text-gray-700 hover:text-gray-900 transition">
                        Reviews
                    </a>
                    <Link
                        :href="route('login')"
                        class="text-gray-700 hover:text-gray-900 transition"
                    >
                        Login
                    </Link>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
