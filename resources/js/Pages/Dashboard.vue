<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3'
import BookingConfirmationModal from '@/components/BookingConfirmationModal.vue';
import { loadStripe } from '@stripe/stripe-js';
import BarberSelector from '@/components/BarberSelector.vue';
import axios from 'axios';
import RescheduleModal from '@/components/RescheduleModal.vue';

// You can get the user data from your auth props
const props = defineProps({
    auth: Object
});

// Get current time of day
const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 18) return 'Good afternoon';
    return 'Good evening';
};

// Calendar related data
const selectedDate = ref(new Date());
const selectedTime = ref(null);
const availableTimeSlots = ref([
    '9:00 AM', '10:00 AM', '11:00 AM',
    '1:00 PM', '2:00 PM', '3:00 PM',
    '4:00 PM', '5:00 PM', '6:00 PM', '7:00 PM'
]);

// Calendar generation logic
const getDaysInMonth = (year, month) => {
    return new Date(year, month + 1, 0).getDate();
};

const getFirstDayOfMonth = (year, month) => {
    return new Date(year, month, 1).getDay();
};

const calendarDays = computed(() => {
    const year = selectedDate.value.getFullYear();
    const month = selectedDate.value.getMonth();
    const daysInMonth = getDaysInMonth(year, month);
    const firstDayOfMonth = getFirstDayOfMonth(year, month);
    const days = [];

    // Add empty spaces for days before the first day of the month
    for (let i = 0; i < firstDayOfMonth; i++) {
        days.push({ day: '', isCurrentMonth: false });
    }

    // Add the days of the current month
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const isToday = new Date().toDateString() === date.toDateString();
        const isSelected = selectedDate.value.toDateString() === date.toDateString();

        days.push({
            day,
            isCurrentMonth: true,
            isToday,
            isSelected,
            date
        });
    }

    return days;
});

const selectDate = (date) => {
    // Create a new Date object to ensure reactivity
    selectedDate.value = new Date(date);
    selectedTime.value = null;
    fetchAvailableSlots(date);
};

const isDateSelectable = (date) => {
    if (!date) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const compareDate = new Date(date);
    compareDate.setHours(0, 0, 0, 0);
    return compareDate >= today;
};

const showConfirmationModal = ref(false);
const errors = ref({});
const successMessage = ref('');
const isProcessing = ref(false);
const errorMessage = ref('');

const handleBooking = () => {
    if (!selectedDate.value || !selectedTime.value || !selectedBarber.value) {
        successMessage.value = 'Please select a date, time, and barber before booking.';
        messageType.value = 'error';
        setTimeout(() => {
            successMessage.value = '';
        }, 3000);
        return;
    }
    showConfirmationModal.value = true;
};

// Add user bookings management
const userBookings = ref([]);

const fetchUserBookings = async () => {
    try {
        const response = await fetch('/api/user-bookings');
        const data = await response.json();
        userBookings.value = data.bookings;
    } catch (error) {
        console.error('Error fetching bookings:', error);
    }
};

// Call this when component mounts
onMounted(() => {
    fetchUserBookings();

    // Listen for booking updates
    window.Echo.channel('bookings')
        .listen('.booking.created', (e) => {
            if (selectedDate.value && e.booking.date === selectedDate.value) {
                bookedSlots.value = e.booked_slots;
            }
            if (e.booking.user_id === props.auth.user.id) {
                fetchUserBookings();
            }
        })
        .listen('.booking.cancelled', (e) => {
            if (selectedDate.value && e.booking.date === selectedDate.value) {
                bookedSlots.value = e.booked_slots;
            }
            if (e.booking.user_id === props.auth.user.id) {
                fetchUserBookings();
            }
        })
        .listen('.booking.rescheduled_from', (e) => {
            if (selectedDate.value && e.booking.date === selectedDate.value) {
                bookedSlots.value = e.booked_slots;
            }
            if (e.booking.user_id === props.auth.user.id) {
                fetchUserBookings();
            }
        })
        .listen('.booking.rescheduled_to', (e) => {
            if (selectedDate.value && e.booking.date === selectedDate.value) {
                bookedSlots.value = e.booked_slots;
            }
            if (e.booking.user_id === props.auth.user.id) {
                fetchUserBookings();
            }
        });

    // Listen for user-specific updates
    if (props.auth.user) {
        window.Echo.channel(`user.${props.auth.user.id}`)
            .listen('.booking.updated', (e) => {
                fetchUserBookings();
            });
    }

    // Listen for barber-specific updates if user is a barber
    if (props.auth.user?.isBarber) {
        window.Echo.channel(`barber.${props.auth.user.barber.id}`)
            .listen('.booking.updated', (e) => {
                fetchUserBookings();
            });
    }
});

