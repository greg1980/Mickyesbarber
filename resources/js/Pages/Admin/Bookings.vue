<script setup>
import { Head, router, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { format } from 'date-fns';
import { watch, computed } from 'vue';

const props = defineProps({
    bookings: {
        type: Object,
        required: true
    },
    filters: {
        type: Object,
        required: true
    },
    date_filters: {
        type: Object,
        required: true
    },
    statuses: {
        type: Object,
        required: true
    }
});

// Calculate booking statistics
const bookingStats = computed(() => {
    const stats = {
        total: props.bookings.total || 0,
        confirmed: 0,
        pending_payment: 0,
        cancelled: 0,
        completed: 0,
        rescheduled: 0
    };

    props.bookings.data.forEach(booking => {
        if (stats.hasOwnProperty(booking.status)) {
            stats[booking.status]++;
        }
    });

    return stats;
});

// Calculate percentage for confirmed bookings
const confirmedBookingsPercentage = computed(() => {
    return (bookingStats.value.confirmed / bookingStats.value.total) * 100 || 0;
});

// Helper function to calculate circle path for donut chart
const calculateCirclePath = (percentage) => {
    const radius = 40;
    const circumference = 2 * Math.PI * radius;
    const dashArray = (percentage * circumference) / 100;
    return {
        circumference,
        dashArray
    };
};

const formatDate = (date) => {
    return format(new Date(date), 'MMM dd, yyyy');
};

const formatTime = (time) => {
    try {
        if (!time) return '';
        // Ensure the time string is in HH:mm:ss format
        const [hours, minutes] = time.split(':');
        const date = new Date();
        date.setHours(parseInt(hours));
        date.setMinutes(parseInt(minutes));
        return format(date, 'h:mm a');
    } catch (error) {
        console.error('Error formatting time:', error);
        return time || '';
    }
};

const getStatusClass = (status) => {
    const classes = {
        'pending_payment': 'bg-yellow-100 text-yellow-800',
        'confirmed': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800',
        'completed': 'bg-blue-100 text-blue-800',
        'rescheduled': 'bg-purple-100 text-purple-800'
    };
    return `${classes[status] || 'bg-gray-100 text-gray-800'} px-2 inline-flex text-xs leading-5 font-semibold rounded-full`;
};

const getStatusDisplay = (status) => {
    return props.statuses[status] || status;
};

const handleFilterChange = (filterType, value) => {
    router.get(
        '/admin/bookings',
        {
            ...props.filters,
            [filterType]: value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
};
</script>

<template>
    <Head title="Manage Bookings" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manage Bookings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Booking Statistics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-4 border-b border-gray-200">Booking Statistics</h3>
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
                                        stroke="#10B981"
                                        stroke-width="8"
                                        fill="none"
                                        class="group-hover:stroke-green-500 transition-all duration-300"
                                        :stroke-dasharray="calculateCirclePath(confirmedBookingsPercentage).circumference"
                                        :stroke-dashoffset="calculateCirclePath(confirmedBookingsPercentage).circumference - calculateCirclePath(confirmedBookingsPercentage).dashArray"
                                    />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <div class="text-base font-bold text-gray-900 group-hover:text-green-600">{{ Math.round(confirmedBookingsPercentage) }}%</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 text-center">
                            <div class="text-[10px] text-gray-500 group-hover:text-green-500">Confirmed Bookings</div>
                            <div class="text-sm text-gray-500 mt-1">
                                {{ bookingStats.confirmed }} confirmed out of {{ bookingStats.total }} total bookings
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-sm font-medium text-yellow-600">{{ bookingStats.pending_payment }}</div>
                                <div class="text-xs text-gray-500">Pending Payment</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-medium text-red-600">{{ bookingStats.cancelled }}</div>
                                <div class="text-xs text-gray-500">Cancelled</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-medium text-blue-600">{{ bookingStats.completed }}</div>
                                <div class="text-xs text-gray-500">Completed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-sm font-medium text-purple-600">{{ bookingStats.rescheduled }}</div>
                                <div class="text-xs text-gray-500">Rescheduled</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filters -->
                        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="date_filter" class="block text-sm font-medium text-gray-700">Date Filter</label>
                                <select
                                    id="date_filter"
                                    v-model="filters.date_filter"
                                    @change="handleFilterChange('date_filter', $event.target.value)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">All Bookings</option>
                                    <option v-for="(label, value) in date_filters" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status Filter</label>
                                <select
                                    id="status"
                                    v-model="filters.status"
                                    @change="handleFilterChange('status', $event.target.value)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option value="">All Statuses</option>
                                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Client
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Barber
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Time
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Price
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-if="bookings.data.length === 0">
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No bookings found matching the selected filters.
                                        </td>
                                    </tr>
                                    <tr v-else v-for="booking in bookings.data" :key="booking.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ booking.user.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ booking.user.email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ booking.barber?.name || 'Not Assigned' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(booking.booking_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatTime(booking.booking_time) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusClass(booking.status)">
                                                {{ getStatusDisplay(booking.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            £{{ booking.service_price }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            <template v-if="bookings.links.length > 3">
                                <div class="flex justify-between items-center">
                                    <p class="text-sm text-gray-700">
                                        Showing {{ bookings.from }} to {{ bookings.to }} of {{ bookings.total }} bookings
                                    </p>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <Link
                                            v-for="(link, key) in bookings.links"
                                            :key="key"
                                            :href="link.url"
                                            v-html="link.label"
                                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                            :class="[
                                                link.url === null ? 'text-gray-500 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-50',
                                                link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'border-gray-300',
                                                key === 0 ? 'rounded-l-md' : '',
                                                key === bookings.links.length - 1 ? 'rounded-r-md' : '',
                                            ]"
                                        />
                                    </nav>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
circle {
    transition: stroke-dashoffset 0.5s ease, stroke 0.3s ease;
}
</style>
