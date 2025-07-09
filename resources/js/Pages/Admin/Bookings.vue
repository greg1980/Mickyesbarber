<template>
    <Head title="Admin - Bookings" />

    <SidebarLayout>
        <div class="p-4 sm:p-6">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Admin - Bookings</h1>

            <div class="bg-white shadow rounded-lg p-4 sm:p-6">
                <p class="text-gray-600 mb-4">Manage all bookings in the system</p>

                <div class="space-y-4">
                    <Link href="/booking/create" class="inline-flex sm:inline-flex w-full sm:w-auto items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm sm:text-base">
                        + Create New Booking
                    </Link>
                </div>

                <div class="mt-6">
                    <p class="text-xs sm:text-sm text-gray-500">Advanced booking management features coming soon...</p>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
</script>
