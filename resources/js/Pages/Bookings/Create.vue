<template>
    <Head title="Book Appointment" />

    <UserLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Book Appointment
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <Alert v-if="$page.props.flash.success" class="mb-6" variant="success">
                            {{ $page.props.flash.success }}
                        </Alert>
                        <Alert v-if="$page.props.flash.error" class="mb-6" variant="danger">
                            {{ $page.props.flash.error }}
                        </Alert>
                        <Alert v-if="error" class="mb-6" variant="danger">
                            {{ error }}
                        </Alert>

                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <!-- Date and Time Selection -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Calendar Selection -->
                                <div>
                                    <InputLabel value="Select Date" />
                                    <div class="mt-2 p-2 bg-white rounded-lg shadow max-w-sm">
                                        <div class="flex items-center justify-between mb-2">
                                            <button type="button" @click="previousMonth" class="p-1 hover:bg-gray-100 rounded-full">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                            <h3 class="text-sm font-semibold">{{ currentMonthName }} {{ currentYear }}</h3>
                                            <button type="button" @click="nextMonth" class="p-1 hover:bg-gray-100 rounded-full">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-7 gap-0.5 mb-1">
                                            <div v-for="day in ['S', 'M', 'T', 'W', 'T', 'F', 'S']"
                                                 :key="day"
                                                 class="text-center text-xs font-medium text-gray-600 py-1">
                                                {{ day }}
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-7 gap-0.5">
                                            <div v-for="(date, index) in calendarDays"
                                                 :key="index"
                                                 class="aspect-square">
                                                <button
                                                    type="button"
                                                    v-if="date"
                                                    :disabled="!isDateSelectable(date)"
                                                    @click="selectDate(date)"
                                                    :class="[
                                                        'w-full h-full flex items-center justify-center rounded-full text-xs',
                                                        isSelectedDate(date) ? 'bg-blue-600 text-white' :
                                                        isDateSelectable(date) ? 'hover:bg-gray-100' : 'text-gray-400 cursor-not-allowed',
                                                        isPastDate(date) ? 'text-gray-400 cursor-not-allowed' : ''
                                                    ]"
                                                >
                                                    {{ date.getDate() }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <InputError :message="form.errors.date" class="mt-2" />
                                </div>

                                <!-- Time Slots -->
                                <div v-if="form.date">
                                    <InputLabel value="Available Time Slots" />
                                    <div class="mt-2 grid grid-cols-3 gap-2">
                                        <button
                                            v-for="slot in availableSlots"
                                            :key="slot"
                                            type="button"
                                            :class="[
                                                'px-3 py-2 text-xs font-medium rounded-md transition-colors duration-150',
                                                form.time === slot
                                                    ? 'bg-blue-600 text-white'
                                                    : 'bg-gray-100 text-gray-900 hover:bg-gray-200'
                                            ]"
                                            @click="form.time = slot"
                                        >
                                            {{ formatTime(slot) }}
                                        </button>
                                    </div>
                                    <InputError :message="form.errors.time" class="mt-2" />
                                </div>
                                <div v-else class="flex items-center justify-center text-gray-500 text-sm">
                                    Please select a date to see available time slots
                                </div>
                            </div>

                            <!-- Barber Selection -->
                            <div class="mt-6">
                                <div v-if="error" class="mb-4 p-4 bg-red-50 text-red-700 rounded-md">
                                    {{ error }}
                                </div>
                                <div v-if="isLoading" class="text-center text-gray-500">
                                    Loading available barbers...
                                </div>
                                <BarberSelector
                                    v-else-if="form.date"
                                    :barbers="barbers"
                                    :selected-date="form.date"
                                    :available-barbers="availableBarberIds"
                                    @select="selectBarber"
                                />
                                <div v-else class="text-center text-gray-500">
                                    Please select a date to see available barbers
                                </div>
                                <InputError :message="form.errors.barber_id" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div class="mt-6">
                                <InputLabel for="notes" value="Additional Notes (Optional)" />
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                ></textarea>
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>

                            <!-- Payment Option -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Payment Option</label>
                                <div class="mt-2 space-y-2">
                                    <div class="flex items-center">
                                        <input
                                            type="radio"
                                            id="deposit"
                                            v-model="form.payment_type"
                                            value="deposit"
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-2 border-gray-400 cursor-pointer"
                                        >
                                        <label for="deposit" class="ml-3">
                                            <span class="block text-sm font-medium text-gray-700">Pay Deposit (£6.25)</span>
                                            <span class="block text-sm text-gray-500">Pay £6.25 now and £18.75 at the appointment</span>
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input
                                            type="radio"
                                            id="full"
                                            v-model="form.payment_type"
                                            value="full"
                                            class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-2 border-gray-400 cursor-pointer"
                                        >
                                        <label for="full" class="ml-3">
                                            <span class="block text-sm font-medium text-gray-700">Pay Full Amount (£25.00)</span>
                                            <span class="block text-sm text-gray-500">Pay the full amount now</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Stripe Elements -->
                            <div class="mt-6">
                                <InputLabel value="Card Details" />
                                <div id="card-element" class="mt-2 p-2 bg-white rounded-lg shadow max-w-sm"></div>
                                <div v-if="stripeError" class="mt-2 text-sm text-red-600">{{ stripeError }}</div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Please enter your card details to pay {{ form.payment_type === 'deposit' ? 'the deposit' : 'the full amount' }}.
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end mt-6">
                                <Button
                                    type="submit"
                                    :disabled="form.processing || processing"
                                    :processing="form.processing || processing"
                                >
                                    Book Appointment and Pay {{ form.payment_type === 'deposit' ? '£6.25 Deposit' : '£25.00' }}
                                </Button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/components/InputLabel.vue';
