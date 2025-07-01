<template>
    <Head title="Booking Details" />

    <SidebarLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Booking Details</h1>

            <div class="bg-white shadow rounded-lg p-6">
                <p class="text-gray-600">Booking details page - Under development</p>

                <div class="mt-4">
                    <Link href="/customer/bookings" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        ← Back to Bookings
                    </Link>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import SidebarLayout from '@/Layouts/SidebarLayout.vue'
</script>
