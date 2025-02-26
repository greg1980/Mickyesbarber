<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Payment for Booking
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Booking Summary -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Summary</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600">Date</p>
                                        <p class="font-medium">{{ formatDate(booking.booking_date) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Time</p>
                                        <p class="font-medium">{{ booking.booking_time }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Barber</p>
                                        <p class="font-medium">{{ booking.barber.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Deposit Amount</p>
                                        <p class="font-medium">£{{ formatPrice(amount) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <div v-if="!paymentProcessed">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>

                            <!-- Stripe Elements Card -->
                            <div class="mb-4">
                                <div ref="card" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></div>
                                <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
                            </div>

                            <!-- Payment Button -->
                            <div class="flex justify-end mt-6">
                                <button
                                    @click="handleSubmit"
                                    :disabled="processing"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition"
                                >
                                    <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ processing ? 'Processing...' : 'Pay Deposit' }}
                                </button>
                            </div>
                        </div>

                        <!-- Success Message -->
                        <div v-else class="text-center py-8">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Payment Successful!</h3>
                            <p class="text-sm text-gray-600 mb-6">Your booking has been confirmed.</p>
                            <Link
                                :href="route('dashboard')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                            >
                                Return to Dashboard
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { loadStripe } from '@stripe/stripe-js';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    booking: {
        type: Object,
        required: true
    },
    amount: {
        type: Number,
        required: true
    }
});

const stripe = ref(null);
const elements = ref(null);
const card = ref(null);
const error = ref('');
const processing = ref(false);
const paymentProcessed = ref(false);

onMounted(async () => {
    // Get the Stripe public key from Inertia shared data
    const stripeKey = usePage().props.stripe_key;

    if (!stripeKey) {
        error.value = 'Stripe key not found. Please check your configuration.';
        console.error('Stripe key is missing from configuration');
        return;
    }

    // Initialize Stripe
    try {
        stripe.value = await loadStripe(stripeKey);
        if (!stripe.value) {
            throw new Error('Failed to initialize Stripe');
        }

        elements.value = stripe.value.elements();

        // Create card element with styling
        const cardElement = elements.value.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });

        cardElement.mount(card.value);

        // Handle real-time validation errors
        cardElement.on('change', (event) => {
            error.value = event.error ? event.error.message : '';
        });
    } catch (e) {
        console.error('Stripe initialization error:', e);
        error.value = 'Failed to initialize payment system. Please try again.';
    }
});

const handleSubmit = async () => {
    if (processing.value) return;

    processing.value = true;
    error.value = '';

    try {
        // Create payment intent
        const response = await fetch(`/bookings/${props.booking.id}/payment-intent`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                amount: props.amount,
                booking_id: props.booking.id
            }),
            credentials: 'same-origin'
        });

        const data = await response.json();
        console.log('Payment intent response:', data); // Debug log

        if (!response.ok) {
            console.error('Payment intent error:', data); // Debug log
            throw new Error(data.error || `Server error: ${response.status}`);
        }

        if (data.error) {
            error.value = data.error;
            processing.value = false;
            return;
        }

        if (!data.clientSecret) {
            throw new Error('No client secret received from the server');
        }

        // Confirm card payment
        const { error: stripeError, paymentIntent } = await stripe.value.confirmCardPayment(data.clientSecret, {
            payment_method: {
                card: elements.value.getElement('card'),
                billing_details: {
                    name: props.booking.user?.name || 'Unknown',
                }
            }
        });

        if (stripeError) {
            console.error('Stripe error:', stripeError); // Debug log
            error.value = stripeError.message;
            processing.value = false;
            return;
        }

        // Payment successful
        paymentProcessed.value = true;

        // Update booking status
        await router.post(`/bookings/${props.booking.id}/payment`, {
            payment_method: 'card',
            payment_intent_id: paymentIntent.id,
        }, {
            preserveState: true,
            onSuccess: () => {
                setTimeout(() => {
                    window.location.href = route('dashboard');
                }, 2000);
            },
            onError: (errors) => {
                console.error('Booking update error:', errors); // Debug log
                error.value = 'Payment processed but failed to update booking. Please contact support.';
            }
        });

    } catch (e) {
        console.error('Payment error:', e);
        error.value = e.message || 'An error occurred while processing your payment. Please try again.';
    } finally {
        processing.value = false;
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatPrice = (price) => {
    return (price / 100).toFixed(2);
};
</script>