import TextInput from '@/components/TextInput.vue';
import InputError from '@/components/InputError.vue';
import Button from '@/components/Button.vue';
import Alert from '@/components/Alert.vue';
import BarberSelector from '@/components/BarberSelector.vue';
import { ref, watch, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { loadStripe } from '@stripe/stripe-js';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import axios from 'axios';

const props = defineProps({
    barbers: {
        type: Array,
        required: true
    }
});

const toast = useToast();
const stripe = ref(null);
const card = ref(null);
const processing = ref(false);
const error = ref('');
const stripeError = ref('');

// Calendar state
const currentDate = ref(new Date());
const selectedDate = ref(null);

// Calendar computed properties
const currentMonthName = computed(() => {
    return new Intl.DateTimeFormat('en-US', { month: 'long' }).format(currentDate.value);
});

const currentYear = computed(() => {
    return currentDate.value.getFullYear();
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDay = firstDay.getDay();

    const days = Array(42).fill(null);

    for (let i = 0; i < daysInMonth; i++) {
        days[i + startingDay] = new Date(year, month, i + 1);
    }

    return days;
});

// Calendar methods
const previousMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() - 1);
};

const nextMonth = () => {
    currentDate.value = new Date(currentDate.value.getFullYear(), currentDate.value.getMonth() + 1);
};

const isDateSelectable = (date) => {
    if (!date) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return date >= today;
};

const isPastDate = (date) => {
    if (!date) return false;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return date < today;
};

const isSelectedDate = (date) => {
    if (!date || !selectedDate.value) return false;
    return date.toDateString() === selectedDate.value.toDateString();
};

const selectDate = (date) => {
    try {
        if (!isDateSelectable(date)) return;
        selectedDate.value = date;
        form.date = date.toISOString().split('T')[0];
    } catch (err) {
        console.error('Error selecting date:', err);
        error.value = 'Failed to select date. Please try again.';
    }
};

// Time slots (1-hour intervals)
const availableSlots = ref([
    '09:00', '10:00', '11:00', '12:00', '13:00',
    '14:00', '15:00', '16:00', '17:00'
]);

const formatTime = (time) => {
    const [hours, minutes] = time.split(':');
    const date = new Date();
    date.setHours(parseInt(hours), parseInt(minutes));
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
};

const form = useForm({
    barber_id: '',
    date: '',
    time: '',
    notes: '',
    payment_type: 'deposit'
});

const availableBarberIds = ref([]);
const isLoading = ref(false);

const selectBarber = (barber) => {
    try {
        if (!barber || !barber.id) return;
        form.barber_id = barber.id;
    } catch (err) {
        console.error('Error selecting barber:', err);
        error.value = 'Failed to select barber. Please try again.';
    }
};