onUnmounted(() => {
    // Clean up WebSocket connections
    window.Echo.leave('bookings');
    if (props.auth.user) {
        window.Echo.leave(`user.${props.auth.user.id}`);
    }
    if (props.auth.user?.isBarber) {
        window.Echo.leave(`barber.${props.auth.user.barber.id}`);
    }
});

// Add computed property for upcoming appointments count
const upcomingAppointments = computed(() => {
    return userBookings.value.filter(booking => {
        const bookingDate = new Date(booking.booking_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return bookingDate >= today && booking.status !== 'cancelled';
    }).length;
});

// Add these refs
const selectedBarber = ref(null);
const barbers = ref([]);
const availableBarbers = ref([]);
const canBook = computed(() => selectedDate.value && selectedTime.value && selectedBarber.value);

const onBarberSelect = (barber) => {
    selectedBarber.value = barber;
};

// Update the confirmBooking function
const confirmBooking = () => {
    const servicePrice = SERVICE_PRICE;
    const depositAmount = SERVICE_PRICE * 0.25;
    isProcessing.value = true;

    axios.post('/bookings', {
        barber_id: selectedBarber.value.id,
        booking_date: selectedDate.value.toISOString().split('T')[0],
        booking_time: selectedTime.value,
        service_price: servicePrice,
        deposit_amount: depositAmount
    })
    .then(response => {
        if (response.data.success) {
            showConfirmationModal.value = false;
            selectedTime.value = null;
            selectedBarber.value = null;
            successMessage.value = response.data.message;
            setTimeout(() => {
                window.location.href = response.data.payment_url;
            }, 1500);
        } else {
            throw new Error(response.data.message);
        }
    })
    .catch(error => {
        console.error('Booking error:', error);
        errorMessage.value = error.response?.data?.message || 'Failed to create booking. Please try again.';
        setTimeout(() => {
            errorMessage.value = '';
        }, 3000);
    })
    .finally(() => {
        isProcessing.value = false;
    });
};

// Also update the cancelBooking function to refresh the data
const cancelBooking = async (bookingId) => {
    if (confirm('Are you sure you want to cancel this booking?')) {
        try {
            await router.delete(`/bookings/${bookingId}`);
            // Fetch updated bookings immediately
            fetchUserBookings();
        } catch (error) {
            console.error('Error canceling booking:', error);
        }
    }
};

// Add these refs
const bookedSlots = ref([]);

// Update the fetchAvailableSlots function
const fetchAvailableSlots = async (date) => {
    try {
        const response = await fetch(`/api/available-slots?date=${date.toISOString().split('T')[0]}`);
        const data = await response.json();
        availableTimeSlots.value = data.slots;
        bookedSlots.value = data.booked_slots;

        // Fetch available barbers
        const barbersResponse = await fetch(`/api/available-barbers?date=${date.toISOString().split('T')[0]}&time=${selectedTime.value}`);
        const barbersData = await barbersResponse.json();
        barbers.value = barbersData.barbers;
        availableBarbers.value = barbersData.available_barber_ids;
    } catch (error) {
        console.error('Error fetching availability:', error);
    }
};

// Add a function to check if a slot is booked
const isSlotBooked = (time) => {
    return bookedSlots.value.includes(time);
};

// Update the isDateSelected computed property
const isDateSelected = (date) => {
    if (!date || !selectedDate.value) return false;
    return date.toDateString() === selectedDate.value.toDateString();
};

// Add this information panel above the booking calendar
const SERVICE_PRICE = 25.00;

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

const canCancel = (booking) => {
    if (booking.status === 'cancelled' || booking.status === 'completed') return false;
    const bookingDate = new Date(booking.booking_date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return bookingDate > today;
};

const canReschedule = (booking) => {
    return booking.status !== 'cancelled' && booking.status !== 'completed';
};

const handlePayment = async (booking) => {
    const stripe = await loadStripe(process.env.MIX_STRIPE_KEY);

    // Get payment intent
    const response = await fetch(`/bookings/${booking.id}/payment-intent`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
    });

    const data = await response.json();

    // Handle payment
    const result = await stripe.confirmCardPayment(data.clientSecret, {
        payment_method: {
            card: elements.getElement('card'),
            billing_details: {
                name: props.auth.user.name,
                email: props.auth.user.email,
            },
        },
    });

    if (result.error) {
        // Handle error
        alert(result.error.message);
    } else {
        // Payment successful
        fetchUserBookings();
    }
};

const isRescheduleModalOpen = ref(false);
const selectedBookingForReschedule = ref(null);
const rescheduleAvailableTimeSlots = ref([]);

const showRescheduleModal = async (booking) => {
    selectedBookingForReschedule.value = booking;
    // Fetch available slots for the next day
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const response = await fetch(`/api/available-slots?date=${tomorrow.toISOString().split('T')[0]}`);
    const data = await response.json();
    rescheduleAvailableTimeSlots.value = data.slots;
    isRescheduleModalOpen.value = true;
};

const handleReschedule = async (newSchedule) => {
    try {
        isProcessing.value = true;
        errorMessage.value = '';

        const response = await axios.post(`/bookings/${selectedBookingForReschedule.value.id}/reschedule`, {
            new_date: newSchedule.date,
            new_time: newSchedule.time
        });

        if (response.data.success) {
            isRescheduleModalOpen.value = false;
            selectedBookingForReschedule.value = null;
            fetchUserBookings(); // Refresh the bookings list
            successMessage.value = 'Booking rescheduled successfully!';
            setTimeout(() => {
                successMessage.value = '';
            }, 3000);
        } else {
            throw new Error(response.data.message || 'Failed to reschedule booking');
        }
    } catch (error) {
        console.error('Error rescheduling booking:', error);
        errorMessage.value = error.response?.data?.message || 'Failed to reschedule booking. Please try again.';
        setTimeout(() => {
            errorMessage.value = '';
        }, 3000);
    } finally {
        isProcessing.value = false;
    }
};

// Add the handleRescheduleDateSelected function
const handleRescheduleDateSelected = async (date) => {
    try {
        const response = await fetch(`/api/available-slots?date=${date}`);
        const data = await response.json();
        rescheduleAvailableTimeSlots.value = data.slots;
        bookedSlots.value = data.booked_slots;
    } catch (error) {
        console.error('Error fetching availability:', error);
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <div class="text-gray-900">
                                <h3 class="text-2xl font-bold mb-2">
                                    {{ getGreeting() }}, {{ auth.user.name }}! 👋
                                </h3>
                                <p class="text-gray-600">
                                    Welcome to your MickyesBarber dashboard. Here you can manage your appointments and preferences.
                                </p>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            <!-- Upcoming Appointments -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="p-5">
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
                                                        {{ upcomingAppointments }}
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Last Visit -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="p-5">
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
                                                        Never
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Loyalty Points -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="p-5">
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
                                                        0
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success Message -->
                <div v-if="successMessage" class="mb-4 rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                {{ successMessage }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Validation Errors -->
                <div v-if="Object.keys(errors).length > 0" class="mb-4 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were errors with your submission
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc space-y-1 pl-5">
                                    <li v-for="(error, field) in errors" :key="field">
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barber Registration Promotion -->
                <div v-if="!$page.props.auth.user.isBarber" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Want to Join Our Team?</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Are you a professional barber? Join our platform and start taking bookings today!
                                </p>
                            </div>
                            <Link
                                :href="route('barber.register')"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Register as Barber
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Existing Bookings -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">
                            Your Bookings
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Time
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="booking in userBookings" :key="booking.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ new Date(booking.booking_date).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ booking.booking_time }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                                                'bg-yellow-100 text-yellow-800': booking.status === 'pending_payment',
                                                'bg-green-100 text-green-800': booking.status === 'confirmed',
                                                'bg-red-100 text-red-800': booking.status === 'cancelled',
                                                'bg-blue-100 text-blue-800': booking.status === 'completed',
                                                'bg-purple-100 text-purple-800': booking.status === 'rescheduled'
                                            }">
                                                {{ getStatusDisplay(booking.status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button
                                                    v-if="canCancel(booking)"
                                                    @click="cancelBooking(booking.id)"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                >
                                                    Cancel
                                                </button>
                                                <button
                                                    v-if="canReschedule(booking)"
                                                    @click="showRescheduleModal(booking)"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                >
                                                    Reschedule
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Booking Calendar Section -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">
                            Book Your Next Appointment
                        </h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Calendar -->
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <button
                                        class="p-2 hover:bg-gray-100 rounded-full"
                                        @click="selectedDate = new Date(selectedDate.setMonth(selectedDate.getMonth() - 1))"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <span class="text-lg font-semibold">
                                        {{ selectedDate.toLocaleString('default', { month: 'long', year: 'numeric' }) }}
                                    </span>
                                    <button
                                        class="p-2 hover:bg-gray-100 rounded-full"
                                        @click="selectedDate = new Date(selectedDate.setMonth(selectedDate.getMonth() + 1))"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="grid grid-cols-7 gap-1">
                                    <!-- Day headers -->
                                    <template v-for="(day, index) in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']">
                                        <div
                                            class="text-center text-sm font-medium py-2"
                                            :class="[
                                                index === 0 || index === 6
                                                    ? 'text-red-500'
                                                    : 'text-gray-500'
                                            ]"
                                        >
                                            {{ day }}
                                        </div>
                                    </template>

                                    <!-- Calendar days -->
                                    <template v-for="{ day, isCurrentMonth, isToday, date } in calendarDays" :key="day">
                                        <button
                                            v-if="isCurrentMonth"
                                            :class="[
                                                'p-2 text-sm rounded-full w-full',
                                                isDateSelected(date) ? 'bg-green-600 text-white' :
                                                isToday ? 'bg-green-100 text-green-600' :
                                                date && (date.getDay() === 0 || date.getDay() === 6)
                                                    ? 'text-red-500' : 'text-gray-700',
                                                !isDateSelectable(date) ? 'text-gray-300 cursor-not-allowed' :
                                                'hover:bg-gray-100',
                                                'text-center'
                                            ]"
                                            :disabled="!isDateSelectable(date)"
                                            @click="selectDate(date)"
                                        >
                                            {{ day }}
                                        </button>
                                        <div v-else class="p-2"></div>
                                    </template>
                                </div>
                            </div>

                            <!-- Time Slots -->
                            <div class="bg-white rounded-lg shadow p-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">
                                    Available Time Slots
                                </h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    <button
                                        v-for="time in availableTimeSlots"
                                        :key="time"
                                        :class="[
                                            'px-4 py-2 text-sm rounded-md',
                                            selectedTime === time
                                                ? 'bg-green-600 text-white'
                                                : isSlotBooked(time)
                                                    ? 'bg-pink-100 text-pink-500 cursor-not-allowed'
                                                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                                        ]"
                                        @click="!isSlotBooked(time) && (selectedTime = time)"
                                        :disabled="isSlotBooked(time)"
                                    >
                                        {{ time }}
                                    </button>
                                </div>

                                <!-- Barber Selector -->
                                <BarberSelector
                                    v-if="selectedDate && selectedTime"
                                    :barbers="barbers"
                                    :selected-date="selectedDate"
                                    :available-barbers="availableBarbers"
                                    @select="onBarberSelect"
                                />

                                <!-- Booking Button -->
                                <div class="mt-6">
                                    <button
                                        @click="handleBooking"
                                        :disabled="!canBook"
                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed mt-4"
                                    >
                                        {{ !selectedBarber ? 'Please select a barber' : 'Book Appointment' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add this information panel above the booking calendar -->
                <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
                    <h4 class="text-lg font-medium text-gray-900 mb-2">Booking Policies</h4>
                    <ul class="list-disc pl-5 text-sm text-gray-600 space-y-1">
                        <li>A 25% deposit (£{{ SERVICE_PRICE * 0.25 }}) is required to confirm your booking</li>
                        <li>Cancellations are allowed up until the day before your appointment</li>
                        <li>Same-day cancellations are not eligible for refunds</li>
                        <li>You can reschedule your appointment if needed</li>
                        <li>The remaining balance (£{{ SERVICE_PRICE * 0.75 }}) is paid at the salon</li>
                    </ul>
                </div>

                <!-- Confirmation Modal -->
                <BookingConfirmationModal
                    :show="showConfirmationModal"
                    :booking-date="selectedDate"
                    :booking-time="selectedTime"
                    :barber="selectedBarber"
                    :service-price="SERVICE_PRICE"
                    :deposit-amount="SERVICE_PRICE * 0.25"
                    :is-processing="isProcessing"
                    :error-message="errorMessage"
                    :success-message="successMessage"
                    @close="showConfirmationModal = false"
                    @confirm="confirmBooking"
                />

                <!-- Add RescheduleModal component -->
                <RescheduleModal
                    :show="isRescheduleModalOpen"
                    :booking="selectedBookingForReschedule"
                    :available-time-slots="rescheduleAvailableTimeSlots"
                    :booked-slots="bookedSlots"
                    :is-processing="isProcessing"
                    @confirm="handleReschedule"
                    @cancel="isRescheduleModalOpen = false"
                    @dateSelected="handleRescheduleDateSelected"
                />
            </div>
        </div>

        <footer class="bg-gray-800" aria-labelledby="footer-heading">
            <h2 id="footer-heading" class="sr-only">Footer</h2>
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                    <div class="space-y-8 xl:col-span-1">
                        <span class="text-white text-xl font-bold">MickyesBarber</span>
                        <p class="text-gray-300 text-base">
                            Professional barbering services with style and precision.
                        </p>
                        <div class="flex space-x-6">
                            <a href="#" class="text-gray-400 hover:text-gray-300 transition-colors duration-300">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                </svg>
                            </a>

                            <a href="#" class="text-gray-400 hover:text-gray-300 transition-colors duration-300">
                                <span class="sr-only">Instagram</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                                </svg>
                            </a>

                            <a href="#" class="text-gray-400 hover:text-gray-300 transition-colors duration-300">
                                <span class="sr-only">Twitter</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>

                            <a href="#" class="text-gray-400 hover:text-gray-300 transition-colors duration-300">
                                <span class="sr-only">WhatsApp</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </AuthenticatedLayout>
</template>
