<script setup>
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <SidebarLayout>
        <div class="p-6">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Welcome to Mickyes Coiffure</h1>
                <p class="text-gray-600 mt-2">Your personal dashboard for managing your barber services</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center py-8">
                    <div class="text-6xl mb-4">✂️</div>
                    <h2 class="text-2xl font-semibold text-gray-900 mb-2">You're logged in!</h2>
                    <p class="text-gray-600 mb-6">Welcome to your Mickyes Coiffure dashboard. Use the sidebar to navigate to different sections.</p>
                    <div class="text-sm text-gray-500">
                        <p>Professional barbering services with style and precision.</p>
                        <p class="mt-1">Your trusted destination for quality haircuts and grooming in Newcastle upon Tyne.</p>
                    </div>
                </div>
            </div>
        </div>
    </SidebarLayout>
</template>
