<script setup>
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Button from '@/components/Button.vue';
import Pagination from '@/components/Pagination.vue';
import RescheduleModal from '@/components/RescheduleModal.vue';
import PaymentModal from '@/components/PaymentModal.vue';
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const props = defineProps({
    bookings: Object
});

const toast = useToast();
const showRescheduleModal = ref(false);
const selectedBooking = ref(null);
const isProcessing = ref(false);
const availableTimeSlots = ref([
    '09:00', '10:00', '11:00', '12:00', '13:00',
    '14:00', '15:00', '16:00', '17:00'
]);
const bookedSlots = ref([]);
const showPaymentModal = ref(false);
const selectedBookingForPayment = ref(null);
const showHistorical = ref(false);

const filteredBookings = computed(() => {
    if (!props.bookings?.data) return [];
    return props.bookings.data.filter(booking => {
        const isHistorical = ['cancelled', 'completed', 'rescheduled'].includes(booking.status.toLowerCase());
        return showHistorical.value ? isHistorical : !isHistorical;
    });
});

onMounted(() => {
    console.log('Bookings Index component mounted');
    console.log('PaymentModal component available:', !!PaymentModal);
    console.log('Initial showPaymentModal state:', showPaymentModal.value);
    console.log('Initial selectedBookingForPayment state:', selectedBookingForPayment.value);
});

const openRescheduleModal = (booking) => {
    selectedBooking.value = booking;
    showRescheduleModal.value = true;
    availableTimeSlots.value = [
        '09:00', '10:00', '11:00', '12:00', '13:00',
        '14:00', '15:00', '16:00', '17:00'
    ];
    bookedSlots.value = [];
};

const openPaymentModal = (booking) => {
    console.log('openPaymentModal called with booking:', booking);
    console.log('Current modal state:', showPaymentModal.value);
    console.log('Current selectedBookingForPayment:', selectedBookingForPayment.value);

    try {
        selectedBookingForPayment.value = booking;
        showPaymentModal.value = true;

        console.log('After update - modal state:', showPaymentModal.value);
        console.log('After update - selectedBookingForPayment:', selectedBookingForPayment.value);
        console.log('PaymentModal component:', !!PaymentModal);
        console.log('Is PaymentModal mounted:', document.querySelector('.payment-modal') !== null);
    } catch (error) {
        console.error('Error in openPaymentModal:', error);
    }
};

async function handleDateSelected(date) {
    try {
        const response = await axios.get('/api/available-slots', {
            params: {
                date,
                barber_id: selectedBooking.value?.barber_id
            }
        });
        availableTimeSlots.value = response.data.available_slots || [];
        bookedSlots.value = [];
    } catch (error) {
        console.error('Error fetching booked slots:', error);
        availableTimeSlots.value = [];
        bookedSlots.value = [];
    }
}

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
    if (!date) return 'Invalid Date';
    try {
        const dateObj = new Date(date);
        const weekday = dateObj.toLocaleDateString('en-GB', { weekday: 'short' });
        const day = dateObj.getDate();
        const month = dateObj.toLocaleDateString('en-GB', { month: 'short' });
        const year = dateObj.getFullYear().toString().substr(-2);
        return `${weekday} ${day} ${month} ${year}`;
    } catch (error) {
        return 'Invalid Date';
    }
};

const formatTime = (time) => {
    if (!time) return 'Invalid Time';
    try {
        const date = new Date(time);
        if (isNaN(date.getTime())) {
            throw new Error('Invalid date');
        }
        return date.toLocaleTimeString('en-GB', {
            hour: 'numeric',
            minute: 'numeric',
            hour12: false
        });
    } catch (error) {
        return 'Invalid Time';
    }
};

const getStatusDisplay = (status) => {
    const displays = {
        pending: 'Awaiting Payment',
        pending_payment: 'Awaiting Payment',
        confirmed: 'Confirmed',
        cancelled: 'Cancelled',
        completed: 'Completed',
        rescheduled: 'Rescheduled'
    };
    return displays[status] || status;
};

