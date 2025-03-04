<template>
    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Admin Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-6">
                    <!-- Users Donut Chart -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-4 border-b border-gray-200">User Statistics</h3>
                            <div class="flex items-center justify-center pt-2">
                                <div class="relative group hover:scale-105 transition-transform duration-300 cursor-pointer">
                                    <svg class="transform -rotate-90 w-32 h-32">
                                        <circle
                                            cx="64"
                                            cy="64"
                                            r="40"
                                            stroke="#E5E7EB"
                                            stroke-width="8"
                                            fill="none"
                                            class="group-hover:stroke-gray-300 transition-colors duration-300"
                                        />
                                        <circle
                                            cx="64"
                                            cy="64"
                                            r="40"
                                            stroke="#3B82F6"
                                            stroke-width="8"
                                            fill="none"
                                            class="group-hover:stroke-blue-500 transition-all duration-300"
                                            :stroke-dasharray="calculateCirclePath(userActivePercentage).circumference"
                                            :stroke-dashoffset="calculateCirclePath(userActivePercentage).circumference - calculateCirclePath(userActivePercentage).dashArray"
                                        />
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <div class="text-base font-bold text-gray-900 -mb-0.5 group-hover:text-blue-600">{{ Math.round(userActivePercentage) }}%</div>
                                        <div class="text-[10px] text-gray-500 group-hover:text-blue-500">Active Users</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <div class="text-sm text-gray-500">
                                    {{ stats.active_users }} active out of {{ stats.total_users }} total users
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Barbers Donut Chart -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-4 border-b border-gray-200">Barber Statistics</h3>
                            <div class="flex items-center justify-center pt-2">
                                <div class="relative group hover:scale-105 transition-transform duration-300 cursor-pointer">
                                    <svg class="transform -rotate-90 w-32 h-32">
                                        <circle
                                            cx="64"
                                            cy="64"
                                            r="40"
                                            stroke="#E5E7EB"
                                            stroke-width="8"
                                            fill="none"
                                            class="group-hover:stroke-gray-300 transition-colors duration-300"
                                        />
                                        <circle
                                            cx="64"
                                            cy="64"
                                            r="40"
                                            stroke="#EAB308"
                                            stroke-width="8"
                                            fill="none"
                                            class="group-hover:stroke-yellow-500 transition-all duration-300"
                                            :stroke-dasharray="calculateCirclePath(barberActivePercentage).circumference"
                                            :stroke-dashoffset="calculateCirclePath(barberActivePercentage).circumference - calculateCirclePath(barberActivePercentage).dashArray"
                                        />
                                    </svg>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <div class="text-base font-bold text-gray-900 -mb-0.5 group-hover:text-yellow-600">{{ Math.round(barberActivePercentage) }}%</div>
                                        <div class="text-[10px] text-gray-500 group-hover:text-yellow-500">Active Barbers</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <div class="text-sm text-gray-500">
                                    {{ stats.active_barbers }} active out of {{ stats.total_barbers }} total barbers
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 pb-4 border-b border-gray-200">Quick Stats</h3>
                            <div class="space-y-4 pt-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Total Users:</span>
                                    <span class="font-semibold">{{ stats.total_users }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Active Users:</span>
                                    <span class="font-semibold text-green-600">{{ stats.active_users }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Total Barbers:</span>
                                    <span class="font-semibold">{{ stats.total_barbers }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Active Barbers:</span>
                                    <span class="font-semibold text-green-600">{{ stats.active_barbers }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-4 border-b border-gray-200">Quick Actions</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 pt-2">
                            <Link
                                :href="route('admin.users')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity-25 transition"
                            >
                                Manage Users
                            </Link>
                            <Link
                                :href="route('admin.bookings')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition"
                            >
                                Manage Bookings
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true
    }
});

// Compute percentages for the donut charts
const userActivePercentage = computed(() => {
    return (props.stats.active_users / props.stats.total_users) * 100 || 0;
});

const barberActivePercentage = computed(() => {
    return (props.stats.active_barbers / props.stats.total_barbers) * 100 || 0;
});

// Helper function to calculate circle path
const calculateCirclePath = (percentage) => {
    const radius = 40;
    const circumference = 2 * Math.PI * radius;
    const dashArray = (percentage * circumference) / 100;
    return {
        circumference,
        dashArray
    };
};
</script>

<style scoped>
circle {
    transition: stroke-dashoffset 0.5s ease, stroke 0.3s ease;
}
</style>
