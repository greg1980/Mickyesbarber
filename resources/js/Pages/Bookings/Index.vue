<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import Button from '@/components/Button.vue';
import Pagination from '@/components/Pagination.vue';
import { ref } from 'vue';

const props = defineProps({
    bookings: Object
});

const getStatusClass = (status) => {
    const classes = {
        pending_payment: 'bg-yellow-100 text-yellow-800',
        confirmed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
        completed: 'bg-blue-100 text-blue-800',
        rescheduled: 'bg-purple-100 text-purple-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatTime = (time) => {
    return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-GB', {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
};

const getStatusDisplay = (status) => {
    const displays = {
        pending_payment: 'Awaiting Payment',
        confirmed: 'Confirmed',
        cancelled: 'Cancelled',
        completed: 'Completed',
        rescheduled: 'Rescheduled'
    };
    return displays[status] || status;
};
</script>

<template>
    <Head title="My Bookings" />

    <UserLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-800">My Bookings</h2>
                        <Button
                            :href="route('dashboard')"
                            variant="primary"
                        >
                            Book New Appointment
                        </Button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date & Time
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Barber
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="booking in bookings.data" :key="booking.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ formatDate(booking.booking_date) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ formatTime(booking.booking_time) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ booking.barber.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="getStatusClass(booking.status)"
                                        >
                                            {{ getStatusDisplay(booking.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        £{{ booking.service_price }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <Button
                                                v-if="booking.status === 'pending_payment'"
                                                :href="route('bookings.payment', booking.id)"
                                                variant="primary"
                                                class="text-xs"
                                            >
                                                Pay Deposit
                                            </Button>
                                            <Button
                                                v-if="['confirmed', 'pending_payment'].includes(booking.status)"
                                                @click="$inertia.delete(route('bookings.cancel', booking.id))"
                                                variant="danger"
                                                class="text-xs"
                                            >
                                                Cancel
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="bookings.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No bookings found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        <Pagination :links="bookings.links" />
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