const formatBarberName = (name) => {
    if (!name) return '';
    return name.split(' ')
        .map(part => part[0])
        .join('.');
};

const handleRescheduled = (updatedBooking) => {
    // Update the booking in the list
    const index = props.bookings.data.findIndex(b => b.id === updatedBooking.id);
    if (index !== -1) {
        props.bookings.data[index] = updatedBooking;
    }

    // Show success message
    toast.success('Appointment rescheduled successfully!');

    // Close modal and reset state
    showRescheduleModal.value = false;
    selectedBooking.value = null;
    availableTimeSlots.value = [];
    bookedSlots.value = [];
};

const formatPrice = (value) => {
    const number = parseFloat(value) || 0;
    return number.toFixed(2);
};
</script>

<template>
    <Head title="My Bookings" />

    <UserLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4">
                            <h2 class="text-2xl font-semibold text-gray-800">{{ showHistorical ? 'Historical Bookings' : 'Active Bookings' }}</h2>
                            <Button
                                @click="showHistorical = !showHistorical"
                                variant="secondary"
                                class="text-sm"
                            >
                                Show {{ showHistorical ? 'Active' : 'Historical' }} Bookings
                            </Button>
                        </div>
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
                                        Deposit
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Balance
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="booking in filteredBookings" :key="booking.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ formatDate(booking.booking_date) }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ formatTime(booking.booking_time) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ formatBarberName(booking.barber.name) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="getStatusClass(booking.status)"
                                        >
                                            {{ getStatusDisplay(booking.status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        £{{ formatPrice(booking.service_price) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        £{{ formatPrice(booking.deposit_amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        £{{ formatPrice(booking.balance_amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <Button
                                                v-if="['pending', 'pending_payment', 'awaiting_payment', 'deposit_paid', 'confirmed'].includes(booking.status) && parseFloat(booking.balance_amount) > 0"
                                                @click="openPaymentModal(booking)"
                                                class="text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg transform transition hover:scale-105 focus:ring-emerald-500"
                                                type="button"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                                </svg>
                                                {{ parseFloat(booking.deposit_amount) > 0 ? 'Pay Balance' : 'Pay Deposit' }}
                                                (£{{ formatPrice(parseFloat(booking.deposit_amount) > 0 ? booking.balance_amount : 6.25) }})
                                            </Button>
                                            <Button
                                                v-if="['pending', 'pending_payment', 'awaiting_payment', 'deposit_paid'].includes(booking.status)"
                                                @click="$inertia.post(route('bookings.cancel', booking.id))"
                                                variant="danger"
                                                class="text-[8px] px-1.5 py-0.5 min-w-[40px]"
                                            >
                                                Cancel
                                            </Button>
                                            <Button
                                                v-if="booking.status === 'confirmed'"
                                                @click="$inertia.post(route('barber.complete-appointment', booking.id))"
                                                variant="success"
                                                class="text-[8px] px-1.5 py-0.5 min-w-[40px]"
                                            >
                                                Complete
                                            </Button>
                                            <Button
                                                v-if="['confirmed', 'deposit_paid'].includes(booking.status)"
                                                @click="openRescheduleModal(booking)"
                                                variant="info"
                                                class="text-[8px] px-1.5 py-0.5 min-w-[40px]"
                                            >
                                                Reschedule
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredBookings.length === 0">
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

                    <RescheduleModal
                        :show="showRescheduleModal"
                        :booking="selectedBooking"
                        :available-time-slots="availableTimeSlots"
                        :booked-slots="bookedSlots"
                        :is-processing="isProcessing"
                        @close="showRescheduleModal = false"
                        @cancel="showRescheduleModal = false"
                        @dateSelected="handleDateSelected"
                        @rescheduled="handleRescheduled"
                    />

                    <PaymentModal
                        v-if="showPaymentModal"
                        :show="showPaymentModal"
                        :booking="selectedBookingForPayment"
                        @close="() => {
                            console.log('Modal close event received');
                            showPaymentModal = false;
                            selectedBookingForPayment = null;
                        }"
                    />
                </div>
            </div>
        </div>
    </UserLayout>
</template>