// Fetch available barbers when date changes
watch(() => form.date, async (newDate) => {
    if (newDate) {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await fetch(`/api/available-barbers?date=${newDate}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid response format');
            }

            availableBarberIds.value = Array.isArray(data.available_barber_ids) ? data.available_barber_ids : [];
            availableSlots.value = Array.isArray(data.available_slots) ? data.available_slots : [
                '09:00', '10:00', '11:00', '12:00', '13:00',
                '14:00', '15:00', '16:00', '17:00'
            ];

            // Reset barber selection if previously selected barber is no longer available
            if (form.barber_id && !availableBarberIds.value.includes(form.barber_id)) {
                form.barber_id = '';
            }
        } catch (err) {
            console.error('Error fetching available barbers:', err);
            error.value = 'Failed to load available barbers. Please try again.';
            availableBarberIds.value = [];
            form.barber_id = '';
        } finally {
            isLoading.value = false;
        }
    } else {
        availableBarberIds.value = [];
        form.barber_id = '';
        error.value = null;
    }
}, { immediate: false });

// Initialize Stripe
onMounted(async () => {
    try {
        const stripeKey = usePage().props.stripe_key;
        if (!stripeKey) {
            error.value = 'Stripe key is missing';
            return;
        }

        stripe.value = await loadStripe(stripeKey);
        const elements = stripe.value.elements();

        card.value = elements.create('card');

        // Add card element after payment option
        await nextTick();
        const cardElement = document.getElementById('card-element');
        if (cardElement) {
            card.value.mount('#card-element');
        }

        card.value.on('change', (event) => {
            if (event.error) {
                stripeError.value = event.error.message;
            } else {
                stripeError.value = '';
            }
        });
    } catch (e) {
        console.error('Stripe initialization error:', e);
        error.value = 'Failed to initialize payment form';
    }
});

onUnmounted(() => {
    if (card.value) {
        card.value.unmount();
    }
});

const handleSubmit = async (e) => {
    e.preventDefault();
    if (processing.value) return;

    processing.value = true;
    error.value = '';
    stripeError.value = '';

    try {
        // Validate required fields
        if (!form.barber_id || !form.date || !form.time) {
            error.value = 'Please fill in all required fields.';
            processing.value = false;
            return;
        }

        // Always validate card details since we need them for both payment types
        const { error: cardError } = await stripe.value.createPaymentMethod({
            type: 'card',
            card: card.value,
        });

        if (cardError) {
            stripeError.value = 'Please enter valid card details.';
            processing.value = false;
            return;
        }

        // Create booking first
        const bookingResponse = await axios.post(route('booking.store'), {
            barber_id: form.barber_id,
            date: form.date,
            time: form.time,
            notes: form.notes,
            payment_type: form.payment_type
        });

        console.log('Booking response:', bookingResponse.data);

        if (!bookingResponse.data || typeof bookingResponse.data.id === 'undefined') {
            console.error('Invalid booking response:', bookingResponse.data);
            throw new Error('Server returned an invalid booking response');
        }

        const bookingId = bookingResponse.data.id;
        const paymentAmount = form.payment_type === 'deposit' ? 6.25 : 25.00;

        // Create payment intent
        const paymentResponse = await axios.post(route('payment.create-intent', { booking: bookingId }), {
            payment_type: form.payment_type,
            amount: paymentAmount
        });

        if (!paymentResponse.data || !paymentResponse.data.clientSecret) {
            console.error('Invalid payment intent response:', paymentResponse.data);
            throw new Error('Failed to create payment intent');
        }

        const { clientSecret } = paymentResponse.data;

        // Confirm payment
        const result = await stripe.value.confirmCardPayment(clientSecret, {
            payment_method: {
                card: card.value,
            }
        });

        if (result.error) {
            console.error('Payment confirmation error:', result.error);
            stripeError.value = result.error.message || 'Payment failed. Please check your card details and try again.';
            return;
        }

        if (result.paymentIntent.status === 'succeeded') {
            // Process the successful payment
            await axios.post(route('payment.process', { booking: bookingId }), {
                payment_intent_id: result.paymentIntent.id,
                amount: paymentAmount,
                payment_type: form.payment_type
            });

            toast.success('Booking created and payment processed successfully!');
            window.location.href = route('bookings.user');
        }
    } catch (e) {
        console.error('Payment error:', e);
        error.value = e.response?.data?.message || e.message || 'Payment failed. Please try again.';
        if (e.response?.data?.errors) {
            const errorMessages = Object.values(e.response.data.errors).flat();
            error.value = errorMessages.join(' ');
        }
    } finally {
        processing.value = false;
    }
};
</script>

<style>
.animate-scale-in {
    animation: scale-in 0.2s ease-out;
}

@keyframes scale-in {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

