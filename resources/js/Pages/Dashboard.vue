<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import Button from '@/components/Button.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const dashboardData = ref({
    upcomingAppointments: 0,
    lastVisit: null,
    loyaltyPoints: 0,
    bookingStats: {
        total: 0,
        pending: 0,
        confirmed: 0,
        completed: 0,
        cancelled: 0,
        deposit_paid: 0,
        fully_paid: 0
    }
});

const isLoading = ref(true);
const error = ref(null);

// Compute percentages for the donut charts
const bookingStatusPercentage = computed(() => {
    const total = dashboardData.value.bookingStats.total || 1;
    const active = dashboardData.value.bookingStats.deposit_paid +
                  dashboardData.value.bookingStats.fully_paid;
    return (active / total) * 100;
});

const paymentStatusPercentage = computed(() => {
    const total = (dashboardData.value.bookingStats.deposit_paid +
                  dashboardData.value.bookingStats.fully_paid) || 1;
    const paid = dashboardData.value.bookingStats.fully_paid;
    return (paid / total) * 100;
});

// Helper function to calculate circle path for donut charts
const calculateCirclePath = (percentage) => {
    const radius = 40;
    const circumference = 2 * Math.PI * radius;
    const dashArray = (percentage * circumference) / 100;
    return {
        circumference,
        dashArray
    };
};

onMounted(async () => {
    try {
        const response = await axios.get(route('dashboard.data'));
        dashboardData.value = response.data;
        console.log('Dashboard Data:', dashboardData.value);
        console.log('Booking Stats:', dashboardData.value.bookingStats);
        console.log('Payment Status Calculation:', {
            total: dashboardData.value.bookingStats.deposit_paid + dashboardData.value.bookingStats.fully_paid,
            fullyPaid: dashboardData.value.bookingStats.fully_paid,
            percentage: paymentStatusPercentage.value
        });
    } catch (err) {
        error.value = 'Failed to load dashboard data';
        console.error('Error loading dashboard data:', err);
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <UserLayout>
        <div class="space-y-6">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">
                        Good morning, {{ $page.props.auth.user.name }}! 👋
                    </h2>
                    <p class="text-gray-600">
                        Welcome to your MickyesBarber dashboard. Here you can manage your appointments and preferences.
                    </p>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ error }}
            </div>

            <!-- Stats Grid -->
            <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Booking Status Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Status</h3>
                        <div class="flex items-center justify-center pt-2">
                            <div class="relative group hover:scale-105 transition-transform duration-300">
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
                                        :stroke-dasharray="calculateCirclePath(bookingStatusPercentage).circumference"
                                        :stroke-dashoffset="calculateCirclePath(bookingStatusPercentage).circumference - calculateCirclePath(bookingStatusPercentage).dashArray"
                                    />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ Math.round(bookingStatusPercentage) }}%</div>
                                </div>
                                <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-center">
                                    <div class="text-[10px] text-gray-500">Active Bookings</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-center text-sm">
                            <div>
                                <div class="font-medium text-blue-600">{{ dashboardData.bookingStats.deposit_paid + dashboardData.bookingStats.fully_paid }}</div>
                                <div class="text-xs text-gray-500">Active</div>
                            </div>
                            <div>
                                <div class="font-medium text-yellow-600">{{ dashboardData.bookingStats.pending }}</div>
                                <div class="text-xs text-gray-500">Pending</div>
                            </div>
                            <div>
                                <div class="font-medium text-green-600">{{ dashboardData.bookingStats.completed }}</div>
                                <div class="text-xs text-gray-500">Completed</div>
                            </div>
                            <div>
                                <div class="font-medium text-red-600">{{ dashboardData.bookingStats.cancelled }}</div>
                                <div class="text-xs text-gray-500">Cancelled</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Status Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Status</h3>
                        <div class="flex items-center justify-center pt-2">
                            <div class="relative group hover:scale-105 transition-transform duration-300">
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
                                        stroke="#10B981"
                                        stroke-width="8"
                                        fill="none"
                                        class="group-hover:stroke-green-500 transition-all duration-300"
                                        :stroke-dasharray="calculateCirclePath(paymentStatusPercentage).circumference"
                                        :stroke-dashoffset="calculateCirclePath(paymentStatusPercentage).circumference - calculateCirclePath(paymentStatusPercentage).dashArray"
                                    />
                                </svg>
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ Math.round(paymentStatusPercentage) }}%</div>
                                </div>
                                <div class="absolute -bottom-6 left-1/2 transform -translate-x-1/2 text-center">
                                    <div class="text-[10px] text-gray-500">Fully Paid</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-2 text-center text-sm">
                            <div>
                                <div class="font-medium text-green-600">{{ dashboardData.bookingStats.fully_paid }}</div>
                                <div class="text-xs text-gray-500">Fully Paid</div>
                            </div>
                            <div>
                                <div class="font-medium text-blue-600">{{ dashboardData.bookingStats.deposit_paid }}</div>
                                <div class="text-xs text-gray-500">Deposit Paid</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Stats</h3>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Upcoming Appointments
                                        </dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                {{ dashboardData.upcomingAppointments }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Last Visit
                                        </dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                {{ dashboardData.lastVisit || 'Never' }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>

                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Loyalty Points
                                        </dt>
                                        <dd class="flex items-baseline">
                                            <div class="text-2xl font-semibold text-gray-900">
                                                {{ dashboardData.loyaltyPoints }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Join Our Team Section -->
            <div v-if="!$page.props.auth.user.isBarber" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Join Our Team</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Are you a skilled barber? Join our team and start serving clients today!
                            </p>
                        </div>
                        <Button
                            :href="route('barber.register')"
                            variant="primary"
                        >
                            Register as Barber
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<style scoped>
circle {
    transition: stroke-dashoffset 0.5s ease, stroke 0.3s ease;
}
</style>
